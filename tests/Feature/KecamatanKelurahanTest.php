<?php

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\SurveyLocation;
use Database\Seeders\KecamatanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('kecamatans and kelurahans tables exist with expected columns including kode', function () {
    expect(Schema::hasTable('kecamatans'))->toBeTrue()
        ->and(Schema::hasTable('kelurahans'))->toBeTrue();

    $expectedKecamatanColumns = ['id', 'kode', 'name', 'created_at', 'updated_at'];
    foreach ($expectedKecamatanColumns as $col) {
        expect(Schema::hasColumn('kecamatans', $col))->toBeTrue();
    }

    $expectedKelurahanColumns = ['id', 'kecamatan_id', 'kode', 'name', 'created_at', 'updated_at'];
    foreach ($expectedKelurahanColumns as $col) {
        expect(Schema::hasColumn('kelurahans', $col))->toBeTrue();
    }
});

test('kecamatan and kelurahan singular table/view aliases are queryable and include kode', function () {
    expect(DB::table('kecamatan')->count())->toBeGreaterThan(0)
        ->and(DB::table('kelurahan')->count())->toBeGreaterThan(0);

    $sampleKec = DB::table('kecamatan')->where('kode', '63.71.01')->first();
    expect($sampleKec)->not->toBeNull()
        ->and($sampleKec->name)->toBe('Banjarmasin Selatan');

    $sampleKel = DB::table('kelurahan')->where('kode', '63.71.01.1001')->first();
    expect($sampleKel)->not->toBeNull()
        ->and($sampleKel->name)->toBe('Mantuil');
});

test('all 5 kecamatan and 52 kelurahan for kota banjarmasin are seeded with official kemendagri codes', function () {
    expect(Kecamatan::count())->toBe(5)
        ->and(Kelurahan::count())->toBe(52);

    $expectedKecamatans = [
        '63.71.01' => 'Banjarmasin Selatan',
        '63.71.02' => 'Banjarmasin Timur',
        '63.71.03' => 'Banjarmasin Barat',
        '63.71.04' => 'Banjarmasin Utara',
        '63.71.05' => 'Banjarmasin Tengah',
    ];

    foreach ($expectedKecamatans as $kode => $name) {
        $kec = Kecamatan::where('kode', $kode)->first();
        expect($kec)->not->toBeNull()
            ->and($kec->name)->toBe($name);
    }

    $allKelurahans = Kelurahan::all();
    expect($allKelurahans->whereNull('kode')->count())->toBe(0);

    foreach ($allKelurahans as $kel) {
        expect($kel->kode)->toMatch('/^63\.71\.\d{2}\.\d{4}$/')
            ->and($kel->kecamatan_id)->toBeGreaterThan(0);
    }

    expect(Kelurahan::where('kode', '63.71.01.1001')->first()?->name)->toBe('Mantuil');
    expect(Kelurahan::where('kode', '63.71.01.1012')->first()?->name)->toBe('Basirih Selatan');
    expect(Kelurahan::where('kode', '63.71.02.1005')->first()?->name)->toBe('Sungai Lulut');
    expect(Kelurahan::where('kode', '63.71.05.1001')->first()?->name)->toBe('Kertak Baru Ilir');
});

test('kecamatan seeder runs idempotently without duplicating data or wiping kode', function () {
    $initialKecamatanCount = Kecamatan::count();
    $initialKelurahanCount = Kelurahan::count();

    $seeder = new KecamatanSeeder;
    $seeder->run();

    expect(Kecamatan::count())->toBe($initialKecamatanCount)
        ->and(Kelurahan::count())->toBe($initialKelurahanCount);

    $tengah = Kecamatan::where('kode', '63.71.05')->first();
    expect($tengah)->not->toBeNull()
        ->and($tengah->name)->toBe('Banjarmasin Tengah')
        ->and($tengah->kelurahans->count())->toBe(12);
});

test('kelurahan belongs to kecamatan and kecamatan has many kelurahans', function () {
    $kecamatan = Kecamatan::where('name', 'Banjarmasin Barat')->first();
    expect($kecamatan->kelurahans)->not->toBeEmpty();

    $kelurahan = $kecamatan->kelurahans->first();
    expect($kelurahan->kecamatan)->not->toBeNull()
        ->and($kelurahan->kecamatan->id)->toBe($kecamatan->id);
});

test('attribute aliases work for kode/code and name/nama on models', function () {
    $kecamatan = Kecamatan::where('kode', '63.71.01')->first();
    expect($kecamatan->nama)->toBe($kecamatan->name)
        ->and($kecamatan->code)->toBe('63.71.01');

    $kecamatan->nama = 'Kecamatan Test';
    $kecamatan->code = '63.71.99';
    expect($kecamatan->name)->toBe('Kecamatan Test')
        ->and($kecamatan->kode)->toBe('63.71.99');

    $kelurahan = Kelurahan::where('kode', '63.71.01.1001')->first();
    expect($kelurahan->nama)->toBe($kelurahan->name)
        ->and($kelurahan->code)->toBe('63.71.01.1001');

    $kelurahan->nama = 'Kelurahan Test';
    $kelurahan->code = '63.71.01.9999';
    expect($kelurahan->name)->toBe('Kelurahan Test')
        ->and($kelurahan->kode)->toBe('63.71.01.9999');
});

test('kecamatan and kelurahan factories work properly with kode', function () {
    $customKecamatan = Kecamatan::factory()->create([
        'kode' => '63.71.91',
        'name' => 'Kecamatan Khusus',
    ]);
    expect($customKecamatan->id)->toBeGreaterThan(0)
        ->and($customKecamatan->kode)->toBe('63.71.91')
        ->and($customKecamatan->name)->toBe('Kecamatan Khusus');

    $customKelurahan = Kelurahan::factory()->create([
        'kecamatan_id' => $customKecamatan->id,
        'kode' => '63.71.91.1001',
        'name' => 'Kelurahan Khusus',
    ]);
    expect($customKelurahan->id)->toBeGreaterThan(0)
        ->and($customKelurahan->kode)->toBe('63.71.91.1001')
        ->and($customKelurahan->kecamatan_id)->toBe($customKecamatan->id)
        ->and($customKelurahan->name)->toBe('Kelurahan Khusus');
});

test('survey location helper methods load data from database tables', function () {
    $kecamatanList = SurveyLocation::getKecamatanList();
    expect($kecamatanList)->toContain('Banjarmasin Barat', 'Banjarmasin Tengah');

    $kelurahanMap = SurveyLocation::getKelurahanMap();
    expect($kelurahanMap)->toHaveKey('Banjarmasin Barat')
        ->and($kelurahanMap['Banjarmasin Barat'])->toContain('Basirih');

    $kelurahanList = SurveyLocation::getKelurahanByKecamatan('Banjarmasin Timur');
    expect($kelurahanList)->toContain('Kebun Bunga', 'Kuripan', 'Sungai Lulut');
});

test('survey location can resolve kecamatanModel and kelurahanModel relations', function () {
    $survey = SurveyLocation::create([
        'kecamatan' => 'Banjarmasin Tengah',
        'kelurahan' => 'Kertak Baru Ilir',
        'alamat' => 'Jl. Lambung Mangkurat',
    ]);

    expect($survey->kecamatanModel)->not->toBeNull()
        ->and($survey->kecamatanModel->name)->toBe('Banjarmasin Tengah')
        ->and($survey->kecamatanModel->kode)->toBe('63.71.05')
        ->and($survey->kelurahanModel)->not->toBeNull()
        ->and($survey->kelurahanModel->name)->toBe('Kertak Baru Ilir')
        ->and($survey->kelurahanModel->kode)->toBe('63.71.05.1001');
});

test('deleting a kecamatan cascades delete to its kelurahans', function () {
    $kecamatan = Kecamatan::factory()->create(['name' => 'Kecamatan Hapus']);
    $kelurahan = Kelurahan::factory()->create([
        'kecamatan_id' => $kecamatan->id,
        'name' => 'Kelurahan Hapus',
    ]);

    $kelurahanId = $kelurahan->id;
    $kecamatan->delete();

    expect(Kelurahan::find($kelurahanId))->toBeNull();
});
