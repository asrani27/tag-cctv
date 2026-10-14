<?php

namespace Database\Seeders;

use App\Models\Kelurahan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KelurahanUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('kelurahanbanjarmasin');

        // Collect names from KecamatanSeeder::DATA
        $kelurahanNames = [];
        foreach (KecamatanSeeder::DATA as $kecamatanData) {
            foreach ($kecamatanData['kelurahans'] as $kelurahan) {
                $kelurahanNames[] = $kelurahan['name'];
            }
        }

        // Also merge with any kelurahans in database if present
        try {
            $dbKelurahans = Kelurahan::pluck('name')->all();
            $kelurahanNames = array_unique(array_merge($kelurahanNames, $dbKelurahans));
        } catch (\Throwable) {
        }

        foreach ($kelurahanNames as $name) {
            $email = strtolower(str_replace(' ', '', $name)).'@banjarmasinkota.go.id';

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'Kelurahan '.$name,
                    'password' => $password,
                    'role' => User::ROLE_KELURAHAN,
                    'is_active' => true,
                ]
            );
        }
    }
}
