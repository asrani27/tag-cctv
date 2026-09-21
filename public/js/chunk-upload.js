/**
 * Alpine.js Chunked Photo Upload Component
 *
 * Supports: camera capture (environment), gallery pick, chunk slicing,
 * resume check, progress bars, cancel, and remove.
 */
(function () {
    'use strict';

    function initChunkUploader() {
        if (typeof Alpine === 'undefined') return;

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
            uploadStatus: 'idle', // 'idle' | 'uploading' | 'success' | 'error'
            lastErrorMessage: '',

            uuid() {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
                    const r = Math.random() * 16 | 0;
                    return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
                });
            },

            formatSize(bytes) {
                if (!bytes) return '0 B';
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
                        id: uploadId,
                        file,
                        name: file.name,
                        size: file.size,
                        previewUrl: URL.createObjectURL(file),
                        progress: 0,
                        status: 'pending',
                        errorText: '',
                        controller: null,
                        tempId: null,
                        resultUrl: null,
                        resultPhotoId: null,
                    };
                    this.queue.push(item);
                    total++;

                    this.uploadStatus = 'uploading';
                    this.lastErrorMessage = '';

                    // Retrieve the reactive item from this.queue so Alpine tracks mutations
                    const reactiveItem = this.queue.find(q => q.id === uploadId) || item;
                    this.startUpload(reactiveItem);
                }
            },

            uploadChunkXhr(formData, qItem, startBytes, fileSize, signal) {
                return new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', this.urlChunk);
                    xhr.setRequestHeader('X-CSRF-TOKEN', this.csrfToken);
                    xhr.setRequestHeader('Accept', 'application/json');

                    let aborted = false;
                    const onAbort = () => {
                        aborted = true;
                        xhr.abort();
                        reject(new DOMException('Aborted', 'AbortError'));
                    };

                    if (signal) {
                        if (signal.aborted) {
                            return onAbort();
                        }
                        signal.addEventListener('abort', onAbort, { once: true });
                    }

                    xhr.upload.onprogress = (event) => {
                        if (event.lengthComputable && qItem.status === 'uploading') {
                            const bytesLoaded = startBytes + event.loaded;
                            const percent = Math.min(90, Math.round((bytesLoaded / fileSize) * 90));
                            if (percent > qItem.progress) {
                                qItem.progress = percent;
                            }
                        }
                    };

                    xhr.onload = () => {
                        if (signal) {
                            signal.removeEventListener('abort', onAbort);
                        }
                        if (xhr.status >= 200 && xhr.status < 300) {
                            try {
                                const data = JSON.parse(xhr.responseText);
                                resolve(data);
                            } catch (_) {
                                resolve({});
                            }
                        } else {
                            let message = `Gagal mengupload chunk (${xhr.status})`;
                            try {
                                const data = JSON.parse(xhr.responseText);
                                if (data.message) message = data.message;
                            } catch (_) {}
                            reject(new Error(message));
                        }
                    };

                    xhr.onerror = () => {
                        if (signal) signal.removeEventListener('abort', onAbort);
                        if (!aborted) reject(new Error('Koneksi terputus saat mengupload chunk.'));
                    };

                    xhr.onabort = () => {
                        if (signal) signal.removeEventListener('abort', onAbort);
                        reject(new DOMException('Aborted', 'AbortError'));
                    };

                    xhr.send(formData);
                });
            },

        async startUpload(item) {
            const qItem = this.queue.find(q => q.id === item.id) || item;
            qItem.status = 'uploading';
            qItem.errorText = '';
            qItem.controller = new AbortController();

            this.uploadStatus = 'uploading';
            this.lastErrorMessage = '';

            const totalChunks = Math.ceil(qItem.file.size / this.chunkSize);
            let uploadedChunks = [];

            try {
                const statusRes = await fetch(
                    `${this.urlChunkStatus}?upload_id=${qItem.id}`,
                    {
                        signal: qItem.controller.signal,
                        headers: { 'Accept': 'application/json' }
                    }
                );
                if (statusRes.ok) {
                    const statusData = await statusRes.json();
                    uploadedChunks = statusData.uploaded_chunks || [];
                }
            } catch (e) {
                if (e.name === 'AbortError') return;
            }

            if (uploadedChunks.length > 0) {
                qItem.progress = Math.round((uploadedChunks.length / totalChunks) * 90);
            }

            for (let i = 0; i < totalChunks; i++) {
                if (qItem.status === 'cancelled') return;

                if (uploadedChunks.includes(i)) {
                    qItem.progress = Math.round(((i + 1) / totalChunks) * 90);
                    continue;
                }

                const start = i * this.chunkSize;
                const blob = qItem.file.slice(start, Math.min(start + this.chunkSize, qItem.file.size));
                const fd = new FormData();
                fd.append('upload_id', qItem.id);
                fd.append('chunk_index', i);
                fd.append('total_chunks', totalChunks);
                fd.append('original_filename', qItem.name);
                fd.append('chunk', blob);
                if (this.surveyId) fd.append('survey_id', this.surveyId);

                try {
                    await this.uploadChunkXhr(fd, qItem, start, qItem.file.size, qItem.controller.signal);
                    qItem.progress = Math.round(((i + 1) / totalChunks) * 90);
                } catch (e) {
                    if (e.name === 'AbortError') return;
                    qItem.status = 'error';
                    qItem.errorText = e.message || `Chunk ${i} gagal`;
                    this.lastErrorMessage = qItem.errorText;
                    this.updateUploadStatus();
                    return;
                }
            }

            qItem.progress = 95;

            try {
                const body = {
                    upload_id: qItem.id,
                    total_chunks: totalChunks,
                    original_filename: qItem.name,
                };
                if (this.surveyId) body.survey_id = this.surveyId;

                const res = await fetch(this.urlComplete, {
                    method: 'POST',
                    body: JSON.stringify(body),
                    signal: qItem.controller.signal,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                });

                if (!res.ok) {
                    const err = await res.json().catch(() => ({}));
                    throw new Error(err.message || 'Gagal menyelesaikan upload.');
                }

                const result = await res.json();
                qItem.progress = 100;
                qItem.status = 'complete';
                qItem.resultUrl = result.photo?.url || qItem.previewUrl;
                qItem.tempId = result.temp_id || null;
                qItem.resultPhotoId = result.photo?.id || null;

                this.updateUploadStatus();
            } catch (e) {
                if (e.name === 'AbortError') return;
                qItem.status = 'error';
                qItem.errorText = e.message || 'Gagal menyelesaikan upload.';
                this.lastErrorMessage = qItem.errorText;
                this.updateUploadStatus();
            }
        },

        updateUploadStatus() {
            const isStillUploading = this.queue.some(q => q.status === 'uploading' || q.status === 'pending');
            if (isStillUploading) {
                this.uploadStatus = 'uploading';
                return;
            }

            const hasErrors = this.queue.some(q => q.status === 'error');
            if (hasErrors) {
                this.uploadStatus = 'error';
                const err = this.queue.find(q => q.status === 'error' && q.errorText);
                if (err) this.lastErrorMessage = err.errorText;
                return;
            }

            const hasCompleted = this.queue.some(q => q.status === 'complete');
            if (hasCompleted) {
                this.uploadStatus = 'success';
                this.lastErrorMessage = '';
                return;
            }

            this.uploadStatus = 'idle';
            this.lastErrorMessage = '';
        },


        retryUpload(item) {
            const qItem = this.queue.find(q => q.id === item.id) || item;
            qItem.status = 'pending';
            qItem.progress = 0;
            qItem.errorText = '';
            this.updateUploadStatus();
            this.startUpload(qItem);
        },

        async cancelUpload(item) {
            const qItem = this.queue.find(q => q.id === item.id) || item;
            if (qItem.controller) qItem.controller.abort();
            qItem.status = 'cancelled';
            try {
                await fetch(`${this.urlCancel}/${qItem.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                });
            } catch (_) {}
            this.removeFromQueue(qItem.id);
        },

        async removeFromQueue(id) {
            const idx = this.queue.findIndex(q => q.id === id);
            if (idx !== -1) {
                const item = this.queue[idx];
                if (item.previewUrl) URL.revokeObjectURL(item.previewUrl);

                // If temp upload (create mode)
                if (item.status === 'complete' && item.tempId) {
                    try {
                        await fetch(`${this.urlCancel}/${item.tempId}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                        });
                    } catch (_) {}
                }

                // If permanent upload attached to survey (edit mode)
                if (item.status === 'complete' && item.resultPhotoId && this.surveyId) {
                    try {
                        await fetch(`/surveys/${this.surveyId}/photos/${item.resultPhotoId}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                        });
                    } catch (_) {}
                }

                this.queue.splice(idx, 1);
                this.updateUploadStatus();
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

        get hasError() {
            return this.queue.some(q => q.status === 'error');
        },

        get uploadState() {
            if (this.isUploading) return 'uploading';
            if (this.uploadStatus === 'success') return 'success';
            if (this.uploadStatus === 'error' || this.hasError) return 'error';
            return 'idle';
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
}

if (window.Alpine) {
    initChunkUploader();
} else {
    document.addEventListener('alpine:init', initChunkUploader);
}
})();

