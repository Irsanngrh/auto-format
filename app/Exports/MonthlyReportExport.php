<?php

namespace App\Exports;

use App\Models\MonthlyReport;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MonthlyReportExport implements FromView, ShouldAutoSize
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $report = MonthlyReport::with(['director.creditCards', 'transactions'])->findOrFail($this->id);
        $totalExpenses = $report->transactions->sum('amount');
        $remainingLimit = $report->credit_limit - $totalExpenses;

        return view('reports.excel', compact('report', 'totalExpenses', 'remainingLimit'));
    }
}