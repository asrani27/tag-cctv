<?php

namespace App\Http\Controllers;

use App\Models\SurveyLocation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicSurveyController extends Controller
{
    /**
     * Display public survey listing.
     */
    public function index(Request $request): View
    {
        $query = SurveyLocation::query()->latest();

        // 1. Search keyword
        $search = trim((string) ($request->input('q') ?? $request->input('search') ?? ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('alamat', 'like', "%{$search}%")
                    ->orWhere('kelurahan', 'like', "%{$search}%")
                    ->orWhere('kecamatan', 'like', "%{$search}%");
            });
        }

        // 2. Filter Kecamatan
        $currentKecamatan = $request->input('kecamatan', '');
        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $currentKecamatan);
        }

        // 3. Filter Kelurahan
        $currentKelurahan = $request->input('kelurahan', '');
        if ($request->filled('kelurahan')) {
            $query->where('kelurahan', $currentKelurahan);
        }

        // 4. Filter Konektivitas
        $currentKoneksi = $request->input('koneksi', '');
        if ($request->filled('koneksi')) {
            $koneksi = strtolower((string) $currentKoneksi);
            if ($koneksi === 'fiber' || $koneksi === 'fo') {
                $query->where('tersedia_fiber_optik', true);
            } elseif ($koneksi === '4g' || $koneksi === '5g') {
                $query->where('tersedia_4g_5g', true);
            } elseif ($koneksi === 'p2p') {
                $query->where('tersedia_link_p2p', true);
            }
        }

        $surveys = $query->paginate(12)->withQueryString();

        $kecamatanList = SurveyLocation::KECAMATAN_LIST;
        $kelurahanList = ($currentKecamatan && isset(SurveyLocation::KELURAHAN_BY_KECAMATAN[$currentKecamatan]))
            ? SurveyLocation::KELURAHAN_BY_KECAMATAN[$currentKecamatan]
            : [];

        return view('public.surveys.index', compact(
            'surveys',
            'kecamatanList',
            'kelurahanList',
            'search',
            'currentKecamatan',
            'currentKelurahan',
            'currentKoneksi'
        ));
    }

    /**
     * Display public survey detail.
     */
    public function show(int $id): View
    {
        $survey = SurveyLocation::findOrFail($id);

        return view('public.surveys.show', compact('survey'));
    }
}
