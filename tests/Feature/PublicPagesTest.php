<?php

namespace Tests\Feature;

use App\Models\SurveyLocation;
use App\Models\SurveyPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_can_be_rendered_with_database_statistics(): void
    {
        SurveyLocation::factory()->create([
            'alamat' => 'Jl. Lambung Mangkurat No. 1',
            'kecamatan' => 'Banjarmasin Tengah',
            'kelurahan' => 'Kertak Baru Ulu',
            'latitude' => -3.3194000,
            'longitude' => 114.5908000,
            'jumlah_cctv' => 4,
            'jumlah_ap' => 2,
            'tersedia_fiber_optik' => true,
            'status' => SurveyLocation::STATUS_BELUM_TERPASANG,
        ]);

        SurveyLocation::factory()->create([
            'alamat' => 'Jl. A. Yani Km 2',
            'kecamatan' => 'Banjarmasin Tengah',
            'kelurahan' => 'Sungai Baru',
            'latitude' => -3.3250000,
            'longitude' => 114.5950000,
            'jumlah_cctv' => 7,
            'jumlah_ap' => 1,
            'tersedia_fiber_optik' => true,
            'status' => SurveyLocation::STATUS_SELESAI_DIPASANG,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Sistem Informasi Survei');
        $response->assertSee('CCTV &amp; WiFi', false);
        $response->assertSee('CCTV Terpasang');
        $response->assertSee('Sudah Terpasang');
        $response->assertViewHas('stats', function (array $stats): bool {
            return $stats['total_cctv'] === 11
                && $stats['total_cctv_terpasang'] === 7
                && $stats['total_titik_terpasang'] === 1;
        });
        $response->assertSee('Jl. Lambung Mangkurat No. 1');
        $response->assertSee('Banjarmasin Tengah');
        $response->assertSee('Persebaran Titik Survei');
        $response->assertSee('Fitur Utama Pengelolaan Data Survei');
        $response->assertSee('Standar Pengumpulan Data Survei Lapangan');
    }

    public function test_landing_page_handles_empty_database_gracefully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Belum ada data survei yang tersedia');
    }

    public function test_public_map_can_be_rendered_and_filtered(): void
    {
        SurveyLocation::factory()->create([
            'alamat' => 'Titik Banjarmasin Utara',
            'kecamatan' => 'Banjarmasin Utara',
            'kelurahan' => 'Sungai Miai',
            'latitude' => -3.2950,
            'longitude' => 114.5900,
        ]);

        SurveyLocation::factory()->create([
            'alamat' => 'Titik Banjarmasin Selatan',
            'kecamatan' => 'Banjarmasin Selatan',
            'kelurahan' => 'Pemurus Dalam',
            'latitude' => -3.3450,
            'longitude' => 114.6100,
        ]);

        $response = $this->get('/map');
        $response->assertStatus(200);
        $response->assertSee('Peta Persebaran Titik Survei');
        $response->assertSee('Titik Banjarmasin Utara');
        $response->assertSee('Titik Banjarmasin Selatan');

        // Test filtering by kecamatan
        $filteredResponse = $this->get('/map?kecamatan=Banjarmasin+Utara');
        $filteredResponse->assertStatus(200);
        $filteredResponse->assertSee('Titik Banjarmasin Utara');
        $filteredResponse->assertDontSee('Titik Banjarmasin Selatan');
    }

    public function test_public_map_can_be_filtered_by_perangkat_cctv_or_wifi(): void
    {
        SurveyLocation::factory()->create([
            'alamat' => 'Titik Khusus CCTV',
            'kecamatan' => 'Banjarmasin Utara',
            'kelurahan' => 'Sungai Miai',
            'latitude' => -3.2950,
            'longitude' => 114.5900,
            'jumlah_cctv' => 3,
            'jumlah_ap' => 0,
        ]);

        SurveyLocation::factory()->create([
            'alamat' => 'Titik Khusus WiFi AP',
            'kecamatan' => 'Banjarmasin Selatan',
            'kelurahan' => 'Pemurus Dalam',
            'latitude' => -3.3450,
            'longitude' => 114.6100,
            'jumlah_cctv' => 0,
            'jumlah_ap' => 2,
        ]);

        $cctvResponse = $this->get('/map?perangkat=cctv');
        $cctvResponse->assertStatus(200);
        $cctvResponse->assertSee('Titik Khusus CCTV');
        $cctvResponse->assertDontSee('Titik Khusus WiFi AP');

        $wifiResponse = $this->get('/map?perangkat=wifi');
        $wifiResponse->assertStatus(200);
        $wifiResponse->assertSee('Titik Khusus WiFi AP');
        $wifiResponse->assertDontSee('Titik Khusus CCTV');
    }

    public function test_public_survey_index_can_be_rendered_and_searched(): void
    {
        SurveyLocation::factory()->create([
            'alamat' => 'Kantor Walikota Banjarmasin',
            'kecamatan' => 'Banjarmasin Tengah',
            'kelurahan' => 'Kertak Baru Ilir',
        ]);

        SurveyLocation::factory()->create([
            'alamat' => 'Pelabuhan Trisakti',
            'kecamatan' => 'Banjarmasin Barat',
            'kelurahan' => 'Telaga Biru',
        ]);

        $response = $this->get('/survey');
        $response->assertStatus(200);
        $response->assertSee('Kantor Walikota Banjarmasin');
        $response->assertSee('Pelabuhan Trisakti');

        // Test keyword search
        $searchResponse = $this->get('/survey?q=Trisakti');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Pelabuhan Trisakti');
        $searchResponse->assertDontSee('Kantor Walikota Banjarmasin');
    }

    public function test_public_survey_show_displays_survey_details(): void
    {
        $survey = SurveyLocation::factory()->create([
            'alamat' => 'Siring Menara Pandang',
            'kecamatan' => 'Banjarmasin Tengah',
            'kelurahan' => 'Sungai Baru',
            'latitude' => -3.3225,
            'longitude' => 114.5930,
            'jumlah_cctv' => 5,
            'jumlah_ap' => 3,
            'tersedia_fiber_optik' => true,
        ]);

        $response = $this->get("/survey/{$survey->id}");

        $response->assertStatus(200);
        $response->assertSee('Siring Menara Pandang');
        $response->assertSee('Sungai Baru');
        $response->assertSee('Banjarmasin Tengah');
        $response->assertSee('5 Unit');
        $response->assertSee('3 Unit');
    }

    public function test_public_survey_show_handles_null_coordinates(): void
    {
        $survey = SurveyLocation::factory()->create([
            'alamat' => 'Titik Tanpa GPS',
            'latitude' => null,
            'longitude' => null,
        ]);

        $response = $this->get("/survey/{$survey->id}");

        $response->assertStatus(200);
        $response->assertSee('Titik Tanpa GPS');
        $response->assertSee('Nihil');
    }

    public function test_public_survey_show_displays_survey_photos_when_available(): void
    {
        $survey = SurveyLocation::factory()->create([
            'alamat' => 'Lokasi Berfoto',
        ]);

        SurveyPhoto::factory()->create([
            'survey_location_id' => $survey->id,
            'original_name' => 'tiang_cctv_depan.jpg',
            'file_path' => 'surveys/'.$survey->id.'/tiang_cctv_depan.jpg',
        ]);

        $response = $this->get("/survey/{$survey->id}");

        $response->assertStatus(200);
        $response->assertSee('Foto Dokumentasi');
        $response->assertSee('1 Foto');
        $response->assertSee('tiang_cctv_depan.jpg');
    }

    public function test_public_survey_show_displays_empty_state_when_no_photos(): void
    {
        $survey = SurveyLocation::factory()->create([
            'alamat' => 'Lokasi Tanpa Foto',
        ]);

        $response = $this->get("/survey/{$survey->id}");

        $response->assertStatus(200);
        $response->assertSee('Foto Dokumentasi');
        $response->assertSee('Belum ada foto dokumentasi untuk titik survei ini.');
    }

    public function test_public_map_markers_have_status_colors_and_can_be_filtered_by_status(): void
    {
        SurveyLocation::factory()->create([
            'alamat' => 'Titik Merah Belum',
            'latitude' => -3.3100,
            'longitude' => 114.5800,
            'status' => SurveyLocation::STATUS_BELUM_TERPASANG,
        ]);

        SurveyLocation::factory()->create([
            'alamat' => 'Titik Biru Proses',
            'latitude' => -3.3200,
            'longitude' => 114.5900,
            'status' => SurveyLocation::STATUS_PROSES_PEMASANGAN,
        ]);

        SurveyLocation::factory()->create([
            'alamat' => 'Titik Hijau Selesai',
            'latitude' => -3.3300,
            'longitude' => 114.6000,
            'status' => SurveyLocation::STATUS_SELESAI_DIPASANG,
        ]);

        // Filter map by status
        $resSelesai = $this->get('/map?status='.urlencode(SurveyLocation::STATUS_SELESAI_DIPASANG));
        $resSelesai->assertStatus(200);
        $resSelesai->assertSee('Titik Hijau Selesai');
        $resSelesai->assertDontSee('Titik Merah Belum');
        $resSelesai->assertDontSee('Titik Biru Proses');

        // Verify geojson colors: Merah (#dc2626), Biru (#2563eb), Hijau (#16a34a)
        $resGeoAll = $this->getJson('/map/geojson');
        $resGeoAll->assertStatus(200);
        $features = collect($resGeoAll->json('features'))->keyBy('properties.alamat');

        $this->assertEquals('#dc2626', $features['Titik Merah Belum']['properties']['marker_color']);
        $this->assertEquals('#2563eb', $features['Titik Biru Proses']['properties']['marker_color']);
        $this->assertEquals('#16a34a', $features['Titik Hijau Selesai']['properties']['marker_color']);
    }
}
