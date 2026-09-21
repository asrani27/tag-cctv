<!-- Section: Foto Dokumentasi -->
<x-card title="Dokumentasi Foto" subtitle="Foto dokumentasi lokasi survey (opsional, maks. 20 foto, JPEG/PNG/WebP, maks. 25 MB/foto)">
    <div
        x-data="chunkUploader({
            surveyId: {{ isset($survey) && $survey->exists ? $survey->id : 'null' }},
            maxFileSize: {{ config('upload.max_file_size', 25 * 1024 * 1024) }},
            chunkSize: {{ config('upload.chunk_size', 2 * 1024 * 1024) }},
            urlChunkStatus: '{{ route('surveys.photos.chunk-status') }}',
            urlChunk: '{{ route('surveys.photos.chunk') }}',
            urlComplete: '{{ route('surveys.photos.complete') }}',
            urlCancel: '/surveys/photos/chunk',
            existingPhotos: {{ Js::from(
                isset($survey) && $survey->exists && $survey->relationLoaded('photos')
                    ? $survey->photos->map(fn ($p) => [
                        'id' => $p->id,
                        'url' => $p->url,
                        'original_name' => $p->original_name,
                        'file_size' => $p->file_size,
                        'formatted_size' => $p->formatted_size,
                        'deleteUrl' => route('surveys.photos.destroy', [$survey->id, $p->id]),
                    ])->values()
                    : []
            ) }},
        })"
        class="space-y-4"
    >
        <!-- Error Alert -->
        <div x-show="errorMessage" x-cloak class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700 flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <span x-text="errorMessage"></span>
            <button type="button" @click="errorMessage = ''" class="ml-auto text-rose-400 hover:text-rose-600">&times;</button>
        </div>

        <!-- Upload Zone -->
        <div class="relative border-2 border-dashed rounded-xl p-6 text-center transition-colors"
            :class="isDragging ? 'border-emerald-400 bg-emerald-50' : 'border-slate-300 bg-slate-50/50 hover:border-slate-400'"
            @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false" @drop.prevent="handleDrop($event)">
            <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-700">Ambil atau pilih foto</p>
                    <p class="text-xs text-slate-500 mt-0.5">Drag & drop, pilih dari galeri, atau ambil langsung dari kamera</p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-2">
                    <label class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-emerald-600 text-white hover:bg-emerald-700 transition cursor-pointer shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Kamera
                        <input type="file" accept="image/jpeg,image/png,image/webp" capture="environment" class="sr-only" @change="handleFiles($event)" />
                    </label>
                    <label class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-white text-slate-700 border border-slate-300 hover:bg-slate-100 transition cursor-pointer shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Pilih Foto
                        <input type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only" @change="handleFiles($event)" />
                    </label>
                </div>
            </div>
        </div>

        <!-- Upload status banners -->
        <div x-show="isUploading" x-cloak class="text-xs text-blue-700 bg-blue-50 border border-blue-200 rounded-lg p-2.5 flex items-center gap-2">
            <svg class="animate-spin w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            <span class="font-medium">Sedang mengupload foto...</span>
        </div>

        <div x-show="!isUploading && uploadStatus === 'success'" x-cloak class="text-xs text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-lg p-2.5 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="font-medium">Upload selesai. Foto berhasil diunggah.</span>
            <button type="button" @click="uploadStatus = 'idle'" class="ml-auto text-emerald-600 hover:text-emerald-800 p-0.5 rounded text-sm leading-none font-bold">&times;</button>
        </div>

        <div x-show="!isUploading && (uploadStatus === 'error' || hasError)" x-cloak class="text-xs text-rose-700 bg-rose-50 border border-rose-200 rounded-lg p-2.5 flex items-center gap-2">
            <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <span class="font-medium" x-text="lastErrorMessage ? ('Upload gagal: ' + lastErrorMessage) : 'Sebagian atau semua foto gagal diunggah.'"></span>
            <button type="button" @click="uploadStatus = 'idle'" class="ml-auto text-rose-400 hover:text-rose-600 p-0.5 rounded text-sm leading-none font-bold">&times;</button>
        </div>

        <!-- Hidden fields for temp photo IDs -->
        <template x-for="tid in completedTempIds" :key="tid">
            <input type="hidden" name="temp_photos[]" :value="tid" />
        </template>

        @include('surveys.partials._photo_grids')
    </div>
</x-card>

@push('scripts')
    <script src="{{ asset('js/chunk-upload.js') }}"></script>
@endpush
