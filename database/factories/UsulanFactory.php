<?php

namespace Database\Factories;

use App\Models\Usulan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usulan>
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
}
