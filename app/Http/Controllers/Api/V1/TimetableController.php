<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\TimetableResource;
use App\Models\Term;
use App\Models\Timetable;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $user = $request->user();

        $activeTerm = Term::where('school_id', $user->school_id)
            ->where('is_active', true)
            ->first();

        if (!$activeTerm) {
            return $this->notFound('No active term found.');
        }

        $query = Timetable::with(['subject', 'teacher', 'section.classLevel'])
            ->where('term_id', $activeTerm->id);

        if ($user->hasRole('Teacher')) {
            $query->where('teacher_id', $user->id);
        } elseif ($user->hasRole('Student')) {
            $sectionId = $user->studentProfile?->section_id;
            if (!$sectionId) {
                return $this->notFound('No class section assigned.');
            }
            $query->where('section_id', $sectionId);
        } else {
            return $this->unauthorized('Parents do not have a timetable.');
        }

        $timetable = TimetableResource::collection(
            $query->orderBy('day_of_week')->orderBy('start_time')->get()
        )->groupBy('day');

        return $this->success([
            'term' => $activeTerm->name,
            'timetable' => $timetable,
        ]);
    }
}
