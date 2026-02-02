<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class YearlyRecapExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $reports;
    protected $year;

    public function __construct($reports, $year)
    {
        $this->reports = $reports;
        $this->year = $year;
    }

    public function view(): View
    {
        return view('reports.pdf_yearly', [
            'reports' => $this->reports,
            'year' => $this->year
        ]);
    }

    public function title(): string
    {
        return 'Rekap Tahunan ' . $this->year;
    }
}