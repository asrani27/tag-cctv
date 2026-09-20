<?php

namespace App\Exports;

use App\Models\SurveyLocation;
use App\Models\SurveyPhoto;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SurveySummarySheet implements
    FromCollection,
    ShouldAutoSize,
    WithStyles,
    WithTitle
{
    public function __construct(
        private readonly array $filters,
        private readonly User $user,
    ) {}

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function collection(): Collection
    {
        $baseQuery = SurveyLocation::query()
            ->applyFilters($this->filters, $this->user);

        $surveyIds = (clone $baseQuery)->pluck('id');

        $totalSurvey = (clone $baseQuery)->count();
        $totalCctv = (int) (clone $baseQuery)->sum('jumlah_cctv');
        $totalAp = (int) (clone $baseQuery)->sum('jumlah_ap');
        $totalFoto = SurveyPhoto::whereIn('survey_location_id', $surveyIds)->count();

        $totalKecamatan = (clone $baseQuery)
            ->whereNotNull('kecamatan')
            ->where('kecamatan', '!=', '')
            ->distinct()
            ->count('kecamatan');

        $totalKelurahan = (clone $baseQuery)
            ->whereNotNull('kelurahan')
            ->where('kelurahan', '!=', '')
            ->distinct()
            ->count('kelurahan');

        $totalUser = (clone $baseQuery)
            ->whereNotNull('user_id')
            ->distinct()
            ->count('user_id');

        $rows = collect([
            ['Ringkasan', 'Nilai'],
            ['Total Survey', $totalSurvey],
            ['Total CCTV', $totalCctv],
            ['Total AP/WiFi', $totalAp],
            ['Total Foto', $totalFoto],
            ['Total User (Penginput)', $totalUser],
            ['Total Kecamatan', $totalKecamatan],
            ['Total Kelurahan', $totalKelurahan],
            ['', ''],
            ['Filter yang Digunakan', ''],
        ]);

        // Append active filters for audit clarity
        $filterLabels = [
            'search' => 'Pencarian',
            'kecamatan' => 'Kecamatan',
            'kelurahan' => 'Kelurahan',
            'koneksi' => 'Konektivitas',
            'user_id' => 'User ID',
            'tanggal_mulai' => 'Tanggal Mulai',
            'tanggal_akhir' => 'Tanggal Akhir',
        ];

        $hasFilter = false;
        foreach ($filterLabels as $key => $label) {
            $value = $this->filters[$key] ?? '';
            if ($value !== '' && $value !== null) {
                $rows->push([$label, $value]);
                $hasFilter = true;
            }
        }

        if (! $hasFilter) {
            $rows->push(['(Tidak ada filter)', '']);
        }

        $rows->push(['', '']);
        $rows->push(['Tanggal Export', now()->format('Y-m-d H:i:s')]);
        $rows->push(['Diexport Oleh', $this->user->name . ' (' . $this->user->email . ')']);

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
