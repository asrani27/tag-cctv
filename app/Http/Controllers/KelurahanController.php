<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\SurveyLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KelurahanController extends Controller
{
    /**
     * Display a listing of kelurahan.
     */
    public function index(Request $request): View
    {
        $query = Kelurahan::query()
            ->with('kecamatan')
            ->withCount('surveys')
            ->orderBy('kode');

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $selectedKecamatanId = $request->input('kecamatan_id');
        if (! empty($selectedKecamatanId)) {
            $query->where('kecamatan_id', $selectedKecamatanId);
        }

        $kelurahans = $query->paginate(15)->withQueryString();
        $kecamatans = Kecamatan::orderBy('kode')->get();
        $totalKecamatan = $kecamatans->count();

        return view('kelurahans.index', [
            'kelurahans' => $kelurahans,
            'kecamatans' => $kecamatans,
            'search' => $search,
            'selectedKecamatanId' => $selectedKecamatanId,
            'totalKecamatan' => $totalKecamatan,
        ]);
    }

    /**
     * Show the form for creating a new kelurahan.
     */
    public function create(Request $request): View
    {
        $kecamatans = Kecamatan::orderBy('kode')->get();
        $selectedKecamatanId = $request->input('kecamatan_id');

        return view('kelurahans.create', [
            'kecamatans' => $kecamatans,
            'selectedKecamatanId' => $selectedKecamatanId,
        ]);
    }

    /**
     * Store a newly created kelurahan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $kecamatanId = $request->input('kecamatan_id');

        $validated = $request->validate([
            'kecamatan_id' => ['required', 'exists:kecamatans,id'],
            'kode' => ['nullable', 'string', 'max:20', 'unique:kelurahans,kode'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kelurahans', 'name')->where(fn ($query) => $query->where('kecamatan_id', $kecamatanId)),
            ],
        ], [
            'kecamatan_id.required' => 'Pilih kecamatan terlebih dahulu.',
            'kecamatan_id.exists' => 'Kecamatan yang dipilih tidak valid.',
            'kode.unique' => 'Kode kelurahan sudah terdaftar.',
            'name.required' => 'Nama kelurahan wajib diisi.',
            'name.unique' => 'Nama kelurahan sudah terdaftar di kecamatan ini.',
        ]);

        $kelurahan = Kelurahan::create($validated);

        return redirect()
            ->route('kelurahans.index', ['kecamatan_id' => $kelurahan->kecamatan_id])
            ->with('success', "Kelurahan {$kelurahan->name} berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified kelurahan.
     */
    public function edit(Kelurahan $kelurahan): View
    {
        $kecamatans = Kecamatan::orderBy('kode')->get();

        return view('kelurahans.edit', [
            'kelurahan' => $kelurahan,
            'kecamatans' => $kecamatans,
        ]);
    }

    /**
     * Update the specified kelurahan in storage.
     */
    public function update(Request $request, Kelurahan $kelurahan): RedirectResponse
    {
        $kecamatanId = $request->input('kecamatan_id');

        $validated = $request->validate([
            'kecamatan_id' => ['required', 'exists:kecamatans,id'],
            'kode' => ['nullable', 'string', 'max:20', Rule::unique('kelurahans', 'kode')->ignore($kelurahan->id)],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kelurahans', 'name')
                    ->where(fn ($query) => $query->where('kecamatan_id', $kecamatanId))
                    ->ignore($kelurahan->id),
            ],
        ], [
            'kecamatan_id.required' => 'Pilih kecamatan terlebih dahulu.',
            'kecamatan_id.exists' => 'Kecamatan yang dipilih tidak valid.',
            'kode.unique' => 'Kode kelurahan sudah terdaftar.',
            'name.required' => 'Nama kelurahan wajib diisi.',
            'name.unique' => 'Nama kelurahan sudah terdaftar di kecamatan ini.',
        ]);

        $oldName = $kelurahan->name;
        $kelurahan->update($validated);

        // Keep survey locations in sync if name was updated
        if ($oldName !== $kelurahan->name) {
            SurveyLocation::where('kelurahan', $oldName)->update(['kelurahan' => $kelurahan->name]);
        }

        return redirect()
            ->route('kelurahans.index', ['kecamatan_id' => $kelurahan->kecamatan_id])
            ->with('success', "Data kelurahan {$kelurahan->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified kelurahan from storage.
     */
    public function destroy(Kelurahan $kelurahan): RedirectResponse
    {
        if ($kelurahan->surveys()->exists()) {
            return redirect()
                ->route('kelurahans.index', ['kecamatan_id' => $kelurahan->kecamatan_id])
                ->with('error', "Kelurahan {$kelurahan->name} tidak dapat dihapus karena masih digunakan pada data survey.");
        }

        $name = $kelurahan->name;
        $kecamatanId = $kelurahan->kecamatan_id;
        $kelurahan->delete();

        return redirect()
            ->route('kelurahans.index', ['kecamatan_id' => $kecamatanId])
            ->with('success', "Kelurahan {$name} berhasil dihapus.");
    }
}
