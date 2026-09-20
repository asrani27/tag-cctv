<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SurveyExport implements Export, WithMultipleSheets
{
    use Exportable;

    public function __construct(
        private readonly array $filters,
        private readonly User $user,
    ) {}

    /**
     * Return the sheets that belong to this workbook.
     *
     * @return array<int, object>
     */
    public function sheets(): array
    {
        return [
            new SurveyDataSheet($this->filters, $this->user),
            new SurveyPhotoSheet($this->filters, $this->user),
            new SurveySummarySheet($this->filters, $this->user),
        ];
    }
}

