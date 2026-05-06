<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolAdminRequest;
use App\Http\Requests\UpdateSchoolAdminRequest;
use App\Models\Attendance;
use App\Models\ClassLevel;
use App\Models\School;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SchoolAdminController extends Controller
{
    public function dashboard(School $school)
    {
        // Stats
        $studentCount = User::role('Student')
            ->where('school_id', $school->id)
            ->count();

        $staffCount = User::role('Teacher')
            ->where('school_id', $school->id)
            ->count();

        $classCount = ClassLevel::count();

        $subjectCount = Subject::count();

        $classes = ClassLevel::with('sections')->latest()->take(5)->get();

        // Weekly attendance data for chart
        // Get last 7 days
        $days = collect();
        $presentData = collect();
        $absentData = collect();
        $lateData = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayLabel = Carbon::now()->subDays($i)->format('D d/m');

            $days->push($dayLabel);

            $presentData->push(
                Attendance::whereDate('date', $date)
                    ->where('status', 'PRESENT')
                    ->count()
            );

            $absentData->push(
                Attendance::whereDate('date', $date)
                    ->where('status', 'ABSENT')
                    ->count()
            );

            $lateData->push(
                Attendance::whereDate('date', $date)
                    ->where('status', 'LATE')
                    ->count()
            );
        }

        $attendanceChart = [
            'labels' => $days,
            'present' => $presentData,
            'absent' => $absentData,
            'late' => $lateData,
        ];

        return view('schooladmin.index', compact(
            'school',
            'studentCount',
            'staffCount',
            'classCount',
            'subjectCount',
            'classes',
            'attendanceChart'
        ));
    }

    public function index()
    {
        $admins = User::role('SchoolAdmin')
            ->with('school')
            ->latest()
            ->get();

        $schools = School::all();

        return view('superadmin.admins.index', compact('admins', 'schools'));
    }

    public function create()
    {
        $schools = School::all();
        return view('superadmin.admins.create', compact('schools'));
    }

    public function store(StoreSchoolAdminRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'school_id' => $request->school_id,
        ]);

        $user->assignRole('SchoolAdmin');

        return redirect()->route('superadmin.admins.index')
            ->with('success', 'School Admin created successfully!');
    }

    public function edit(User $admin)
    {
        $schools = School::all();
        return view('superadmin.admins.edit', compact('admin', 'schools'));
    }

    public function update(UpdateSchoolAdminRequest $request, User $admin)
    {
        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'school_id' => $request->school_id,
        ]);

        if ($request->filled('password')) {
            $admin->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('superadmin.admins.index')
            ->with('success', 'School Admin updated successfully!');
    }

    public function destroy(User $admin)
    {
        // Prevent deleting yourself
        if ($admin->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        return back()->with('success', 'School Admin removed successfully!');
    }

}
