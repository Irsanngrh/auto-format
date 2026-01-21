<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BulkReportExport implements WithMultipleSheets
{
    protected $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->ids as $id) {
            $sheets[] = new MonthlyReportExport($id);
        }

        return $sheets;
    }
}