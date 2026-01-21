<?php

namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\MonthlyReport;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MonthlyReportExport;
use App\Exports\BulkReportExport;
use Carbon\Carbon;

class ReportController extends Controller
{
    private function findReportBySlug($year, $month, $slug)
    {
        $director = Director::where('slug', $slug)->firstOrFail();
        
        return MonthlyReport::with(['director.creditCards', 'transactions'])
            ->where('director_id', $director->id)
            ->where('year', $year)
            ->where('month', $month)
            ->firstOrFail();
    }

    public function index(Request $request)
    {
        $availableYears = MonthlyReport::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $defaultYear = $availableYears->first() ?? date('Y');
        
        $filterYear = $request->input('year', $defaultYear);
        $filterMonth = $request->input('month');
        $filterType = $request->input('type', 'monthly');
        $sortBy = $request->input('sort', 'period_desc');

        $query = MonthlyReport::with(['director', 'transactions'])
            ->where('year', $filterYear);

        if ($filterMonth) {
            $query->where('month', $filterMonth);
        }

        if ($filterType == 'yearly') {
            $reports = $query->get()
                ->groupBy('director_id')
                ->map(function ($group) use ($filterYear) {
                    $director = $group->first()->director;
                    $totalLimit = $group->sum('credit_limit');
                    $totalExpenses = $group->sum(function ($report) {
                        return $report->transactions->sum('amount');
                    });

                    return (object) [
                        'id' => $group->first()->id,
                        'director' => $director,
                        'month_name' => 'TAHUNAN (REKAP)',
                        'month' => 0,
                        'year' => $filterYear,
                        'credit_limit' => $totalLimit,
                        'total_expenses' => $totalExpenses,
                        'remaining_limit' => $totalLimit - $totalExpenses,
                        'is_aggregate' => true 
                    ];
                })->values();
        } else {
            $reports = $query->get()
                ->map(function ($report) {
                    $total = $report->transactions->sum('amount');
                    $report->total_expenses = $total;
                    $report->remaining_limit = $report->credit_limit - $total;
                    $report->is_aggregate = false;
                    return $report;
                });
        }

        switch ($sortBy) {
            case 'period_asc':
                $reports = $reports->sortBy(function($row) { return sprintf('%d-%02d', $row->year, $row->month); });
                break;
            case 'period_desc':
                $reports = $reports->sortByDesc(function($row) { return sprintf('%d-%02d', $row->year, $row->month); });
                break;
            case 'pagu_high':
                $reports = $reports->sortByDesc('credit_limit');
                break;
            case 'pagu_low':
                $reports = $reports->sortBy('credit_limit');
                break;
            case 'realisasi_high':
                $reports = $reports->sortByDesc('total_expenses');
                break;
            case 'realisasi_low':
                $reports = $reports->sortBy('total_expenses');
                break;
            case 'sisa_high':
                $reports = $reports->sortByDesc('remaining_limit');
                break;
            case 'sisa_low':
                $reports = $reports->sortBy('remaining_limit');
                break;
        }

        return view('reports.index', compact('reports', 'availableYears', 'filterYear', 'filterMonth', 'filterType', 'sortBy'));
    }

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

        $exists = MonthlyReport::where('director_id', $request->director_id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Laporan sudah ada.']);
        }

        $report = MonthlyReport::create($request->all());

        return redirect()->route('reports.show', [
            'year' => $report->year,
            'month' => $report->month,
            'slug' => $report->director->slug
        ]);
    }

    public function show($year, $month, $slug)
    {
        $report = $this->findReportBySlug($year, $month, $slug);
        
        $totalExpenses = $report->transactions->sum('amount');
        $remainingLimit = $report->credit_limit - $totalExpenses;

        $startDate = Carbon::createFromDate($year, $month, 1)->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');

        return view('reports.show', compact('report', 'totalExpenses', 'remainingLimit', 'startDate', 'endDate'));
    }

    public function destroy($id)
    {
        $report = MonthlyReport::findOrFail($id);
        $report->transactions()->delete();
        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Laporan dihapus.');
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

        return redirect()->back()->with('success', 'Transaksi disimpan.');
    }

    public function destroyTransaction($id)
    {
        Transaction::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Transaksi dihapus.');
    }

    public function exportPdf($year, $month, $slug)
    {
        $report = $this->findReportBySlug($year, $month, $slug);
        $totalExpenses = $report->transactions->sum('amount');
        $remainingLimit = $report->credit_limit - $totalExpenses;

        $pdf = Pdf::loadView('reports.pdf', compact('report', 'totalExpenses', 'remainingLimit'));
        
        return $pdf->setPaper('a4', 'landscape')->download('Laporan-'.$slug.'.pdf');
    }

    public function exportExcel($year, $month, $slug)
    {
        $report = $this->findReportBySlug($year, $month, $slug);
        return Excel::download(new MonthlyReportExport($report->id), 'Laporan-'.$slug.'.xlsx');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'report_ids' => 'required|array',
            'action' => 'required'
        ]);

        $ids = $request->report_ids;

        if ($request->action == 'excel') {
            return Excel::download(new BulkReportExport($ids), 'Laporan-Gabungan.xlsx');
        }

        if ($request->action == 'pdf') {
            $reports = MonthlyReport::with(['director.creditCards', 'transactions'])
                ->whereIn('id', $ids)
                ->get();
            
            $pdf = Pdf::loadView('reports.bulk_pdf', compact('reports'));
            return $pdf->setPaper('a4', 'landscape')->download('Laporan-Gabungan.pdf');
        }

        return redirect()->back();
    }
}