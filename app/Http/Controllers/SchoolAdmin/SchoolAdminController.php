<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassLevel;
use App\Models\School;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Carbon\Carbon;

class SchoolAdminController extends Controller
{
    public function index(School $school)
    {
        // Stats
        $studentCount = StudentProfile::count();

        $staffCount = TeacherProfile::count();

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
            'labels'  => $days,
            'present' => $presentData,
            'absent'  => $absentData,
            'late'    => $lateData,
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
}
