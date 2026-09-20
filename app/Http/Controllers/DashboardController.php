<?php

namespace App\Http\Controllers;

use App\Models\SurveyLocation;
use App\Models\SurveyPhoto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard with role-based statistics.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
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
            $totalUsers = User::count();

            $totalWithCoords = SurveyLocation::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->count();
            $totalFiber = SurveyLocation::where('tersedia_fiber_optik', true)->count();
            $totalPhotos = SurveyPhoto::count();

            // 5 survey terbaru dari semua user
            $recentSurveys = SurveyLocation::with('user')->latest()->take(5)->get();

            return view('dashboard', [
                'isSuperAdmin' => true,
                'totalSurvey' => $totalSurvey,
                'totalCctv' => $totalCctv,
                'totalAp' => $totalAp,
                'totalKecamatan' => $totalKecamatan,
                'totalKelurahan' => $totalKelurahan,
                'totalUsers' => $totalUsers,
                'totalWithCoords' => $totalWithCoords,
                'totalFiber' => $totalFiber,
                'totalPhotos' => $totalPhotos,
                'recentSurveys' => $recentSurveys,
            ]);
        }

        // Regular User: statistics strictly scoped to the authenticated user
        $userSurveys = SurveyLocation::where('user_id', $user->id);

        $totalSurvey = (clone $userSurveys)->count();
        $totalCctv = (int) (clone $userSurveys)->sum('jumlah_cctv');
        $totalAp = (int) (clone $userSurveys)->sum('jumlah_ap');
        $totalKecamatan = (clone $userSurveys)
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->distinct('kecamatan')
            ->count('kecamatan');
        $totalKelurahan = (clone $userSurveys)
            ->whereNotNull('kelurahan')
            ->where('kelurahan', '!=', '')
            ->distinct('kelurahan')
            ->count('kelurahan');

        $totalWithCoords = (clone $userSurveys)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->count();
        $totalFiber = (clone $userSurveys)
            ->where('tersedia_fiber_optik', true)
            ->count();

        $surveyIds = (clone $userSurveys)->pluck('id');
        $totalPhotos = SurveyPhoto::whereIn('survey_location_id', $surveyIds)->count();

        // 5 survey terbaru hanya milik user yang bersangkutan
        $recentSurveys = (clone $userSurveys)->latest()->take(5)->get();

        return view('dashboard', [
            'isSuperAdmin' => false,
            'totalSurvey' => $totalSurvey,
            'totalCctv' => $totalCctv,
            'totalAp' => $totalAp,
            'totalKecamatan' => $totalKecamatan,
            'totalKelurahan' => $totalKelurahan,
            'totalUsers' => null,
            'totalWithCoords' => $totalWithCoords,
            'totalFiber' => $totalFiber,
            'totalPhotos' => $totalPhotos,
            'recentSurveys' => $recentSurveys,
        ]);
    }
}

