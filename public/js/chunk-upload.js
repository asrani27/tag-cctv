/**
 * Alpine.js Chunked Photo Upload Component
 *
 * Supports: camera capture (environment), gallery pick, chunk slicing,
 * resume check, progress bars, cancel, and remove.
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('chunkUploader', (config = {}) => ({
        surveyId: config.surveyId || null,
        maxFileSize: config.maxFileSize || 25 * 1024 * 1024,
        chunkSize: config.chunkSize || 2 * 1024 * 1024,
        allowedTypes: config.allowedTypes || ['image/jpeg', 'image/png', 'image/webp'],
        maxFiles: config.maxFiles || 20,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
        urlChunkStatus: config.urlChunkStatus || '/surveys/photos/chunk-status',
        urlChunk: config.urlChunk || '/surveys/photos/chunk',
        urlComplete: config.urlComplete || '/surveys/photos/complete',
        urlCancel: config.urlCancel || '/surveys/photos/chunk',

        photos: config.existingPhotos || [],
        queue: [],
        isDragging: false,
        errorMessage: '',

        uuid() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
                const r = Math.random() * 16 | 0;
                return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
            });
        },

        formatSize(bytes) {
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
            if (bytes >= 1024) return (bytes / 1024).toFixed(0) + ' KB';
            return bytes + ' B';
        },

        handleFiles(event) {
            const files = Array.from(event.target.files || []);
            event.target.value = '';
            this.addFiles(files);
        },

        handleDrop(event) {
            this.isDragging = false;
            const files = Array.from(event.dataTransfer.files || []);
            this.addFiles(files);
        },

        addFiles(files) {
            this.errorMessage = '';
            let total = this.photos.length + this.queue.length;

            for (const file of files) {
                if (total + 1 > this.maxFiles) {
                    this.errorMessage = `Maksimum ${this.maxFiles} foto per survey.`;
                    break;
                }
                if (!this.allowedTypes.includes(file.type)) {
                    this.errorMessage = `Format ${file.name} tidak didukung. Gunakan JPEG, PNG, atau WebP.`;
                    continue;
                }
                if (file.size > this.maxFileSize) {
                    this.errorMessage = `${file.name} melebihi batas ${this.formatSize(this.maxFileSize)}.`;
                    continue;
                }

                const uploadId = this.uuid();
                const item = {
                    id: uploadId, file, name: file.name, size: file.size,
                    previewUrl: URL.createObjectURL(file),
                    progress: 0, status: 'pending', errorText: '',
                    controller: null, tempId: null, resultUrl: null, resultPhotoId: null,
                };
                this.queue.push(item);
                total++;
                this.startUpload(item);
            }
        },

        async startUpload(item) {
            item.status = 'uploading';
            item.controller = new AbortController();
            const totalChunks = Math.ceil(item.file.size / this.chunkSize);
            let uploadedChunks = [];

            try {
                const statusRes = await fetch(
                    `${this.urlChunkStatus}?upload_id=${item.id}`,
                    { signal: item.controller.signal, headers: { 'Accept': 'application/json' } }
                );
                if (statusRes.ok) {
                    uploadedChunks = (await statusRes.json()).uploaded_chunks || [];
                }
            } catch (e) { if (e.name === 'AbortError') return; }

            for (let i = 0; i < totalChunks; i++) {
                if (item.status === 'cancelled') return;
                if (uploadedChunks.includes(i)) {
                    item.progress = Math.round(((i + 1) / totalChunks) * 90);
                    continue;
                }
                const start = i * this.chunkSize;
                const blob = item.file.slice(start, Math.min(start + this.chunkSize, item.file.size));
                const fd = new FormData();
                fd.append('upload_id', item.id);
                fd.append('chunk_index', i);
                fd.append('total_chunks', totalChunks);
                fd.append('original_filename', item.name);
                fd.append('chunk', blob);
                if (this.surveyId) fd.append('survey_id', this.surveyId);
                try {
                    const res = await fetch(this.urlChunk, {
                        method: 'POST', body: fd, signal: item.controller.signal,
                        headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                    });
                    if (!res.ok) {
                        const err = await res.json().catch(() => ({}));
                        throw new Error(err.message || `Chunk ${i} gagal`);
                    }
                    item.progress = Math.round(((i + 1) / totalChunks) * 90);
                } catch (e) {
                    if (e.name === 'AbortError') return;
                    item.status = 'error'; item.errorText = e.message; return;
                }
            }

            item.progress = 92;
            try {
                const body = { upload_id: item.id, total_chunks: totalChunks, original_filename: item.name };
                if (this.surveyId) body.survey_id = this.surveyId;
                const res = await fetch(this.urlComplete, {
                    method: 'POST', body: JSON.stringify(body), signal: item.controller.signal,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                });
                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    throw new Error(err.message || 'Gagal menyelesaikan upload.');
                }
                const result = await res.json();
                item.progress = 100; item.status = 'complete';
                item.resultUrl = result.photo?.url || null;
                item.tempId = result.temp_id || null;
                item.resultPhotoId = result.photo?.id || null;
            } catch (e) {
                if (e.name === 'AbortError') return;
                item.status = 'error'; item.errorText = e.message;
            }
        },


        retryUpload(item) {
            item.status = 'pending'; item.progress = 0; item.errorText = '';
            this.startUpload(item);
        },

        async cancelUpload(item) {
            if (item.controller) item.controller.abort();
            item.status = 'cancelled';
            try {
                await fetch(`${this.urlCancel}/${item.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                });
            } catch (_) {}
            this.removeFromQueue(item.id);
        },

        async removeFromQueue(id) {
            const idx = this.queue.findIndex(q => q.id === id);
            if (idx !== -1) {
                const item = this.queue[idx];
                if (item.previewUrl) URL.revokeObjectURL(item.previewUrl);
                if (item.status === 'complete' && item.tempId) {
                    try {
                        await fetch(`${this.urlCancel}/${item.tempId}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                        });
                    } catch (_) {}
                }
                this.queue.splice(idx, 1);
            }
        },

        async removeExistingPhoto(photo) {
            if (!confirm(`Hapus foto "${photo.original_name}"?`)) return;
            try {
                const res = await fetch(photo.deleteUrl, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                });
                if (res.ok) {
                    this.photos = this.photos.filter(p => p.id !== photo.id);
                } else {
                    const err = await res.json().catch(() => ({}));
                    this.errorMessage = err.message || 'Gagal menghapus foto.';
                }
            } catch (e) {
                this.errorMessage = 'Gagal menghapus foto: ' + e.message;
            }
        },

        get completedTempIds() {
            return this.queue.filter(q => q.status === 'complete' && q.tempId).map(q => q.tempId);
        },
        get isUploading() {
            return this.queue.some(q => q.status === 'uploading' || q.status === 'pending');
        },
        get totalPhotoCount() {
            return this.photos.length + this.queue.filter(q => q.status === 'complete').length;
        },
        progressColor(status) {
            if (status === 'complete') return 'bg-emerald-500';
            if (status === 'error') return 'bg-rose-500';
            return 'bg-blue-500';
        },
    }));
});

