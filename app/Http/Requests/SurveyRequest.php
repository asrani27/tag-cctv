<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'keamanan' => $this->boolean('keamanan'),
            'akses_wifi' => $this->boolean('akses_wifi'),
            'lalin' => $this->boolean('lalin'),
            'panic_sensor' => $this->boolean('panic_sensor'),
            'tersedia_fiber_optik' => $this->boolean('tersedia_fiber_optik'),
            'tersedia_4g_5g' => $this->boolean('tersedia_4g_5g'),
            'tersedia_link_p2p' => $this->boolean('tersedia_link_p2p'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 1. Informasi Lokasi
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'alamat' => ['nullable', 'string'],
            'rt' => ['nullable', 'string', 'max:10'],
            'rw' => ['nullable', 'string', 'max:10'],
            'kelurahan' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],

            // 2. Tujuan Layanan
            'keamanan' => ['nullable', 'boolean'],
            'akses_wifi' => ['nullable', 'boolean'],
            'lalin' => ['nullable', 'boolean'],
            'panic_sensor' => ['nullable', 'boolean'],

            // 3. Infrastruktur
            'jumlah_cctv' => ['nullable', 'integer', 'min:0'],
            'jumlah_ap' => ['nullable', 'integer', 'min:0'],
            'bandwidth' => ['nullable', 'string', 'max:255'],
            'backhaul' => ['nullable', 'string', 'max:255'],
            'storage' => ['nullable', 'string', 'max:255'],

            // 4. Kondisi Kamera
            'jenis_jalan' => ['nullable', 'string', 'max:255'],
            'tiang' => ['nullable', 'string', 'max:255'],
            'jenis_tiang' => ['nullable', 'string', 'max:255'],
            'tembok' => ['nullable', 'string', 'max:255'],
            'arah_pantau' => ['nullable', 'string', 'max:255'],
            'jarak_pantau' => ['nullable', 'string', 'max:255'],
            'pencahayaan_malam' => ['nullable', 'string', 'max:255'],
            'potensi_silau' => ['nullable', 'string', 'max:255'],
            'penghalang' => ['nullable', 'string', 'max:255'],
            'potensi_vandalisme' => ['nullable', 'string', 'max:255'],

            // 5. WiFi
            'luas_area' => ['nullable', 'numeric', 'min:0'],
            'prakiraan_pengguna_max' => ['nullable', 'integer', 'min:0'],
            'tempat_ap_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'tempat_ap_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'potensi_interferensi' => ['nullable', 'string', 'max:255'],

            // 6. Listrik
            'sumber_listrik' => ['nullable', 'string', 'max:255'],
            'posisi_panel_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'posisi_panel_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'daya_tersedia' => ['nullable', 'numeric', 'min:0'],
            'proteksi_petir' => ['nullable', 'string', 'max:255'],

            // 7. Komunikasi
            'tersedia_fiber_optik' => ['nullable', 'boolean'],
            'tersedia_4g_5g' => ['nullable', 'boolean'],
            'tersedia_link_p2p' => ['nullable', 'boolean'],
            'jumlah_provider' => ['nullable', 'integer', 'min:0'],
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
        ];
    }
}
