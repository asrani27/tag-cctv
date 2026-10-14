<?php

namespace Tests\Feature;

use App\Models\Kelurahan;
use App\Models\User;
use App\Models\Usulan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsulanCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    private User $surveyorA;

    private User $surveyorB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->superadmin()->create(['name' => 'Admin Usulan']);
        $this->surveyorA = User::factory()->create(['name' => 'Surveyor A']);
        $this->surveyorB = User::factory()->create(['name' => 'Surveyor B']);
    }

    public function test_guest_cannot_access_usulans(): void
    {
        $response = $this->get(route('usulans.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('usulans.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_surveyor_only_sees_own_usulans_in_index(): void
    {
        Usulan::factory()->create([
            'user_id' => $this->surveyorA->id,
            'nama_ketua_rt' => 'Pak Budi RT 01',
            'kelurahan' => 'Kertak Baru Ilir',
        ]);
        Usulan::factory()->create([
            'user_id' => $this->surveyorB->id,
            'nama_ketua_rt' => 'Pak Joko RT 02',
            'kelurahan' => 'Melayu',
        ]);

        $response = $this->actingAs($this->surveyorA)->get(route('usulans.index'));

        $response->assertStatus(200);
        $response->assertSee('Pak Budi RT 01');
        $response->assertDontSee('Pak Joko RT 02');
    }

    public function test_superadmin_sees_all_usulans_in_index(): void
    {
        Usulan::factory()->create([
            'user_id' => $this->surveyorA->id,
            'nama_ketua_rt' => 'Pak Budi RT 01',
        ]);
        Usulan::factory()->create([
            'user_id' => $this->surveyorB->id,
            'nama_ketua_rt' => 'Pak Joko RT 02',
        ]);

        $response = $this->actingAs($this->superadmin)->get(route('usulans.index'));

        $response->assertStatus(200);
        $response->assertSee('Pak Budi RT 01');
        $response->assertSee('Pak Joko RT 02');
    }

    public function test_create_usulan_view_renders_properly(): void
    {
        $response = $this->actingAs($this->surveyorA)->get(route('usulans.create'));

        $response->assertStatus(200);
        $response->assertSee('id="survey-form-map"', false);
        $response->assertSee('id="btn-get-location"', false);
        $response->assertSee('survey-location-picker.js');
        $response->assertSee('Nama Lokasi / Titik Usulan');
        $response->assertSee('Kategori Titik');
        $response->assertSee('Infrastruktur Penyangga');
    }

    public function test_user_can_create_usulan_with_all_fields(): void
    {
        $payload = [
            'latitude' => -3.3194372,
            'longitude' => 114.5908123,
            'kelurahan' => 'Kertak Baru Ilir',
            'rt' => '05',
            'nama_ketua_rt' => 'Haji Mansyur',
            'no_hp' => '081234567890',
            'nama_lokasi' => 'Gang',
            'kategori_titik' => 'Pintu Masuk RT',
            'infrastruktur_penyangga' => 'Tiang PLN',
            'sumber_daya_listrik' => 'Ada',
            'cctv_eksisting' => 'Belum',
            'keterangan' => 'Gerbang masuk gang rawan kehilangan motor pada malam hari.',
        ];

        $response = $this->actingAs($this->surveyorA)->post(route('usulans.store'), $payload);

        $response->assertRedirect(route('usulans.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('usulans', [
            'user_id' => $this->surveyorA->id,
            'kelurahan' => 'Kertak Baru Ilir',
            'rt' => '05',
            'nama_ketua_rt' => 'Haji Mansyur',
            'no_hp' => '081234567890',
            'nama_lokasi' => 'Gang',
            'kategori_titik' => 'Pintu Masuk RT',
            'infrastruktur_penyangga' => 'Tiang PLN',
            'sumber_daya_listrik' => 'Ada',
            'cctv_eksisting' => 'Belum',
            'status' => 'Diajukan',
        ]);
    }

    public function test_validation_requires_mandatory_fields(): void
    {
        $response = $this->actingAs($this->surveyorA)->post(route('usulans.store'), []);

        $response->assertSessionHasErrors([
            'latitude',
            'longitude',
            'kelurahan',
            'rt',
            'nama_lokasi',
            'kategori_titik',
        ]);
    }

    public function test_coordinate_boundaries_validation(): void
    {
        $response = $this->actingAs($this->surveyorA)->post(route('usulans.store'), [
            'latitude' => 95, // invalid (>90)
            'longitude' => -190, // invalid (<-180)
            'kelurahan' => 'Kertak Baru Ilir',
            'rt' => '01',
            'nama_lokasi' => 'Gang',
            'kategori_titik' => 'Persimpangan',
        ]);

        $response->assertSessionHasErrors(['latitude', 'longitude']);
    }

    public function test_surveyor_cannot_view_or_update_or_delete_other_user_usulan(): void
    {
        $usulanA = Usulan::factory()->create(['user_id' => $this->surveyorA->id]);

        $response = $this->actingAs($this->surveyorB)->get(route('usulans.show', $usulanA));
        $response->assertStatus(403);

        $response = $this->actingAs($this->surveyorB)->get(route('usulans.edit', $usulanA));
        $response->assertStatus(403);

        $response = $this->actingAs($this->surveyorB)->put(route('usulans.update', $usulanA), [
            'latitude' => -3.31,
            'longitude' => 114.59,
            'kelurahan' => 'Kertak Baru Ilir',
            'rt' => '01',
            'nama_lokasi' => 'Jalan',
            'kategori_titik' => 'Persimpangan',
        ]);
        $response->assertStatus(403);

        $response = $this->actingAs($this->surveyorB)->delete(route('usulans.destroy', $usulanA));
        $response->assertStatus(403);
    }

    public function test_user_can_view_own_usulan_detail_with_map(): void
    {
        $usulan = Usulan::factory()->create([
            'user_id' => $this->surveyorA->id,
            'nama_lokasi' => 'Gang',
            'rt' => '12',
            'kelurahan' => 'Kertak Baru Ilir',
            'latitude' => -3.3194,
            'longitude' => 114.5908,
            'nama_ketua_rt' => 'Pak Haji Amir',
            'no_hp' => '081298765432',
        ]);

        $response = $this->actingAs($this->surveyorA)->get(route('usulans.show', $usulan));

        $response->assertStatus(200);
        $response->assertSee('Detail Usulan Titik CCTV');
        $response->assertSee('Pak Haji Amir');
        $response->assertSee('081298765432');
        $response->assertSee('id="mini-map"', false);
    }

    public function test_user_can_delete_own_usulan(): void
    {
        $usulan = Usulan::factory()->create([
            'user_id' => $this->surveyorA->id,
        ]);

        $response = $this->actingAs($this->surveyorA)->delete(route('usulans.destroy', $usulan));

        $response->assertRedirect(route('usulans.index'));
        $this->assertDatabaseMissing('usulans', [
            'id' => $usulan->id,
        ]);
    }

    public function test_superadmin_can_update_and_change_status(): void
    {
        $usulan = Usulan::factory()->create([
            'user_id' => $this->surveyorA->id,
            'status' => 'Diajukan',
        ]);

        $response = $this->actingAs($this->superadmin)->put(route('usulans.update', $usulan), [
            'latitude' => $usulan->latitude,
            'longitude' => $usulan->longitude,
            'kelurahan' => $usulan->kelurahan,
            'rt' => $usulan->rt,
            'nama_lokasi' => $usulan->nama_lokasi,
            'kategori_titik' => $usulan->kategori_titik,
            'status' => 'Disetujui',
        ]);

        $response->assertRedirect(route('usulans.show', $usulan));
        $this->assertDatabaseHas('usulans', [
            'id' => $usulan->id,
            'status' => 'Disetujui',
        ]);
    }

    public function test_filtering_and_search_on_index(): void
    {
        Usulan::factory()->create([
            'user_id' => $this->surveyorA->id,
            'kelurahan' => 'Kertak Baru Ilir',
            'nama_lokasi' => 'Gang',
            'kategori_titik' => 'Persimpangan',
            'nama_ketua_rt' => 'Haji Mansyur RT05',
        ]);
        Usulan::factory()->create([
            'user_id' => $this->surveyorA->id,
            'kelurahan' => 'Melayu',
            'nama_lokasi' => 'Jalan',
            'kategori_titik' => 'Rawan Kriminalitas',
            'nama_ketua_rt' => 'Pak Joko RT02',
        ]);

        // Search term
        $response = $this->actingAs($this->surveyorA)->get(route('usulans.index', ['search' => 'Mansyur']));
        $response->assertSee('Haji Mansyur RT05');
        $response->assertDontSee('Pak Joko RT02');

        // Filter kelurahan
        $response = $this->actingAs($this->surveyorA)->get(route('usulans.index', ['kelurahan' => 'Melayu']));
        $response->assertSee('Pak Joko RT02');
        $response->assertDontSee('Haji Mansyur RT05');

        // Filter nama lokasi
        $response = $this->actingAs($this->surveyorA)->get(route('usulans.index', ['nama_lokasi' => 'Gang']));
        $response->assertSee('Haji Mansyur RT05');
        $response->assertDontSee('Pak Joko RT02');
    }
}
