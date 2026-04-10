<?php
namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request, $school = null)
    {
        $parent = Auth::user();

        // 1. Fetch ALL children linked to this parent
        $children = User::role('Student')
            ->whereHas('studentProfile', function($query) use ($parent) {
                $query->where('parent_id', $parent->id);
            })
            ->with(['studentProfile.section.classLevel'])
            ->get();

        // If no children are linked, show an empty state
        if ($children->isEmpty()) {
            return view('parent.dashboard-empty', compact('parent'));
        }

        // 2. Determine the "Active" Child (Default to the first one)
        $activeChildId = $request->child_id ?? $children->first()->id;
        $activeChild = $children->firstWhere('id', $activeChildId);

        if (!$activeChild) {
            $activeChild = $children->first();
        }

        $sectionId = $activeChild->studentProfile->section_id;

        // 3. Data for the Active Child
        $today = now()->format('Y-m-d');

        // Last Attendance (Checks if attendance was marked today)
        $lastAttendance = Attendance::where('student_id', $activeChild->id)
            ->where('date', $today)
            ->first();

        // --- NEW VARIABLES FOR THE SUMMARY CARDS ---

        // Dynamic mock balance: If it's the first child, they owe money. Otherwise, 0.
        $outstandingBalance = $activeChild->id == $children->first()->id ? 15000 : 0;

        $attendanceRate = 92; // 92% attendance this term

        $pendingTasks = 2; // 2 homework assignments due

        $unreadMessages = 1;

        $latestGrade = (object)[
            'score' => '88%',
            'subject' => (object)['name' => 'Physics'],
            'created_at' => now()->subDays(1)
        ];

        // Today's Classes (Mocked Timeline)
        $todayClasses = collect([
            (object)[ 'start_time' => '08:00 AM', 'end_time' => '09:00 AM', 'subject' => (object)['name' => 'Mathematics'], 'teacher' => (object)['name' => 'Mr. Roberts'] ],
            (object)[ 'start_time' => '09:30 AM', 'end_time' => '10:30 AM', 'subject' => (object)['name' => 'Physics'], 'teacher' => (object)['name' => 'Mrs. Davis'] ],
        ]);

        // Recent Grades Feed
        $recentGrades = collect([
            (object)['subject' => (object)['name' => 'Mathematics'], 'score' => '92%', 'assessment_type' => 'Mid-Term', 'created_at' => now()->subDays(5)],
            (object)['subject' => (object)['name' => 'English'], 'score' => '85%', 'assessment_type' => 'Homework', 'created_at' => now()->subWeeks(1)],
        ]);

        return view('parent.dashboard', compact(
            'parent', 'children', 'activeChild',
            'outstandingBalance', 'attendanceRate', 'pendingTasks',
            'latestGrade', 'todayClasses', 'recentGrades',
            'lastAttendance', 'unreadMessages'
        ));
    }
}
