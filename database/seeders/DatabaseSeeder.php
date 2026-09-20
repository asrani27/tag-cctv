<?php

namespace Database\Seeders;

use App\Models\SurveyLocation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User (Superadmin)
        $admin = User::firstOrCreate(
            ['email' => 'admin@banjarmasin.go.id'],
            [
                'name' => 'Administrator CCTV Banjarmasin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPERADMIN,
                'is_active' => true,
            ]
        );
        $admin->update([
            'role' => User::ROLE_SUPERADMIN,
            'is_active' => true,
        ]);

        // Test User (Surveyor / Regular User)
        $surveyor = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Surveyor Lapangan',
                'password' => Hash::make('password'),
                'role' => User::ROLE_USER,
                'is_active' => true,
            ]
        );
        $surveyor->update([
            'role' => User::ROLE_USER,
            'is_active' => true,
        ]);


        // Seed sample survey locations if empty
        if (SurveyLocation::count() === 0) {
            SurveyLocation::create([
                'latitude' => -3.319400,
                'longitude' => 114.590800,
                'alamat' => 'Jl. RE Martadinata No. 1 (Balai Kota)',
                'rt' => '05',
                'rw' => '02',
                'kelurahan' => 'Kertak Baru Ilir',
                'kecamatan' => 'Banjarmasin Tengah',
                'keamanan' => true,
                'akses_wifi' => true,
                'lalin' => true,
                'panic_sensor' => true,
                'jumlah_cctv' => 6,
                'jumlah_ap' => 4,
                'bandwidth' => '200 Mbps',
                'backhaul' => 'Fiber Optic',
                'storage' => '4 TB NVR',
                'jenis_jalan' => 'Jalan Protokol',
                'tiang' => 'Ada',
                'jenis_tiang' => 'Oktagonal 9M',
                'tembok' => 'Ada',
                'arah_pantau' => 'Simpang Balai Kota',
                'jarak_pantau' => '60 meter',
                'pencahayaan_malam' => 'Terang',
                'potensi_silau' => 'Rendah',
                'penghalang' => 'Tidak Ada',
                'potensi_vandalisme' => 'Rendah',
                'luas_area' => 500.0,
                'prakiraan_pengguna_max' => 150,
                'tempat_ap_latitude' => -3.319400,
                'tempat_ap_longitude' => 114.590800,
                'potensi_interferensi' => 'Sedang',
                'sumber_listrik' => 'PLN Balai Kota',
                'posisi_panel_latitude' => -3.319410,
                'posisi_panel_longitude' => 114.590810,
                'daya_tersedia' => 4400,
                'proteksi_petir' => 'Surge Arrester & Grounding',
                'tersedia_fiber_optik' => true,
                'tersedia_4g_5g' => true,
                'tersedia_link_p2p' => false,
                'jumlah_provider' => 3,
            ]);

            SurveyLocation::factory()->count(24)->create();
        }
    }
}
