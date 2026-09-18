<?php

namespace App\Http\Controllers;

use App\Http\Requests\SurveyRequest;
use App\Models\SurveyLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveyController extends Controller
{
    /**
     * Display a listing of the surveys with search, filter, and pagination.
     */
    public function index(Request $request): View
    {
        $query = SurveyLocation::query()->latest();

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

        return view('surveys.index', [
            'surveys' => $surveys,
            'kecamatanOptions' => $kecamatanOptions,
            'kelurahanOptions' => $kelurahanOptions,
            'currentSearch' => $request->input('search', ''),
            'currentKecamatan' => $request->input('kecamatan', ''),
            'currentKelurahan' => $request->input('kelurahan', ''),
            'currentKoneksi' => $request->input('koneksi', ''),
        ]);
    }

    /**
     * Show the form for creating a new survey.
     */
    public function create(): View
    {
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
        $survey = SurveyLocation::create($request->validated());

        return redirect()
            ->route('surveys.index')
            ->with('success', 'Data survey berhasil ditambahkan.');
    }

    /**
     * Display the specified survey.
     */
    public function show(SurveyLocation $survey): View
    {
        return view('surveys.show', [
            'survey' => $survey,
        ]);
    }

    /**
     * Show the form for editing the specified survey.
     */
    public function edit(SurveyLocation $survey): View
    {
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
        $survey->delete();

        return redirect()
            ->route('surveys.index')
            ->with('success', 'Data survey berhasil dihapus.');
    }
}
