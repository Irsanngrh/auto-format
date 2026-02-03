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
use App\Exports\MonthlyRecapExport;
use App\Exports\SingleRecapExport;
use App\Exports\YearlyRecapExport;
use Carbon\Carbon;

class ReportController extends Controller
{
    private function findReportBySlug($year, $month, $slug)
    {
        $director = Director::where('slug', $slug)->firstOrFail();
        return MonthlyReport::with(['director.creditCards', 'transactions'])
            ->where('director_id', $director->id)
            ->where('year', $year)->where('month', $month)->firstOrFail();
    }

    private function terbilang($nilai) {
        $nilai = abs($nilai);
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($nilai < 12) {
            $temp = " ". $huruf[$nilai];
        } else if ($nilai < 20) {
            $temp = $this->terbilang($nilai - 10). " Belas";
        } else if ($nilai < 100) {
            $temp = $this->terbilang((int)($nilai/10))." Puluh". $this->terbilang($nilai % 10);
        } else if ($nilai < 200) {
            $temp = " Seratus" . $this->terbilang($nilai - 100);
        } else if ($nilai < 1000) {
            $temp = $this->terbilang((int)($nilai/100)) . " Ratus" . $this->terbilang($nilai % 100);
        } else if ($nilai < 2000) {
            $temp = " Seribu" . $this->terbilang($nilai - 1000);
        } else if ($nilai < 1000000) {
            $temp = $this->terbilang((int)($nilai/1000)) . " Ribu" . $this->terbilang($nilai % 1000);
        } else if ($nilai < 1000000000) {
            $temp = $this->terbilang((int)($nilai/1000000)) . " Juta" . $this->terbilang($nilai % 1000000);
        }
        return $temp;
    }

    private function getRomawi($bulan) {
        $map = [1=>'I', 2=>'II', 3=>'III', 4=>'IV', 5=>'V', 6=>'VI', 7=>'VII', 8=>'VIII', 9=>'IX', 10=>'X', 11=>'XI', 12=>'XII'];
        return $map[$bulan] ?? 'I';
    }

    private function getMonthName($bulan) {
        $map = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
        return $map[$bulan] ?? '';
    }

    public function index(Request $request)
    {
        $availableYears = MonthlyReport::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $defaultYear = $availableYears->first() ?? date('Y');
        
        $filterYear = $request->input('year', $defaultYear);
        $filterMonth = $request->input('month');
        $filterDirector = $request->input('director_id');
        $filterType = $request->input('type', 'monthly');
        $sortBy = $request->input('sort', 'period_desc');

        $query = MonthlyReport::with(['director', 'transactions'])->where('year', $filterYear);

        if ($filterMonth && $filterType != 'yearly') {
            $query->where('month', $filterMonth);
        }

        if ($filterDirector) {
            $query->where('director_id', $filterDirector);
        }

        if ($filterType == 'yearly') {
            $reports = $query->get()->groupBy('director_id')->map(function ($group) use ($filterYear) {
                $director = $group->first()->director;
                $totalLimit = $group->sum('credit_limit');
                $totalExpenses = $group->sum(function ($r) { return $r->transactions->sum('amount'); });
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
            $reports = $query->get()->map(function ($report) {
                $total = $report->transactions->sum('amount');
                $report->total_expenses = $total;
                $report->remaining_limit = $report->credit_limit - $total;
                $report->is_aggregate = false;
                return $report;
            });
        }

        switch ($sortBy) {
            case 'period_asc': $reports = $reports->sortBy(function($row) { return sprintf('%d-%02d', $row->year, $row->month); }); break;
            case 'period_desc': $reports = $reports->sortByDesc(function($row) { return sprintf('%d-%02d', $row->year, $row->month); }); break;
            case 'pagu_high': $reports = $reports->sortByDesc('credit_limit'); break;
            case 'pagu_low': $reports = $reports->sortBy('credit_limit'); break;
            case 'realisasi_high': $reports = $reports->sortByDesc('total_expenses'); break;
            case 'realisasi_low': $reports = $reports->sortBy('total_expenses'); break;
            case 'sisa_high': $reports = $reports->sortByDesc('remaining_limit'); break;
            case 'sisa_low': $reports = $reports->sortBy('remaining_limit'); break;
        }

        $directors = Director::all();

        return view('reports.index', compact('reports', 'availableYears', 'directors', 'filterYear', 'filterMonth', 'filterDirector', 'filterType', 'sortBy'));
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $exportType = $request->input('type');
        $year = $request->input('year');
        $ids = $request->input('report_ids');
        $rawRekap = $request->input('rekap_no');

        if ($exportType == 'yearly') {
            $query = MonthlyReport::with('director')->where('year', $year);
            if ($ids && count($ids) > 0) {
                $selectedSamples = MonthlyReport::whereIn('id', $ids)->get();
                $directorIds = $selectedSamples->pluck('director_id')->unique();
                $query->whereIn('director_id', $directorIds);
            }
            $yearlyReports = $query->get()->groupBy('director_id')->map(function ($group) use ($year) {
                return (object) [
                    'director' => $group->first()->director,
                    'year' => $year,
                    'credit_limit' => $group->sum('credit_limit'),
                    'total_expenses' => $group->sum(function($r){ return $r->transactions->sum('amount'); }),
                    'remaining_limit' => $group->sum('credit_limit') - $group->sum(function($r){ return $r->transactions->sum('amount'); })
                ];
            });

            $names = $yearlyReports->pluck('director.name')->implode(' + ');
            $filename = "{$names} - Rekap Tahun {$year}";

            if ($action == 'excel') {
                return Excel::download(new YearlyRecapExport($yearlyReports, $year), $filename . '.xlsx');
            } else {
                $pdf = Pdf::loadView('reports.pdf_yearly', ['reports' => $yearlyReports, 'year' => $year]);
                return $pdf->setPaper('a4', 'landscape')->download($filename . '.pdf');
            }
        }

        $request->validate(['report_ids' => 'required|array']);
        $reports = MonthlyReport::with(['director.creditCards', 'transactions'])->whereIn('id', $ids)->get();
        $directorCount = $reports->pluck('director_id')->unique()->count();
        
        $names = $reports->pluck('director.name')->unique()->implode(' + ');
        $first = $reports->first();
        $monthName = $this->getMonthName($first->month);
        $romawi = $this->getRomawi($first->month);
        $filename = "{$names} - Rekap {$monthName} {$first->year}";

        if (is_numeric($rawRekap)) {
            $rekapNo = "Rekap/{$rawRekap}-AS/{$romawi}/{$first->year}-DIVUM";
        } else {
            $rekapNo = $rawRekap ?: '-';
        }

        $manualData = [
            'rekap_no' => $rekapNo,
            'po_no' => $request->input('po_no', '-'),
            'signer1_name' => $request->input('signer1_name', 'Nama Pejabat'),
            'signer1_pos' => $request->input('signer1_pos', 'Jabatan Pejabat'),
            'signer2_name' => $request->input('signer2_name', 'Nama Pejabat'),
            'signer2_pos' => $request->input('signer2_pos', 'Jabatan Pejabat'),
        ];

        if ($directorCount == 1) {
            $report = $reports->first();
            $total = $report->transactions->sum('amount');
            $terbilang = $this->terbilang($total);

            if ($action == 'excel') {
                return Excel::download(new SingleRecapExport($report, $terbilang, $manualData), $filename . '.xlsx');
            } else {
                $pdf = Pdf::loadView('reports.pdf_single', compact('report', 'terbilang', 'manualData'));
                return $pdf->setPaper('a4', 'portrait')->download($filename . '.pdf');
            }
        } else {
            if ($action == 'excel') {
                return Excel::download(new MonthlyRecapExport($reports), $filename . '.xlsx');
            } else {
                $pdf = Pdf::loadView('reports.pdf_monthly', compact('reports'));
                return $pdf->setPaper('a4', 'landscape')->download($filename . '.pdf');
            }
        }
        return redirect()->back();
    }

    public function create() { $directors = Director::with('creditCards')->get(); return view('reports.create', compact('directors')); }
    public function store(Request $request) { $cleanLimit = str_replace('.', '', $request->credit_limit); $request->merge(['credit_limit' => $cleanLimit]); $request->validate(['director_id' => 'required', 'month' => 'required|numeric', 'year' => 'required|numeric', 'credit_limit' => 'required|numeric']); $exists = MonthlyReport::where('director_id', $request->director_id)->where('month', $request->month)->where('year', $request->year)->exists(); if ($exists) { return redirect()->back()->withInput()->withErrors(['error' => 'Laporan sudah ada.']); } $report = MonthlyReport::create($request->all()); return redirect()->route('reports.show', ['year' => $report->year, 'month' => $report->month, 'slug' => $report->director->slug]); }
    public function show($year, $month, $slug) { $report = $this->findReportBySlug($year, $month, $slug); $totalExpenses = $report->transactions->sum('amount'); $remainingLimit = $report->credit_limit - $totalExpenses; $startDate = Carbon::createFromDate($year, $month, 1)->format('Y-m-d'); $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d'); return view('reports.show', compact('report', 'totalExpenses', 'remainingLimit', 'startDate', 'endDate')); }
    public function destroy($id) { $report = MonthlyReport::findOrFail($id); $report->transactions()->delete(); $report->delete(); return redirect()->route('reports.index')->with('success', 'Laporan dihapus.'); }
    public function storeTransaction(Request $request, $id) { $cleanAmount = str_replace('.', '', $request->amount); $request->merge(['amount' => $cleanAmount]); $request->validate(['transaction_date' => 'required|date', 'description' => 'required|string', 'amount' => 'required|numeric']); Transaction::create(['monthly_report_id' => $id, 'transaction_date' => $request->transaction_date, 'description' => $request->description, 'amount' => $request->amount]); return redirect()->back()->with('success', 'Transaksi disimpan.'); }
    public function updateTransaction(Request $request, $id) { $cleanAmount = str_replace('.', '', $request->amount); $request->merge(['amount' => $cleanAmount]); $request->validate(['transaction_date' => 'required|date', 'description' => 'required|string', 'amount' => 'required|numeric']); $transaction = Transaction::findOrFail($id); $transaction->update(['transaction_date' => $request->transaction_date, 'description' => $request->description, 'amount' => $request->amount]); return redirect()->back()->with('success', 'Transaksi diperbarui.'); }
    public function destroyTransaction($id) { Transaction::findOrFail($id)->delete(); return redirect()->back()->with('success', 'Transaksi dihapus.'); }
    public function exportPdf($year, $month, $slug) { return redirect()->back(); }
    public function exportExcel($year, $month, $slug) { return redirect()->back(); }
}