<?php

namespace Database\Factories;

use App\Models\Usulan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Usulan>
 */
class UsulanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'jenis' => Usulan::JENIS_CCTV,
            'latitude' => fake()->latitude(-3.36, -3.28),
            'longitude' => fake()->longitude(114.56, 114.64),
            'kelurahan' => fake()->randomElement(['Kertak Baru Ilir', 'Sungai Miai', 'Teluk Tiram', 'Kuripan', 'Pemurus Baru']),
            'kecamatan' => fake()->randomElement(['Banjarmasin Tengah', 'Banjarmasin Utara', 'Banjarmasin Barat', 'Banjarmasin Timur', 'Banjarmasin Selatan']),
            'rt' => sprintf('%02d', fake()->numberBetween(1, 30)),
            'nama_ketua_rt' => fake()->name(),
            'no_hp' => fake()->phoneNumber(),
            'nama_lokasi' => fake()->randomElement(Usulan::NAMA_LOKASI_OPTIONS),
            'kategori_titik' => fake()->randomElement(Usulan::KATEGORI_TITIK_OPTIONS),
            'infrastruktur_penyangga' => fake()->randomElement(Usulan::INFRASTRUKTUR_PENYANGGA_OPTIONS),
            'sumber_daya_listrik' => fake()->randomElement(Usulan::SUMBER_LISTRIK_OPTIONS),
            'cctv_eksisting' => fake()->randomElement(Usulan::CCTV_EKSISTING_OPTIONS),
            'keterangan' => fake()->sentence(),
            'status' => 'Diajukan',
        ];
    }

    /**
     * Indicate that the usulan is for Wifi.
     */
    public function wifi(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis' => Usulan::JENIS_WIFI,
            'nama_lokasi' => null,
            'kategori_titik' => null,
            'infrastruktur_penyangga' => null,
            'sumber_daya_listrik' => null,
            'cctv_eksisting' => null,
            'nama_fasilitas_publik' => fake()->randomElement(Usulan::FASILITAS_PUBLIK_OPTIONS),
            'jumlah_pengguna_potensial' => (string) fake()->numberBetween(20, 200),
            'akses_jaringan_terdekat' => 'Tiang Telkom '.fake()->numberBetween(5, 50).' meter',
            'tempat_pemasangan' => fake()->randomElement(Usulan::TEMPAT_PEMASANGAN_OPTIONS),
            'ketersediaan_listrik' => fake()->randomElement(Usulan::KETERSEDIAAN_LISTRIK_OPTIONS),
        ]);
    }
}
