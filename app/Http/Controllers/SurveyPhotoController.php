<?php

namespace App\Http\Controllers;

use App\Models\SurveyLocation;
use App\Models\SurveyPhoto;
use App\Models\SurveyTempPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SurveyPhotoController extends Controller
{
    /**
     * Check uploaded chunks for a given upload_id (Supports resumable upload).
     */
    public function chunkStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'upload_id' => ['required', 'string', 'regex:/^[0-9a-fA-F-]{36}$/'],
        ]);

        $userId = $request->user()->id;
        $uploadId = $validated['upload_id'];
        $chunkDir = storage_path("app/chunks/{$userId}/{$uploadId}");

        $uploadedChunks = [];
        if (File::isDirectory($chunkDir)) {
            $files = File::files($chunkDir);
            foreach ($files as $file) {
                if (preg_match('/^chunk_(\d+)$/', $file->getFilename(), $matches)) {
                    $uploadedChunks[] = (int) $matches[1];
                }
            }
            sort($uploadedChunks);
        }

        return response()->json([
            'upload_id' => $uploadId,
            'uploaded_chunks' => $uploadedChunks,
        ]);
    }

    /**
     * Handle incoming binary chunk upload via multipart/form-data.
     */
    public function uploadChunk(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'upload_id' => ['required', 'string', 'regex:/^[0-9a-fA-F-]{36}$/'],
            'chunk_index' => ['required', 'integer', 'min:0'],
            'total_chunks' => ['required', 'integer', 'min:1'],
            'original_filename' => ['required', 'string', 'max:255'],
            'survey_id' => ['nullable', 'integer', 'exists:survey_locations,id'],
            'chunk' => ['required', 'file', 'max:10240'],
        ]);

        if (! empty($validated['survey_id'])) {
            $survey = SurveyLocation::findOrFail($validated['survey_id']);
            Gate::authorize('update', $survey);
        }

        $userId = $request->user()->id;
        $uploadId = $validated['upload_id'];
        $chunkIndex = (int) $validated['chunk_index'];

        $chunkDir = storage_path("app/chunks/{$userId}/{$uploadId}");
        if (! File::isDirectory($chunkDir)) {
            File::makeDirectory($chunkDir, 0755, true);
        }

        $chunkFile = $request->file('chunk');
        $chunkFile->move($chunkDir, "chunk_{$chunkIndex}");

        return response()->json([
            'status' => 'success',
            'upload_id' => $uploadId,
            'chunk_index' => $chunkIndex,
        ]);
    }

    /**
     * Combine all chunks, validate image integrity & security, and store final photo.
     */
    public function completeUpload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'upload_id' => ['required', 'string', 'regex:/^[0-9a-fA-F-]{36}$/'],
            'total_chunks' => ['required', 'integer', 'min:1'],
            'original_filename' => ['required', 'string', 'max:255'],
            'survey_id' => ['nullable', 'integer', 'exists:survey_locations,id'],
        ]);

        $userId = $request->user()->id;
        $uploadId = $validated['upload_id'];
        $totalChunks = (int) $validated['total_chunks'];
        $cleanOriginalName = basename(strip_tags($validated['original_filename']));

        $survey = null;
        if (! empty($validated['survey_id'])) {
            $survey = SurveyLocation::findOrFail($validated['survey_id']);
            Gate::authorize('update', $survey);
        }

        $chunkDir = storage_path("app/chunks/{$userId}/{$uploadId}");

        if (! File::isDirectory($chunkDir)) {
            return response()->json([
                'message' => 'Direktori chunk tidak ditemukan atau sesi upload telah kadaluarsa.',
            ], 422);
        }

        for ($i = 0; $i < $totalChunks; $i++) {
            if (! File::exists("{$chunkDir}/chunk_{$i}")) {
                return response()->json([
                    'message' => "Chunk ke-{$i} tidak ditemukan. Upload belum lengkap.",
                ], 422);
            }
        }

        $mergedTempDir = storage_path('app/temp_merged');
        if (! File::isDirectory($mergedTempDir)) {
            File::makeDirectory($mergedTempDir, 0755, true);
        }

        $mergedTempPath = "{$mergedTempDir}/merged_{$uploadId}.tmp";
        $destHandle = @fopen($mergedTempPath, 'wb');

        if (! $destHandle) {
            File::deleteDirectory($chunkDir);
            return response()->json(['message' => 'Gagal membuat file sementara untuk penggabungan.'], 500);
        }

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = "{$chunkDir}/chunk_{$i}";
            $chunkHandle = @fopen($chunkPath, 'rb');
            if ($chunkHandle) {
                while (! feof($chunkHandle)) {
                    $buffer = fread($chunkHandle, 65536);
                    if ($buffer !== false) {
                        fwrite($destHandle, $buffer);
                    }
                }
                fclose($chunkHandle);
            }
        }
        fclose($destHandle);

        return $this->processAndStoreMergedPhoto($userId, $uploadId, $cleanOriginalName, $mergedTempPath, $chunkDir, $survey);
    }
    /**
     * Validate and store the merged photo permanently or temporarily.
     */
    protected function processAndStoreMergedPhoto(
        int $userId,
        string $uploadId,
        string $cleanOriginalName,
        string $mergedTempPath,
        string $chunkDir,
        ?SurveyLocation $survey
    ): JsonResponse {
        $allowedMimes = config('upload.allowed_mimes', ['image/jpeg', 'image/png', 'image/webp']);
        $maxFileSize = config('upload.max_file_size', 25 * 1024 * 1024);

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMime = finfo_file($finfo, $mergedTempPath);
        finfo_close($finfo);

        $fileSize = (int) @filesize($mergedTempPath);

        if (! in_array($detectedMime, $allowedMimes, true)) {
            @unlink($mergedTempPath);
            File::deleteDirectory($chunkDir);
            return response()->json([
                'message' => 'File harus berupa gambar dengan format JPEG, PNG, atau WebP yang valid.',
            ], 422);
        }

        if ($fileSize > $maxFileSize || $fileSize === 0) {
            @unlink($mergedTempPath);
            File::deleteDirectory($chunkDir);
            return response()->json([
                'message' => 'Ukuran file melebihi batas maksimum ' . number_format($maxFileSize / 1048576, 1) . ' MB.',
            ], 422);
        }

        $imageInfo = @getimagesize($mergedTempPath);
        if ($imageInfo === false || empty($imageInfo[0]) || empty($imageInfo[1])) {
            @unlink($mergedTempPath);
            File::deleteDirectory($chunkDir);
            return response()->json([
                'message' => 'File gambar tidak valid atau rusak.',
            ], 422);
        }

        $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $safeExt = $extMap[$detectedMime] ?? 'jpg';
        $finalFilename = Str::uuid()->toString() . '.' . $safeExt;

        File::deleteDirectory($chunkDir);
        $publicDisk = Storage::disk('public');

        if ($survey) {
            $targetDir = "surveys/{$survey->id}";
            $targetPath = "{$targetDir}/{$finalFilename}";

            if (! $publicDisk->exists($targetDir)) {
                $publicDisk->makeDirectory($targetDir);
            }

            $stream = fopen($mergedTempPath, 'r');
            $publicDisk->put($targetPath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
            @unlink($mergedTempPath);

            $maxSort = (int) $survey->photos()->max('sort_order');

            $photo = SurveyPhoto::create([
                'survey_location_id' => $survey->id,
                'user_id' => $userId,
                'file_path' => $targetPath,
                'original_name' => $cleanOriginalName,
                'mime_type' => $detectedMime,
                'file_size' => $fileSize,
                'sort_order' => $maxSort + 1,
            ]);

            return response()->json([
                'status' => 'success',
                'type' => 'permanent',
                'photo' => [
                    'id' => $photo->id,
                    'url' => $photo->url,
                    'original_name' => $photo->original_name,
                    'file_size' => $photo->file_size,
                    'formatted_size' => $photo->formatted_size,
                ],
            ]);
        }

        $tempDir = "surveys/temp/{$userId}";
        $targetPath = "{$tempDir}/{$finalFilename}";

        if (! $publicDisk->exists($tempDir)) {
            $publicDisk->makeDirectory($tempDir);
        }

        $stream = fopen($mergedTempPath, 'r');
        $publicDisk->put($targetPath, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }
        @unlink($mergedTempPath);

        $tempPhoto = SurveyTempPhoto::create([
            'user_id' => $userId,
            'upload_id' => $uploadId,
            'file_path' => $targetPath,
            'original_name' => $cleanOriginalName,
            'mime_type' => $detectedMime,
            'file_size' => $fileSize,
        ]);

        return response()->json([
            'status' => 'success',
            'type' => 'temporary',
            'temp_id' => $uploadId,
            'photo' => [
                'temp_id' => $uploadId,
                'url' => $tempPhoto->url,
                'original_name' => $tempPhoto->original_name,
                'file_size' => $tempPhoto->file_size,
            ],
        ]);
    }
    /**
     * Delete temporary upload / cancel upload in progress.
     */
    public function cancelUpload(Request $request, string $uploadId): JsonResponse
    {
        if (! preg_match('/^[0-9a-fA-F-]{36}$/', $uploadId)) {
            return response()->json(['message' => 'Format upload ID tidak valid.'], 422);
        }

        $userId = $request->user()->id;

        $chunkDir = storage_path("app/chunks/{$userId}/{$uploadId}");
        if (File::isDirectory($chunkDir)) {
            File::deleteDirectory($chunkDir);
        }

        $tempPhoto = SurveyTempPhoto::where('user_id', $userId)
            ->where('upload_id', $uploadId)
            ->first();

        if ($tempPhoto) {
            $tempPhoto->delete();
        }

        return response()->json([
            'status' => 'deleted',
            'upload_id' => $uploadId,
        ]);
    }

    /**
     * Delete a permanent survey photo.
     */
    public function destroy(Request $request, SurveyLocation $survey, SurveyPhoto $photo): JsonResponse|RedirectResponse
    {
        if ($photo->survey_location_id !== $survey->id) {
            abort(404);
        }

        Gate::authorize('delete', $photo);

        $photoName = $photo->original_name;
        $photo->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => "Foto {$photoName} berhasil dihapus.",
            ]);
        }

        return back()->with('success', "Foto {$photoName} berhasil dihapus.");
    }
}


