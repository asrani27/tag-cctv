<?php

namespace App\Http\Controllers;

use App\Models\SurveyLocation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard with real statistics.
     */
    public function index(): View
    {
        $totalSurvey = SurveyLocation::count();
        $totalCctv = (int) SurveyLocation::sum('jumlah_cctv');
        $totalAp = (int) SurveyLocation::sum('jumlah_ap');
        $totalKecamatan = SurveyLocation::whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->distinct('kecamatan')
            ->count('kecamatan');
        $totalKelurahan = SurveyLocation::whereNotNull('kelurahan')
            ->where('kelurahan', '!=', '')
            ->distinct('kelurahan')
            ->count('kelurahan');

        // Tambahan info statistik bermanfaat
        $totalWithCoords = SurveyLocation::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();
        $totalFiber = SurveyLocation::where('tersedia_fiber_optik', true)->count();

        // 5 survey terbaru
        $recentSurveys = SurveyLocation::latest()->take(5)->get();

        return view('dashboard', [
            'totalSurvey' => $totalSurvey,
            'totalCctv' => $totalCctv,
            'totalAp' => $totalAp,
            'totalKecamatan' => $totalKecamatan,
            'totalKelurahan' => $totalKelurahan,
            'totalWithCoords' => $totalWithCoords,
            'totalFiber' => $totalFiber,
            'recentSurveys' => $recentSurveys,
        ]);
    }
}
