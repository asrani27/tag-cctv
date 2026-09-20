<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyRequest;
use App\Models\SurveyLocation;
use App\Models\SurveyPhoto;
use App\Models\SurveyTempPhoto;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SurveyController extends Controller
{
    /**
     * Display a listing of the surveys with search, filter, and pagination.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Base query scoped by user role
        if ($user->isSuperAdmin()) {
            $query = SurveyLocation::with(['user', 'photos'])->latest();

            // Filter User khusus superadmin
            if ($request->filled('user_id')) {
                $filterUserId = $request->input('user_id');
                if ($filterUserId === 'legacy') {
                    $query->whereNull('user_id');
                } else {
                    $query->where('user_id', $filterUserId);
                }
            }
        } else {
            // User biasa HANYA boleh melihat data yang ia input sendiri
            $query = SurveyLocation::with('photos')->where('user_id', $user->id)->latest();
        }

        // 1. Search alamat, kelurahan, atau kecamatan
        $search = trim((string) ($request->input('search') ?? $request->input('q') ?? ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('alamat', 'like', "%{$search}%")
                    ->orWhere('kelurahan', 'like', "%{$search}%")
                    ->orWhere('kecamatan', 'like', "%{$search}%");
            });
        }

        // 2. Filter Kecamatan
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->input('kecamatan'));
        }

        // 3. Filter Kelurahan
        if ($request->filled('kelurahan')) {
            $query->where('kelurahan', $request->input('kelurahan'));
        }

        // 4. Filter Konektivitas
        if ($request->filled('koneksi')) {
            $koneksi = strtolower((string) $request->input('koneksi'));
            if ($koneksi === 'fiber' || $koneksi === 'fo') {
                $query->where('tersedia_fiber_optik', true);
            } elseif ($koneksi === '4g' || $koneksi === '5g' || $koneksi === 'cellular') {
                $query->where('tersedia_4g_5g', true);
            } elseif ($koneksi === 'p2p') {
                $query->where('tersedia_link_p2p', true);
            }
        }

        $surveys = $query->paginate(10)->withQueryString();

        // Options for dropdown filters
        $kecamatanOptions = SurveyLocation::KECAMATAN_LIST;

        $kelurahanOptions = SurveyLocation::whereNotNull('kelurahan')
            ->where('kelurahan', '!=', '')
            ->distinct()
            ->orderBy('kelurahan')
            ->pluck('kelurahan');

        $userOptions = $user->isSuperAdmin()
            ? User::orderBy('name')->get(['id', 'name', 'email'])
            : collect();

        return view('surveys.index', [
            'surveys' => $surveys,
            'kecamatanOptions' => $kecamatanOptions,
            'kelurahanOptions' => $kelurahanOptions,
            'userOptions' => $userOptions,
            'currentSearch' => $request->input('search', ''),
            'currentKecamatan' => $request->input('kecamatan', ''),
            'currentKelurahan' => $request->input('kelurahan', ''),
            'currentKoneksi' => $request->input('koneksi', ''),
            'currentUserId' => $request->input('user_id', ''),
            'isSuperAdmin' => $user->isSuperAdmin(),
        ]);
    }

    /**
     * Show the form for creating a new survey.
     */
    public function create(): View
    {
        Gate::authorize('create', SurveyLocation::class);

        return view('surveys.create', [
            'kecamatanList' => SurveyLocation::KECAMATAN_LIST,
            'kelurahanMap' => SurveyLocation::KELURAHAN_BY_KECAMATAN,
        ]);
    }

    /**
     * Store a newly created survey in storage.
     */
    public function store(SurveyRequest $request): RedirectResponse
    {
        Gate::authorize('create', SurveyLocation::class);

        $survey = DB::transaction(function () use ($request) {
            $survey = new SurveyLocation($request->validated());
            $survey->user_id = $request->user()->id;
            $survey->save();

            $tempUploadIds = $request->input('temp_photos', []);
            if (is_array($tempUploadIds) && count($tempUploadIds) > 0) {
                $tempPhotos = SurveyTempPhoto::where('user_id', $request->user()->id)
                    ->whereIn('upload_id', $tempUploadIds)
                    ->get();

                $publicDisk = Storage::disk('public');
                $targetDir = "surveys/{$survey->id}";
                if (! $publicDisk->exists($targetDir)) {
                    $publicDisk->makeDirectory($targetDir);
                }

                $order = 1;
                foreach ($tempPhotos as $temp) {
                    $filename = basename($temp->file_path);
                    $newPath = "{$targetDir}/{$filename}";

                    if ($publicDisk->exists($temp->file_path)) {
                        $publicDisk->move($temp->file_path, $newPath);

                        SurveyPhoto::create([
                            'survey_location_id' => $survey->id,
                            'user_id' => $request->user()->id,
                            'file_path' => $newPath,
                            'original_name' => $temp->original_name,
                            'mime_type' => $temp->mime_type,
                            'file_size' => $temp->file_size,
                            'sort_order' => $order++,
                        ]);
                    }

                    $temp->delete();
                }
            }

            return $survey;
        });

        return redirect()
            ->route('surveys.index')
            ->with('success', 'Data survey berhasil ditambahkan.');
    }

    /**
     * Display the specified survey.
     */
    public function show(SurveyLocation $survey): View
    {
        Gate::authorize('view', $survey);

        $survey->load(['user', 'photos.user']);

        return view('surveys.show', [
            'survey' => $survey,
        ]);
    }

    /**
     * Show the form for editing the specified survey.
     */
    public function edit(SurveyLocation $survey): View
    {
        Gate::authorize('update', $survey);

        $survey->load('photos');

        return view('surveys.edit', [
            'survey' => $survey,
            'kecamatanList' => SurveyLocation::KECAMATAN_LIST,
            'kelurahanMap' => SurveyLocation::KELURAHAN_BY_KECAMATAN,
        ]);
    }

    /**
     * Update the specified survey in storage.
     */
    public function update(SurveyRequest $request, SurveyLocation $survey): RedirectResponse
    {
        Gate::authorize('update', $survey);

        $survey->update($request->validated());

        return redirect()
            ->route('surveys.show', $survey)
            ->with('success', 'Data survey berhasil diperbarui.');
    }

    /**
     * Remove the specified survey from storage.
     */
    public function destroy(SurveyLocation $survey): RedirectResponse
    {
        Gate::authorize('delete', $survey);

        $survey->delete();

        return redirect()
            ->route('surveys.index')
            ->with('success', 'Data survey berhasil dihapus.');
    }
}

