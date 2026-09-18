<?php

namespace Database\Factories;

use App\Models\SurveyLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SurveyLocation>
 */
class SurveyLocationFactory extends Factory
{
    protected $model = SurveyLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kecamatan = fake()->randomElement(array_keys(SurveyLocation::KELURAHAN_BY_KECAMATAN));
        $kelurahanList = SurveyLocation::KELURAHAN_BY_KECAMATAN[$kecamatan];
        $kelurahan = fake()->randomElement($kelurahanList);

        // Coordinates around Banjarmasin: ~ -3.3194, 114.5908
        $latitude = fake()->latitude(-3.3400, -3.2900);
        $longitude = fake()->longitude(114.5700, 114.6200);

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'alamat' => fake()->streetAddress(),
            'rt' => str_pad((string) fake()->numberBetween(1, 30), 2, '0', STR_PAD_LEFT),
            'rw' => str_pad((string) fake()->numberBetween(1, 10), 2, '0', STR_PAD_LEFT),
            'kelurahan' => $kelurahan,
            'kecamatan' => $kecamatan,
            'keamanan' => fake()->boolean(80),
            'akses_wifi' => fake()->boolean(60),
            'lalin' => fake()->boolean(50),
            'panic_sensor' => fake()->boolean(30),
            'jumlah_cctv' => fake()->numberBetween(1, 6),
            'jumlah_ap' => fake()->numberBetween(0, 4),
            'bandwidth' => fake()->randomElement(['50 Mbps', '100 Mbps', '200 Mbps']),
            'backhaul' => fake()->randomElement(['Fiber Optic', 'Wireless P2P']),
            'storage' => fake()->randomElement(['1 TB', '2 TB', '4 TB', 'Cloud NVR']),
            'jenis_jalan' => fake()->randomElement(['Jalan Protokol', 'Jalan Kolektor', 'Jalan Lingkungan']),
            'tiang' => fake()->randomElement(['Ada', 'Tidak Ada', 'Menumpang']),
            'jenis_tiang' => fake()->randomElement(['Oktagonal 9M', 'Pipa Besi 7M', 'Tiang PLN']),
            'tembok' => fake()->randomElement(['Ada', 'Tidak Ada']),
            'arah_pantau' => fake()->randomElement(['Simpang Utara', 'Arah Jembatan', 'Kawasan Pasar', 'Gerbang Masuk']),
            'jarak_pantau' => fake()->randomElement(['30 meter', '50 meter', '80 meter']),
            'pencahayaan_malam' => fake()->randomElement(['Terang', 'Cukup', 'Kurang']),
            'potensi_silau' => fake()->randomElement(['Rendah', 'Sedang', 'Tinggi']),
            'penghalang' => fake()->randomElement(['Tidak Ada', 'Pohon Rimbun', 'Kabel Melintang', 'Papan Reklame']),
            'potensi_vandalisme' => fake()->randomElement(['Rendah', 'Sedang', 'Tinggi']),
            'luas_area' => fake()->randomFloat(2, 50, 500),
            'prakiraan_pengguna_max' => fake()->numberBetween(20, 200),
            'tempat_ap_latitude' => $latitude,
            'tempat_ap_longitude' => $longitude,
            'potensi_interferensi' => fake()->randomElement(['Rendah', 'Sedang', 'Tinggi']),
            'sumber_listrik' => fake()->randomElement(['PLN Pasca Bayar', 'PLN Prabayar', 'PJU']),
            'posisi_panel_latitude' => $latitude,
            'posisi_panel_longitude' => $longitude,
            'daya_tersedia' => fake()->randomElement([1300, 2200, 3500, 4400]),
            'proteksi_petir' => fake()->randomElement(['Surge Arrester & Grounding', 'Grounding Saja', 'Belum Ada']),
            'tersedia_fiber_optik' => fake()->boolean(70),
            'tersedia_4g_5g' => fake()->boolean(90),
            'tersedia_link_p2p' => fake()->boolean(40),
            'jumlah_provider' => fake()->numberBetween(1, 4),
        ];
    }
}
