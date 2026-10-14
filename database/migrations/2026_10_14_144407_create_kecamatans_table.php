<?php

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
        Schema::create('kecamatans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Create view 'kecamatan' for singular access compatibility
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('CREATE OR REPLACE VIEW kecamatan AS SELECT * FROM kecamatans');
        } else {
            DB::statement('CREATE VIEW IF NOT EXISTS kecamatan AS SELECT * FROM kecamatans');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS kecamatan');
        Schema::dropIfExists('kecamatans');
    }
};
