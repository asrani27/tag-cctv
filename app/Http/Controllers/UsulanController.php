<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsulanRequest;
use App\Models\Kelurahan;
use App\Models\SurveyLocation;
use App\Models\User;
use App\Models\Usulan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class UsulanController extends Controller
{
    private function getFilters(Request $request): array
    {
        $search = $request->input('search');
        if ($search === null || $search === '') {
            $search = $request->input('q', '');
        }

        return [
            'search' => (string) $search,
            'kelurahan' => (string) $request->input('kelurahan', ''),
            'kategori_titik' => (string) $request->input('kategori_titik', ''),
            'nama_lokasi' => (string) $request->input('nama_lokasi', ''),
            'status' => (string) $request->input('status', ''),
            'user_id' => (string) $request->input('user_id', ''),
        ];
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $filters = $this->getFilters($request);

        $query = Usulan::query()
            ->with(['user', 'kelurahanModel'])
            ->applyFilters($filters, $user)
            ->latest();

        $usulans = $query->paginate(10)->withQueryString();

        try {
            $kelurahanOptions = Kelurahan::orderBy('name')->pluck('name')->unique()->values();
            if ($kelurahanOptions->isEmpty()) {
                $kelurahanOptions = Usulan::whereNotNull('kelurahan')->where('kelurahan', '!=', '')->distinct()->orderBy('kelurahan')->pluck('kelurahan');
            }
        } catch (\Throwable) {
            $kelurahanOptions = Usulan::whereNotNull('kelurahan')->where('kelurahan', '!=', '')->distinct()->orderBy('kelurahan')->pluck('kelurahan');
        }

        $userOptions = $user->isSuperAdmin()
            ? User::orderBy('name')->get(['id', 'name', 'email'])
            : collect();

        return view('usulans.index', [
            'usulans' => $usulans,
            'kelurahanOptions' => $kelurahanOptions,
            'userOptions' => $userOptions,
            'kategoriOptions' => Usulan::KATEGORI_TITIK_OPTIONS,
            'namaLokasiOptions' => Usulan::NAMA_LOKASI_OPTIONS,
            'statusOptions' => Usulan::STATUS_OPTIONS,
            'currentSearch' => $filters['search'],
            'currentKelurahan' => $filters['kelurahan'],
            'currentKategori' => $filters['kategori_titik'],
            'currentNamaLokasi' => $filters['nama_lokasi'],
            'currentStatus' => $filters['status'],
            'currentUserId' => $filters['user_id'],
            'isSuperAdmin' => $user->isSuperAdmin(),
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Usulan::class);

        $user = $request->user();
        $defaultKelurahan = null;
        $defaultKecamatan = null;

        if ($user && $user->isKelurahan()) {
            $defaultKelurahan = $user->getKelurahanName();
            if ($defaultKelurahan) {
                try {
                    $kel = Kelurahan::with('kecamatan')->where('name', $defaultKelurahan)->first();
                    if ($kel && $kel->kecamatan) {
                        $defaultKecamatan = $kel->kecamatan->name;
                    }
                } catch (\Throwable) {
                }
            }
        }

        return view('usulans.create', [
            'kelurahanMap' => SurveyLocation::getKelurahanMap(),
            'kecamatanList' => SurveyLocation::getKecamatanList(),
            'namaLokasiOptions' => Usulan::NAMA_LOKASI_OPTIONS,
            'kategoriTitikOptions' => Usulan::KATEGORI_TITIK_OPTIONS,
            'infrastrukturOptions' => Usulan::INFRASTRUKTUR_PENYANGGA_OPTIONS,
            'sumberListrikOptions' => Usulan::SUMBER_LISTRIK_OPTIONS,
            'cctvEksistingOptions' => Usulan::CCTV_EKSISTING_OPTIONS,
            'defaultKelurahan' => $defaultKelurahan,
            'defaultKecamatan' => $defaultKecamatan,
        ]);
    }

    public function store(UsulanRequest $request): RedirectResponse
    {
        Gate::authorize('create', Usulan::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if (! empty($data['kelurahan']) && empty($data['kecamatan'])) {
            try {
                $kel = Kelurahan::with('kecamatan')->where('name', $data['kelurahan'])->first();
                if ($kel && $kel->kecamatan) {
                    $data['kecamatan'] = $kel->kecamatan->name;
                }
            } catch (\Throwable) {
            }
        }

        Usulan::create($data);

        return redirect()->route('usulans.index')->with('success', 'Data usulan titik baru berhasil ditambahkan.');
    }

    public function show(Usulan $usulan): View
    {
        Gate::authorize('view', $usulan);
        $usulan->load(['user', 'kelurahanModel']);

        return view('usulans.show', compact('usulan'));
    }

    public function edit(Usulan $usulan): View
    {
        Gate::authorize('update', $usulan);

        return view('usulans.edit', [
            'usulan' => $usulan,
            'kelurahanMap' => SurveyLocation::getKelurahanMap(),
            'kecamatanList' => SurveyLocation::getKecamatanList(),
            'namaLokasiOptions' => Usulan::NAMA_LOKASI_OPTIONS,
            'kategoriTitikOptions' => Usulan::KATEGORI_TITIK_OPTIONS,
            'infrastrukturOptions' => Usulan::INFRASTRUKTUR_PENYANGGA_OPTIONS,
            'sumberListrikOptions' => Usulan::SUMBER_LISTRIK_OPTIONS,
            'cctvEksistingOptions' => Usulan::CCTV_EKSISTING_OPTIONS,
            'statusOptions' => Usulan::STATUS_OPTIONS,
        ]);
    }

    public function update(UsulanRequest $request, Usulan $usulan): RedirectResponse
    {
        Gate::authorize('update', $usulan);

        $data = $request->validated();
        if (! empty($data['kelurahan']) && empty($data['kecamatan'])) {
            try {
                $kel = Kelurahan::with('kecamatan')->where('name', $data['kelurahan'])->first();
                if ($kel && $kel->kecamatan) {
                    $data['kecamatan'] = $kel->kecamatan->name;
                }
            } catch (\Throwable) {
            }
        }

        $usulan->update($data);

        return redirect()->route('usulans.show', $usulan)->with('success', 'Data usulan berhasil diperbarui.');
    }

    public function destroy(Usulan $usulan): RedirectResponse
    {
        Gate::authorize('delete', $usulan);
        $usulan->delete();

        return redirect()->route('usulans.index')->with('success', 'Data usulan berhasil dihapus.');
    }
}
