<?php

namespace App\Exports;

use App\Models\SurveyLocation;
use App\Models\SurveyPhoto;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SurveyPhotoSheet implements
    FromQuery,
    ShouldAutoSize,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle
{
    public function __construct(
        private readonly array $filters,
        private readonly User $user,
    ) {}

    public function title(): string
    {
        return 'Foto';
    }

    public function query(): Builder
    {
        // Get IDs of surveys matching the current filters
        $surveyIds = SurveyLocation::query()
            ->applyFilters($this->filters, $this->user)
            ->select('id');

        return SurveyPhoto::query()
            ->whereIn('survey_location_id', $surveyIds)
            ->with(['surveyLocation', 'user'])
            ->orderBy('survey_location_id')
            ->orderBy('sort_order');
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return [
            'ID Foto',
            'Survey ID',
            'Diinput Oleh',
            'Nama File',
            'Ukuran',
            'Tipe File',
            'Path',
        ];
    }

    /** @param  SurveyPhoto  $photo */
    public function map(mixed $photo): array
    {
        return [
            $photo->id,
            $photo->survey_location_id,
            $photo->user?->name ?? 'Data Lama',
            $photo->original_name,
            $photo->formatted_size,
            $photo->mime_type,
            $photo->file_path,
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
