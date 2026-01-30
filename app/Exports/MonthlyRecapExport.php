<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class MonthlyRecapExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $reports;

    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    public function view(): View
    {
        return view('reports.pdf_monthly', ['reports' => $this->reports]);
    }

    public function title(): string
    {
        return 'Rekap Bulanan';
    }
}