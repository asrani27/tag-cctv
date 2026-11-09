<?php

namespace Tests\Feature;

use App\Models\SurveyLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAndSurveyIsolationTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    private User $userA;

    private User $userB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->superadmin()->create(['name' => 'Super Admin']);
        $this->userA = User::factory()->create(['name' => 'Surveyor A']);
        $this->userB = User::factory()->create(['name' => 'Surveyor B']);
    }

    public function test_surveyor_only_sees_own_surveys_in_index(): void
    {
        SurveyLocation::factory()->create(['user_id' => $this->userA->id, 'alamat' => 'Survey Milik User A']);
        SurveyLocation::factory()->create(['user_id' => $this->userB->id, 'alamat' => 'Survey Milik User B']);
        SurveyLocation::factory()->create(['user_id' => null, 'alamat' => 'Survey Data Lama']);

        $response = $this->actingAs($this->userA)->get(route('surveys.index'));

        $response->assertStatus(200);
        $response->assertSee('Survey Milik User A');
        $response->assertDontSee('Survey Milik User B');
        $response->assertDontSee('Survey Data Lama');
    }

    public function test_superadmin_sees_all_surveys_including_legacy(): void
    {
        SurveyLocation::factory()->create(['user_id' => $this->userA->id, 'alamat' => 'Survey Milik User A']);
        SurveyLocation::factory()->create(['user_id' => $this->userB->id, 'alamat' => 'Survey Milik User B']);
        SurveyLocation::factory()->create(['user_id' => null, 'alamat' => 'Survey Data Lama']);

        $response = $this->actingAs($this->superadmin)->get(route('surveys.index'));

        $response->assertStatus(200);
        $response->assertSee('Survey Milik User A');
        $response->assertSee('Survey Milik User B');
        $response->assertSee('Survey Data Lama');
    }

    public function test_superadmin_can_filter_surveys_by_user_or_legacy(): void
    {
        SurveyLocation::factory()->create(['user_id' => $this->userA->id, 'alamat' => 'Survey Milik User A']);
        SurveyLocation::factory()->create(['user_id' => $this->userB->id, 'alamat' => 'Survey Milik User B']);
        SurveyLocation::factory()->create(['user_id' => null, 'alamat' => 'Survey Data Lama']);

        $resA = $this->actingAs($this->superadmin)->get(route('surveys.index', ['user_id' => $this->userA->id]));
        $resA->assertSee('Survey Milik User A');
        $resA->assertDontSee('Survey Milik User B');
        $resA->assertDontSee('Survey Data Lama');

        $resLegacy = $this->actingAs($this->superadmin)->get(route('surveys.index', ['user_id' => 'legacy']));
        $resLegacy->assertSee('Survey Data Lama');
        $resLegacy->assertDontSee('Survey Milik User A');
        $resLegacy->assertDontSee('Survey Milik User B');
    }

    public function test_surveyor_cannot_view_or_modify_another_surveyors_survey(): void
    {
        $surveyB = SurveyLocation::factory()->create(['user_id' => $this->userB->id, 'alamat' => 'Alamat Asli B']);
        $legacySurvey = SurveyLocation::factory()->create(['user_id' => null]);

        $this->actingAs($this->userA)->get(route('surveys.show', $surveyB))->assertForbidden();
        $this->actingAs($this->userA)->get(route('surveys.show', $legacySurvey))->assertForbidden();
        $this->actingAs($this->userA)->get(route('surveys.edit', $surveyB))->assertForbidden();

        $this->actingAs($this->userA)->put(route('surveys.update', $surveyB), [
            'alamat' => 'Alamat Dibajak A',
        ])->assertForbidden();

        $this->actingAs($this->userA)->delete(route('surveys.destroy', $surveyB))->assertForbidden();
        $this->assertDatabaseHas('survey_locations', ['id' => $surveyB->id, 'alamat' => 'Alamat Asli B']);
    }

    public function test_superadmin_can_view_edit_and_delete_any_survey(): void
    {
        $surveyB = SurveyLocation::factory()->create([
            'user_id' => $this->userB->id,
            'alamat' => 'Alamat B',
            'jumlah_cctv' => 2,
        ]);

        $this->actingAs($this->superadmin)->get(route('surveys.show', $surveyB))->assertOk();
        $this->actingAs($this->superadmin)->get(route('surveys.edit', $surveyB))->assertOk();

        $resUpdate = $this->actingAs($this->superadmin)->put(route('surveys.update', $surveyB), [
            'alamat' => 'Alamat Diedit Superadmin',
            'jumlah_cctv' => 5,
        ]);
        $resUpdate->assertRedirect(route('surveys.show', $surveyB));
        $this->assertDatabaseHas('survey_locations', ['id' => $surveyB->id, 'alamat' => 'Alamat Diedit Superadmin']);

        $resDelete = $this->actingAs($this->superadmin)->delete(route('surveys.destroy', $surveyB));
        $resDelete->assertRedirect(route('surveys.index'));
        $this->assertDatabaseMissing('survey_locations', ['id' => $surveyB->id]);
    }

    public function test_created_survey_automatically_assigned_to_authenticated_user(): void
    {
        $payload = [
            'latitude' => -3.32,
            'longitude' => 114.59,
            'alamat' => 'Titik Baru Surveyor A',
            'kecamatan' => 'Banjarmasin Tengah',
            'kelurahan' => 'Kertak Baru Ilir',
            'jumlah_cctv' => 2,
        ];

        $this->actingAs($this->userA)->post(route('surveys.store'), $payload);

        $this->assertDatabaseHas('survey_locations', [
            'alamat' => 'Titik Baru Surveyor A',
            'user_id' => $this->userA->id,
        ]);
    }

    public function test_map_and_geojson_scoped_for_surveyor_and_unrestricted_for_superadmin(): void
    {
        SurveyLocation::factory()->create([
            'user_id' => $this->userA->id,
            'alamat' => 'Peta Titik User A',
            'latitude' => -3.31,
            'longitude' => 114.58,
        ]);

        SurveyLocation::factory()->create([
            'user_id' => $this->userB->id,
            'alamat' => 'Peta Titik User B',
            'latitude' => -3.32,
            'longitude' => 114.59,
        ]);

        $resUserMap = $this->actingAs($this->userA)->get(route('map'));
        $resUserMap->assertOk();
        $resUserMap->assertSee('Peta Titik User A');
        $resUserMap->assertDontSee('Peta Titik User B');

        $resAdminMap = $this->actingAs($this->superadmin)->get(route('map'));
        $resAdminMap->assertOk();
        $resAdminMap->assertSee('Peta Titik User A');
        $resAdminMap->assertSee('Peta Titik User B');

        $resGeojsonA = $this->actingAs($this->userA)->getJson(route('map.geojson'));
        $resGeojsonA->assertOk();
        $this->assertCount(1, $resGeojsonA->json('features'));

        $resGeojsonAdmin = $this->actingAs($this->superadmin)->getJson(route('map.geojson'));
        $resGeojsonAdmin->assertOk();
        $this->assertCount(2, $resGeojsonAdmin->json('features'));
    }

    public function test_survey_status_can_only_be_updated_by_superadmin_or_owner(): void
    {
        $surveyA = SurveyLocation::factory()->create([
            'user_id' => $this->userA->id,
            'status' => SurveyLocation::STATUS_BELUM_TERPASANG,
        ]);

        $surveyB = SurveyLocation::factory()->create([
            'user_id' => $this->userB->id,
            'status' => SurveyLocation::STATUS_BELUM_TERPASANG,
        ]);

        $legacySurvey = SurveyLocation::factory()->create([
            'user_id' => null,
            'status' => SurveyLocation::STATUS_BELUM_TERPASANG,
        ]);

        // Owner (User A) can update own survey status
        $this->actingAs($this->userA)
            ->patch(route('surveys.update-status', $surveyA), [
                'status' => SurveyLocation::STATUS_PROSES_PEMASANGAN,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('survey_locations', [
            'id' => $surveyA->id,
            'status' => SurveyLocation::STATUS_PROSES_PEMASANGAN,
        ]);

        // User A cannot update User B's survey status
        $this->actingAs($this->userA)
            ->patch(route('surveys.update-status', $surveyB), [
                'status' => SurveyLocation::STATUS_SELESAI_DIPASANG,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('survey_locations', [
            'id' => $surveyB->id,
            'status' => SurveyLocation::STATUS_BELUM_TERPASANG,
        ]);

        // User A cannot update legacy survey status
        $this->actingAs($this->userA)
            ->patch(route('surveys.update-status', $legacySurvey), [
                'status' => SurveyLocation::STATUS_SELESAI_DIPASANG,
            ])
            ->assertForbidden();

        // Superadmin can update any survey status (User B's and legacy)
        $this->actingAs($this->superadmin)
            ->patch(route('surveys.update-status', $surveyB), [
                'status' => SurveyLocation::STATUS_SELESAI_DIPASANG,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('survey_locations', [
            'id' => $surveyB->id,
            'status' => SurveyLocation::STATUS_SELESAI_DIPASANG,
        ]);

        $this->actingAs($this->superadmin)
            ->patch(route('surveys.update-status', $legacySurvey), [
                'status' => SurveyLocation::STATUS_PROSES_PEMASANGAN,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('survey_locations', [
            'id' => $legacySurvey->id,
            'status' => SurveyLocation::STATUS_PROSES_PEMASANGAN,
        ]);
    }
}
