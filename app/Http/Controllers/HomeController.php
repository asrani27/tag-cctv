<?php

namespace App\Http\Controllers;

use App\Models\SurveyLocation;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display public landing page.
     */
    public function index(): View
    {
        // 1. Calculate actual database statistics safely
        $stats = [
            'total_survey' => SurveyLocation::count(),
            'total_cctv' => (int) (SurveyLocation::sum('jumlah_cctv') ?? 0),
            'total_ap' => (int) (SurveyLocation::sum('jumlah_ap') ?? 0),
            'total_kecamatan' => SurveyLocation::whereNotNull('kecamatan')
                ->where('kecamatan', '!=', '')
                ->distinct('kecamatan')
                ->count('kecamatan'),
            'total_kelurahan' => SurveyLocation::whereNotNull('kelurahan')
                ->where('kelurahan', '!=', '')
                ->distinct('kelurahan')
                ->count('kelurahan'),
            'total_fo' => SurveyLocation::where('tersedia_fiber_optik', true)->count(),
        ];

        // 2. Query valid coordinates for map markers (exclude null coordinates)
        $mapSurveys = SurveyLocation::withCoordinates()
            ->select([
                'id',
                'alamat',
                'kecamatan',
                'kelurahan',
                'latitude',
                'longitude',
                'jumlah_cctv',
                'jumlah_ap',
                'tersedia_fiber_optik',
            ])
            ->get();

        // 3. Query recent survey records
        $recentSurveys = SurveyLocation::latest()
            ->take(6)
            ->select([
                'id',
                'alamat',
                'kecamatan',
                'kelurahan',
                'rt',
                'rw',
                'latitude',
                'longitude',
                'jumlah_cctv',
                'jumlah_ap',
                'tersedia_fiber_optik',
                'tersedia_4g_5g',
                'tersedia_link_p2p',
                'created_at',
            ])
            ->get();

        return view('home', compact('stats', 'mapSurveys', 'recentSurveys'));
    }
}
