<?php

namespace App\Http\Controllers\Academic;

use App\Exports\DailyAttendanceExport;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Section;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $sections = Section::with('classLevel')->get();
        $selectedSection = null;
        $students = collect();
        $attendances = [];

        $date = $request->date ?? date('Y-m-d');

        if ($request->has('section_id') && $request->section_id != '') {
            $selectedSection = Section::find($request->section_id);

            $students = User::role('Student')->whereHas('studentProfile', function ($query) use ($selectedSection) {
                $query->where('section_id', $selectedSection->id);
            })->with('studentProfile')->get();

            $attendances = Attendance::where('section_id', $selectedSection->id)
                ->where('date', $date)
                ->get()
                ->keyBy('student_id');
        }

        return view('academics.attendance.index', compact('sections', 'selectedSection', 'students', 'date', 'attendances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'date' => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
            'attendance.*' => 'in:PRESENT,ABSENT,LATE',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string|max:255',
        ]);

        $activeTerm = Term::where('is_active', true)->first();
        if (!$activeTerm) {
            return back()->with('error', 'Please set an active Term in Academic Settings first.');
        }

        foreach ($request->attendance as $studentId => $status) {
            $remark = $request->remarks[$studentId] ?? null;

            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $request->date,
                ],
                [
                    'term_id' => $activeTerm->id,
                    'section_id' => $request->section_id,
                    'status' => $status,
                    'remarks' => $remark,
                ]
            );
        }

        return back()->with('success', 'Attendance recorded successfully!');
    }

    public function export(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'date' => 'required|date',
        ]);

        $section = Section::with('classLevel')->findOrFail($request->section_id);
        $fileName = str_replace(' ', '_', $section->classLevel->name . '_' . $section->name) . '_Attendance_' . $request->date . '.xlsx';

        return Excel::download(new DailyAttendanceExport($request->section_id, $request->date), $fileName);
    }
}
