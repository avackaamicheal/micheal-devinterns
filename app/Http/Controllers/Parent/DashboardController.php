<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Invoice;
use App\Models\School;
use App\Models\Term;
use App\Models\Timetable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request, School $school)
    {
        $parent = Auth::user();
        $activeTerm = Term::getActive();

        // Fetch ALL children linked to this parent
        $children = User::role('Student')
            ->whereHas('studentProfile', function ($query) use ($parent) {
                $query->where('parent_id', $parent->id);
            })
            ->with([
                'studentProfile.section.classLevel',
                // Only published grades
                'grades' => function ($q) use ($activeTerm) {
                    $q->where('term_id', $activeTerm?->id)
                      ->where('is_locked', true)
                      ->with('subject');
                },
                // Invoices with payments
                'invoices' => function ($q) use ($activeTerm) {
                    $q->where('term_id', $activeTerm?->id)
                      ->withSum('payments', 'amount')
                      ->with('items');
                },
            ])
            ->get();

        if ($children->isEmpty()) {
            return view('parent.dashboard-empty', compact('parent'));
        }

        $today = Carbon::now()->format('l');

        // Build data for each child
        $childrenData = $children->map(function ($child) use ($activeTerm, $today) {

            $sectionId = $child->studentProfile->section_id;

            // --- ATTENDANCE ---
            // Term attendance
            $termAttendance = Attendance::where('student_id', $child->id)
                ->where('term_id', $activeTerm?->id)
                ->get();

            $termTotal   = $termAttendance->count();
            $termPresent = $termAttendance->where('status', 'PRESENT')->count();
            $termRate    = $termTotal > 0
                ? round(($termPresent / $termTotal) * 100, 1)
                : 0;

            // Monthly attendance breakdown (last 6 months)
            $monthlyBreakdown = collect();
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthRecords = Attendance::where('student_id', $child->id)
                    ->whereYear('date', $month->year)
                    ->whereMonth('date', $month->month)
                    ->get();

                $total   = $monthRecords->count();
                $present = $monthRecords->where('status', 'PRESENT')->count();

                $monthlyBreakdown->push([
                    'month'   => $month->format('M'),
                    'total'   => $total,
                    'present' => $present,
                    'absent'  => $monthRecords->where('status', 'ABSENT')->count(),
                    'late'    => $monthRecords->where('status', 'LATE')->count(),
                    'rate'    => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                ]);
            }

            // Today's attendance
            $todayAttendance = Attendance::where('student_id', $child->id)
                ->where('date', now()->format('Y-m-d'))
                ->first();

            // --- GRADES ---
            $grades       = $child->grades;
            $latestGrade  = $grades->sortByDesc('updated_at')->first();
            $average      = $grades->count() > 0
                ? round($grades->avg('total_score'), 2)
                : null;

            // --- INVOICES ---
            $invoices           = $child->invoices;
            $totalBilled        = $invoices->sum('total_amount');
            $totalPaid          = $invoices->sum('payments_sum_amount');
            $outstandingBalance = $totalBilled - $totalPaid;

            // --- TODAY'S TIMETABLE ---
            $todayClasses = Timetable::with(['subject', 'teacher'])
                ->where('section_id', $sectionId)
                ->where('term_id', $activeTerm?->id)
                ->where('day_of_week', $today)
                ->orderBy('start_time')
                ->get();

            return [
                'student'            => $child,
                'section'            => $child->studentProfile->section,
                'classLevel'         => $child->studentProfile->section->classLevel,
                'termRate'           => $termRate,
                'termTotal'          => $termTotal,
                'termPresent'        => $termPresent,
                'termAbsent'         => $termAttendance->where('status', 'ABSENT')->count(),
                'monthlyBreakdown'   => $monthlyBreakdown,
                'todayAttendance'    => $todayAttendance,
                'grades'             => $grades,
                'latestGrade'        => $latestGrade,
                'average'            => $average,
                'invoices'           => $invoices,
                'totalBilled'        => $totalBilled,
                'totalPaid'          => $totalPaid,
                'outstandingBalance' => $outstandingBalance,
                'todayClasses'       => $todayClasses,
            ];
        });

        // Overall stats across all children
        $totalOutstanding = $childrenData->sum('outstandingBalance');
        $unreadMessages   = \App\Models\MessageThread::where(function ($q) use ($parent) {
                $q->where('user_one_id', $parent->id)
                  ->orWhere('user_two_id', $parent->id);
            })
            ->with(['messages' => function ($q) use ($parent) {
                $q->where('sender_id', '!=', $parent->id)
                  ->whereNull('read_at');
            }])
            ->get()
            ->sum(fn($thread) => $thread->messages->count());

        return view('parent.dashboard', compact(
            'parent',
            'children',
            'childrenData',
            'activeTerm',
            'totalOutstanding',
            'unreadMessages'
        ));
    }
}
