<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class SingleRecapExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $report;
    protected $terbilang;
    protected $nomorSurat;

    public function __construct($report, $terbilang, $nomorSurat)
    {
        $this->report = $report;
        $this->terbilang = $terbilang;
        $this->nomorSurat = $nomorSurat;
    }

    public function view(): View
    {
        return view('reports.pdf_single', [
            'report' => $this->report, 
            'terbilang' => $this->terbilang,
            'nomorSurat' => $this->nomorSurat
        ]);
    }

    public function title(): string
    {
        return substr($this->report->director->name, 0, 30);
    }
}