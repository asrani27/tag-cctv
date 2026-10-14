<?php

namespace Tests\Feature;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\SurveyLocation;
use App\Models\User;
use Database\Seeders\KecamatanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KecamatanKelurahanManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    private User $surveyor;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default master data
        $this->seed(KecamatanSeeder::class);

        $this->superadmin = User::factory()->superadmin()->create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@banjarmasin.go.id',
        ]);

        $this->surveyor = User::factory()->create([
            'name' => 'Regular Surveyor',
            'email' => 'surveyor@banjarmasin.go.id',
        ]);
    }

    public function test_guest_cannot_access_kecamatan_or_kelurahan_management(): void
    {
        $kecamatan = Kecamatan::first();
        $kelurahan = Kelurahan::first();

        $this->get(route('kecamatans.index'))->assertRedirect(route('login'));
        $this->get(route('kecamatans.create'))->assertRedirect(route('login'));
        $this->post(route('kecamatans.store'), [])->assertRedirect(route('login'));
        $this->get(route('kecamatans.edit', $kecamatan))->assertRedirect(route('login'));
        $this->put(route('kecamatans.update', $kecamatan), [])->assertRedirect(route('login'));
        $this->delete(route('kecamatans.destroy', $kecamatan))->assertRedirect(route('login'));

        $this->get(route('kelurahans.index'))->assertRedirect(route('login'));
        $this->get(route('kelurahans.create'))->assertRedirect(route('login'));
        $this->post(route('kelurahans.store'), [])->assertRedirect(route('login'));
        $this->get(route('kelurahans.edit', $kelurahan))->assertRedirect(route('login'));
        $this->put(route('kelurahans.update', $kelurahan), [])->assertRedirect(route('login'));
        $this->delete(route('kelurahans.destroy', $kelurahan))->assertRedirect(route('login'));
    }

    public function test_surveyor_cannot_access_kecamatan_or_kelurahan_management(): void
    {
        $kecamatan = Kecamatan::first();
        $kelurahan = Kelurahan::first();

        $this->actingAs($this->surveyor)->get(route('kecamatans.index'))->assertForbidden();
        $this->actingAs($this->surveyor)->get(route('kecamatans.create'))->assertForbidden();
        $this->actingAs($this->surveyor)->post(route('kecamatans.store'), [])->assertForbidden();
        $this->actingAs($this->surveyor)->get(route('kecamatans.edit', $kecamatan))->assertForbidden();
        $this->actingAs($this->surveyor)->put(route('kecamatans.update', $kecamatan), [])->assertForbidden();
        $this->actingAs($this->surveyor)->delete(route('kecamatans.destroy', $kecamatan))->assertForbidden();

        $this->actingAs($this->surveyor)->get(route('kelurahans.index'))->assertForbidden();
        $this->actingAs($this->surveyor)->get(route('kelurahans.create'))->assertForbidden();
        $this->actingAs($this->surveyor)->post(route('kelurahans.store'), [])->assertForbidden();
        $this->actingAs($this->surveyor)->get(route('kelurahans.edit', $kelurahan))->assertForbidden();
        $this->actingAs($this->surveyor)->put(route('kelurahans.update', $kelurahan), [])->assertForbidden();
        $this->actingAs($this->surveyor)->delete(route('kelurahans.destroy', $kelurahan))->assertForbidden();
    }

    public function test_superadmin_can_view_kecamatan_list_and_navigation(): void
    {
        $res = $this->actingAs($this->superadmin)->get(route('kecamatans.index'));

        $res->assertOk();
        $res->assertSee('Manajemen Wilayah');
        $res->assertSee('Banjarmasin Selatan');
        $res->assertSee('63.71.01');
    }

    public function test_superadmin_can_search_kecamatan(): void
    {
        $res = $this->actingAs($this->superadmin)->get(route('kecamatans.index', ['search' => 'Selatan']));

        $res->assertOk();
        $res->assertSee('Banjarmasin Selatan');
        $res->assertDontSee('Banjarmasin Utara');
    }

    public function test_superadmin_can_create_kecamatan_with_validation(): void
    {
        $createPage = $this->actingAs($this->superadmin)->get(route('kecamatans.create'));
        $createPage->assertOk();
        $createPage->assertSee('Tambah Kecamatan Baru');

        $failRes = $this->actingAs($this->superadmin)->post(route('kecamatans.store'), [
            'kode' => '63.71.01',
            'name' => 'Banjarmasin Selatan',
        ]);
        $failRes->assertSessionHasErrors(['kode', 'name']);

        $successRes = $this->actingAs($this->superadmin)->post(route('kecamatans.store'), [
            'kode' => '63.71.06',
            'name' => 'Kecamatan Baru Banjarmasin',
        ]);

        $successRes->assertRedirect(route('kecamatans.index'));
        $this->assertDatabaseHas('kecamatans', [
            'kode' => '63.71.06',
            'name' => 'Kecamatan Baru Banjarmasin',
        ]);
    }

    public function test_superadmin_can_edit_and_update_kecamatan_and_sync_surveys(): void
    {
        $kecamatan = Kecamatan::where('kode', '63.71.01')->first();
        $oldName = $kecamatan->name;

        $survey = SurveyLocation::factory()->create([
            'kecamatan' => $oldName,
            'kelurahan' => 'Kelayan Selatan',
            'user_id' => $this->superadmin->id,
        ]);

        $editPage = $this->actingAs($this->superadmin)->get(route('kecamatans.edit', $kecamatan));
        $editPage->assertOk();
        $editPage->assertSee($kecamatan->name);

        $updateRes = $this->actingAs($this->superadmin)->put(route('kecamatans.update', $kecamatan), [
            'kode' => '63.71.01',
            'name' => 'Banjarmasin Selatan Update',
        ]);

        $updateRes->assertRedirect(route('kecamatans.index'));
        $this->assertDatabaseHas('kecamatans', [
            'id' => $kecamatan->id,
            'name' => 'Banjarmasin Selatan Update',
        ]);

        $this->assertDatabaseHas('survey_locations', [
            'id' => $survey->id,
            'kecamatan' => 'Banjarmasin Selatan Update',
        ]);
    }

    public function test_superadmin_cannot_delete_kecamatan_with_kelurahans_or_surveys(): void
    {
        $kecamatan = Kecamatan::where('kode', '63.71.01')->first();

        $res = $this->actingAs($this->superadmin)->delete(route('kecamatans.destroy', $kecamatan));
        $res->assertRedirect(route('kecamatans.index'));
        $res->assertSessionHas('error');
        $this->assertDatabaseHas('kecamatans', ['id' => $kecamatan->id]);
    }

    public function test_superadmin_can_delete_empty_kecamatan(): void
    {
        $emptyKecamatan = Kecamatan::create([
            'kode' => '63.71.99',
            'name' => 'Kecamatan Kosong',
        ]);

        $res = $this->actingAs($this->superadmin)->delete(route('kecamatans.destroy', $emptyKecamatan));
        $res->assertRedirect(route('kecamatans.index'));
        $res->assertSessionHas('success');
        $this->assertDatabaseMissing('kecamatans', ['id' => $emptyKecamatan->id]);
    }

    public function test_superadmin_can_view_kelurahan_list_and_filters(): void
    {
        $res = $this->actingAs($this->superadmin)->get(route('kelurahans.index'));
        $res->assertOk();
        $res->assertSee('Data Kelurahan');
        $res->assertSee('Mantuil');
        $res->assertSee('63.71.01.1001');

        // Filter by kecamatan
        $kecamatanSelatan = Kecamatan::where('kode', '63.71.01')->first();
        $resFiltered = $this->actingAs($this->superadmin)->get(route('kelurahans.index', [
            'kecamatan_id' => $kecamatanSelatan->id,
        ]));
        $resFiltered->assertOk();
        $resFiltered->assertSee('Mantuil');
        $resFiltered->assertDontSee('Kebun Bunga');
    }

    public function test_superadmin_can_create_kelurahan_with_validation(): void
    {
        $kecamatan = Kecamatan::first();

        $createPage = $this->actingAs($this->superadmin)->get(route('kelurahans.create', ['kecamatan_id' => $kecamatan->id]));
        $createPage->assertOk();
        $createPage->assertSee('Tambah Kelurahan Baru');

        // Duplicate code error
        $existing = Kelurahan::first();
        $failRes = $this->actingAs($this->superadmin)->post(route('kelurahans.store'), [
            'kecamatan_id' => $kecamatan->id,
            'kode' => $existing->kode,
            'name' => 'Kelurahan Duplikat',
        ]);
        $failRes->assertSessionHasErrors(['kode']);

        // Success create
        $successRes = $this->actingAs($this->superadmin)->post(route('kelurahans.store'), [
            'kecamatan_id' => $kecamatan->id,
            'kode' => '63.71.01.9999',
            'name' => 'Kelurahan Baru Mantuil Dua',
        ]);

        $successRes->assertRedirect(route('kelurahans.index', ['kecamatan_id' => $kecamatan->id]));
        $this->assertDatabaseHas('kelurahans', [
            'kode' => '63.71.01.9999',
            'name' => 'Kelurahan Baru Mantuil Dua',
            'kecamatan_id' => $kecamatan->id,
        ]);
    }

    public function test_superadmin_can_edit_and_update_kelurahan_and_sync_surveys(): void
    {
        $kelurahan = Kelurahan::where('kode', '63.71.01.1001')->first();
        $oldName = $kelurahan->name;

        $survey = SurveyLocation::factory()->create([
            'kecamatan' => 'Banjarmasin Selatan',
            'kelurahan' => $oldName,
            'user_id' => $this->superadmin->id,
        ]);

        $editPage = $this->actingAs($this->superadmin)->get(route('kelurahans.edit', $kelurahan));
        $editPage->assertOk();
        $editPage->assertSee($kelurahan->name);

        $updateRes = $this->actingAs($this->superadmin)->put(route('kelurahans.update', $kelurahan), [
            'kecamatan_id' => $kelurahan->kecamatan_id,
            'kode' => '63.71.01.1001',
            'name' => 'Mantuil Barat Baru',
        ]);

        $updateRes->assertRedirect(route('kelurahans.index', ['kecamatan_id' => $kelurahan->kecamatan_id]));
        $this->assertDatabaseHas('kelurahans', [
            'id' => $kelurahan->id,
            'name' => 'Mantuil Barat Baru',
        ]);

        $this->assertDatabaseHas('survey_locations', [
            'id' => $survey->id,
            'kelurahan' => 'Mantuil Barat Baru',
        ]);
    }

    public function test_superadmin_cannot_delete_kelurahan_with_surveys(): void
    {
        $kelurahan = Kelurahan::first();

        SurveyLocation::factory()->create([
            'kecamatan' => $kelurahan->kecamatan->name,
            'kelurahan' => $kelurahan->name,
            'user_id' => $this->superadmin->id,
        ]);

        $res = $this->actingAs($this->superadmin)->delete(route('kelurahans.destroy', $kelurahan));
        $res->assertRedirect(route('kelurahans.index', ['kecamatan_id' => $kelurahan->kecamatan_id]));
        $res->assertSessionHas('error');
        $this->assertDatabaseHas('kelurahans', ['id' => $kelurahan->id]);
    }

    public function test_superadmin_can_delete_unused_kelurahan(): void
    {
        $kecamatan = Kecamatan::first();
        $unusedKelurahan = Kelurahan::create([
            'kecamatan_id' => $kecamatan->id,
            'kode' => '63.71.01.9998',
            'name' => 'Kelurahan Tanpa Survey',
        ]);

        $res = $this->actingAs($this->superadmin)->delete(route('kelurahans.destroy', $unusedKelurahan));
        $res->assertRedirect(route('kelurahans.index', ['kecamatan_id' => $kecamatan->id]));
        $res->assertSessionHas('success');
        $this->assertDatabaseMissing('kelurahans', ['id' => $unusedKelurahan->id]);
    }

    public function test_sidebar_and_navbar_show_kecamatan_kelurahan_menu_for_superadmin(): void
    {
        $adminDashboard = $this->actingAs($this->superadmin)->get(route('dashboard'));
        $adminDashboard->assertOk();
        $adminDashboard->assertSee(route('kecamatans.index'));
        $adminDashboard->assertSee('Kecamatan & Kelurahan', false);

        $surveyorDashboard = $this->actingAs($this->surveyor)->get(route('dashboard'));
        $surveyorDashboard->assertOk();
        $surveyorDashboard->assertDontSee(route('kecamatans.index'));
    }
}
