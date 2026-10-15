<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Usulan;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UsulanExport implements Export, WithMultipleSheets
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
        $tab = $this->filters['tab'] ?? Usulan::JENIS_CCTV;

        if ($tab === Usulan::JENIS_WIFI) {
            return [
                new UsulanWifiSheet($this->filters, $this->user),
            ];
        }

        if ($tab === 'all') {
            return [
                new UsulanCctvSheet($this->filters, $this->user),
                new UsulanWifiSheet($this->filters, $this->user),
            ];
        }

        return [
            new UsulanCctvSheet($this->filters, $this->user),
        ];
    }
}
