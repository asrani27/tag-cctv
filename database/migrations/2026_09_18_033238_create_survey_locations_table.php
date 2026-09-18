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
        Schema::create('survey_locations', function (Blueprint $table) {
            // Primary Key: BIGINT UNSIGNED AUTO_INCREMENT (ID internal database, NO. TITIK tidak disimpan)
            $table->id();

            // 1. Informasi Lokasi
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt', 10)->nullable();
            $table->string('rw', 10)->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();

            // 2. Tujuan Layanan
            $table->boolean('keamanan')->nullable();
            $table->boolean('akses_wifi')->nullable();
            $table->boolean('lalin')->nullable();
            $table->boolean('panic_sensor')->nullable();

            // 3. Infrastruktur CCTV / WiFi
            $table->unsignedInteger('jumlah_cctv')->nullable();
            $table->unsignedInteger('jumlah_ap')->nullable();
            $table->string('bandwidth')->nullable();
            $table->string('backhaul')->nullable();
            $table->string('storage')->nullable();

            // 4. Fungsi Kamera / Kondisi Lokasi
            $table->string('jenis_jalan')->nullable();
            $table->string('tiang')->nullable();
            $table->string('jenis_tiang')->nullable();
            $table->string('tembok')->nullable();
            $table->string('arah_pantau')->nullable();
            $table->string('jarak_pantau')->nullable();
            $table->string('pencahayaan_malam')->nullable();
            $table->string('potensi_silau')->nullable();
            $table->string('penghalang')->nullable();
            $table->string('potensi_vandalisme')->nullable();

            // 5. Fungsi WiFi
            $table->decimal('luas_area', 10, 2)->nullable();
            $table->unsignedInteger('prakiraan_pengguna_max')->nullable();
            $table->decimal('tempat_ap_latitude', 10, 7)->nullable();
            $table->decimal('tempat_ap_longitude', 10, 7)->nullable();
            $table->string('potensi_interferensi')->nullable();

            // 6. Ketersediaan Listrik
            $table->string('sumber_listrik')->nullable();
            $table->decimal('posisi_panel_latitude', 10, 7)->nullable();
            $table->decimal('posisi_panel_longitude', 10, 7)->nullable();
            $table->decimal('daya_tersedia', 10, 2)->nullable();
            $table->string('proteksi_petir')->nullable();

            // 7. Komunikasi / Konektivitas
            $table->boolean('tersedia_fiber_optik')->nullable();
            $table->boolean('tersedia_4g_5g')->nullable();
            $table->boolean('tersedia_link_p2p')->nullable();
            $table->unsignedInteger('jumlah_provider')->nullable();

            // Audit Fields
            $table->timestamps();

            // Indexes untuk pencarian/filter
            $table->index('kecamatan');
            $table->index('kelurahan');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_locations');
    }
};
