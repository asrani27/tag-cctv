<?php

namespace App\Http\Requests;

use App\Models\Usulan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UsulanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isWifi = $this->input('jenis') === Usulan::JENIS_WIFI;

        $rules = [
            'jenis' => ['nullable', 'string', Rule::in([Usulan::JENIS_CCTV, Usulan::JENIS_WIFI])],

            // 1. Koordinat Lokasi Peta
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],

            // 2. Kelurahan & Kecamatan
            'kelurahan' => ['required', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],

            // 3. RT, Nama Ketua RT, No HP
            'rt' => ['required', 'string', 'max:20'],
            'nama_ketua_rt' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:30'],

            // 9. Keterangan
            'keterangan' => ['nullable', 'string'],

            // Status Usulan (opsional)
            'status' => ['nullable', 'string', Rule::in(Usulan::STATUS_OPTIONS)],
        ];

        if ($isWifi) {
            $rules['nama_fasilitas_publik'] = ['required', 'string', Rule::in(Usulan::FASILITAS_PUBLIK_OPTIONS)];
            $rules['jumlah_pengguna_potensial'] = ['nullable', 'max:100'];
            $rules['akses_jaringan_terdekat'] = ['nullable', 'string', 'max:255'];
            $rules['tempat_pemasangan'] = ['required', 'string', Rule::in(Usulan::TEMPAT_PEMASANGAN_OPTIONS)];
            $rules['ketersediaan_listrik'] = ['required', 'string', Rule::in(Usulan::KETERSEDIAAN_LISTRIK_OPTIONS)];

            // CCTV fields nullable for Wifi
            $rules['nama_lokasi'] = ['nullable', 'string'];
            $rules['kategori_titik'] = ['nullable', 'string'];
            $rules['infrastruktur_penyangga'] = ['nullable', 'string'];
            $rules['sumber_daya_listrik'] = ['nullable', 'string'];
            $rules['cctv_eksisting'] = ['nullable', 'string'];
        } else {
            // CCTV fields required
            $rules['nama_lokasi'] = ['required', 'string', Rule::in(Usulan::NAMA_LOKASI_OPTIONS)];
            $rules['kategori_titik'] = ['required', 'string', Rule::in(Usulan::KATEGORI_TITIK_OPTIONS)];
            $rules['infrastruktur_penyangga'] = ['nullable', 'string', Rule::in(Usulan::INFRASTRUKTUR_PENYANGGA_OPTIONS)];
            $rules['sumber_daya_listrik'] = ['nullable', 'string', Rule::in(Usulan::SUMBER_LISTRIK_OPTIONS)];
            $rules['cctv_eksisting'] = ['nullable', 'string', Rule::in(Usulan::CCTV_EKSISTING_OPTIONS)];

            // Wifi fields nullable for CCTV
            $rules['nama_fasilitas_publik'] = ['nullable', 'string'];
            $rules['jumlah_pengguna_potensial'] = ['nullable', 'string'];
            $rules['akses_jaringan_terdekat'] = ['nullable', 'string'];
            $rules['tempat_pemasangan'] = ['nullable', 'string'];
            $rules['ketersediaan_listrik'] = ['nullable', 'string'];
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'latitude.numeric' => 'Latitude harus berupa angka yang valid.',
            'latitude.between' => 'Latitude harus berada dalam rentang -90 sampai 90.',
            'longitude.numeric' => 'Longitude harus berupa angka yang valid.',
            'longitude.between' => 'Longitude harus berada dalam rentang -180 sampai 180.',
            'nama_lokasi.in' => 'Nama lokasi/titik usulan tidak valid. Pilih: Gang, Jalan, atau Fasum.',
            'kategori_titik.in' => 'Kategori titik tidak valid.',
            'infrastruktur_penyangga.in' => 'Infrastruktur penyangga tidak valid.',
            'sumber_daya_listrik.in' => 'Sumber daya listrik tidak valid. Pilih: Ada atau Tidak.',
            'cctv_eksisting.in' => 'Ketersediaan CCTV eksisting tidak valid. Pilih: Sudah Ada atau Belum.',
            'nama_fasilitas_publik.required' => 'Nama fasilitas publik wajib dipilih.',
            'nama_fasilitas_publik.in' => 'Nama fasilitas publik tidak valid. Pilih: Pos Kamling, Balai RT, Taman Warga, atau Posyandu.',
            'tempat_pemasangan.required' => 'Tempat pemasangan perangkat router/AP wajib dipilih.',
            'tempat_pemasangan.in' => 'Tempat pemasangan perangkat tidak valid. Pilih: Indoor atau Outdoor Terlindung.',
            'ketersediaan_listrik.required' => 'Ketersediaan listrik 24 jam wajib dipilih.',
            'ketersediaan_listrik.in' => 'Ketersediaan listrik tidak valid. Pilih: Tersedia atau Perlu Tarikan Baru.',
        ];
    }
}
