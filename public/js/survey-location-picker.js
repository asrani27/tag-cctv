(function () {
    'use strict';

    function initSurveyLocationPicker() {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const btnGetLocation = document.getElementById('btn-get-location');
        const btnClearLocation = document.getElementById('btn-clear-location');
        const btnText = document.getElementById('btn-location-text');
        const btnIcon = document.getElementById('btn-location-icon');
        const btnSpinner = document.getElementById('btn-location-spinner');

        const alertBox = document.getElementById('location-feedback-alert');
        const alertIcon = document.getElementById('feedback-alert-icon');
        const alertTitle = document.getElementById('feedback-alert-title');
        const alertDesc = document.getElementById('feedback-alert-desc');
        const btnDismiss = document.getElementById('btn-dismiss-feedback');

        const coordsBadge = document.getElementById('coords-display-text');
        const coordsDot = document.getElementById('coords-status-dot');
        const mapContainer = document.getElementById('survey-form-map');

        if (!latInput || !lngInput || !btnGetLocation) {
            return;
        }

        const BANJARMASIN_LAT = -3.3194;
        const BANJARMASIN_LNG = 114.5908;
        const DEFAULT_ZOOM = 13;
        const LOCATED_ZOOM = 17;

        let map = null;
        let marker = null;

        function parseCoord(val) {
            if (val === null || val === undefined) return NaN;
            const cleaned = String(val).trim().replace(',', '.');
            return cleaned === '' ? NaN : parseFloat(cleaned);
        }

        function showAlert(type, title, desc) {
            if (!alertBox) return;
            alertBox.classList.remove('hidden', 'bg-emerald-50', 'border-emerald-200', 'text-emerald-900', 'bg-amber-50', 'border-amber-200', 'text-amber-900', 'bg-rose-50', 'border-rose-200', 'text-rose-900');

            if (type === 'success') {
                alertBox.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-900');
                if (alertIcon) alertIcon.textContent = '✓';
            } else if (type === 'warning') {
                alertBox.classList.add('bg-amber-50', 'border-amber-200', 'text-amber-900');
                if (alertIcon) alertIcon.textContent = '⚠️';
            } else {
                alertBox.classList.add('bg-rose-50', 'border-rose-200', 'text-rose-900');
                if (alertIcon) alertIcon.textContent = '⚠️';
            }

            if (alertTitle) alertTitle.textContent = title;
            if (alertDesc) alertDesc.textContent = desc;
        }

        function hideAlert() {
            if (alertBox) alertBox.classList.add('hidden');
        }

        if (btnDismiss) {
            btnDismiss.addEventListener('click', hideAlert);
        }

        function updateBadge(lat, lng) {
            if (!coordsBadge || !coordsDot) return;
            if (lat !== null && lng !== null && !isNaN(lat) && !isNaN(lng)) {
                coordsBadge.textContent = lat.toFixed(7) + ', ' + lng.toFixed(7);
                coordsDot.className = 'w-2 h-2 rounded-full bg-emerald-500';
                if (btnClearLocation) btnClearLocation.classList.remove('hidden');
            } else {
                coordsBadge.textContent = 'Belum ada titik dipilih';
                coordsDot.className = 'w-2 h-2 rounded-full bg-slate-300';
                if (btnClearLocation) btnClearLocation.classList.add('hidden');
            }
        }

        function attachMarkerDrag(m) {
            m.on('dragend', function (e) {
                const pos = e.target.getLatLng();
                const latStr = pos.lat.toFixed(7);
                const lngStr = pos.lng.toFixed(7);
                latInput.value = latStr;
                lngInput.value = lngStr;
                latInput.dispatchEvent(new Event('change', { bubbles: true }));
                lngInput.dispatchEvent(new Event('change', { bubbles: true }));
                updateBadge(parseFloat(latStr), parseFloat(lngStr));
            });
        }

        function setMarker(lat, lng) {
            if (!map) return;
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                attachMarkerDrag(marker);
            }
            updateBadge(lat, lng);
        }

        if (mapContainer && typeof L !== 'undefined') {
            if (L.Icon && L.Icon.Default) {
                delete L.Icon.Default.prototype._getIconUrl;
                L.Icon.Default.mergeOptions({
                    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                });
            }

            const initialLat = parseCoord(latInput.value);
            const initialLng = parseCoord(lngInput.value);
            const hasInitial = !isNaN(initialLat) && !isNaN(initialLng) &&
                initialLat >= -90 && initialLat <= 90 && initialLng >= -180 && initialLng <= 180;

            const startLat = hasInitial ? initialLat : BANJARMASIN_LAT;
            const startLng = hasInitial ? initialLng : BANJARMASIN_LNG;
            const startZoom = hasInitial ? LOCATED_ZOOM : DEFAULT_ZOOM;

            map = L.map('survey-form-map', { scrollWheelZoom: true }).setView([startLat, startLng], startZoom);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a>'
            }).addTo(map);

            if (hasInitial) {
                setMarker(initialLat, initialLng);
            }

            map.on('click', function (e) {
                const latStr = e.latlng.lat.toFixed(7);
                const lngStr = e.latlng.lng.toFixed(7);
                latInput.value = latStr;
                lngInput.value = lngStr;
                latInput.dispatchEvent(new Event('change', { bubbles: true }));
                lngInput.dispatchEvent(new Event('change', { bubbles: true }));
                setMarker(parseFloat(latStr), parseFloat(lngStr));
            });

            function syncFromInputs() {
                const lat = parseCoord(latInput.value);
                const lng = parseCoord(lngInput.value);
                if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                    setMarker(lat, lng);
                    map.panTo([lat, lng]);
                } else if (String(latInput.value).trim() === '' && String(lngInput.value).trim() === '') {
                    if (marker) {
                        map.removeLayer(marker);
                        marker = null;
                    }
                    updateBadge(null, null);
                }
            }

            latInput.addEventListener('input', syncFromInputs);
            lngInput.addEventListener('input', syncFromInputs);
            latInput.addEventListener('change', syncFromInputs);
            lngInput.addEventListener('change', syncFromInputs);

            if (btnClearLocation) {
                btnClearLocation.addEventListener('click', function () {
                    latInput.value = '';
                    lngInput.value = '';
                    latInput.dispatchEvent(new Event('change', { bubbles: true }));
                    lngInput.dispatchEvent(new Event('change', { bubbles: true }));
                    if (marker) {
                        map.removeLayer(marker);
                        marker = null;
                    }
                    updateBadge(null, null);
                    map.setView([BANJARMASIN_LAT, BANJARMASIN_LNG], DEFAULT_ZOOM);
                    hideAlert();
                });
            }

            window.addEventListener('resize', function () {
                if (map) map.invalidateSize();
            });
            setTimeout(function () {
                if (map) map.invalidateSize();
            }, 300);
        }

        btnGetLocation.addEventListener('click', function () {
            hideAlert();

            if (!('geolocation' in navigator)) {
                showAlert(
                    'danger',
                    'Browser Tidak Mendukung Geolocation',
                    'Peramban web yang Anda gunakan tidak mendukung fitur pembacaan lokasi otomatis. Silakan masukkan koordinat secara manual.'
                );
                return;
            }

            const isSecure = window.isSecureContext ||
                window.location.hostname === 'localhost' ||
                window.location.hostname === '127.0.0.1';

            if (!isSecure) {
                showAlert(
                    'warning',
                    'Koneksi HTTPS Diperlukan',
                    'Fitur geolokasi browser membutuhkan koneksi HTTPS aman (atau localhost) demi keamanan privasi. Silakan hubungkan melalui HTTPS atau masukkan koordinat secara manual.'
                );
                return;
            }

            btnGetLocation.disabled = true;
            if (btnIcon) btnIcon.classList.add('hidden');
            if (btnSpinner) btnSpinner.classList.remove('hidden');
            if (btnText) btnText.textContent = 'Mencari lokasi...';

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const rawLat = position.coords.latitude;
                    const rawLng = position.coords.longitude;
                    const accuracy = Math.round(position.coords.accuracy || 0);

                    const formattedLat = rawLat.toFixed(7);
                    const formattedLng = rawLng.toFixed(7);
                    const numLat = parseFloat(formattedLat);
                    const numLng = parseFloat(formattedLng);

                    latInput.value = formattedLat;
                    lngInput.value = formattedLng;

                    latInput.dispatchEvent(new Event('change', { bubbles: true }));
                    lngInput.dispatchEvent(new Event('change', { bubbles: true }));

                    if (map) {
                        map.setView([numLat, numLng], LOCATED_ZOOM);
                        setMarker(numLat, numLng);
                    }

                    const accText = accuracy > 0 ? ' (Akurasi: ±' + accuracy + ' meter)' : '';
                    showAlert(
                        'success',
                        '✓ Lokasi Berhasil Ditemukan',
                        'Latitude: ' + formattedLat + ', Longitude: ' + formattedLng + '.' + accText + ' Anda dapat menyesuaikan titik dengan menggeser penanda pada peta.'
                    );

                    btnGetLocation.disabled = false;
                    if (btnSpinner) btnSpinner.classList.add('hidden');
                    if (btnIcon) btnIcon.classList.remove('hidden');
                    if (btnText) btnText.textContent = 'Gunakan Lokasi Saya';
                },
                function (error) {
                    let title = 'Gagal Mengambil Lokasi';
                    let desc = '';

                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            title = 'Akses Lokasi Ditolak';
                            desc = 'Izin akses lokasi ditolak oleh pengguna atau pengaturan browser. Silakan izinkan akses lokasi pada browser Anda.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            title = 'Lokasi Tidak Tersedia';
                            desc = 'Informasi lokasi perangkat tidak dapat diperoleh. Pastikan sinyal GPS atau koneksi jaringan Anda aktif.';
                            break;
                        case error.TIMEOUT:
                            title = 'Batas Waktu Habis';
                            desc = 'Waktu permintaan lokasi habis (timeout). Silakan periksa koneksi Anda dan coba kembali.';
                            break;
                        default:
                            desc = error.message || 'Terjadi kesalahan saat mengambil lokasi dari perangkat Anda.';
                            break;
                    }

                    showAlert('danger', title, desc);

                    btnGetLocation.disabled = false;
                    if (btnSpinner) btnSpinner.classList.add('hidden');
                    if (btnIcon) btnIcon.classList.remove('hidden');
                    if (btnText) btnText.textContent = 'Gunakan Lokasi Saya';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSurveyLocationPicker);
    } else {
        initSurveyLocationPicker();
    }
})();
