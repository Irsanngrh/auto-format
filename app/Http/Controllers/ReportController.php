<?php

namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\MonthlyReport;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonthlyReportExport;

class ReportController extends Controller
{
    public function create()
    {
        $directors = Director::with('creditCards')->get();
        return view('reports.create', compact('directors'));
    }

    public function store(Request $request)
    {
        $cleanLimit = str_replace('.', '', $request->credit_limit);
        $request->merge(['credit_limit' => $cleanLimit]);

        $request->validate([
            'director_id' => 'required',
            'month' => 'required|numeric',
            'year' => 'required|numeric',
            'credit_limit' => 'required|numeric',
        ]);

        $report = MonthlyReport::create($request->all());

        return redirect()->route('reports.show', $report->id);
    }

    public function show($id)
    {
        $report = MonthlyReport::with(['director.creditCards', 'transactions'])->findOrFail($id);
        
        $totalExpenses = $report->transactions->sum('amount');
        $remainingLimit = $report->credit_limit - $totalExpenses;

        return view('reports.show', compact('report', 'totalExpenses', 'remainingLimit'));
    }

    public function storeTransaction(Request $request, $id)
    {
        $cleanAmount = str_replace('.', '', $request->amount);
        $request->merge(['amount' => $cleanAmount]);

        $request->validate([
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        Transaction::create([
            'monthly_report_id' => $id,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
            'amount' => $request->amount,
        ]);

        return redirect()->back()->with('success', 'Data saved');
    }

    public function destroyTransaction($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return redirect()->back()->with('success', 'Data deleted');
    }

    public function exportPdf($id)
    {
        $report = MonthlyReport::with(['director.creditCards', 'transactions'])->findOrFail($id);
        $totalExpenses = $report->transactions->sum('amount');
        $remainingLimit = $report->credit_limit - $totalExpenses;

        $pdf = Pdf::loadView('reports.pdf', compact('report', 'totalExpenses', 'remainingLimit'));
        
        return $pdf->setPaper('a4', 'landscape')->download('Laporan-'.$report->director->name.'.pdf');
    }

    public function exportExcel($id)
    {
        return Excel::download(new MonthlyReportExport($id), 'Laporan.xlsx');
    }
}