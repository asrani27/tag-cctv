<?php

namespace Tests\Feature;

use App\Models\SurveyLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapTest extends TestCase
{
    use RefreshDatabase;

    public function test_map_page_can_be_rendered_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        SurveyLocation::factory()->create([
            'alamat' => 'Titik Map Siring',
            'latitude' => -3.319400,
            'longitude' => 114.590800,
        ]);

        SurveyLocation::factory()->create([
            'alamat' => 'Titik Map Tanpa Koordinat',
            'latitude' => null,
            'longitude' => null,
        ]);

        $response = $this->actingAs($user)->get('/map');

        $response->assertStatus(200);
        $response->assertSee('Peta Sebaran Titik Survei');
        $response->assertSee('Titik Terpetakan');
    }

    public function test_map_page_can_be_rendered_for_guests(): void
    {
        $response = $this->get('/map');

        $response->assertStatus(200);
        $response->assertSee('Peta Sebaran Titik Survei');
    }
}

