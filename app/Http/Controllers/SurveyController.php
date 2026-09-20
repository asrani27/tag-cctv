<?php

namespace App\Http\Controllers;

use App\Exports\SurveyExport;
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
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SurveyController extends Controller
{
    /**
     * Collect filter parameters from the request into a reusable array.
     */
    private function getFilters(Request $request): array
    {
        $search = $request->input('search');
        if ($search === null || $search === '') {
            $search = $request->input('q', '');
        }

        return [
            'search' => (string) $search,
            'kecamatan' => (string) $request->input('kecamatan', ''),
            'kelurahan' => (string) $request->input('kelurahan', ''),
            'koneksi' => (string) $request->input('koneksi', ''),
            'user_id' => (string) $request->input('user_id', ''),
            'tanggal_mulai' => (string) $request->input('tanggal_mulai', ''),
            'tanggal_akhir' => (string) $request->input('tanggal_akhir', ''),
        ];
    }

    /**
     * Display a listing of the surveys with search, filter, and pagination.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $filters = $this->getFilters($request);

        $query = SurveyLocation::query()
            ->applyFilters($filters, $user)
            ->latest();

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
            'currentSearch' => $filters['search'],
            'currentKecamatan' => $request->input('kecamatan', ''),
            'currentKelurahan' => $request->input('kelurahan', ''),
            'currentKoneksi' => $request->input('koneksi', ''),
            'currentUserId' => $request->input('user_id', ''),
            'currentTanggalMulai' => $request->input('tanggal_mulai', ''),
            'currentTanggalAkhir' => $request->input('tanggal_akhir', ''),
            'isSuperAdmin' => $user->isSuperAdmin(),
        ]);
    }

    /**
     * Export survey data to Excel (superadmin only).
     *
     * Uses the same filter scope as the index listing so results are consistent.
     * Pagination does NOT limit export — all matching records are included.
     */
    public function export(Request $request): BinaryFileResponse
    {
        Gate::authorize('export', SurveyLocation::class);

        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'kelurahan' => ['nullable', 'string', 'max:255'],
            'koneksi' => ['nullable', 'string', 'max:50'],
            'user_id' => ['nullable', 'string', 'max:50'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_akhir' => ['nullable', 'date'],
        ]);

        $filters = $this->getFilters($request);
        $user = $request->user();

        // Build an informative & sanitised filename
        $parts = ['survey-cctv-wifi'];
        if (! empty($filters['kecamatan'])) {
            $parts[] = Str::slug($filters['kecamatan']);
        }
        if (! empty($filters['kelurahan'])) {
            $parts[] = Str::slug($filters['kelurahan']);
        }
        $parts[] = now()->format('Y-m-d');
        $filename = implode('-', $parts) . '.xlsx';

        return Excel::download(new SurveyExport($filters, $user), $filename);
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

