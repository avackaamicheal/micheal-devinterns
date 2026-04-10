<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    // 1. The Smart Roster
    public function index(Request $request, $school = null)
    {
        $teacher = Auth::user();
    $activeTerm = Term::where('is_active', true)->first();

    $allocations = $teacher->allocations()
        ->with(['subject', 'section.classLevel'])
        ->get();

    $sectionIds = $allocations->pluck('section_id')->unique()->toArray();
    $subjectsBySection = $allocations->groupBy('section_id');

    // Security check
    $selectedSectionId = $request->section_id;
    if ($selectedSectionId && !in_array((int) $selectedSectionId, $sectionIds)) {
        abort(403, 'Unauthorized: You are not assigned to this section.');
    }

    // Base query
    $studentsQuery = User::role('Student')
        ->whereHas('studentProfile', function ($q) use ($sectionIds, $selectedSectionId) {
            if ($selectedSectionId) {
                $q->where('section_id', $selectedSectionId);
            } else {
                $q->whereIn('section_id', $sectionIds);
            }
        })
        ->with([
            'studentProfile.section.classLevel',
            'grades' => function ($q) use ($activeTerm, $subjectsBySection, $selectedSectionId, $sectionIds) {
                $subjectIds = [];
                $sectionsToCheck = $selectedSectionId ? [$selectedSectionId] : $sectionIds;
                foreach ($sectionsToCheck as $sectionId) {
                    $ids = $subjectsBySection[$sectionId]?->pluck('subject_id')->toArray() ?? [];
                    $subjectIds = array_merge($subjectIds, $ids);
                }
                $q->where('term_id', $activeTerm?->id)
                  ->whereIn('subject_id', array_unique($subjectIds));
            },
            'parents',
        ]);

    // Search by name or admission number
    if ($request->filled('search')) {
        $search = $request->search;
        $studentsQuery->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhereHas('studentProfile', function ($q) use ($search) {
                  $q->where('admission_number', 'like', "%{$search}%");
              });
        });
    }

    $students = $studentsQuery->get();

    // Sections for filter dropdown
    $sections = Section::with('classLevel')
        ->whereIn('id', $sectionIds)
        ->get();

    return view('teacher.students.index', compact(
        'teacher',
        'students',
        'sections',
        'allocations',
        'subjectsBySection',
        'activeTerm',
        'selectedSectionId'
    ));
    }

    // 2. The 360-Degree Profile Card
    public function show($school, User $student)
    {
        $teacher = Auth::user();
        $assignedSectionIds = $teacher->assignments()->pluck('section_id')->toArray();

        // SECURITY CHECK: Is this student actually in one of the teacher's classes?
        $studentSectionId = $student->studentProfile->section_id ?? null;
        if (!in_array($studentSectionId, $assignedSectionIds)) {
            abort(403, 'Unauthorized: You can only view profiles of students currently enrolled in your classes.');
        }

        // Load necessary relationships (Parent, Grades for the teacher's subjects, etc.)
        $student->load(['studentProfile.section.classLevel', 'studentProfile.parent']);

        // Find out exactly what subjects this teacher teaches this specific student
        $teacherSubjects = $teacher->assignments()
            ->where('section_id', $studentSectionId)
            ->with('subject')
            ->get();

        return view('teacher.students.show', compact('student', 'teacherSubjects'));
    }
}
