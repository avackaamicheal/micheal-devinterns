<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\GradeRecord;
use App\Models\Invoice;
use App\Models\School;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request, School $school)
    {
        $student = Auth::user()->load('studentProfile.section.classLevel');
        $activeTerm = Term::where('is_active', true)->first();

        // 1. Fetch Academic Results
        $grades = GradeRecord::with('subject')
            ->where('student_id', $student->id)
            ->where('term_id', $activeTerm?->id)
            ->get();

        // 2. Fetch Financials
        $invoices = Invoice::where('student_id', $student->id)
            ->where('term_id', $activeTerm?->id)
            ->withSum('payments', 'amount')
            ->get();

        return view('student.dashboard', compact('student', 'activeTerm', 'grades', 'invoices'));
    }
}
