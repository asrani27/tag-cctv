<!-- Existing Photos Grid (edit mode) -->
<template x-if="photos.length > 0">
    <div>
        <p class="text-xs font-semibold text-slate-600 mb-2">Foto Tersimpan (<span x-text="photos.length"></span>)</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            <template x-for="photo in photos" :key="photo.id">
                <div class="group relative rounded-lg overflow-hidden border border-slate-200 bg-white shadow-xs">
                    <img :src="photo.url" :alt="photo.original_name" class="w-full h-28 object-cover" loading="lazy" />
                    <div class="px-2 py-1.5">
                        <p class="text-[10px] text-slate-600 truncate" x-text="photo.original_name"></p>
                        <p class="text-[10px] text-slate-400" x-text="photo.formatted_size"></p>
                    </div>
                    <button type="button" @click="removeExistingPhoto(photo)"
                        class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-black/50 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition hover:bg-rose-600 cursor-pointer"
                        title="Hapus foto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>
    </div>
</template>

<!-- Upload Queue -->
<template x-if="queue.length > 0">
    <div>
        <p class="text-xs font-semibold text-slate-600 mb-2">Upload Baru (<span x-text="queue.length"></span>)</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
            <template x-for="item in queue" :key="item.id">
                <div class="relative rounded-lg overflow-hidden border bg-white shadow-xs"
                     :class="item.status === 'error' ? 'border-rose-300' : 'border-slate-200'">
                    <img :src="item.resultUrl || item.previewUrl" :alt="item.name" class="w-full h-28 object-cover" />
                    <!-- Progress Bar -->
                    <div class="absolute bottom-10 left-0 right-0 px-2" x-show="item.status === 'uploading'">
                        <div class="h-1.5 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-300"
                                 :class="progressColor(item.status)"
                                 :style="'width:' + item.progress + '%'"></div>
                        </div>
                    </div>
                    <!-- Status badge -->
                    <div class="absolute top-1.5 left-1.5">
                        <span x-show="item.status === 'complete'" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800">✓</span>
                        <span x-show="item.status === 'error'" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-rose-100 text-rose-700">✗</span>
                        <span x-show="item.status === 'uploading'" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-700" x-text="item.progress + '%'"></span>
                    </div>
                    <div class="px-2 py-1.5">
                        <p class="text-[10px] text-slate-600 truncate" x-text="item.name"></p>
                        <p class="text-[10px] text-slate-400" x-text="formatSize(item.size)"></p>
                        <p x-show="item.status === 'error'" class="text-[10px] text-rose-600 truncate mt-0.5" x-text="item.errorText"></p>
                    </div>
                    <!-- Action buttons -->
                    <div class="absolute top-1.5 right-1.5 flex gap-1">
                        <button type="button" x-show="item.status === 'error'" @click="retryUpload(item)"
                            class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center hover:bg-blue-600 cursor-pointer" title="Coba lagi">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                        <button type="button" x-show="item.status === 'uploading'" @click="cancelUpload(item)"
                            class="w-6 h-6 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-rose-600 cursor-pointer" title="Batalkan">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <button type="button" x-show="item.status === 'complete' || item.status === 'error'" @click="removeFromQueue(item.id)"
                            class="w-6 h-6 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-rose-600 cursor-pointer" title="Hapus">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
