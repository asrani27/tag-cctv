<?php

namespace App\Http\Controllers;

use App\Models\SurveyLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    /**
     * Display the survey locations map.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = SurveyLocation::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($user) {
            if ($user->isSuperAdmin()) {
                $query->with('user');
            } else {
                // Regular user: ONLY see their own survey points
                $query->where('user_id', $user->id);
            }
        }

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
            'user_id',
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
        ])->map(function ($s) use ($user) {
            return [
                'id' => $s->id,
                'latitude' => $s->latitude,
                'longitude' => $s->longitude,
                'alamat' => $s->alamat,
                'kelurahan' => $s->kelurahan,
                'kecamatan' => $s->kecamatan,
                'jumlah_cctv' => $s->jumlah_cctv,
                'jumlah_ap' => $s->jumlah_ap,
                'tersedia_fiber_optik' => $s->tersedia_fiber_optik,
                'tersedia_4g_5g' => $s->tersedia_4g_5g,
                'tersedia_link_p2p' => $s->tersedia_link_p2p,
                'diinput_oleh' => $s->user?->name ?? 'Data Lama',
                'detail_url' => $user ? route('surveys.show', $s) : route('public.surveys.show', $s),
            ];
        });

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
            'isSuperAdmin' => $user?->isSuperAdmin() ?? false,
            'isUser' => $user?->isUser() ?? false,
        ]);
    }

    /**
     * Return GeoJSON representation of survey locations.
     */
    public function geojson(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = SurveyLocation::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($user) {
            if ($user->isSuperAdmin()) {
                $query->with('user');
            } else {
                $query->where('user_id', $user->id);
            }
        }

        if ($request->filled('kecamatan')) {
            $query->where('kecamatan', $request->input('kecamatan'));
        }

        if ($request->filled('kelurahan')) {
            $query->where('kelurahan', $request->input('kelurahan'));
        }

        $locations = $query->get();

        $features = $locations->map(function ($loc) use ($user) {
            $detailUrl = $user ? route('surveys.show', $loc) : route('public.surveys.show', $loc);
            $creator = $loc->user?->name ?? 'Data Lama';

            return [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float) $loc->longitude, (float) $loc->latitude],
                ],
                'properties' => [
                    'id' => $loc->id,
                    'user_id' => $loc->user_id,
                    'alamat' => $loc->alamat,
                    'kelurahan' => $loc->kelurahan,
                    'kecamatan' => $loc->kecamatan,
                    'jumlah_cctv' => $loc->jumlah_cctv,
                    'jumlah_ap' => $loc->jumlah_ap,
                    'diinput_oleh' => $creator,
                    'creator' => $creator,
                    'detail_url' => $detailUrl,
                ],
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}


