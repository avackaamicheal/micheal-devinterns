<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimetableRequest;
use App\Models\School;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimetableController extends Controller
{
    public function index(Request $request, School $school)
    {
        $user = Auth::user();
        $allowedSectionIds = $user->allowedSectionIds();

        // Sections filtered by what the user is allowed to see
        $sections = Section::with('classLevel')
            ->whereIn('id', $allowedSectionIds)
            ->get();

        // Teachers — admins see all, teachers only see themselves
        $teachers = $user->hasRole('Teacher')
            ? User::where('id', $user->id)->get()
            : User::role('Teacher')
            ->where('school_id', session('active_school'))
            ->get();

        $subjects = Subject::all();
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $timetableGrid = [];
        $activeFilter = null;
        $selectedEntity = null;
        $activeTerm = Term::where('is_active', true)
        ->where('school_id', session('active_school'))
        ->first();

        // SCENARIO A: Searching by Class Section
        if ($request->has('section_id') && $request->section_id != '') {

            // Security check
            if (!in_array((int) $request->section_id, $allowedSectionIds)) {
                abort(403, 'Unauthorized: You are not assigned to this classroom.');
            }

            $activeFilter = 'section';
            $selectedEntity = Section::with('classLevel')->find($request->section_id);

            if ($activeTerm && $selectedEntity) {
                $rawTimetable = Timetable::with(['subject', 'teacher'])
                    ->where('term_id', $activeTerm->id)
                    ->where('section_id', $selectedEntity->id)
                    ->orderBy('start_time')
                    ->get();

                $timetableGrid = $rawTimetable->groupBy('day_of_week');
            }
        }

        // SCENARIO B: Searching by Teacher
        elseif ($request->has('teacher_id') && $request->teacher_id != '') {

            // Teachers can only view their own timetable
            if ($user->hasRole('Teacher') && $request->teacher_id != $user->id) {
                abort(403, 'Unauthorized: You can only view your own timetable.');
            }

            $activeFilter = 'teacher';
            $selectedEntity = User::find($request->teacher_id);

            if ($activeTerm && $selectedEntity) {
                $rawTimetable = Timetable::with(['subject', 'section.classLevel'])
                    ->where('term_id', $activeTerm->id)
                    ->where('teacher_id', $selectedEntity->id)
                    ->orderBy('start_time')
                    ->get();

                $timetableGrid = $rawTimetable->groupBy('day_of_week');
            }
        }

        // Auto-load teacher's own timetable if they land on the page
        elseif ($user->hasRole('Teacher')) {
            $activeFilter = 'teacher';
            $selectedEntity = $user;

            if ($activeTerm) {
                $rawTimetable = Timetable::with(['subject', 'section.classLevel'])
                    ->where('term_id', $activeTerm->id)
                    ->where('teacher_id', $user->id)
                    ->orderBy('start_time')
                    ->get();

                $timetableGrid = $rawTimetable->groupBy('day_of_week');
            }
        }

        return view('academics.timetable.index', compact(
            'sections',
            'subjects',
            'teachers',
            'daysOfWeek',
            'timetableGrid',
            'activeFilter',
            'selectedEntity'
        ));
    }

    public function store(StoreTimetableRequest $request, School $school)
    {
        $activeTerm = Term::where('is_active', true)
        ->where('school_id', session('active_school'))
        ->first();

        if (!$activeTerm) {
            return back()->with('error', 'Please set an active Term in Academic Settings first.');
        }

        // Teacher conflict check
        $teacherConflict = Timetable::where('term_id', $activeTerm->id)
            ->where('teacher_id', $request->teacher_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->exists();

        if ($teacherConflict) {
            return back()->with('error', 'Schedule Conflict: This teacher already has a class during this time on ' . $request->day_of_week);
        }

        // Section conflict check
        $sectionConflict = Timetable::where('term_id', $activeTerm->id)
            ->where('section_id', $request->section_id)
            ->where('day_of_week', $request->day_of_week)
            ->where('start_time', '<', $request->end_time)
            ->where('end_time', '>', $request->start_time)
            ->exists();

        if ($sectionConflict) {
            return back()->with('error', 'Schedule Conflict: This section already has a class during this time on ' . $request->day_of_week);
        }

        Timetable::create([
            'term_id' => $activeTerm->id,
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return back()->with('success', 'Timetable slot created successfully!');
    }

    public function destroy(School $school, Timetable $timetable)
    {
        $timetable->delete();
        return back()->with('success', 'Timetable slot removed.');
    }
}
