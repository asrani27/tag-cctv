<?php

namespace Tests\Feature;

use App\Exports\SurveyDataSheet;
use App\Exports\SurveyExport;
use App\Exports\SurveyPhotoSheet;
use App\Exports\SurveySummarySheet;
use App\Models\SurveyLocation;
use App\Models\SurveyPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class SurveyExportTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private User $surveyor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->superadmin()->create(['name' => 'Super Admin', 'email' => 'super@example.com']);
        $this->surveyor = User::factory()->create(['name' => 'Regular Surveyor', 'email' => 'surveyor@example.com']);
    }

    public function test_guests_cannot_export_surveys(): void
    {
        $response = $this->get(route('surveys.export'));

        $response->assertRedirect(route('login'));
    }

    public function test_surveyors_cannot_export_surveys(): void
    {
        $response = $this->actingAs($this->surveyor)->get(route('surveys.export'));

        $response->assertForbidden();
    }

    public function test_inactive_superadmin_cannot_export_surveys(): void
    {
        $inactiveSuperadmin = User::factory()->superadmin()->create([
            'is_active' => false,
        ]);

        $response = $this->actingAs($inactiveSuperadmin)->get(route('surveys.export'));

        $response->assertRedirect(route('login'));
    }

    public function test_superadmin_can_export_surveys(): void
    {
        Excel::fake();

        SurveyLocation::factory()->create([
            'user_id' => $this->surveyor->id,
            'kecamatan' => 'Banjarmasin Tengah',
            'kelurahan' => 'Kertak Baru Ulu',
        ]);

        $response = $this->actingAs($this->superadmin)->get(route('surveys.export'));

        $response->assertOk();

        $expectedFilename = 'survey-cctv-wifi-' . now()->format('Y-m-d') . '.xlsx';
        Excel::assertDownloaded($expectedFilename, function (SurveyExport $export) {
            $sheets = $export->sheets();
            $this->assertCount(3, $sheets);
            $this->assertInstanceOf(SurveyDataSheet::class, $sheets[0]);
            $this->assertInstanceOf(SurveyPhotoSheet::class, $sheets[1]);
            $this->assertInstanceOf(SurveySummarySheet::class, $sheets[2]);

            return true;
        });
    }

    public function test_export_filename_includes_kecamatan_and_kelurahan_slugs(): void
    {
        Excel::fake();

        $response = $this->actingAs($this->superadmin)->get(route('surveys.export', [
            'kecamatan' => 'Banjarmasin Barat',
            'kelurahan' => 'Teluk Tiram',
        ]));

        $response->assertOk();

        $expectedFilename = 'survey-cctv-wifi-banjarmasin-barat-teluk-tiram-' . now()->format('Y-m-d') . '.xlsx';
        Excel::assertDownloaded($expectedFilename);
    }

    public function test_export_data_sheet_applies_filters_consistently(): void
    {
        $surveyMatching = SurveyLocation::factory()->create([
            'user_id' => $this->surveyor->id,
            'alamat' => 'Kantor Dinas Kominfo',
            'kecamatan' => 'Banjarmasin Tengah',
            'kelurahan' => 'Kertak Baru Ulu',
            'tersedia_fiber_optik' => true,
            'created_at' => now()->subDays(2),
        ]);

        $surveyOther = SurveyLocation::factory()->create([
            'user_id' => $this->surveyor->id,
            'alamat' => 'Puskesmas Pelambuan',
            'kecamatan' => 'Banjarmasin Barat',
            'kelurahan' => 'Pelambuan',
            'tersedia_fiber_optik' => false,
            'created_at' => now()->subDays(10),
        ]);

        // Filter by kecamatan
        $sheetKecamatan = new SurveyDataSheet(['kecamatan' => 'Banjarmasin Tengah'], $this->superadmin);
        $resultsKecamatan = $sheetKecamatan->query()->get();
        $this->assertTrue($resultsKecamatan->contains('id', $surveyMatching->id));
        $this->assertFalse($resultsKecamatan->contains('id', $surveyOther->id));

        // Filter by date range
        $sheetDate = new SurveyDataSheet([
            'tanggal_mulai' => now()->subDays(3)->format('Y-m-d'),
            'tanggal_akhir' => now()->format('Y-m-d'),
        ], $this->superadmin);
        $resultsDate = $sheetDate->query()->get();
        $this->assertTrue($resultsDate->contains('id', $surveyMatching->id));
        $this->assertFalse($resultsDate->contains('id', $surveyOther->id));

        // Filter by koneksi
        $sheetKoneksi = new SurveyDataSheet(['koneksi' => 'fiber'], $this->superadmin);
        $resultsKoneksi = $sheetKoneksi->query()->get();
        $this->assertTrue($resultsKoneksi->contains('id', $surveyMatching->id));
        $this->assertFalse($resultsKoneksi->contains('id', $surveyOther->id));
    }

    public function test_export_photo_sheet_only_includes_photos_for_filtered_surveys(): void
    {
        $survey1 = SurveyLocation::factory()->create(['kecamatan' => 'Banjarmasin Tengah']);
        $survey2 = SurveyLocation::factory()->create(['kecamatan' => 'Banjarmasin Selatan']);

        $photo1 = SurveyPhoto::factory()->create([
            'survey_location_id' => $survey1->id,
            'user_id' => $this->surveyor->id,
            'original_name' => 'photo_tengah.jpg',
            'file_path' => 'surveys/' . $survey1->id . '/photo_tengah.jpg',
        ]);

        $photo2 = SurveyPhoto::factory()->create([
            'survey_location_id' => $survey2->id,
            'user_id' => $this->surveyor->id,
            'original_name' => 'photo_selatan.jpg',
            'file_path' => 'surveys/' . $survey2->id . '/photo_selatan.jpg',
        ]);

        $photoSheet = new SurveyPhotoSheet(['kecamatan' => 'Banjarmasin Tengah'], $this->superadmin);
        $photos = $photoSheet->query()->get();

        $this->assertTrue($photos->contains('id', $photo1->id));
        $this->assertFalse($photos->contains('id', $photo2->id));
    }


    public function test_export_summary_sheet_calculates_correct_totals(): void
    {
        $survey1 = SurveyLocation::factory()->create([
            'user_id' => $this->surveyor->id,
            'kecamatan' => 'Banjarmasin Tengah',
            'kelurahan' => 'Kertak Baru Ulu',
            'jumlah_cctv' => 4,
            'jumlah_ap' => 2,
        ]);

        $survey2 = SurveyLocation::factory()->create([
            'user_id' => $this->surveyor->id,
            'kecamatan' => 'Banjarmasin Tengah',
            'kelurahan' => 'Melayu',
            'jumlah_cctv' => 6,
            'jumlah_ap' => 3,
        ]);

        SurveyPhoto::factory()->count(3)->create([
            'survey_location_id' => $survey1->id,
            'user_id' => $this->surveyor->id,
        ]);

        $summarySheet = new SurveySummarySheet(['kecamatan' => 'Banjarmasin Tengah'], $this->superadmin);
        $collection = $summarySheet->collection();

        $summaryArray = $collection->pluck(1, 0)->all();

        $this->assertEquals(2, $summaryArray['Total Survey']);
        $this->assertEquals(10, $summaryArray['Total CCTV']);
        $this->assertEquals(5, $summaryArray['Total AP/WiFi']);
        $this->assertEquals(3, $summaryArray['Total Foto']);
        $this->assertEquals(1, $summaryArray['Total Kecamatan']);
        $this->assertEquals(2, $summaryArray['Total Kelurahan']);
        $this->assertEquals('Banjarmasin Tengah', $summaryArray['Kecamatan']);
    }

    public function test_export_excel_button_rendered_for_superadmin_only(): void
    {
        $resSuper = $this->actingAs($this->superadmin)->get(route('surveys.index'));
        $resSuper->assertStatus(200);
        $resSuper->assertSee('Export Excel');
        $resSuper->assertSee(route('surveys.export'));

        $resSurveyor = $this->actingAs($this->surveyor)->get(route('surveys.index'));
        $resSurveyor->assertStatus(200);
        $resSurveyor->assertDontSee('Export Excel');
        $resSurveyor->assertDontSee(route('surveys.export'));
    }

    public function test_index_renders_date_range_filters(): void
    {
        $response = $this->actingAs($this->superadmin)->get(route('surveys.index'));

        $response->assertStatus(200);
        $response->assertSee('name="tanggal_mulai"', false);
        $response->assertSee('name="tanggal_akhir"', false);
        $response->assertSee('Tanggal Mulai');
        $response->assertSee('Tanggal Akhir');
    }

    public function test_actual_file_download_generates_valid_binary_response(): void
    {
        SurveyLocation::factory()->create([
            'user_id' => $this->surveyor->id,
            'alamat' => 'Uji Download Excel',
            'latitude' => -3.316694,
            'longitude' => 114.590111,
            'keamanan' => true,
            'akses_wifi' => true,
            'jumlah_cctv' => 2,
            'jumlah_ap' => 1,
        ]);

        $response = $this->actingAs($this->superadmin)->get(route('surveys.export'));

        $response->assertOk();
        $this->assertTrue(
            str_contains($response->headers->get('content-type') ?? '', 'spreadsheetml') ||
            str_contains($response->headers->get('content-type') ?? '', 'octet-stream')
        );
        $this->assertStringContainsString('survey-cctv-wifi', $response->headers->get('content-disposition') ?? '');
    }

}
