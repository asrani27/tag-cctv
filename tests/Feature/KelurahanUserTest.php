<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Usulan;
use Database\Seeders\KecamatanSeeder;
use Database\Seeders\KelurahanUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KelurahanUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_kelurahan_user_seeder_creates_all_kelurahan_accounts_correctly(): void
    {
        $this->seed(KecamatanSeeder::class);
        $this->seed(KelurahanUserSeeder::class);

        // All 52 kelurahans in Banjarmasin should have an account
        $this->assertEquals(52, User::where('role', User::ROLE_KELURAHAN)->count());

        // Check specific example: Pemurus Dalam -> pemurusdalam@banjarmasinkota.go.id
        $pemurusDalam = User::where('email', 'pemurusdalam@banjarmasinkota.go.id')->first();
        $this->assertNotNull($pemurusDalam);
        $this->assertEquals('Kelurahan Pemurus Dalam', $pemurusDalam->name);
        $this->assertEquals(User::ROLE_KELURAHAN, $pemurusDalam->role);
        $this->assertTrue($pemurusDalam->is_active);
        $this->assertTrue(Hash::check('kelurahanbanjarmasin', $pemurusDalam->password));
        $this->assertEquals('Pemurus Dalam', $pemurusDalam->getKelurahanName());
    }

    public function test_kelurahan_user_can_login_with_default_password_and_is_redirected_to_profile(): void
    {
        $this->seed(KecamatanSeeder::class);
        $this->seed(KelurahanUserSeeder::class);

        $response = $this->post('/login', [
            'email' => 'pemurusdalam@banjarmasinkota.go.id',
            'password' => 'kelurahanbanjarmasin',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('warning');
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isKelurahan());
        $this->assertTrue(auth()->user()->hasDefaultPassword());
    }

    public function test_kelurahan_user_with_custom_password_redirects_to_usulans_on_login(): void
    {
        $this->seed(KecamatanSeeder::class);
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->create([
            'email' => 'pemurusdalam@banjarmasinkota.go.id',
            'password' => Hash::make('CustomPass123!'),
            'password_changed_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'pemurusdalam@banjarmasinkota.go.id',
            'password' => 'CustomPass123!',
        ]);

        $response->assertRedirect(route('usulans.index'));
        $this->assertAuthenticated();
        $this->assertFalse(auth()->user()->hasDefaultPassword());
    }

    public function test_kelurahan_user_navigation_only_shows_data_usulan_and_profil(): void
    {
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->create();

        $response = $this->actingAs($kelurahanUser)->get(route('usulans.index'));
        $response->assertStatus(200);

        // Should see Data Usulan and Profil
        $response->assertSee(route('usulans.index'));
        $response->assertSee(route('profile.edit'));
        $response->assertSee('Data Usulan');
        $response->assertSee('Profil');

        // Should NOT see other navigation menus
        $response->assertDontSee(route('dashboard'));
        $response->assertDontSee(route('surveys.index'));
        $response->assertDontSee(route('map'));
        $response->assertDontSee(route('users.index'));
        $response->assertDontSee(route('kecamatans.index'));
        $response->assertDontSee('Tambah Survey');
    }

    public function test_kelurahan_user_cannot_access_dashboard_and_is_redirected_to_usulans(): void
    {
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->create();

        $response = $this->actingAs($kelurahanUser)->get(route('dashboard'));
        $response->assertRedirect(route('usulans.index'));
    }

    public function test_kelurahan_user_cannot_access_surveys_and_is_redirected_to_usulans(): void
    {
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->create();

        $response = $this->actingAs($kelurahanUser)->get(route('surveys.index'));
        $response->assertRedirect(route('usulans.index'));

        $response = $this->actingAs($kelurahanUser)->get(route('surveys.create'));
        $response->assertRedirect(route('usulans.index'));
    }

    public function test_kelurahan_user_can_view_profile_page(): void
    {
        $this->seed(KecamatanSeeder::class);
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->create();

        $response = $this->actingAs($kelurahanUser)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('Profil Akun');
        $response->assertSee('Kelurahan Pemurus Dalam');
        $response->assertSee('pemurusdalam@banjarmasinkota.go.id');
        $response->assertSee('Kembali ke Usulan');
    }

    public function test_kelurahan_user_can_update_profile_and_change_password(): void
    {
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->withDefaultPassword()->create();

        $this->assertTrue($kelurahanUser->hasDefaultPassword());

        $response = $this->actingAs($kelurahanUser)->put(route('profile.update'), [
            'name' => 'Kelurahan Pemurus Dalam Baru',
            'current_password' => 'kelurahanbanjarmasin',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $kelurahanUser->refresh();
        $this->assertEquals('Kelurahan Pemurus Dalam Baru', $kelurahanUser->name);
        $this->assertTrue(Hash::check('StrongPass123!', $kelurahanUser->password));
        $this->assertNotNull($kelurahanUser->password_changed_at);
        $this->assertFalse($kelurahanUser->hasDefaultPassword());
    }

    public function test_kelurahan_user_cannot_change_official_email(): void
    {
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->create([
            'email' => 'pemurusdalam@banjarmasinkota.go.id',
        ]);

        $response = $this->actingAs($kelurahanUser)->put(route('profile.update'), [
            'name' => 'Kelurahan Pemurus Dalam Updated',
            'email' => 'hacked@example.com',
        ]);

        $response->assertRedirect(route('profile.edit'));

        $kelurahanUser->refresh();
        $this->assertEquals('pemurusdalam@banjarmasinkota.go.id', $kelurahanUser->email);
    }

    public function test_kelurahan_user_sees_usulans_in_own_kelurahan(): void
    {
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->create();
        $otherUser = User::factory()->create();

        // Usulan in Pemurus Dalam by another user
        Usulan::factory()->create([
            'user_id' => $otherUser->id,
            'kelurahan' => 'Pemurus Dalam',
            'nama_lokasi' => 'Pos Kamling RT 05 Pemurus Dalam',
        ]);

        // Usulan in Melayu by another user
        Usulan::factory()->create([
            'user_id' => $otherUser->id,
            'kelurahan' => 'Melayu',
            'nama_lokasi' => 'Simpang Melayu',
        ]);

        $response = $this->actingAs($kelurahanUser)->get(route('usulans.index'));
        $response->assertStatus(200);
        $response->assertSee('Pos Kamling RT 05 Pemurus Dalam');
        $response->assertDontSee('Simpang Melayu');
    }

    public function test_kelurahan_user_create_usulan_defaults_kelurahan(): void
    {
        $this->seed(KecamatanSeeder::class);
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->create();

        $response = $this->actingAs($kelurahanUser)->get(route('usulans.create'));
        $response->assertStatus(200);
        $response->assertSee('Pemurus Dalam');
        $response->assertSee('Banjarmasin Selatan');

        $responseWifi = $this->actingAs($kelurahanUser)->get(route('usulans.create', ['jenis' => 'wifi']));
        $responseWifi->assertStatus(200);
        $responseWifi->assertSee('Pemurus Dalam');
        $responseWifi->assertSee('Tambah Data Usulan Titik Wifi');
    }

    public function test_kelurahan_user_sees_wifi_usulan_tab_isolated(): void
    {
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->create();
        $otherUser = User::factory()->create();

        // Wifi usulan in Pemurus Dalam
        Usulan::factory()->wifi()->create([
            'user_id' => $otherUser->id,
            'kelurahan' => 'Pemurus Dalam',
            'nama_fasilitas_publik' => 'Balai RT Pemurus',
        ]);

        // Wifi usulan in Melayu
        Usulan::factory()->wifi()->create([
            'user_id' => $otherUser->id,
            'kelurahan' => 'Melayu',
            'nama_fasilitas_publik' => 'Taman Warga Melayu',
        ]);

        $response = $this->actingAs($kelurahanUser)->get(route('usulans.index', ['tab' => 'wifi']));
        $response->assertStatus(200);
        $response->assertSee('Balai RT Pemurus');
        $response->assertDontSee('Taman Warga Melayu');
    }

    public function test_superadmin_can_manage_kelurahan_role_users(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        // Create user with kelurahan role
        $response = $this->actingAs($superadmin)->post(route('users.store'), [
            'name' => 'Kelurahan Custom Baru',
            'email' => 'custombaru@banjarmasinkota.go.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => User::ROLE_KELURAHAN,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'custombaru@banjarmasinkota.go.id',
            'role' => User::ROLE_KELURAHAN,
        ]);
    }

    public function test_kelurahan_user_password_change_validates_complexity_requirements(): void
    {
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->withDefaultPassword()->create();

        // 1. Password is required if on default password
        $res = $this->actingAs($kelurahanUser)->put(route('profile.update'), [
            'name' => 'Kelurahan Pemurus Dalam',
        ]);
        $res->assertSessionHasErrors(['password']);

        // 2. Too short (< 8 chars)
        $res = $this->actingAs($kelurahanUser)->put(route('profile.update'), [
            'name' => 'Kelurahan Pemurus Dalam',
            'current_password' => 'kelurahanbanjarmasin',
            'password' => 'Aa1!',
            'password_confirmation' => 'Aa1!',
        ]);
        $res->assertSessionHasErrors(['password']);

        // 3. No uppercase
        $res = $this->actingAs($kelurahanUser)->put(route('profile.update'), [
            'name' => 'Kelurahan Pemurus Dalam',
            'current_password' => 'kelurahanbanjarmasin',
            'password' => 'lowercase123!',
            'password_confirmation' => 'lowercase123!',
        ]);
        $res->assertSessionHasErrors(['password']);

        // 4. No number
        $res = $this->actingAs($kelurahanUser)->put(route('profile.update'), [
            'name' => 'Kelurahan Pemurus Dalam',
            'current_password' => 'kelurahanbanjarmasin',
            'password' => 'NoNumberSymbol!',
            'password_confirmation' => 'NoNumberSymbol!',
        ]);
        $res->assertSessionHasErrors(['password']);

        // 5. No symbol
        $res = $this->actingAs($kelurahanUser)->put(route('profile.update'), [
            'name' => 'Kelurahan Pemurus Dalam',
            'current_password' => 'kelurahanbanjarmasin',
            'password' => 'NoSymbolNumber123',
            'password_confirmation' => 'NoSymbolNumber123',
        ]);
        $res->assertSessionHasErrors(['password']);

        // 6. Same as current password
        $res = $this->actingAs($kelurahanUser)->put(route('profile.update'), [
            'name' => 'Kelurahan Pemurus Dalam',
            'current_password' => 'kelurahanbanjarmasin',
            'password' => 'kelurahanbanjarmasin',
            'password_confirmation' => 'kelurahanbanjarmasin',
        ]);
        $res->assertSessionHasErrors(['password']);

        // 7. Wrong current password
        $res = $this->actingAs($kelurahanUser)->put(route('profile.update'), [
            'name' => 'Kelurahan Pemurus Dalam',
            'current_password' => 'wrongpassword',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ]);
        $res->assertSessionHasErrors(['current_password']);
    }

    public function test_kelurahan_user_with_default_password_is_blocked_from_submitting_usulan(): void
    {
        $this->seed(KecamatanSeeder::class);
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->withDefaultPassword()->create();

        $this->assertFalse(Gate::forUser($kelurahanUser)->allows('create', Usulan::class));

        // GET usulans.create redirects to profile.edit
        $response = $this->actingAs($kelurahanUser)->get(route('usulans.create'));
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('warning');

        // POST usulans.store redirects to profile.edit and nothing is stored
        $storeResponse = $this->actingAs($kelurahanUser)->post(route('usulans.store'), [
            'jenis' => Usulan::JENIS_CCTV,
            'nama_lokasi' => 'Gang',
            'kelurahan' => 'Pemurus Dalam',
            'kecamatan' => 'Banjarmasin Selatan',
            'rt' => '05',
            'latitude' => -3.33,
            'longitude' => 114.59,
            'kategori_titik' => 'Rawan Kriminalitas',
            'keterangan' => 'Keamanan wilayah lingkungan warga',
        ]);

        $storeResponse->assertRedirect(route('profile.edit'));
        $storeResponse->assertSessionHas('warning');
        $this->assertDatabaseMissing('usulans', [
            'rt' => '05',
            'user_id' => $kelurahanUser->id,
        ]);
    }

    public function test_kelurahan_user_with_default_password_is_blocked_from_modifying_usulan(): void
    {
        $this->seed(KecamatanSeeder::class);
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->withDefaultPassword()->create();

        $usulan = Usulan::factory()->create([
            'user_id' => $kelurahanUser->id,
            'kelurahan' => 'Pemurus Dalam',
            'status' => 'Diajukan',
        ]);

        $this->assertFalse(Gate::forUser($kelurahanUser)->allows('update', $usulan));
        $this->assertFalse(Gate::forUser($kelurahanUser)->allows('delete', $usulan));

        // GET usulans.edit redirects to profile.edit
        $response = $this->actingAs($kelurahanUser)->get(route('usulans.edit', $usulan));
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('warning');

        // PUT usulans.update redirects to profile.edit
        $updateResponse = $this->actingAs($kelurahanUser)->put(route('usulans.update', $usulan), [
            'jenis' => Usulan::JENIS_CCTV,
            'nama_lokasi' => 'Jalan',
            'kelurahan' => 'Pemurus Dalam',
            'rt' => '02',
            'latitude' => -3.33,
            'longitude' => 114.59,
            'kategori_titik' => 'Persimpangan',
        ]);
        $updateResponse->assertRedirect(route('profile.edit'));
        $updateResponse->assertSessionHas('warning');
    }

    public function test_kelurahan_user_after_changing_password_can_submit_usulan(): void
    {
        $this->seed(KecamatanSeeder::class);
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->withDefaultPassword()->create();

        // Initially blocked
        $this->assertFalse(Gate::forUser($kelurahanUser)->allows('create', Usulan::class));

        // Change password
        $response = $this->actingAs($kelurahanUser)->put(route('profile.update'), [
            'name' => 'Kelurahan Pemurus Dalam',
            'current_password' => 'kelurahanbanjarmasin',
            'password' => 'StrongPass123!',
            'password_confirmation' => 'StrongPass123!',
        ]);
        $response->assertRedirect(route('profile.edit'));

        $kelurahanUser->refresh();
        $this->assertFalse($kelurahanUser->hasDefaultPassword());
        $this->assertTrue(Gate::forUser($kelurahanUser)->allows('create', Usulan::class));

        // Can access create page
        $createResponse = $this->actingAs($kelurahanUser)->get(route('usulans.create'));
        $createResponse->assertStatus(200);

        // Can store usulan
        $storeResponse = $this->actingAs($kelurahanUser)->post(route('usulans.store'), [
            'jenis' => Usulan::JENIS_CCTV,
            'nama_lokasi' => 'Gang',
            'kelurahan' => 'Pemurus Dalam',
            'kecamatan' => 'Banjarmasin Selatan',
            'rt' => '07',
            'latitude' => -3.33,
            'longitude' => 114.59,
            'kategori_titik' => 'Rawan Kriminalitas',
            'keterangan' => 'Keamanan wilayah lingkungan warga',
        ]);

        $storeResponse->assertRedirect(route('usulans.index'));
        $this->assertDatabaseHas('usulans', [
            'rt' => '07',
            'user_id' => $kelurahanUser->id,
        ]);
    }

    public function test_kelurahan_user_with_default_password_sees_warning_banner_on_usulan_index(): void
    {
        $kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->withDefaultPassword()->create();

        $response = $this->actingAs($kelurahanUser)->get(route('usulans.index'));
        $response->assertStatus(200);
        $response->assertSee('Perhatian: Wajib Ganti Kata Sandi');
        $response->assertSee('Ganti Password untuk Tambah Usulan');
        $response->assertDontSee('Tambah Usulan CCTV');
    }
}
