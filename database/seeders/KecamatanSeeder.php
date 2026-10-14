<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\SurveyLocation;
use Illuminate\Database\Seeder;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (SurveyLocation::KELURAHAN_BY_KECAMATAN as $kecamatanName => $kelurahanList) {
            $kecamatan = Kecamatan::firstOrCreate(['name' => $kecamatanName]);

            foreach ($kelurahanList as $kelurahanName) {
                Kelurahan::firstOrCreate([
                    'kecamatan_id' => $kecamatan->id,
                    'name' => $kelurahanName,
                ]);
            }
        }
    }
}
