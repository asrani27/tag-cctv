<!-- Tujuan Layanan -->
<x-card title="Tujuan Layanan" subtitle="Peruntukan operasional titik survey">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-3 rounded-lg border {{ $survey->keamanan ? 'bg-emerald-50/70 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full {{ $survey->keamanan ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                <span class="text-xs font-semibold">Keamanan</span>
            </div>
            <p class="text-[11px] mt-1 {{ $survey->keamanan ? 'text-emerald-600' : 'text-slate-400' }}">{{ $survey->keamanan ? 'Aktif' : 'Tidak' }}</p>
        </div>

        <div class="p-3 rounded-lg border {{ $survey->akses_wifi ? 'bg-emerald-50/70 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full {{ $survey->akses_wifi ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                <span class="text-xs font-semibold">Akses WiFi</span>
            </div>
            <p class="text-[11px] mt-1 {{ $survey->akses_wifi ? 'text-emerald-600' : 'text-slate-400' }}">{{ $survey->akses_wifi ? 'Aktif' : 'Tidak' }}</p>
        </div>

        <div class="p-3 rounded-lg border {{ $survey->lalin ? 'bg-emerald-50/70 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full {{ $survey->lalin ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                <span class="text-xs font-semibold">Lalu Lintas</span>
            </div>
            <p class="text-[11px] mt-1 {{ $survey->lalin ? 'text-emerald-600' : 'text-slate-400' }}">{{ $survey->lalin ? 'Aktif' : 'Tidak' }}</p>
        </div>

        <div class="p-3 rounded-lg border {{ $survey->panic_sensor ? 'bg-emerald-50/70 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-200 text-slate-400' }}">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full {{ $survey->panic_sensor ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                <span class="text-xs font-semibold">Panic Sensor</span>
            </div>
            <p class="text-[11px] mt-1 {{ $survey->panic_sensor ? 'text-emerald-600' : 'text-slate-400' }}">{{ $survey->panic_sensor ? 'Aktif' : 'Tidak' }}</p>
        </div>
    </div>
</x-card>

<!-- Infrastruktur CCTV / WiFi -->
<x-card title="Infrastruktur CCTV / WiFi" subtitle="Perangkat keras dan spesifikasi kapasitas">
    <dl class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-xs">
        <div class="p-3 bg-slate-50 rounded-lg">
            <dt class="text-slate-400 font-medium">Jumlah CCTV</dt>
            <dd class="text-base font-bold text-slate-900 mt-1">{{ $survey->jumlah_cctv ?? 0 }} Unit</dd>
        </div>
        <div class="p-3 bg-slate-50 rounded-lg">
            <dt class="text-slate-400 font-medium">Jumlah AP</dt>
            <dd class="text-base font-bold text-slate-900 mt-1">{{ $survey->jumlah_ap ?? 0 }} Unit</dd>
        </div>
        <div class="p-3 bg-slate-50 rounded-lg">
            <dt class="text-slate-400 font-medium">Bandwidth</dt>
            <dd class="text-sm font-semibold text-slate-900 mt-1">{{ $survey->bandwidth ?? '-' }}</dd>
        </div>
        <div class="p-3 bg-slate-50 rounded-lg">
            <dt class="text-slate-400 font-medium">Backhaul</dt>
            <dd class="text-sm font-semibold text-slate-900 mt-1">{{ $survey->backhaul ?? '-' }}</dd>
        </div>
        <div class="p-3 bg-slate-50 rounded-lg">
            <dt class="text-slate-400 font-medium">Storage</dt>
            <dd class="text-sm font-semibold text-slate-900 mt-1">{{ $survey->storage ?? '-' }}</dd>
        </div>
    </dl>
</x-card>

<!-- Kondisi Kamera -->
<x-card title="Kondisi Kamera & Fisik Lokasi" subtitle="Struktur tiang dan visibilitas pantauan">
    <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div><dt class="text-slate-400">Jenis Jalan</dt><dd class="font-medium text-slate-800 mt-0.5">{{ $survey->jenis_jalan ?? '-' }}</dd></div>
        <div><dt class="text-slate-400">Tiang</dt><dd class="font-medium text-slate-800 mt-0.5">{{ $survey->tiang ?? '-' }}</dd></div>
        <div><dt class="text-slate-400">Jenis Tiang</dt><dd class="font-medium text-slate-800 mt-0.5">{{ $survey->jenis_tiang ?? '-' }}</dd></div>
        <div><dt class="text-slate-400">Tembok / Struktur</dt><dd class="font-medium text-slate-800 mt-0.5">{{ $survey->tembok ?? '-' }}</dd></div>
        <div><dt class="text-slate-400">Arah Pantau</dt><dd class="font-medium text-slate-800 mt-0.5">{{ $survey->arah_pantau ?? '-' }}</dd></div>
        <div><dt class="text-slate-400">Jarak Pantau</dt><dd class="font-medium text-slate-800 mt-0.5">{{ $survey->jarak_pantau ?? '-' }}</dd></div>
        <div><dt class="text-slate-400">Pencahayaan</dt><dd class="font-medium text-slate-800 mt-0.5">{{ $survey->pencahayaan_malam ?? '-' }}</dd></div>
        <div><dt class="text-slate-400">Potensi Silau</dt><dd class="font-medium text-slate-800 mt-0.5">{{ $survey->potensi_silau ?? '-' }}</dd></div>
        <div><dt class="text-slate-400">Penghalang</dt><dd class="font-medium text-slate-800 mt-0.5">{{ $survey->penghalang ?? '-' }}</dd></div>
        <div><dt class="text-slate-400">Vandalisme</dt><dd class="font-medium text-slate-800 mt-0.5">{{ $survey->potensi_vandalisme ?? '-' }}</dd></div>
    </dl>
</x-card>
