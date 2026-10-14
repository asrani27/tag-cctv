<?php

use App\Models\SurveyLocation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kelurahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['kecamatan_id', 'name']);
        });

        // Create view 'kelurahan' for singular access compatibility
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('CREATE OR REPLACE VIEW kelurahan AS SELECT * FROM kelurahans');
        } else {
            DB::statement('CREATE VIEW IF NOT EXISTS kelurahan AS SELECT * FROM kelurahans');
        }

        // Seed initial data from SurveyLocation::KELURAHAN_BY_KECAMATAN
        $now = now();
        foreach (SurveyLocation::KELURAHAN_BY_KECAMATAN as $kecamatanName => $kelurahanList) {
            $existing = DB::table('kecamatans')->where('name', $kecamatanName)->first();
            $kecamatanId = $existing
                ? $existing->id
                : DB::table('kecamatans')->insertGetId([
                    'name' => $kecamatanName,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

            $kelurahanRows = [];
            foreach ($kelurahanList as $kelurahanName) {
                $kelurahanRows[] = [
                    'kecamatan_id' => $kecamatanId,
                    'name' => $kelurahanName,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (! empty($kelurahanRows)) {
                DB::table('kelurahans')->insert($kelurahanRows);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS kelurahan');
        Schema::dropIfExists('kelurahans');
    }
};
