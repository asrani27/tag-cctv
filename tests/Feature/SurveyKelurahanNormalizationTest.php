<?php

use App\Models\Kelurahan;
use App\Models\SurveyLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('normalizeKelurahanName converts variations and aliases to canonical names', function () {
    expect(SurveyLocation::normalizeKelurahanName('Kelurahan Gadang'))->toBe('Gadang')
        ->and(SurveyLocation::normalizeKelurahanName('kelurahan antasan besar'))->toBe('Antasan Besar')
        ->and(SurveyLocation::normalizeKelurahanName('antasan besar'))->toBe('Antasan Besar')
        ->and(SurveyLocation::normalizeKelurahanName('Kelayan Barat'))->toBe('Kelayan Barat')
        ->and(SurveyLocation::normalizeKelurahanName('Kelurahan Kelayan Barat'))->toBe('Kelayan Barat')
        ->and(SurveyLocation::normalizeKelurahanName('Benua Anyar'))->toBe('Banua Anyar')
        ->and(SurveyLocation::normalizeKelurahanName('Kelurahan Pekapuran'))->toBe('Pekapuran Laut')
        ->and(SurveyLocation::normalizeKelurahanName('Kelurahan Alalak TengH'))->toBe('Alalak Tengah')
        ->and(SurveyLocation::normalizeKelurahanName('Pemurus Luar'))->toBe('Pemurus Luar')
        ->and(SurveyLocation::normalizeKelurahanName('Kelurahan pemurus luar'))->toBe('Pemurus Luar')
        ->and(SurveyLocation::normalizeKelurahanName(null))->toBeNull()
        ->and(SurveyLocation::normalizeKelurahanName(''))->toBe('');
});

test('saving survey location automatically normalizes kelurahan name', function () {
    $survey1 = SurveyLocation::create([
        'alamat' => 'Jl. Test 1',
        'kecamatan' => 'Banjarmasin Tengah',
        'kelurahan' => 'Kelurahan Gadang',
    ]);
    expect($survey1->kelurahan)->toBe('Gadang');

    $survey2 = SurveyLocation::create([
        'alamat' => 'Jl. Test 2',
        'kecamatan' => 'Banjarmasin Tengah',
        'kelurahan' => 'antasan besar',
    ]);
    expect($survey2->kelurahan)->toBe('Antasan Besar');

    $survey3 = SurveyLocation::create([
        'alamat' => 'Jl. Test 3',
        'kecamatan' => 'Banjarmasin Utara',
        'kelurahan' => 'Kelurahan Alalak TengH',
    ]);
    expect($survey3->kelurahan)->toBe('Alalak Tengah');

    $survey4 = SurveyLocation::create([
        'alamat' => 'Jl. Test 4',
        'kecamatan' => 'Banjarmasin Timur',
        'kelurahan' => 'Benua Anyar',
    ]);
    expect($survey4->kelurahan)->toBe('Banua Anyar');
});

test('survey location kelurahanModel relation resolves properly after normalization', function () {
    $survey = SurveyLocation::create([
        'alamat' => 'Jl. Test Relasi',
        'kecamatan' => 'Banjarmasin Tengah',
        'kelurahan' => 'Kelurahan Gadang',
    ]);

    expect($survey->kelurahan)->toBe('Gadang')
        ->and($survey->kelurahanModel)->not->toBeNull()
        ->and($survey->kelurahanModel->name)->toBe('Gadang');
});

test('survey controller store normalizes kelurahan input', function () {
    $user = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);

    $response = $this->actingAs($user)->post(route('surveys.store'), [
        'alamat' => 'Jl. Pangeran Samudra',
        'kecamatan' => 'Banjarmasin Tengah',
        'kelurahan' => 'Kelurahan Kertak Baru Ilir',
        'latitude' => -3.32,
        'longitude' => 114.59,
    ]);

    $response->assertRedirect(route('surveys.index'));

    $created = SurveyLocation::latest('id')->first();
    expect($created->kelurahan)->toBe('Kertak Baru Ilir');
});

test('landing page stats count distinct kelurahan without duplicates', function () {
    // Create surveys with different variations of the same kelurahan
    SurveyLocation::factory()->create([
        'kecamatan' => 'Banjarmasin Selatan',
        'kelurahan' => 'Kelayan Barat',
    ]);
    SurveyLocation::factory()->create([
        'kecamatan' => 'Banjarmasin Selatan',
        'kelurahan' => 'Kelurahan Kelayan Barat',
    ]);
    SurveyLocation::factory()->create([
        'kecamatan' => 'Banjarmasin Selatan',
        'kelurahan' => 'Pekauman',
    ]);
    SurveyLocation::factory()->create([
        'kecamatan' => 'Banjarmasin Selatan',
        'kelurahan' => 'Kelurahan Pekauman',
    ]);

    // Should count as 2 kelurahan, not 4
    $distinctCount = SurveyLocation::whereNotNull('kelurahan')
        ->where('kelurahan', '!=', '')
        ->distinct('kelurahan')
        ->count('kelurahan');

    expect($distinctCount)->toBe(2);

    $response = $this->get(route('home'));
    $response->assertStatus(200);
    $response->assertViewHas('stats', function ($stats) {
        return $stats['total_kelurahan'] === 2;
    });
});
