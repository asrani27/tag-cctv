<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            // Jenis usulan: 'cctv' atau 'wifi'
            $table->string('jenis', 20)->default('cctv')->after('user_id')->index();

            // Kolom spesifik usulan titik Wifi
            // 4. Nama Fasilitas Publik Lokasi Pemasangan (Pos Kamling / Balai RT / Taman Warga / Posyandu)
            $table->string('nama_fasilitas_publik')->nullable()->after('cctv_eksisting');

            // 5. Perkiraan Jumlah Pengguna Potensial (Warga/Hari)
            $table->string('jumlah_pengguna_potensial')->nullable()->after('nama_fasilitas_publik');

            // 6. Akses Jaringan Kabel/Tiang Terdekat
            $table->string('akses_jaringan_terdekat')->nullable()->after('jumlah_pengguna_potensial');

            // 7. Tempat Pemasangan Perangkat Router/AP (Indoor / Outdoor Terlindung)
            $table->string('tempat_pemasangan')->nullable()->after('akses_jaringan_terdekat');

            // 8. Ketersediaan Listrik 24 Jam (Tersedia / Perlu Tarikan Baru)
            $table->string('ketersediaan_listrik')->nullable()->after('tempat_pemasangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usulans', function (Blueprint $table) {
            $table->dropIndex(['jenis']);
            $table->dropColumn([
                'jenis',
                'nama_fasilitas_publik',
                'jumlah_pengguna_potensial',
                'akses_jaringan_terdekat',
                'tempat_pemasangan',
                'ketersediaan_listrik',
            ]);
        });
    }
};
