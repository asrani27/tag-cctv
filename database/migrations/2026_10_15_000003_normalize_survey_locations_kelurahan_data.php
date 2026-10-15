<?php

use Database\Seeders\KecamatanSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Fix record ID 49: JL. KS TUBUN KOMP GAGAH LURUS GG. TENTRAM 1 (kecamatan Banjarmasin Selatan)
        // was erroneously saved with kelurahan = 'Banjarmasin Barat'.
        // It should be 'Kelayan Barat', matching adjacent ID 48.
        DB::table('survey_locations')
            ->where('id', 49)
            ->where('kelurahan', 'Banjarmasin Barat')
            ->update(['kelurahan' => 'Kelayan Barat']);

        // 2. Build canonical map from master data
        $canonicalMap = [];
        foreach (KecamatanSeeder::DATA as $kecamatanData) {
            foreach ($kecamatanData['kelurahans'] as $kel) {
                $canonicalMap[strtolower($kel['name'])] = $kel['name'];
            }
        }

        // Specific aliases / typos
        $aliasMap = [
            'benua anyar' => 'Banua Anyar',
            'kelurahan benua anyar' => 'Banua Anyar',
            'pekapuran' => 'Pekapuran Laut',
            'kelurahan pekapuran' => 'Pekapuran Laut',
            'alalak tengh' => 'Alalak Tengah',
            'kelurahan alalak tengh' => 'Alalak Tengah',
        ];

        // 3. Normalize all survey locations
        $surveys = DB::table('survey_locations')
            ->whereNotNull('kelurahan')
            ->where('kelurahan', '!=', '')
            ->select('id', 'kelurahan')
            ->get();

        foreach ($surveys as $survey) {
            $raw = trim($survey->kelurahan);
            $lower = strtolower($raw);

            if (isset($aliasMap[$lower])) {
                $standard = $aliasMap[$lower];
            } else {
                $stripped = trim((string) preg_replace('/^kelurahan\s+/i', '', $raw));
                $strippedLower = strtolower($stripped);
                $standard = $aliasMap[$strippedLower] ?? ($canonicalMap[$strippedLower] ?? $canonicalMap[$lower] ?? $stripped);
            }

            if ($survey->kelurahan !== $standard) {
                DB::table('survey_locations')
                    ->where('id', $survey->id)
                    ->update(['kelurahan' => $standard]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data normalization cannot be reversibly undone without corrupting cleaned data.
    }
};
