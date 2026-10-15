<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Usulan;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsulanWifiSheet implements FromQuery, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping, WithStrictNullComparison, WithStyles, WithTitle
{
    public function __construct(
        private readonly array $filters,
        private readonly User $user,
    ) {}

    public function title(): string
    {
        return 'Usulan Titik Wifi';
    }

    public function query(): Builder
    {
        $sheetFilters = array_merge($this->filters, ['jenis' => Usulan::JENIS_WIFI]);

        return Usulan::query()
            ->with(['user'])
            ->applyFilters($sheetFilters, $this->user)
            ->latest();
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return [
            'ID Usulan',
            'Tanggal Pengajuan',
            'Status',
            'Diusulkan Oleh',
            'Email Pengusul',
            'Kecamatan',
            'Kelurahan',
            'RT',
            'Nama Ketua RT',
            'No HP / WhatsApp',
            'Latitude',
            'Longitude',
            'Fasilitas Publik',
            'Perkiraan Pengguna Potensial',
            'Akses Jaringan Terdekat',
            'Tempat Pemasangan Router/AP',
            'Ketersediaan Listrik 24 Jam',
            'Keterangan',
        ];
    }

    /** @param  Usulan  $usulan */
    public function map(mixed $usulan): array
    {
        return [
            $usulan->id,
            $usulan->created_at?->format('Y-m-d H:i:s'),
            $usulan->status ?? 'Diajukan',
            $usulan->user?->name ?? 'Data Lama',
            $usulan->user?->email ?? '-',
            $usulan->kecamatan ?? '-',
            $usulan->kelurahan ?? '-',
            $usulan->rt ?? '-',
            $usulan->nama_ketua_rt ?? '-',
            $usulan->no_hp ? (string) $usulan->no_hp : '-',
            $usulan->getRawOriginal('latitude'),
            $usulan->getRawOriginal('longitude'),
            $usulan->nama_fasilitas_publik ?? '-',
            $usulan->jumlah_pengguna_potensial ? $usulan->jumlah_pengguna_potensial.' Warga/Hari' : '-',
            $usulan->akses_jaringan_terdekat ?? '-',
            $usulan->tempat_pemasangan ?? '-',
            $usulan->ketersediaan_listrik ?? '-',
            $usulan->keterangan ?? '-',
        ];
    }

    /** @return array<string, string> */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => '#,##0.0000000',
            'L' => '#,##0.0000000',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
