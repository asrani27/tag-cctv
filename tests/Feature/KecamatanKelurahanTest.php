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

test('kecamatans and kelurahans tables exist with expected columns', function () {
    expect(Schema::hasTable('kecamatans'))->toBeTrue()
        ->and(Schema::hasTable('kelurahans'))->toBeTrue();

    $expectedKecamatanColumns = ['id', 'name', 'created_at', 'updated_at'];
    foreach ($expectedKecamatanColumns as $col) {
        expect(Schema::hasColumn('kecamatans', $col))->toBeTrue();
    }

    $expectedKelurahanColumns = ['id', 'kecamatan_id', 'name', 'created_at', 'updated_at'];
    foreach ($expectedKelurahanColumns as $col) {
        expect(Schema::hasColumn('kelurahans', $col))->toBeTrue();
    }
});

test('kecamatan and kelurahan singular table/view aliases are queryable', function () {
    expect(DB::table('kecamatan')->count())->toBeGreaterThan(0)
        ->and(DB::table('kelurahan')->count())->toBeGreaterThan(0);
});

test('default banjarmasin kecamatan and kelurahan are seeded', function () {
    expect(Kecamatan::count())->toBe(5)
        ->and(Kelurahan::count())->toBe(50);

    $kecamatanNames = Kecamatan::orderBy('name')->pluck('name')->all();
    expect($kecamatanNames)->toEqualCanonicalizing(SurveyLocation::KECAMATAN_LIST);

    $tengah = Kecamatan::where('name', 'Banjarmasin Tengah')->first();
    expect($tengah)->not->toBeNull()
        ->and($tengah->kelurahans->count())->toBe(count(SurveyLocation::KELURAHAN_BY_KECAMATAN['Banjarmasin Tengah']));
});

test('kecamatan seeder runs idempotently without duplicating data', function () {
    $initialKecamatanCount = Kecamatan::count();
    $initialKelurahanCount = Kelurahan::count();

    $seeder = new KecamatanSeeder;
    $seeder->run();

    expect(Kecamatan::count())->toBe($initialKecamatanCount)
        ->and(Kelurahan::count())->toBe($initialKelurahanCount);
});

test('kelurahan belongs to kecamatan and kecamatan has many kelurahans', function () {
    $kecamatan = Kecamatan::where('name', 'Banjarmasin Barat')->first();
    expect($kecamatan->kelurahans)->not->toBeEmpty();

    $kelurahan = $kecamatan->kelurahans->first();
    expect($kelurahan->kecamatan)->not->toBeNull()
        ->and($kelurahan->kecamatan->id)->toBe($kecamatan->id);
});

test('nama attribute alias works for both kecamatan and kelurahan models', function () {
    $kecamatan = Kecamatan::first();
    expect($kecamatan->nama)->toBe($kecamatan->name);

    $kecamatan->nama = 'Kecamatan Test';
    expect($kecamatan->name)->toBe('Kecamatan Test');

    $kelurahan = Kelurahan::first();
    expect($kelurahan->nama)->toBe($kelurahan->name);

    $kelurahan->nama = 'Kelurahan Test';
    expect($kelurahan->name)->toBe('Kelurahan Test');
});

test('kecamatan and kelurahan factories work properly', function () {
    $customKecamatan = Kecamatan::factory()->create(['name' => 'Kecamatan Khusus']);
    expect($customKecamatan->id)->toBeGreaterThan(0)
        ->and($customKecamatan->name)->toBe('Kecamatan Khusus');

    $customKelurahan = Kelurahan::factory()->create([
        'kecamatan_id' => $customKecamatan->id,
        'name' => 'Kelurahan Khusus',
    ]);
    expect($customKelurahan->id)->toBeGreaterThan(0)
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
    expect($kelurahanList)->toContain('Kebun Bunga', 'Kuripan');
});

test('survey location can resolve kecamatanModel and kelurahanModel relations', function () {
    $survey = SurveyLocation::create([
        'kecamatan' => 'Banjarmasin Tengah',
        'kelurahan' => 'Kertak Baru Ilir',
        'alamat' => 'Jl. Lambung Mangkurat',
    ]);

    expect($survey->kecamatanModel)->not->toBeNull()
        ->and($survey->kecamatanModel->name)->toBe('Banjarmasin Tengah')
        ->and($survey->kelurahanModel)->not->toBeNull()
        ->and($survey->kelurahanModel->name)->toBe('Kertak Baru Ilir');
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
