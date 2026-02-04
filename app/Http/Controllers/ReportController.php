<?php

namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\MonthlyReport;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SingleRecapExport;
use Carbon\Carbon;

class ReportController extends Controller
{
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

        $query = MonthlyReport::with(['director', 'transactions', 'creditCard'])->where('year', $filterYear);

        if ($filterMonth && $filterType != 'yearly') {
            $query->where('month', $filterMonth);
        }

        if ($filterDirector) {
            $query->where('director_id', $filterDirector);
        }

        if ($filterType == 'yearly') {
            $reports = $query->get()->groupBy('director_id')->map(function ($group) use ($filterYear) {
                $first = $group->first();
                $totalLimit = $group->sum('credit_limit');
                $totalExpenses = $group->sum(function ($r) { return $r->transactions->sum('amount'); });
                
                $dummy = new MonthlyReport();
                $dummy->id = $first->id;
                $dummy->director_id = $first->director_id;
                $dummy->year = $filterYear;
                $dummy->month = 0;
                $dummy->credit_limit = $totalLimit;
                $dummy->setRelation('director', $first->director);
                $dummy->total_expenses = $totalExpenses;
                $dummy->remaining_limit = $totalLimit - $totalExpenses;
                $dummy->is_aggregate = true;
                return $dummy;
            })->values();
        } else {
            $reports = $query->get()->map(function ($report) {
                $report->total_expenses = $report->transactions->sum('amount');
                $report->remaining_limit = $report->credit_limit - $report->total_expenses;
                $report->is_aggregate = false;
                return $report;
            });
        }

        $directors = Director::all();
        $months = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];

        return view('reports.index', compact('reports', 'availableYears', 'directors', 'months', 'filterYear', 'filterMonth', 'filterDirector', 'filterType'));
    }

    public function create() { 
        $directors = Director::with('creditCards')->get();
        $months = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
        return view('reports.create', compact('directors', 'months')); 
    }

    public function store(Request $request) { 
        $cleanLimit = str_replace('.', '', $request->credit_limit); 
        $request->merge(['credit_limit' => $cleanLimit]); 
        
        $request->validate([
            'director_id' => 'required',
            'credit_card_id' => 'required',
            'month' => 'required',
            'year' => 'required',
            'credit_limit' => 'required'
        ]);

        $exists = MonthlyReport::where('director_id', $request->director_id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->where('credit_card_id', $request->credit_card_id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Laporan untuk Direksi, Periode, dan Kartu Kredit tersebut sudah ada!');
        }
        
        $report = MonthlyReport::create($request->all()); 
        
        return redirect()->route('reports.show', [
            'slug' => $report->director->slug, 
            'month' => $report->month, 
            'year' => $report->year
        ]); 
    }

    public function show($slug, $month, $year) { 
        $director = Director::where('slug', $slug)->firstOrFail();

        $report = MonthlyReport::with(['director', 'transactions', 'creditCard'])
            ->where('director_id', $director->id)
            ->where('month', $month)
            ->where('year', $year)
            ->firstOrFail();

        $totalExpenses = $report->transactions->sum('amount'); 
        $remainingLimit = $report->credit_limit - $totalExpenses; 
        
        $startDate = Carbon::createFromDate($report->year, $report->month, 1)->format('Y-m-d');
        $endDate = Carbon::createFromDate($report->year, $report->month, 1)->endOfMonth()->format('Y-m-d');

        return view('reports.show', compact('report', 'totalExpenses', 'remainingLimit', 'startDate', 'endDate')); 
    }

    public function edit($id) {
        $report = MonthlyReport::with(['director.creditCards'])->findOrFail($id);
        $directors = Director::with('creditCards')->get();
        $months = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
        return view('reports.edit', compact('report', 'directors', 'months'));
    }

    public function update(Request $request, $id) {
        $cleanLimit = str_replace('.', '', $request->credit_limit); 
        $request->merge(['credit_limit' => $cleanLimit]); 
        
        $report = MonthlyReport::with('director')->findOrFail($id);
        $report->update($request->all());
        
        return redirect()->route('reports.show', [
            'slug' => $report->director->slug, 
            'month' => $report->month, 
            'year' => $report->year
        ])->with('success', 'Laporan diperbarui.');
    }

    public function destroy($id) { 
        $report = MonthlyReport::findOrFail($id); 
        $report->transactions()->delete(); 
        $report->delete(); 
        return redirect()->route('reports.index')->with('success', 'Laporan berhasil dihapus.'); 
    }

    public function storeTransaction(Request $request, $id) { 
        $cleanAmount = str_replace('.', '', $request->amount); 
        $request->merge(['amount' => $cleanAmount]); 
        Transaction::create(['monthly_report_id' => $id, 'transaction_date' => $request->transaction_date, 'description' => $request->description, 'amount' => $request->amount]); 
        return redirect()->back()->with('success', 'Transaksi berhasil ditambahkan.'); 
    }

    public function updateTransaction(Request $request, $id) { 
        $cleanAmount = str_replace('.', '', $request->amount); 
        $request->merge(['amount' => $cleanAmount]); 
        $transaction = Transaction::findOrFail($id); 
        $transaction->update(['transaction_date' => $request->transaction_date, 'description' => $request->description, 'amount' => $request->amount]); 
        return redirect()->back()->with('success', 'Transaksi berhasil diperbarui.'); 
    }

    public function destroyTransaction($id) { 
        Transaction::findOrFail($id)->delete(); 
        return redirect()->back()->with('success', 'Transaksi berhasil dihapus.'); 
    }

    public function bulkAction(Request $request)
    {
        $action = $request->input('action');
        $exportType = $request->input('type'); 
        $ids = $request->input('report_ids');
        
        if (!$ids || count($ids) == 0) return redirect()->back()->with('error', 'Pilih minimal satu laporan.');
        
        if ($exportType == 'yearly') {
            $sampleReport = MonthlyReport::findOrFail($ids[0]);
            $year = $sampleReport->year;
            $directorId = $sampleReport->director_id;
            
            $reports = MonthlyReport::with(['director', 'transactions', 'creditCard'])
                ->where('director_id', $directorId)
                ->where('year', $year)
                ->get();
            
            $first = $reports->first();
            $director = $first->director;
            $allTransactions = $reports->flatMap(function($r) { return $r->transactions; });
            $first->setRelation('transactions', $allTransactions);
            
            $periodText = "Periode Tahun " . $year;
            $filename = "{$director->name} - Rekap Tahun {$year}";
            $rekapNoSuffix = "Rekap/...-AS/XII/{$year}-DIVUM"; 
        } else {
            $reports = MonthlyReport::with(['director', 'transactions', 'creditCard'])->whereIn('id', $ids)->get();
            $first = $reports->first();
            $director = $first->director;
            
            $monthName = $this->getMonthName($first->month);
            $romawi = $this->getRomawi($first->month);
            $year = $first->year;
            
            $periodText = "Periode Bulan {$monthName} {$year}";
            $filename = "{$director->name} - Rekap {$monthName} {$year}";
            $rekapNoSuffix = "Rekap/...-AS/{$romawi}/{$year}-DIVUM";
        }

        $manualData = [
            'rekap_no' => $request->input('rekap_no', '-'),
            'po_no' => $request->input('po_no', '-'),
            'signer1_name' => $request->input('signer1_name', 'Nama Pejabat'),
            'signer1_pos' => $request->input('signer1_pos', 'Jabatan Pejabat'),
            'signer2_name' => $request->input('signer2_name', 'Nama Pejabat'),
            'signer2_pos' => $request->input('signer2_pos', 'Jabatan Pejabat'),
        ];
        
        if (is_numeric($manualData['rekap_no'])) {
            $manualData['rekap_no'] = str_replace('...', $request->input('rekap_no'), $rekapNoSuffix);
        }
        
        $total = $first->transactions->sum('amount');
        $terbilang = $this->terbilang($total);
        
        if ($action == 'excel') {
            return Excel::download(new SingleRecapExport($first, $terbilang, $manualData, $periodText), $filename . '.xlsx');
        } else {
            $pdf = Pdf::loadView('reports.pdf_single', ['report' => $first, 'terbilang' => $terbilang, 'manualData' => $manualData, 'periodText' => $periodText]);
            return $pdf->setPaper('a4', 'portrait')->download($filename . '.pdf');
        }
    }

    public function exportPdf($id) { 
        $report = MonthlyReport::with(['director', 'transactions', 'creditCard'])->findOrFail($id);
        $total = $report->transactions->sum('amount');
        $terbilang = $this->terbilang($total);
        $monthName = $this->getMonthName($report->month);
        $romawi = $this->getRomawi($report->month);
        $manualData = ['rekap_no' => "Rekap/ ... -AS/{$romawi}/{$report->year}-DIVUM", 'po_no' => '...', 'signer1_name' => '(Nama Pejabat)', 'signer1_pos' => 'Menyetujui', 'signer2_name' => '(Nama Pejabat)', 'signer2_pos' => 'Mengetahui'];
        $periodText = "Periode Bulan {$monthName} {$report->year}";
        $filename = "{$report->director->name} - Rekap {$monthName} {$report->year}.pdf";
        $pdf = Pdf::loadView('reports.pdf_single', ['report' => $report, 'terbilang' => $terbilang, 'manualData' => $manualData, 'periodText' => $periodText]);
        return $pdf->setPaper('a4', 'portrait')->download($filename);
    }

    public function exportExcel($id) { 
        $report = MonthlyReport::with(['director', 'transactions', 'creditCard'])->findOrFail($id);
        $total = $report->transactions->sum('amount');
        $terbilang = $this->terbilang($total);
        $monthName = $this->getMonthName($report->month);
        $romawi = $this->getRomawi($report->month);
        $manualData = ['rekap_no' => "Rekap/ ... -AS/{$romawi}/{$report->year}-DIVUM", 'po_no' => '...', 'signer1_name' => '(Nama Pejabat)', 'signer1_pos' => 'Menyetujui', 'signer2_name' => '(Nama Pejabat)', 'signer2_pos' => 'Mengetahui'];
        $periodText = "Periode Bulan {$monthName} {$report->year}";
        $filename = "{$report->director->name} - Rekap {$monthName} {$report->year}.xlsx";
        return Excel::download(new SingleRecapExport($report, $terbilang, $manualData, $periodText), $filename);
    }
}