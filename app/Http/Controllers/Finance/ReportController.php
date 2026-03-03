<?php

namespace App\Http\Controllers\Finance;

use App\Exports\FinanceReportExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\School;
use App\Models\Term;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request, School $school)
    {
        $activeTerm = Term::where('is_active', true)->first();

        // 1. Fetch all invoices and aggregate the payment totals via the database
        $invoices = collect();
        if ($activeTerm) {
            $invoices = Invoice::with(['student.studentProfile', 'student.school'])
                ->where('term_id', $activeTerm->id)
                ->withSum('payments', 'amount')
                ->orderBy('status', 'asc') // Puts UNPAID at the top
                ->get();
        }

        // 2. Calculate the grand totals for the summary cards
        $totalExpected = $invoices->sum('total_amount');
        $totalCollected = $invoices->sum('payments_sum_amount');
        $totalOutstanding = $totalExpected - $totalCollected;

        return view('finances.reports.index', compact(
            'invoices',
            'activeTerm',
            'totalExpected',
            'totalCollected',
            'totalOutstanding'
        ));
    }

    public function export(Request $request, School $school)
    {
        $activeTerm = Term::where('is_active', true)->first();
        $fileName = 'Finance_Report_' . ($activeTerm->name ?? 'Term') . '_' . date('Y-m-d') . '.xlsx';

        return Excel::download(new FinanceReportExport(session('active_school')), $fileName);
    }
}
