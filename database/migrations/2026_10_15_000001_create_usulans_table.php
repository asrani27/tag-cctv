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
        Schema::create('usulans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // 1. Koordinat Lokasi Peta
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            // 2. Kelurahan
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();

            // 3. RT, Nama Ketua RT, No HP
            $table->string('rt', 20)->nullable();
            $table->string('nama_ketua_rt')->nullable();
            $table->string('no_hp', 30)->nullable();

            // 4. Nama Lokasi / Titik Usulan (Gang/Jalan/Fasum)
            $table->string('nama_lokasi')->nullable();

            // 5. Kategori Titik (Persimpangan/ Pintu Masuk RT/ Rawan Kriminalitas/ Fasos-Fasum)
            $table->string('kategori_titik')->nullable();

            // 6. Infrastruktur Penyangga (Tiang PLN / Tiang Telkom / Bangunan Posyandu/Poskamling / Belum Ada)
            $table->string('infrastruktur_penyangga')->nullable();

            // 7. Sumber Daya Listrik (Ada/Tidak)
            $table->string('sumber_daya_listrik')->nullable();

            // 8. Ketersediaan CCTV Eksisting (Sudah Ada/Belum)
            $table->string('cctv_eksisting')->nullable();

            // 9. Keterangan
            $table->text('keterangan')->nullable();

            // Status Usulan
            $table->string('status', 50)->default('Diajukan');

            $table->timestamps();

            // Indexes
            $table->index('kelurahan');
            $table->index('user_id');
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usulans');
    }
};
