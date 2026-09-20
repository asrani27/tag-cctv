<?php

namespace App\Exports;

use App\Models\SurveyLocation;
use App\Models\User;
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

class SurveyDataSheet implements
    FromQuery,
    ShouldAutoSize,
    WithColumnFormatting,
    WithHeadings,
    WithMapping,
    WithStrictNullComparison,
    WithStyles,
    WithTitle
{
    public function __construct(
        private readonly array $filters,
        private readonly User $user,
    ) {}

    public function title(): string
    {
        return 'Data Survey';
    }

    public function query(): Builder
    {
        return SurveyLocation::query()
            ->applyFilters($this->filters, $this->user)
            ->withCount('photos')
            ->latest();
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return [
            'ID', 'Tanggal Input', 'Diinput Oleh', 'Email',
            'Latitude', 'Longitude', 'Alamat', 'RT', 'RW', 'Kelurahan', 'Kecamatan',
            'Keamanan', 'Akses WiFi', 'Lalin', 'Panic Sensor',
            'Jumlah CCTV', 'Jumlah AP', 'Bandwidth', 'Backhaul', 'Storage',
            'Jenis Jalan', 'Tiang', 'Jenis Tiang', 'Tembok',
            'Arah Pantau', 'Jarak Pantau', 'Pencahayaan Malam', 'Potensi Silau',
            'Penghalang', 'Potensi Vandalisme',
            'Luas Area', 'Prakiraan Pengguna Max',
            'Tempat AP Latitude', 'Tempat AP Longitude', 'Potensi Interferensi',
            'Sumber Listrik', 'Posisi Panel Latitude', 'Posisi Panel Longitude',
            'Daya Tersedia', 'Proteksi Petir',
            'Tersedia Fiber Optik', 'Tersedia 4G/5G', 'Tersedia Link P2P',
            'Jumlah Provider', 'Jumlah Foto',
        ];
    }

    /** @param  SurveyLocation  $survey */
    public function map(mixed $survey): array
    {
        return [
            $survey->id,
            $survey->created_at?->format('Y-m-d H:i:s'),
            $survey->user?->name ?? 'Data Lama',
            $survey->user?->email ?? '-',
            $survey->getRawOriginal('latitude'),
            $survey->getRawOriginal('longitude'),
            $survey->alamat,
            $survey->rt,
            $survey->rw,
            $survey->kelurahan,
            $survey->kecamatan,
            $this->boolLabel($survey->keamanan),
            $this->boolLabel($survey->akses_wifi),
            $this->boolLabel($survey->lalin),
            $this->boolLabel($survey->panic_sensor),
            $survey->jumlah_cctv,
            $survey->jumlah_ap,
            $survey->bandwidth,
            $survey->backhaul,
            $survey->storage,
            $survey->jenis_jalan,
            $survey->tiang,
            $survey->jenis_tiang,
            $survey->tembok,
            $survey->arah_pantau,
            $survey->jarak_pantau,
            $survey->pencahayaan_malam,
            $survey->potensi_silau,
            $survey->penghalang,
            $survey->potensi_vandalisme,
            $survey->luas_area,
            $survey->prakiraan_pengguna_max,
            $survey->getRawOriginal('tempat_ap_latitude'),
            $survey->getRawOriginal('tempat_ap_longitude'),
            $survey->potensi_interferensi,
            $survey->sumber_listrik,
            $survey->getRawOriginal('posisi_panel_latitude'),
            $survey->getRawOriginal('posisi_panel_longitude'),
            $survey->daya_tersedia,
            $survey->proteksi_petir,
            $this->boolLabel($survey->tersedia_fiber_optik),
            $this->boolLabel($survey->tersedia_4g_5g),
            $this->boolLabel($survey->tersedia_link_p2p),
            $survey->jumlah_provider,
            $survey->photos_count,
        ];
    }

    /** @return array<string, string> */
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
            'E' => '#,##0.0000000',
            'F' => '#,##0.0000000',
            'P' => NumberFormat::FORMAT_NUMBER,
            'Q' => NumberFormat::FORMAT_NUMBER,
            'AF' => NumberFormat::FORMAT_NUMBER,
            'AG' => '#,##0.0000000',
            'AH' => '#,##0.0000000',
            'AK' => '#,##0.0000000',
            'AL' => '#,##0.0000000',
            'AR' => NumberFormat::FORMAT_NUMBER,
            'AS' => NumberFormat::FORMAT_NUMBER,
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

    private function boolLabel(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        return (bool) $value ? 'Ya' : 'Tidak';
    }
}

