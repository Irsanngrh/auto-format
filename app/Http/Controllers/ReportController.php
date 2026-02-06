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
        $huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        if ($nilai < 12) return " " . $huruf[$nilai];
        if ($nilai < 20) return $this->terbilang($nilai - 10) . " Belas";
        if ($nilai < 100) return $this->terbilang((int)($nilai/10)) . " Puluh" . $this->terbilang($nilai % 10);
        if ($nilai < 200) return " Seratus" . $this->terbilang($nilai - 100);
        if ($nilai < 1000) return $this->terbilang((int)($nilai/100)) . " Ratus" . $this->terbilang($nilai % 100);
        if ($nilai < 1000000) return $this->terbilang((int)($nilai/1000)) . " Ribu" . $this->terbilang($nilai % 1000);
        return $this->terbilang((int)($nilai/1000000)) . " Juta" . $this->terbilang($nilai % 1000000);
    }

    private function getMonthName($m) {
        return [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'][$m] ?? '';
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

        if ($filterMonth && $filterType != 'yearly') $query->where('month', $filterMonth);
        if ($filterDirector) $query->where('director_id', $filterDirector);

        if ($filterType == 'yearly') {
            $reports = $query->get()->groupBy('director_id')->map(function ($group) use ($filterYear) {
                $first = $group->first();
                $dummy = new MonthlyReport();
                $dummy->id = $first->id;
                $dummy->director_id = $first->director_id;
                $dummy->year = $filterYear;
                $dummy->month = 0;
                $dummy->credit_limit = $group->sum('credit_limit');
                $dummy->setRelation('director', $first->director);
                $dummy->total_expenses = $group->sum(fn($r) => $r->transactions->sum('amount'));
                $dummy->remaining_limit = $dummy->credit_limit - $dummy->total_expenses;
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
        $request->merge(['credit_limit' => str_replace('.', '', $request->credit_limit)]); 
        $request->validate(['director_id' => 'required', 'credit_card_id' => 'required', 'month' => 'required', 'year' => 'required', 'credit_limit' => 'required']);

        $exists = MonthlyReport::where('director_id', $request->director_id)
            ->where('month', $request->month)->where('year', $request->year)
            ->where('credit_card_id', $request->credit_card_id)->exists();

        if ($exists) return redirect()->back()->withInput()->with('error', 'Laporan sudah ada!');
        
        $report = MonthlyReport::create($request->all()); 
        return redirect()->route('reports.show', ['slug' => $report->director->slug, 'month' => $report->month, 'year' => $report->year, 'card_last_digits' => substr($report->creditCard->card_number, -4)]); 
    }

    public function show($slug, $month, $year, $card_last_digits) { 
        $director = Director::where('slug', $slug)->firstOrFail();
        $report = MonthlyReport::with(['director', 'transactions' => fn($q) => $q->orderBy('transaction_date', 'asc'), 'creditCard'])
        ->where('director_id', $director->id)->where('month', $month)->where('year', $year)
        ->whereHas('creditCard', fn($q) => $q->where('card_number', 'LIKE', "%{$card_last_digits}"))->firstOrFail();

        $totalExpenses = $report->transactions->sum('amount'); 
        $remainingLimit = $report->credit_limit - $totalExpenses; 
        $startDate = Carbon::createFromDate($year, $month, 1)->format('Y-m-d');
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');

        return view('reports.show', compact('report', 'totalExpenses', 'remainingLimit', 'startDate', 'endDate')); 
    }

    public function edit($id) {
        $report = MonthlyReport::with(['director.creditCards'])->findOrFail($id);
        $directors = Director::with('creditCards')->get();
        $months = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
        return view('reports.edit', compact('report', 'directors', 'months'));
    }

    public function update(Request $request, $id) {
        $request->merge(['credit_limit' => str_replace('.', '', $request->credit_limit)]); 
        $report = MonthlyReport::with(['director', 'creditCard'])->findOrFail($id);
        $report->update($request->all());
        $report->refresh();
        return redirect()->route('reports.show', ['slug' => $report->director->slug, 'month' => $report->month, 'year' => $report->year, 'card_last_digits' => substr($report->creditCard->card_number, -4)])->with('success', 'Laporan diperbarui.');
    }

    public function destroy($id) { 
        $report = MonthlyReport::findOrFail($id); 
        $report->transactions()->delete(); 
        $report->delete(); 
        return redirect()->route('reports.index')->with('success', 'Laporan dihapus.'); 
    }

    public function storeTransaction(Request $request, $id) { 
        $request->merge(['amount' => str_replace('.', '', $request->amount)]); 
        Transaction::create(['monthly_report_id' => $id, 'transaction_date' => $request->transaction_date, 'description' => $request->description, 'amount' => $request->amount]); 
        return redirect()->back()->with('success', 'Transaksi ditambahkan.'); 
    }

    public function updateTransaction(Request $request, $id) { 
        $request->merge(['amount' => str_replace('.', '', $request->amount)]); 
        Transaction::findOrFail($id)->update(['transaction_date' => $request->transaction_date, 'description' => $request->description, 'amount' => $request->amount]); 
        return redirect()->back()->with('success', 'Transaksi diperbarui.'); 
    }

    public function destroyTransaction($id) { 
        Transaction::findOrFail($id)->delete(); 
        return redirect()->back()->with('success', 'Transaksi dihapus.'); 
    }

    public function exportPdf(Request $request, $id) { 
        $report = MonthlyReport::with(['director', 'transactions' => fn($q) => $q->orderBy('transaction_date', 'asc'), 'creditCard'])->findOrFail($id);
        $total = $report->transactions->sum('amount');
        $terbilang = $this->terbilang($total);
        $manualData = ['rekap_no' => $request->input('rekap_no'), 'po_no' => $request->input('po_no'), 'signer1_name' => $request->input('signer1_name') ?: '(NAMA)', 'signer1_pos' => $request->input('signer1_pos') ?: '(JABATAN)', 'signer2_name' => $request->input('signer2_name') ?: '(NAMA)', 'signer2_pos' => $request->input('signer2_pos') ?: '(JABATAN)'];
        $periodText = "Periode Bulan " . $this->getMonthName($report->month) . " {$report->year}";
        $filename = "{$report->director->name} - Rekap {$report->month} {$report->year}.pdf";
        return Pdf::loadView('reports.pdf_single', compact('report', 'terbilang', 'manualData', 'periodText'))->setPaper('a4', 'portrait')->download($filename);
    }

    public function exportExcel(Request $request, $id) { 
        $report = MonthlyReport::with(['director', 'transactions' => fn($q) => $q->orderBy('transaction_date', 'asc'), 'creditCard'])->findOrFail($id);
        $total = $report->transactions->sum('amount');
        $terbilang = $this->terbilang($total);
        $manualData = ['rekap_no' => $request->input('rekap_no'), 'po_no' => $request->input('po_no'), 'signer1_name' => $request->input('signer1_name') ?: '(NAMA)', 'signer1_pos' => $request->input('signer1_pos') ?: '(JABATAN)', 'signer2_name' => $request->input('signer2_name') ?: '(NAMA)', 'signer2_pos' => $request->input('signer2_pos') ?: '(JABATAN)'];
        $periodText = "Periode Bulan " . $this->getMonthName($report->month) . " {$report->year}";
        return Excel::download(new SingleRecapExport($report, $terbilang, $manualData, $periodText), "{$report->director->name} - Rekap.xlsx");
    }
}