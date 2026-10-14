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
}
