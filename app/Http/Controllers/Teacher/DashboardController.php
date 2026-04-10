<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\GradeRecord;
use App\Models\School;
use App\Models\Term;
use App\Models\Timetable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index(Request $request, School $school)
    {
        $teacher = Auth::user();
        $activeTerm = Term::where('is_active', true)->first();

        $allocations = $teacher->allocations()
            ->with(['subject', 'section.classLevel'])
            ->get();

        $sectionIds = $allocations->pluck('section_id')->unique()->toArray();

        // Stats
        $totalStudents = User::role('Student')
            ->whereHas('studentProfile', function ($q) use ($sectionIds) {
                $q->whereIn('section_id', $sectionIds);
            })->count();

        $totalClasses = $allocations->count();

        $gradedCount = GradeRecord::where('term_id', $activeTerm?->id)
            ->whereIn('section_id', $sectionIds)
            ->where('is_locked', true)
            ->distinct('subject_id')
            ->count('subject_id');
        $pendingGrades = $totalClasses - $gradedCount;

        $totalAttendance = Attendance::whereIn('section_id', $sectionIds)
            ->whereMonth('date', now()->month)
            ->count();
        $presentAttendance = Attendance::whereIn('section_id', $sectionIds)
            ->whereMonth('date', now()->month)
            ->where('status', 'PRESENT')
            ->count();
        $attendanceRate = $totalAttendance > 0
            ? round(($presentAttendance / $totalAttendance) * 100, 1)
            : 0;

        // Today's schedule
        $today = Carbon::now()->format('l');
        $todayClasses = Timetable::with(['subject', 'section.classLevel'])
            ->where('teacher_id', $teacher->id)
            ->where('term_id', $activeTerm?->id)
            ->where('day_of_week', $today)
            ->orderBy('start_time')
            ->get();

        // Recent activity
        $lastAttendance = Attendance::whereIn('section_id', $sectionIds)
            ->latest()->first();

        $lastGrade = GradeRecord::with('subject')
            ->whereIn('section_id', $sectionIds)
            ->where('term_id', $activeTerm?->id)
            ->latest()->first();

        $unreadMessages = \App\Models\MessageThread::where(function ($q) use ($teacher) {
                $q->where('user_one_id', $teacher->id)
                  ->orWhere('user_two_id', $teacher->id);
            })
            ->with(['messages' => function ($q) use ($teacher) {
                $q->where('sender_id', '!=', $teacher->id)->whereNull('read_at');
            }])
            ->get()
            ->sum(fn($thread) => $thread->messages->count());

        // Attendance summary per section
        $attendanceSummary = [];
        foreach ($sectionIds as $sectionId) {
            $total = Attendance::where('section_id', $sectionId)
                ->where('term_id', $activeTerm?->id)->count();
            $present = Attendance::where('section_id', $sectionId)
                ->where('term_id', $activeTerm?->id)
                ->where('status', 'PRESENT')->count();
            $attendanceSummary[$sectionId] = [
                'rate'    => $total > 0 ? round(($present / $total) * 100, 1) : 0,
                'total'   => $total,
                'present' => $present,
            ];
        }

        return view('teacher.dashboard', compact(
            'teacher', 'activeTerm', 'allocations',
            'totalStudents', 'totalClasses', 'pendingGrades',
            'attendanceRate', 'todayClasses', 'lastAttendance',
            'lastGrade', 'unreadMessages', 'attendanceSummary'
        ));
    }

    public function myClasses(Request $request, School $school)
    {
        $teacher = Auth::user();
        $activeTerm = Term::where('is_active', true)->first();

        $allocations = $teacher->allocations()
            ->with(['subject', 'section.classLevel'])
            ->get();

        $sectionIds = $allocations->pluck('section_id')->unique()->toArray();

        // Load students per section with their grades
        $sections = \App\Models\Section::with('classLevel')
            ->whereIn('id', $sectionIds)
            ->get()
            ->map(function ($section) use ($activeTerm) {
                $section->students = User::role('Student')
                    ->whereHas('studentProfile', function ($q) use ($section) {
                        $q->where('section_id', $section->id);
                    })
                    ->with([
                        'studentProfile',
                        'grades' => function ($q) use ($activeTerm) {
                            $q->where('term_id', $activeTerm?->id);
                        }
                    ])
                    ->get();
                return $section;
            });

        return view('teacher.classes', compact('teacher', 'allocations', 'sections', 'activeTerm'));
    }

    public function profile(Request $request, School $school)
    {
        $teacher = Auth::user()->load('teacherProfile');
        return view('teacher.profile', compact('teacher'));
    }

    public function updateProfile(Request $request, School $school)
    {
        $teacher = Auth::user();

        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $teacher->id,
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:500',
            'date_of_birth'   => 'nullable|date',
            'gender'          => 'nullable|in:Male,Female,Other',
            'marital_status'  => 'nullable|in:Single,Married,Divorced,Widowed',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'        => 'nullable|min:8|confirmed',
        ]);

        $teacher->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $teacher->update(['password' => Hash::make($request->password)]);
        }

        $picturePath = $teacher->teacherProfile->profile_picture;
        if ($request->hasFile('profile_picture')) {
            if ($picturePath) {
                Storage::disk('public')->delete($picturePath);
            }
            $picturePath = $request->file('profile_picture')
                ->store('teacher-profiles', 'public');
        }

        $teacher->teacherProfile->update([
            'phone'          => $request->phone,
            'address'        => $request->address,
            'date_of_birth'  => $request->date_of_birth,
            'gender'         => $request->gender,
            'marital_status' => $request->marital_status,
            'profile_picture' => $picturePath,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }
}
