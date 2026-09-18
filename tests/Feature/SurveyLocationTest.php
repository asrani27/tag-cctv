<?php

use App\Models\SurveyLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('survey_locations table has expected columns and no_titik does not exist', function () {
    expect(Schema::hasTable('survey_locations'))->toBeTrue();

    // Verify no_titik does NOT exist
    expect(Schema::hasColumn('survey_locations', 'no_titik'))->toBeFalse();

    // Verify all expected columns exist
    $expectedColumns = [
        'id',
        'latitude',
        'longitude',
        'alamat',
        'rt',
        'rw',
        'kelurahan',
        'kecamatan',
        'keamanan',
        'akses_wifi',
        'lalin',
        'panic_sensor',
        'jumlah_cctv',
        'jumlah_ap',
        'bandwidth',
        'backhaul',
        'storage',
        'jenis_jalan',
        'tiang',
        'jenis_tiang',
        'tembok',
        'arah_pantau',
        'jarak_pantau',
        'pencahayaan_malam',
        'potensi_silau',
        'penghalang',
        'potensi_vandalisme',
        'luas_area',
        'prakiraan_pengguna_max',
        'tempat_ap_latitude',
        'tempat_ap_longitude',
        'potensi_interferensi',
        'sumber_listrik',
        'posisi_panel_latitude',
        'posisi_panel_longitude',
        'daya_tersedia',
        'proteksi_petir',
        'tersedia_fiber_optik',
        'tersedia_4g_5g',
        'tersedia_link_p2p',
        'jumlah_provider',
        'created_at',
        'updated_at',
    ];

    foreach ($expectedColumns as $column) {
        expect(Schema::hasColumn('survey_locations', $column))
            ->toBeTrue("Column {$column} should exist in survey_locations table");
    }
});

test('survey location model can be created with null values', function () {
    $survey = SurveyLocation::create([]);

    expect($survey->id)->toBeGreaterThan(0)
        ->and($survey->latitude)->toBeNull()
        ->and($survey->longitude)->toBeNull()
        ->and($survey->alamat)->toBeNull();

    $survey->delete();
});

test('survey location model correctly casts attributes', function () {
    $survey = SurveyLocation::create([
        'latitude' => -3.3186065,
        'longitude' => 114.5921823,
        'alamat' => 'Jl. Lambung Mangkurat No. 1',
        'rt' => '01',
        'rw' => '02',
        'kelurahan' => 'Kertak Baru Ilir',
        'kecamatan' => 'Banjarmasin Tengah',
        'keamanan' => 1,
        'akses_wifi' => 0,
        'lalin' => true,
        'panic_sensor' => false,
        'jumlah_cctv' => '4',
        'jumlah_ap' => '2',
        'bandwidth' => '100 Mbps',
        'backhaul' => 'Fiber Optik',
        'storage' => '2 TB',
        'jenis_jalan' => 'Protokol',
        'tiang' => 'Ada',
        'jenis_tiang' => 'Oktagonal 9M',
        'tembok' => 'Tidak Ada',
        'arah_pantau' => 'Simpang 4',
        'jarak_pantau' => '50 meter',
        'pencahayaan_malam' => 'Terang',
        'potensi_silau' => 'Rendah',
        'penghalang' => 'Pohon',
        'potensi_vandalisme' => 'Rendah',
        'luas_area' => '250.50',
        'prakiraan_pengguna_max' => '100',
        'tempat_ap_latitude' => -3.3186100,
        'tempat_ap_longitude' => 114.5921900,
        'potensi_interferensi' => 'Rendah',
        'sumber_listrik' => 'PLN',
        'posisi_panel_latitude' => -3.3186200,
        'posisi_panel_longitude' => 114.5922000,
        'daya_tersedia' => '2200.00',
        'proteksi_petir' => 'Surge Arrester',
        'tersedia_fiber_optik' => 1,
        'tersedia_4g_5g' => 1,
        'tersedia_link_p2p' => 0,
        'jumlah_provider' => '3',
    ]);

    expect($survey->id)->toBeGreaterThan(0)
        ->and($survey->keamanan)->toBeTrue()
        ->and($survey->akses_wifi)->toBeFalse()
        ->and($survey->lalin)->toBeTrue()
        ->and($survey->panic_sensor)->toBeFalse()
        ->and($survey->jumlah_cctv)->toBe(4)
        ->and($survey->jumlah_ap)->toBe(2)
        ->and($survey->prakiraan_pengguna_max)->toBe(100)
        ->and($survey->jumlah_provider)->toBe(3)
        ->and($survey->tersedia_fiber_optik)->toBeTrue()
        ->and($survey->tersedia_4g_5g)->toBeTrue()
        ->and($survey->tersedia_link_p2p)->toBeFalse()
        ->and($survey->latitude)->toEqualWithDelta(-3.3186065, 0.0000001)
        ->and($survey->longitude)->toEqualWithDelta(114.5921823, 0.0000001)
        ->and($survey->luas_area)->toBe(250.5)
        ->and($survey->daya_tersedia)->toBe(2200.0);

    $survey->delete();
});
