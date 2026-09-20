<?php

namespace Tests\Feature;

use App\Models\SurveyLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_dashboard_displays_statistics_and_recent_surveys(): void
    {
        SurveyLocation::factory()->create([
            'user_id' => $this->user->id,
            'jumlah_cctv' => 4,
            'jumlah_ap' => 2,
            'tersedia_fiber_optik' => true,
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total Survey');
        $response->assertSee('Total CCTV');
        $response->assertSee('Total AP');
    }

    public function test_surveys_index_can_be_rendered(): void
    {
        SurveyLocation::factory()->create([
            'user_id' => $this->user->id,
            'alamat' => 'Jl. Lambung Mangkurat No. 1',
            'kecamatan' => 'Banjarmasin Tengah',
        ]);

        $response = $this->actingAs($this->user)->get('/surveys');

        $response->assertStatus(200);
        $response->assertSee('Jl. Lambung Mangkurat No. 1');
        $response->assertSee('Banjarmasin Tengah');
    }

    public function test_surveys_search_and_filters_work(): void
    {
        SurveyLocation::factory()->create([
            'user_id' => $this->user->id,
            'alamat' => 'Kantor Walikota Banjarmasin',
            'kecamatan' => 'Banjarmasin Tengah',
            'tersedia_fiber_optik' => true,
        ]);

        SurveyLocation::factory()->create([
            'user_id' => $this->user->id,
            'alamat' => 'Jembatan Barito',
            'kecamatan' => 'Banjarmasin Barat',
            'tersedia_fiber_optik' => false,
        ]);

        $resSearch = $this->actingAs($this->user)->get('/surveys?q=Walikota');
        $resSearch->assertSee('Kantor Walikota');
        $resSearch->assertDontSee('Jembatan Barito');

        $resKecamatan = $this->actingAs($this->user)->get('/surveys?kecamatan=Banjarmasin+Barat');
        $resKecamatan->assertSee('Jembatan Barito');
        $resKecamatan->assertDontSee('Kantor Walikota');

        $resKoneksi = $this->actingAs($this->user)->get('/surveys?koneksi=fo');
        $resKoneksi->assertSee('Kantor Walikota');
    }

    public function test_create_survey_form_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get('/surveys/create');

        $response->assertStatus(200);
        $response->assertSee('Tambah Data Survey');
    }

    public function test_survey_can_be_created(): void
    {
        $payload = [
            'latitude' => -3.319456,
            'longitude' => 114.590823,
            'alamat' => 'Jl. RE Martadinata No. 1',
            'kelurahan' => 'Kertak Baru Ilir',
            'kecamatan' => 'Banjarmasin Tengah',
            'keamanan' => '1',
            'akses_wifi' => '1',
            'jumlah_cctv' => 3,
            'tersedia_fiber_optik' => '1',
        ];

        $response = $this->actingAs($this->user)->post('/surveys', $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('survey_locations', [
            'alamat' => 'Jl. RE Martadinata No. 1',
            'kecamatan' => 'Banjarmasin Tengah',
            'keamanan' => true,
            'akses_wifi' => true,
            'jumlah_cctv' => 3,
            'tersedia_fiber_optik' => true,
        ]);
    }

    public function test_survey_can_be_shown(): void
    {
        $survey = SurveyLocation::factory()->create([
            'user_id' => $this->user->id,
            'alamat' => 'Siring Menara Pandang',
            'latitude' => -3.319456,
            'longitude' => 114.590823,
        ]);

        $response = $this->actingAs($this->user)->get('/surveys/' . $survey->id);

        $response->assertStatus(200);
        $response->assertSee('Siring Menara Pandang');
    }

    public function test_survey_show_handles_null_coordinates_safely(): void
    {
        $survey = SurveyLocation::factory()->create([
            'user_id' => $this->user->id,
            'alamat' => 'Lokasi Belum Dipetakan',
            'latitude' => null,
            'longitude' => null,
        ]);

        $response = $this->actingAs($this->user)->get('/surveys/' . $survey->id);

        $response->assertStatus(200);
        $response->assertSee('Lokasi belum memiliki koordinat.');
    }

    public function test_survey_can_be_updated(): void
    {
        $survey = SurveyLocation::factory()->create([
            'user_id' => $this->user->id,
            'alamat' => 'Alamat Lama',
            'jumlah_cctv' => 1,
        ]);

        $response = $this->actingAs($this->user)->put('/surveys/' . $survey->id, [
            'alamat' => 'Alamat Baru',
            'jumlah_cctv' => 5,
            'keamanan' => '1',
            'tersedia_fiber_optik' => '0',
        ]);

        $response->assertRedirect(route('surveys.show', $survey));
        $this->assertDatabaseHas('survey_locations', [
            'id' => $survey->id,
            'alamat' => 'Alamat Baru',
            'jumlah_cctv' => 5,
            'keamanan' => true,
            'tersedia_fiber_optik' => false,
        ]);
    }

    public function test_survey_can_be_deleted(): void
    {
        $survey = SurveyLocation::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete('/surveys/' . $survey->id);

        $response->assertRedirect(route('surveys.index'));
        $this->assertDatabaseMissing('survey_locations', [
            'id' => $survey->id,
        ]);
    }

    public function test_create_survey_form_renders_location_picker_and_geolocation_ui(): void
    {
        $response = $this->actingAs($this->user)->get('/surveys/create');

        $response->assertStatus(200);
        $response->assertSee('Gunakan Lokasi Saya');
        $response->assertSee('id="btn-get-location"', false);
        $response->assertSee('id="survey-form-map"', false);
        $response->assertSee('id="location-feedback-alert"', false);
        $response->assertSee('survey-location-picker.js');
    }

    public function test_edit_survey_form_renders_location_picker_with_existing_coordinates(): void
    {
        $survey = SurveyLocation::factory()->create([
            'user_id' => $this->user->id,
            'latitude' => -3.3194567,
            'longitude' => 114.5908234,
            'alamat' => 'Jl. Lambung Mangkurat No. 10',
        ]);

        $response = $this->actingAs($this->user)->get('/surveys/' . $survey->id . '/edit');

        $response->assertStatus(200);
        $response->assertSee('Gunakan Lokasi Saya');
        $response->assertSee('id="btn-get-location"', false);
        $response->assertSee('id="survey-form-map"', false);
        $response->assertSee('-3.3194567');
        $response->assertSee('114.5908234');
        $response->assertSee('survey-location-picker.js');
    }

    public function test_survey_coordinate_validation_rules_and_messages(): void
    {
        $response = $this->actingAs($this->user)->post('/surveys', [
            'latitude' => 95.1234,
            'longitude' => 195.1234,
        ]);

        $response->assertSessionHasErrors([
            'latitude' => 'Latitude harus berada dalam rentang -90 sampai 90.',
            'longitude' => 'Longitude harus berada dalam rentang -180 sampai 180.',
        ]);
    }

    public function test_survey_can_be_saved_with_high_precision_coordinates(): void
    {
        $payload = [
            'latitude' => -3.3214567,
            'longitude' => 114.5918234,
            'alamat' => 'Titik Presisi Tinggi GPS',
            'kelurahan' => 'Kertak Baru Ulu',
            'kecamatan' => 'Banjarmasin Tengah',
        ];

        $response = $this->actingAs($this->user)->post('/surveys', $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('survey_locations', [
            'alamat' => 'Titik Presisi Tinggi GPS',
            'latitude' => -3.3214567,
            'longitude' => 114.5918234,
        ]);
    }

}
