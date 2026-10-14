<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\SurveyLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KecamatanController extends Controller
{
    /**
     * Display a listing of kecamatan.
     */
    public function index(Request $request): View
    {
        $query = Kecamatan::query()
            ->withCount(['kelurahans', 'surveys'])
            ->orderBy('kode');

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $kecamatans = $query->paginate(15)->withQueryString();
        $totalKelurahan = Kelurahan::count();

        return view('kecamatans.index', [
            'kecamatans' => $kecamatans,
            'search' => $search,
            'totalKelurahan' => $totalKelurahan,
        ]);
    }

    /**
     * Show the form for creating a new kecamatan.
     */
    public function create(): View
    {
        return view('kecamatans.create');
    }

    /**
     * Store a newly created kecamatan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['nullable', 'string', 'max:20', 'unique:kecamatans,kode'],
            'name' => ['required', 'string', 'max:255', 'unique:kecamatans,name'],
        ], [
            'kode.unique' => 'Kode kecamatan sudah terdaftar.',
            'name.required' => 'Nama kecamatan wajib diisi.',
            'name.unique' => 'Nama kecamatan sudah terdaftar.',
        ]);

        $kecamatan = Kecamatan::create($validated);

        return redirect()
            ->route('kecamatans.index')
            ->with('success', "Kecamatan {$kecamatan->name} berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified kecamatan.
     */
    public function edit(Kecamatan $kecamatan): View
    {
        return view('kecamatans.edit', compact('kecamatan'));
    }

    /**
     * Update the specified kecamatan in storage.
     */
    public function update(Request $request, Kecamatan $kecamatan): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['nullable', 'string', 'max:20', Rule::unique('kecamatans', 'kode')->ignore($kecamatan->id)],
            'name' => ['required', 'string', 'max:255', Rule::unique('kecamatans', 'name')->ignore($kecamatan->id)],
        ], [
            'kode.unique' => 'Kode kecamatan sudah terdaftar.',
            'name.required' => 'Nama kecamatan wajib diisi.',
            'name.unique' => 'Nama kecamatan sudah terdaftar.',
        ]);

        $oldName = $kecamatan->name;
        $kecamatan->update($validated);

        // Keep survey locations in sync if name was updated
        if ($oldName !== $kecamatan->name) {
            SurveyLocation::where('kecamatan', $oldName)->update(['kecamatan' => $kecamatan->name]);
        }

        return redirect()
            ->route('kecamatans.index')
            ->with('success', "Data kecamatan {$kecamatan->name} berhasil diperbarui.");
    }

    /**
     * Remove the specified kecamatan from storage.
     */
    public function destroy(Kecamatan $kecamatan): RedirectResponse
    {
        if ($kecamatan->kelurahans()->exists()) {
            return redirect()
                ->route('kecamatans.index')
                ->with('error', "Kecamatan {$kecamatan->name} tidak dapat dihapus karena masih memiliki data kelurahan terkait.");
        }

        if ($kecamatan->surveys()->exists()) {
            return redirect()
                ->route('kecamatans.index')
                ->with('error', "Kecamatan {$kecamatan->name} tidak dapat dihapus karena masih digunakan pada data survey.");
        }

        $name = $kecamatan->name;
        $kecamatan->delete();

        return redirect()
            ->route('kecamatans.index')
            ->with('success', "Kecamatan {$name} berhasil dihapus.");
    }
}
