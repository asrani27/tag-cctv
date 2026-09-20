<?php

namespace Tests\Feature;

use App\Models\SurveyLocation;
use App\Models\SurveyPhoto;
use App\Models\SurveyTempPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChunkUploadTest extends TestCase
{
    use RefreshDatabase;

    private User $superadmin;
    private User $userA;
    private User $userB;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->superadmin = User::factory()->superadmin()->create();
        $this->userA = User::factory()->create(['name' => 'Surveyor A']);
        $this->userB = User::factory()->create(['name' => 'Surveyor B']);
    }

    protected function tearDown(): void
    {
        foreach (['app/chunks', 'app/temp_merged'] as $dir) {
            $path = storage_path($dir);
            if (File::isDirectory($path)) File::deleteDirectory($path);
        }
        parent::tearDown();
    }

    private function makeJpegContent(int $size = 4096): string
    {
        $img = imagecreatetruecolor(1, 1);
        ob_start();
        imagejpeg($img, null, 100);
        $jpeg = ob_get_clean();
        imagedestroy($img);
        if (strlen($jpeg) < $size) $jpeg .= str_repeat("\x00", $size - strlen($jpeg));
        return $jpeg;
    }

    private function uploadInChunks(User $user, string $content, string $name = 'photo.jpg', int $chunk = 2048, ?int $sid = null): array
    {
        $uid = fake()->uuid();
        $total = (int) ceil(strlen($content) / $chunk);
        for ($i = 0; $i < $total; $i++) {
            $data = substr($content, $i * $chunk, $chunk);
            $file = UploadedFile::fake()->createWithContent("c_{$i}", $data);
            $p = ['upload_id' => $uid, 'chunk_index' => $i, 'total_chunks' => $total, 'original_filename' => $name, 'chunk' => $file];
            if ($sid) $p['survey_id'] = $sid;
            $this->actingAs($user)->post(route('surveys.photos.chunk'), $p)->assertOk();
        }
        $cp = ['upload_id' => $uid, 'total_chunks' => $total, 'original_filename' => $name];
        if ($sid) $cp['survey_id'] = $sid;
        $res = $this->actingAs($user)->postJson(route('surveys.photos.complete'), $cp);
        return ['response' => $res, 'upload_id' => $uid];
    }

    public function test_chunk_status_empty_for_new(): void
    {
        $this->actingAs($this->userA)
            ->getJson(route('surveys.photos.chunk-status', ['upload_id' => fake()->uuid()]))
            ->assertOk()->assertJsonPath('uploaded_chunks', []);
    }

    public function test_chunk_status_rejects_invalid_uuid(): void
    {
        $this->actingAs($this->userA)
            ->getJson(route('surveys.photos.chunk-status', ['upload_id' => 'bad!']))
            ->assertStatus(422);
    }

    public function test_guest_blocked(): void
    {
        $this->getJson(route('surveys.photos.chunk-status', ['upload_id' => fake()->uuid()]))
            ->assertUnauthorized();
    }

    public function test_upload_chunk_stores_file(): void
    {
        $uid = fake()->uuid();
        $this->actingAs($this->userA)->post(route('surveys.photos.chunk'), [
            'upload_id' => $uid, 'chunk_index' => 0, 'total_chunks' => 1,
            'original_filename' => 'test.jpg',
            'chunk' => UploadedFile::fake()->create('c0', 100),
        ])->assertOk()->assertJsonPath('chunk_index', 0);
        $this->assertFileExists(storage_path("app/chunks/{$this->userA->id}/{$uid}/chunk_0"));
    }

    public function test_resume_shows_uploaded_chunks(): void
    {
        $uid = fake()->uuid();
        for ($i = 0; $i < 2; $i++) {
            $this->actingAs($this->userA)->post(route('surveys.photos.chunk'), [
                'upload_id' => $uid, 'chunk_index' => $i, 'total_chunks' => 3,
                'original_filename' => 'test.jpg',
                'chunk' => UploadedFile::fake()->create("c{$i}", 50),
            ])->assertOk();
        }
        $this->actingAs($this->userA)
            ->getJson(route('surveys.photos.chunk-status', ['upload_id' => $uid]))
            ->assertOk()->assertJsonPath('uploaded_chunks', [0, 1]);
    }

    public function test_complete_creates_temp_photo(): void
    {
        $r = $this->uploadInChunks($this->userA, $this->makeJpegContent(), 'loc.jpg');
        $r['response']->assertOk()->assertJsonPath('type', 'temporary');
        $this->assertDatabaseHas('survey_temp_photos', [
            'user_id' => $this->userA->id, 'upload_id' => $r['upload_id'],
        ]);
    }

    public function test_complete_creates_permanent_photo(): void
    {
        $s = SurveyLocation::factory()->create(['user_id' => $this->userA->id]);
        $r = $this->uploadInChunks($this->userA, $this->makeJpegContent(), 'pic.jpg', 2048, $s->id);
        $r['response']->assertOk()->assertJsonPath('type', 'permanent');
        $this->assertDatabaseHas('survey_photos', [
            'survey_location_id' => $s->id, 'user_id' => $this->userA->id,
        ]);
    }

    public function test_complete_rejects_non_image(): void
    {
        $uid = fake()->uuid();
        $this->actingAs($this->userA)->post(route('surveys.photos.chunk'), [
            'upload_id' => $uid, 'chunk_index' => 0, 'total_chunks' => 1,
            'original_filename' => 'bad.php',
            'chunk' => UploadedFile::fake()->createWithContent('c0', str_repeat('x', 500)),
        ])->assertOk();

        $this->actingAs($this->userA)->postJson(route('surveys.photos.complete'), [
            'upload_id' => $uid, 'total_chunks' => 1, 'original_filename' => 'bad.php',
        ])->assertStatus(422);

        $this->assertDatabaseMissing('survey_temp_photos', ['upload_id' => $uid]);
    }

    public function test_cancel_removes_temp(): void
    {
        $r = $this->uploadInChunks($this->userA, $this->makeJpegContent(), 'cancel.jpg');
        $this->assertDatabaseHas('survey_temp_photos', ['upload_id' => $r['upload_id']]);
        $this->actingAs($this->userA)
            ->deleteJson(route('surveys.photos.cancel', $r['upload_id']))
            ->assertOk()->assertJsonPath('status', 'deleted');
        $this->assertDatabaseMissing('survey_temp_photos', ['upload_id' => $r['upload_id']]);
    }

    public function test_owner_deletes_own_photo(): void
    {
        $s = SurveyLocation::factory()->create(['user_id' => $this->userA->id]);
        $r = $this->uploadInChunks($this->userA, $this->makeJpegContent(), 'del.jpg', 2048, $s->id);
        $pid = $r['response']->json('photo.id');
        $this->actingAs($this->userA)
            ->deleteJson(route('surveys.photos.destroy', [$s->id, $pid]))
            ->assertOk();
        $this->assertDatabaseMissing('survey_photos', ['id' => $pid]);
    }

    public function test_superadmin_deletes_any_photo(): void
    {
        $s = SurveyLocation::factory()->create(['user_id' => $this->userA->id]);
        $r = $this->uploadInChunks($this->userA, $this->makeJpegContent(), 'a.jpg', 2048, $s->id);
        $pid = $r['response']->json('photo.id');
        $this->actingAs($this->superadmin)
            ->deleteJson(route('surveys.photos.destroy', [$s->id, $pid]))
            ->assertOk();
        $this->assertDatabaseMissing('survey_photos', ['id' => $pid]);
    }

    public function test_user_cannot_upload_to_others_survey(): void
    {
        $sB = SurveyLocation::factory()->create(['user_id' => $this->userB->id]);
        $this->actingAs($this->userA)->post(route('surveys.photos.chunk'), [
            'upload_id' => fake()->uuid(), 'chunk_index' => 0, 'total_chunks' => 1,
            'original_filename' => 'hack.jpg', 'survey_id' => $sB->id,
            'chunk' => UploadedFile::fake()->create('c0', 100),
        ])->assertForbidden();
    }

    public function test_user_cannot_delete_others_photo(): void
    {
        $sB = SurveyLocation::factory()->create(['user_id' => $this->userB->id]);
        $r = $this->uploadInChunks($this->userB, $this->makeJpegContent(), 'b.jpg', 2048, $sB->id);
        $pid = $r['response']->json('photo.id');
        $this->actingAs($this->userA)
            ->deleteJson(route('surveys.photos.destroy', [$sB->id, $pid]))
            ->assertForbidden();
        $this->assertDatabaseHas('survey_photos', ['id' => $pid]);
    }

    public function test_store_survey_attaches_temp_photos(): void
    {
        $jpeg = $this->makeJpegContent();
        $r1 = $this->uploadInChunks($this->userA, $jpeg, 'p1.jpg');
        $r2 = $this->uploadInChunks($this->userA, $jpeg, 'p2.jpg');
        $this->assertDatabaseHas('survey_temp_photos', ['upload_id' => $r1['upload_id']]);
        $this->assertDatabaseHas('survey_temp_photos', ['upload_id' => $r2['upload_id']]);

        $this->actingAs($this->userA)->post(route('surveys.store'), [
            'latitude' => -3.32, 'longitude' => 114.59, 'alamat' => 'With Photos',
            'kecamatan' => 'Banjarmasin Tengah', 'kelurahan' => 'Kertak Baru Ilir',
            'jumlah_cctv' => 2, 'temp_photos' => [$r1['upload_id'], $r2['upload_id']],
        ])->assertRedirect(route('surveys.index'));

        $survey = SurveyLocation::where('alamat', 'With Photos')->first();
        $this->assertNotNull($survey);
        $this->assertEquals(2, $survey->photos()->count());
        $this->assertDatabaseMissing('survey_temp_photos', ['upload_id' => $r1['upload_id']]);
        $this->assertDatabaseMissing('survey_temp_photos', ['upload_id' => $r2['upload_id']]);
    }
}

