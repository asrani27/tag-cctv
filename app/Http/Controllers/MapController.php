<?php

namespace App\Http\Controllers;

use App\Models\SurveyLocation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    /**
     * Display the public survey locations map.
     */
    public function index(Request $request): View
    {
        $query = SurveyLocation::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        $currentKecamatan = $request->input('kecamatan', '');
        $currentKelurahan = $request->input('kelurahan', '');

        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $currentKecamatan);
        }

        if ($request->filled('kelurahan')) {
            $query->where('kelurahan', $currentKelurahan);
        }

        $surveys = $query->get([
            'id',
            'latitude',
            'longitude',
            'alamat',
            'kelurahan',
            'kecamatan',
            'jumlah_cctv',
            'jumlah_ap',
            'tersedia_fiber_optik',
            'tersedia_4g_5g',
            'tersedia_link_p2p',
        ]);

        $kecamatanOptions = SurveyLocation::KECAMATAN_LIST;
        $kelurahanOptions = ($currentKecamatan && isset(SurveyLocation::KELURAHAN_BY_KECAMATAN[$currentKecamatan]))
            ? SurveyLocation::KELURAHAN_BY_KECAMATAN[$currentKecamatan]
            : [];

        return view('map.index', [
            'surveys' => $surveys,
            'kecamatanOptions' => $kecamatanOptions,
            'kelurahanOptions' => $kelurahanOptions,
            'currentKecamatan' => $currentKecamatan,
            'currentKelurahan' => $currentKelurahan,
        ]);
    }
}

