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
        return [
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

            // 4. Nama Lokasi / Titik Usulan (Gang/Jalan/Fasum)
            'nama_lokasi' => ['required', 'string', Rule::in(Usulan::NAMA_LOKASI_OPTIONS)],

            // 5. Kategori Titik (Persimpangan/ Pintu Masuk RT/ Rawan Kriminalitas/ Fasos-Fasum)
            'kategori_titik' => ['required', 'string', Rule::in(Usulan::KATEGORI_TITIK_OPTIONS)],

            // 6. Infrastruktur Penyangga (Tiang PLN / Tiang Telkom / Bangunan Posyandu/Poskamling / Belum Ada)
            'infrastruktur_penyangga' => ['nullable', 'string', Rule::in(Usulan::INFRASTRUKTUR_PENYANGGA_OPTIONS)],

            // 7. Sumber Daya Listrik (Ada/Tidak)
            'sumber_daya_listrik' => ['nullable', 'string', Rule::in(Usulan::SUMBER_LISTRIK_OPTIONS)],

            // 8. Ketersediaan CCTV Eksisting (Sudah Ada/Belum)
            'cctv_eksisting' => ['nullable', 'string', Rule::in(Usulan::CCTV_EKSISTING_OPTIONS)],

            // 9. Keterangan
            'keterangan' => ['nullable', 'string'],

            // Status Usulan (opsional)
            'status' => ['nullable', 'string', Rule::in(Usulan::STATUS_OPTIONS)],
        ];
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
        ];
    }
}
