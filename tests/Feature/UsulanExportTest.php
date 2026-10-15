<?php

namespace Tests\Feature;

use App\Exports\UsulanCctvSheet;
use App\Exports\UsulanExport;
use App\Exports\UsulanWifiSheet;
use App\Models\User;
use App\Models\Usulan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class UsulanExportTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;

    private User $kelurahanUser;

    private User $surveyor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::factory()->superadmin()->create(['name' => 'Super Admin', 'email' => 'super@example.com']);
        $this->kelurahanUser = User::factory()->kelurahan('Pemurus Dalam')->create(['password' => 'secret123']);
        $this->surveyor = User::factory()->create(['name' => 'Regular Surveyor', 'email' => 'surveyor@example.com']);
    }

    public function test_guests_cannot_export_usulans(): void
    {
        $response = $this->get(route('usulans.export'));

        $response->assertRedirect(route('login'));
    }

    public function test_inactive_users_cannot_export_usulans(): void
    {
        $inactiveUser = User::factory()->create(['is_active' => false]);

        $response = $this->actingAs($inactiveUser)->get(route('usulans.export'));

        $response->assertForbidden();
    }

    public function test_superadmin_can_export_cctv_usulans(): void
    {
        Excel::fake();

        Usulan::factory()->create([
            'user_id' => $this->surveyor->id,
            'jenis' => Usulan::JENIS_CCTV,
            'kelurahan' => 'Kertak Baru Ulu',
        ]);

        $response = $this->actingAs($this->superadmin)->get(route('usulans.export', ['tab' => 'cctv']));

        $response->assertOk();

        $expectedFilename = 'usulan-cctv-'.now()->format('Y-m-d').'.xlsx';
        Excel::assertDownloaded($expectedFilename, function (UsulanExport $export) {
            $sheets = $export->sheets();
            $this->assertCount(1, $sheets);
            $this->assertInstanceOf(UsulanCctvSheet::class, $sheets[0]);

            return true;
        });
    }

    public function test_superadmin_can_export_wifi_usulans(): void
    {
        Excel::fake();

        Usulan::factory()->create([
            'user_id' => $this->surveyor->id,
            'jenis' => Usulan::JENIS_WIFI,
            'kelurahan' => 'Teluk Tiram',
            'nama_fasilitas_publik' => 'Balai RT',
        ]);

        $response = $this->actingAs($this->superadmin)->get(route('usulans.export', ['tab' => 'wifi']));

        $response->assertOk();

        $expectedFilename = 'usulan-wifi-'.now()->format('Y-m-d').'.xlsx';
        Excel::assertDownloaded($expectedFilename, function (UsulanExport $export) {
            $sheets = $export->sheets();
            $this->assertCount(1, $sheets);
            $this->assertInstanceOf(UsulanWifiSheet::class, $sheets[0]);

            return true;
        });
    }

    public function test_superadmin_can_export_all_usulans_multi_sheet(): void
    {
        Excel::fake();

        Usulan::factory()->create([
            'user_id' => $this->surveyor->id,
            'jenis' => Usulan::JENIS_CCTV,
        ]);
        Usulan::factory()->create([
            'user_id' => $this->surveyor->id,
            'jenis' => Usulan::JENIS_WIFI,
        ]);

        $response = $this->actingAs($this->superadmin)->get(route('usulans.export', ['tab' => 'all']));

        $response->assertOk();

        $expectedFilename = 'usulan-cctv-wifi-'.now()->format('Y-m-d').'.xlsx';
        Excel::assertDownloaded($expectedFilename, function (UsulanExport $export) {
            $sheets = $export->sheets();
            $this->assertCount(2, $sheets);
            $this->assertInstanceOf(UsulanCctvSheet::class, $sheets[0]);
            $this->assertInstanceOf(UsulanWifiSheet::class, $sheets[1]);

            return true;
        });
    }

    public function test_kelurahan_user_can_export_usulans_restricted_to_kelurahan(): void
    {
        $usulanInKel = Usulan::factory()->create([
            'user_id' => $this->surveyor->id,
            'jenis' => Usulan::JENIS_CCTV,
            'kelurahan' => 'Pemurus Dalam',
        ]);

        $usulanOtherKel = Usulan::factory()->create([
            'user_id' => $this->surveyor->id,
            'jenis' => Usulan::JENIS_CCTV,
            'kelurahan' => 'Sungai Miai',
        ]);

        $sheet = new UsulanCctvSheet([], $this->kelurahanUser);
        $results = $sheet->query()->get();

        $this->assertTrue($results->contains('id', $usulanInKel->id));
        $this->assertFalse($results->contains('id', $usulanOtherKel->id));
    }

    public function test_surveyor_can_export_own_usulans_only(): void
    {
        $ownUsulan = Usulan::factory()->create([
            'user_id' => $this->surveyor->id,
            'jenis' => Usulan::JENIS_CCTV,
        ]);

        $otherUsulan = Usulan::factory()->create([
            'user_id' => $this->superadmin->id,
            'jenis' => Usulan::JENIS_CCTV,
        ]);

        $sheet = new UsulanCctvSheet([], $this->surveyor);
        $results = $sheet->query()->get();

        $this->assertTrue($results->contains('id', $ownUsulan->id));
        $this->assertFalse($results->contains('id', $otherUsulan->id));
    }

    public function test_export_filename_includes_tab_kelurahan_and_status(): void
    {
        Excel::fake();

        $response = $this->actingAs($this->superadmin)->get(route('usulans.export', [
            'tab' => 'wifi',
            'kelurahan' => 'Pemurus Dalam',
            'status' => 'Diajukan',
        ]));

        $response->assertOk();

        $expectedFilename = 'usulan-wifi-pemurus-dalam-diajukan-'.now()->format('Y-m-d').'.xlsx';
        Excel::assertDownloaded($expectedFilename);
    }

    public function test_export_excel_button_rendered_on_index(): void
    {
        $resCctv = $this->actingAs($this->superadmin)->get(route('usulans.index', ['tab' => 'cctv']));
        $resCctv->assertStatus(200);
        $resCctv->assertSee('Export Excel');
        $resCctv->assertSee(route('usulans.export'));

        $resWifi = $this->actingAs($this->kelurahanUser)->get(route('usulans.index', ['tab' => 'wifi']));
        $resWifi->assertStatus(200);
        $resWifi->assertSee('Export Excel');
        $resWifi->assertSee(route('usulans.export'));
    }

    public function test_export_sheets_contain_correct_headings_and_data(): void
    {
        $cctv = Usulan::factory()->create([
            'user_id' => $this->surveyor->id,
            'jenis' => Usulan::JENIS_CCTV,
            'rt' => '05',
            'nama_ketua_rt' => 'Pak Budi',
            'no_hp' => '08123456789',
            'kelurahan' => 'Melayu',
            'kecamatan' => 'Banjarmasin Tengah',
            'nama_lokasi' => 'Gang Melati',
            'kategori_titik' => 'Persimpangan',
            'infrastruktur_penyangga' => 'Tiang PLN',
            'sumber_daya_listrik' => 'Ada',
            'cctv_eksisting' => 'Belum Ada',
            'status' => 'Diajukan',
        ]);

        $cctvSheet = new UsulanCctvSheet([], $this->superadmin);
        $headingsCctv = $cctvSheet->headings();
        $this->assertContains('ID Usulan', $headingsCctv);
        $this->assertContains('CCTV Eksisting', $headingsCctv);

        $mappedCctv = $cctvSheet->map($cctv);
        $this->assertEquals($cctv->id, $mappedCctv[0]);
        $this->assertEquals('Pak Budi', $mappedCctv[8]);
        $this->assertEquals('08123456789', $mappedCctv[9]);
        $this->assertEquals('Gang Melati', $mappedCctv[12]);

        $wifi = Usulan::factory()->create([
            'user_id' => $this->surveyor->id,
            'jenis' => Usulan::JENIS_WIFI,
            'rt' => '02',
            'nama_ketua_rt' => 'Pak Joko',
            'nama_fasilitas_publik' => 'Balai RT',
            'jumlah_pengguna_potensial' => '50',
            'akses_jaringan_terdekat' => 'Ada Jalur FO',
            'tempat_pemasangan' => 'Indoor',
            'ketersediaan_listrik' => 'Tersedia',
            'status' => 'Disetujui',
        ]);

        $wifiSheet = new UsulanWifiSheet([], $this->superadmin);
        $headingsWifi = $wifiSheet->headings();
        $this->assertContains('ID Usulan', $headingsWifi);
        $this->assertContains('Fasilitas Publik', $headingsWifi);

        $mappedWifi = $wifiSheet->map($wifi);
        $this->assertEquals($wifi->id, $mappedWifi[0]);
        $this->assertEquals('Balai RT', $mappedWifi[12]);
        $this->assertEquals('50 Warga/Hari', $mappedWifi[13]);
    }

    public function test_actual_file_download_generates_valid_binary_response(): void
    {
        Usulan::factory()->create([
            'user_id' => $this->surveyor->id,
            'jenis' => Usulan::JENIS_CCTV,
            'nama_lokasi' => 'Simpang Empat',
            'latitude' => -3.316694,
            'longitude' => 114.590111,
        ]);

        $response = $this->actingAs($this->superadmin)->get(route('usulans.export', ['tab' => 'cctv']));

        $response->assertOk();
        $this->assertTrue(
            str_contains($response->headers->get('content-type') ?? '', 'spreadsheetml') ||
            str_contains($response->headers->get('content-type') ?? '', 'octet-stream')
        );
        $this->assertStringContainsString('usulan-cctv', $response->headers->get('content-disposition') ?? '');
    }
}
