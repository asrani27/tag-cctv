<?php

namespace Tests\Feature;

use App\Models\SurveyLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private User $surveyor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->superadmin()->create([
            'name' => 'Super Admin User',
            'email' => 'superadmin@banjarmasin.go.id',
        ]);

        $this->surveyor = User::factory()->create([
            'name' => 'Regular Surveyor',
            'email' => 'surveyor@banjarmasin.go.id',
        ]);
    }

    public function test_surveyor_cannot_access_user_management(): void
    {
        $this->actingAs($this->surveyor)->get(route('users.index'))->assertForbidden();
        $this->actingAs($this->surveyor)->get(route('users.create'))->assertForbidden();
        $this->actingAs($this->surveyor)->post(route('users.store'), [])->assertForbidden();
        $this->actingAs($this->surveyor)->get(route('users.edit', $this->surveyor))->assertForbidden();
        $this->actingAs($this->surveyor)->put(route('users.update', $this->surveyor), [])->assertForbidden();
        $this->actingAs($this->surveyor)->patch(route('users.toggle-status', $this->surveyor))->assertForbidden();
        $this->actingAs($this->surveyor)->delete(route('users.destroy', $this->surveyor))->assertForbidden();
    }

    public function test_superadmin_can_view_user_list_and_search_filter(): void
    {
        $targetUser = User::factory()->create([
            'name' => 'Ahmad Khusus',
            'email' => 'ahmad@banjarmasin.go.id',
            'role' => User::ROLE_USER,
            'is_active' => true,
        ]);

        $resIndex = $this->actingAs($this->superadmin)->get(route('users.index'));
        $resIndex->assertOk();
        $resIndex->assertSee('Ahmad Khusus');

        $resSearch = $this->actingAs($this->superadmin)->get(route('users.index', ['search' => 'Ahmad']));
        $resSearch->assertOk();
        $resSearch->assertSee('Ahmad Khusus');
        $resSearch->assertDontSee('Regular Surveyor');

        $resRoleFilter = $this->actingAs($this->superadmin)->get(route('users.index', ['role' => 'superadmin']));
        $resRoleFilter->assertOk();
        $resRoleFilter->assertSee('Super Admin User');
        $resRoleFilter->assertDontSee('Ahmad Khusus');
    }

    public function test_superadmin_can_create_new_user(): void
    {
        $payload = [
            'name' => 'Surveyor Baru',
            'email' => 'baru@banjarmasin.go.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => User::ROLE_USER,
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->superadmin)->post(route('users.store'), $payload);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Surveyor Baru',
            'email' => 'baru@banjarmasin.go.id',
            'role' => User::ROLE_USER,
            'is_active' => true,
        ]);
    }

    public function test_superadmin_can_update_user(): void
    {
        $payload = [
            'name' => 'Surveyor Updated',
            'email' => 'surveyor.updated@banjarmasin.go.id',
            'role' => User::ROLE_USER,
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->superadmin)->put(route('users.update', $this->surveyor), $payload);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $this->surveyor->id,
            'name' => 'Surveyor Updated',
            'email' => 'surveyor.updated@banjarmasin.go.id',
        ]);
    }

    public function test_superadmin_can_toggle_user_status(): void
    {
        $this->assertTrue($this->surveyor->is_active);

        $this->actingAs($this->superadmin)->patch(route('users.toggle-status', $this->surveyor));
        $this->assertFalse($this->surveyor->fresh()->is_active);

        $this->actingAs($this->superadmin)->patch(route('users.toggle-status', $this->surveyor));
        $this->assertTrue($this->surveyor->fresh()->is_active);
    }

    public function test_superadmin_cannot_demote_or_deactivate_self(): void
    {
        // Try demoting self
        $this->actingAs($this->superadmin)->put(route('users.update', $this->superadmin), [
            'name' => 'Super Admin Renamed',
            'email' => $this->superadmin->email,
            'role' => User::ROLE_USER,
            'is_active' => '0',
        ]);

        $freshAdmin = $this->superadmin->fresh();
        $this->assertEquals(User::ROLE_SUPERADMIN, $freshAdmin->role);
        $this->assertTrue($freshAdmin->is_active);

        // Try toggling own status
        $this->actingAs($this->superadmin)->patch(route('users.toggle-status', $this->superadmin));
        $this->assertTrue($this->superadmin->fresh()->is_active);
    }

    public function test_superadmin_cannot_delete_self(): void
    {
        $response = $this->actingAs($this->superadmin)->delete(route('users.destroy', $this->superadmin));

        $response->assertRedirect(route('users.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->superadmin->id]);
    }

    public function test_superadmin_can_delete_other_user_and_surveys_persist(): void
    {
        $survey = SurveyLocation::factory()->create([
            'user_id' => $this->surveyor->id,
            'alamat' => 'Survey Milik Akun Terhapus',
        ]);

        $response = $this->actingAs($this->superadmin)->delete(route('users.destroy', $this->surveyor));

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $this->surveyor->id]);

        // Survey still exists in the database
        $this->assertDatabaseHas('survey_locations', [
            'id' => $survey->id,
            'alamat' => 'Survey Milik Akun Terhapus',
        ]);
    }
}
