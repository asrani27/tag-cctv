<?php

namespace App\Http\Controllers;

use App\Exports\UsulanExport;
use App\Http\Requests\UsulanRequest;
use App\Models\Kelurahan;
use App\Models\SurveyLocation;
use App\Models\User;
use App\Models\Usulan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UsulanController extends Controller
{
    private function getFilters(Request $request): array
    {
        $search = $request->input('search');
        if ($search === null || $search === '') {
            $search = $request->input('q', '');
        }

        $tab = $request->input('tab', Usulan::JENIS_CCTV);
        if (! in_array($tab, [Usulan::JENIS_CCTV, Usulan::JENIS_WIFI], true)) {
            $tab = Usulan::JENIS_CCTV;
        }

        return [
            'tab' => $tab,
            'jenis' => $tab,
            'search' => (string) $search,
            'kelurahan' => (string) $request->input('kelurahan', ''),
            'kategori_titik' => (string) $request->input('kategori_titik', ''),
            'nama_lokasi' => (string) $request->input('nama_lokasi', ''),
            'nama_fasilitas_publik' => (string) $request->input('nama_fasilitas_publik', ''),
            'status' => (string) $request->input('status', ''),
            'user_id' => (string) $request->input('user_id', ''),
        ];
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $filters = $this->getFilters($request);

        $countCctv = Usulan::query()
            ->applyFilters(['jenis' => Usulan::JENIS_CCTV], $user)
            ->count();

        $countWifi = Usulan::query()
            ->applyFilters(['jenis' => Usulan::JENIS_WIFI], $user)
            ->count();

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
            'activeTab' => $filters['tab'],
            'countCctv' => $countCctv,
            'countWifi' => $countWifi,
            'kelurahanOptions' => $kelurahanOptions,
            'userOptions' => $userOptions,
            'kategoriOptions' => Usulan::KATEGORI_TITIK_OPTIONS,
            'namaLokasiOptions' => Usulan::NAMA_LOKASI_OPTIONS,
            'fasilitasPublikOptions' => Usulan::FASILITAS_PUBLIK_OPTIONS,
            'tempatPemasanganOptions' => Usulan::TEMPAT_PEMASANGAN_OPTIONS,
            'ketersediaanListrikOptions' => Usulan::KETERSEDIAAN_LISTRIK_OPTIONS,
            'statusOptions' => Usulan::STATUS_OPTIONS,
            'currentSearch' => $filters['search'],
            'currentKelurahan' => $filters['kelurahan'],
            'currentKategori' => $filters['kategori_titik'],
            'currentNamaLokasi' => $filters['nama_lokasi'],
            'currentFasilitasPublik' => $filters['nama_fasilitas_publik'],
            'currentStatus' => $filters['status'],
            'currentUserId' => $filters['user_id'],
            'isSuperAdmin' => $user->isSuperAdmin(),
            'isKelurahanWithDefaultPassword' => $user->isKelurahan() && $user->hasDefaultPassword(),
        ]);
    }

    /**
     * Export usulan data to Excel.
     */
    public function export(Request $request): BinaryFileResponse
    {
        Gate::authorize('export', Usulan::class);

        $request->validate([
            'tab' => ['nullable', 'string', 'in:cctv,wifi,all'],
            'search' => ['nullable', 'string', 'max:255'],
            'q' => ['nullable', 'string', 'max:255'],
            'kelurahan' => ['nullable', 'string', 'max:255'],
            'kategori_titik' => ['nullable', 'string', 'max:255'],
            'nama_lokasi' => ['nullable', 'string', 'max:255'],
            'nama_fasilitas_publik' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'user_id' => ['nullable', 'string', 'max:50'],
        ]);

        $filters = $this->getFilters($request);
        if ($request->input('tab') === 'all') {
            $filters['tab'] = 'all';
            $filters['jenis'] = '';
        }

        $user = $request->user();

        // Build an informative & sanitized filename
        $parts = ['usulan'];
        if ($filters['tab'] === Usulan::JENIS_WIFI) {
            $parts[] = 'wifi';
        } elseif ($filters['tab'] === 'all') {
            $parts[] = 'cctv-wifi';
        } else {
            $parts[] = 'cctv';
        }

        if (! empty($filters['kelurahan'])) {
            $parts[] = Str::slug($filters['kelurahan']);
        }

        if (! empty($filters['status'])) {
            $parts[] = Str::slug($filters['status']);
        }

        $parts[] = now()->format('Y-m-d');
        $filename = implode('-', $parts).'.xlsx';

        return Excel::download(new UsulanExport($filters, $user), $filename);
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()?->isKelurahan() && $request->user()?->hasDefaultPassword()) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Anda wajib mengganti kata sandi default terlebih dahulu sebelum dapat menginput data usulan.');
        }

        Gate::authorize('create', Usulan::class);

        $user = $request->user();
        $defaultKelurahan = null;
        $defaultKecamatan = null;

        $currentJenis = $request->input('jenis', Usulan::JENIS_CCTV);
        if (! in_array($currentJenis, [Usulan::JENIS_CCTV, Usulan::JENIS_WIFI], true)) {
            $currentJenis = Usulan::JENIS_CCTV;
        }

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
            'currentJenis' => $currentJenis,
            'kelurahanMap' => SurveyLocation::getKelurahanMap(),
            'kecamatanList' => SurveyLocation::getKecamatanList(),
            'namaLokasiOptions' => Usulan::NAMA_LOKASI_OPTIONS,
            'kategoriTitikOptions' => Usulan::KATEGORI_TITIK_OPTIONS,
            'infrastrukturOptions' => Usulan::INFRASTRUKTUR_PENYANGGA_OPTIONS,
            'sumberListrikOptions' => Usulan::SUMBER_LISTRIK_OPTIONS,
            'cctvEksistingOptions' => Usulan::CCTV_EKSISTING_OPTIONS,
            'fasilitasPublikOptions' => Usulan::FASILITAS_PUBLIK_OPTIONS,
            'tempatPemasanganOptions' => Usulan::TEMPAT_PEMASANGAN_OPTIONS,
            'ketersediaanListrikOptions' => Usulan::KETERSEDIAAN_LISTRIK_OPTIONS,
            'defaultKelurahan' => $defaultKelurahan,
            'defaultKecamatan' => $defaultKecamatan,
        ]);
    }

    public function store(UsulanRequest $request): RedirectResponse
    {
        if ($request->user()?->isKelurahan() && $request->user()?->hasDefaultPassword()) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Anda wajib mengganti kata sandi default terlebih dahulu sebelum dapat menginput data usulan.');
        }

        Gate::authorize('create', Usulan::class);

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        if (empty($data['jenis'])) {
            $data['jenis'] = Usulan::JENIS_CCTV;
        }

        if (! $request->user()->isSuperAdmin()) {
            $data['status'] = 'Diajukan';
        }

        if (! empty($data['kelurahan']) && empty($data['kecamatan'])) {
            try {
                $kel = Kelurahan::with('kecamatan')->where('name', $data['kelurahan'])->first();
                if ($kel && $kel->kecamatan) {
                    $data['kecamatan'] = $kel->kecamatan->name;
                }
            } catch (\Throwable) {
            }
        }

        $usulan = Usulan::create($data);

        $label = $usulan->isWifi() ? 'titik Wifi' : 'titik CCTV';

        $redirectRoute = $usulan->isWifi()
            ? route('usulans.index', ['tab' => Usulan::JENIS_WIFI])
            : route('usulans.index');

        return redirect()->to($redirectRoute)
            ->with('success', "Data usulan {$label} baru berhasil ditambahkan.");
    }

    public function show(Usulan $usulan): View
    {
        Gate::authorize('view', $usulan);
        $usulan->load(['user', 'kelurahanModel']);

        return view('usulans.show', compact('usulan'));
    }

    public function edit(Request $request, Usulan $usulan): View|RedirectResponse
    {
        if ($request->user()?->isKelurahan() && $request->user()?->hasDefaultPassword()) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Anda wajib mengganti kata sandi default terlebih dahulu sebelum dapat mengubah data usulan.');
        }

        Gate::authorize('update', $usulan);

        $currentJenis = $usulan->jenis ?: Usulan::JENIS_CCTV;

        return view('usulans.edit', [
            'usulan' => $usulan,
            'currentJenis' => $currentJenis,
            'kelurahanMap' => SurveyLocation::getKelurahanMap(),
            'kecamatanList' => SurveyLocation::getKecamatanList(),
            'namaLokasiOptions' => Usulan::NAMA_LOKASI_OPTIONS,
            'kategoriTitikOptions' => Usulan::KATEGORI_TITIK_OPTIONS,
            'infrastrukturOptions' => Usulan::INFRASTRUKTUR_PENYANGGA_OPTIONS,
            'sumberListrikOptions' => Usulan::SUMBER_LISTRIK_OPTIONS,
            'cctvEksistingOptions' => Usulan::CCTV_EKSISTING_OPTIONS,
            'fasilitasPublikOptions' => Usulan::FASILITAS_PUBLIK_OPTIONS,
            'tempatPemasanganOptions' => Usulan::TEMPAT_PEMASANGAN_OPTIONS,
            'ketersediaanListrikOptions' => Usulan::KETERSEDIAAN_LISTRIK_OPTIONS,
            'statusOptions' => Usulan::STATUS_OPTIONS,
        ]);
    }

    public function update(UsulanRequest $request, Usulan $usulan): RedirectResponse
    {
        if ($request->user()?->isKelurahan() && $request->user()?->hasDefaultPassword()) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Anda wajib mengganti kata sandi default terlebih dahulu sebelum dapat mengubah data usulan.');
        }

        Gate::authorize('update', $usulan);

        $data = $request->validated();
        if (! $request->user()->isSuperAdmin()) {
            unset($data['status']);
        }

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
        $isWifi = $usulan->isWifi();
        $usulan->delete();

        $redirectRoute = $isWifi
            ? route('usulans.index', ['tab' => Usulan::JENIS_WIFI])
            : route('usulans.index');

        return redirect()->to($redirectRoute)->with('success', 'Data usulan berhasil dihapus.');
    }
}
