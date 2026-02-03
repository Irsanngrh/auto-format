<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class SingleRecapExport implements FromView, WithTitle, WithDrawings, WithColumnWidths, WithStyles, WithEvents
{
    protected $report;
    protected $terbilang;
    protected $manualData;

    public function __construct($report, $terbilang, $manualData)
    {
        $this->report = $report;
        $this->terbilang = $terbilang;
        $this->manualData = $manualData;
    }

    public function view(): View
    {
        return view('reports.excel_single', [
            'report' => $this->report, 
            'terbilang' => $this->terbilang,
            'manualData' => $this->manualData
        ]);
    }

    public function title(): string
    {
        return substr($this->report->director->name, 0, 30);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   
            'B' => 20,  
            'C' => 30,   
            'D' => 25,  
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo ASABRI');
        $drawing->setDescription('Logo');
        $path = public_path('images/logo-asabri.png');
        
        if (file_exists($path)) {
            $drawing->setPath($path);
            $drawing->setHeight(40); 
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
        }

        return $drawing;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('B')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
        
        $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(11);
        $sheet->getParent()->getDefaultStyle()->getFont()->setBold(false);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);

                $bulanStr = ucfirst(strtolower($this->report->month_name));
                $text = "Rekap Realisasi Biaya Penggunaan Corporate Card Direksi PT ASABRI (Persero) Periode Bulan " . 
                        $bulanStr . " " . $this->report->year . ", dengan rincian sebagai berikut:";
                
                $charLength = strlen($text);
                $charsPerLine = 50; 
                $estimatedTextLines = ceil($charLength / $charsPerLine);
                
                $calculatedHeight = ($estimatedTextLines * 15) + 10;
                $sheet->getRowDimension(10)->setRowHeight(max($calculatedHeight, 30));

                $sheet->getRowDimension(11)->setRowHeight(35);
            },
        ];
    }
}