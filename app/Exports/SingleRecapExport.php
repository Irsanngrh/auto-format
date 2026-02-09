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
    protected $periodText;

    public function __construct($report, $terbilang, $manualData, $periodText)
    {
        $this->report = $report;
        $this->terbilang = $terbilang;
        $this->manualData = $manualData;
        $this->periodText = $periodText;
    }

    public function view(): View
    {
        return view('reports.excel_single', [
            'report' => $this->report, 
            'terbilang' => $this->terbilang,
            'manualData' => $this->manualData,
            'periodText' => $this->periodText
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
            'B' => 50,  
            'C' => 2,   
            'D' => 22,  
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
        $sheet->getParent()->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
        $sheet->getParent()->getDefaultStyle()->getFont()->setSize(11);
        $sheet->getParent()->getDefaultStyle()->getFont()->setBold(false);
        
        $sheet->getStyle('B')->getAlignment()->setWrapText(true);
        $sheet->getStyle('C')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                
                $text = "Rekap Realisasi Biaya Penggunaan Corporate Card Direksi PT ASABRI (Persero) " . 
                        $this->periodText . ", dengan rincian sebagai berikut:";
                
                $charsPerLine = 55; 
                
                // Mencegah DivisionByZero
                if ($charsPerLine > 0) {
                    $numLines = ceil(strlen($text) / $charsPerLine);
                } else {
                    $numLines = 1;
                }

                $rowHeight = ($numLines * 13) + 10; 
                
                $sheet->getRowDimension(9)->setRowHeight($rowHeight);
                
                $sheet->getRowDimension(10)->setRowHeight(40);
                
                $sheet->getStyle('A9:D10')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            },
        ];
    }
}