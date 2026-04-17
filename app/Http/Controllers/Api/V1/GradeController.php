<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\GradeResource;
use App\Models\GradeRecord;
use App\Models\Term;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
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

        if ($user->hasRole('Student')) {
            $grades = GradeRecord::with('subject')
                ->where('student_id', $user->id)
                ->where('term_id', $activeTerm->id)
                ->where('is_locked', true)
                ->get();

            return $this->success([
                'term' => $activeTerm->name,
                'grades' => GradeResource::collection($grades),
                'average' => round($grades->avg('total_score'), 2),
            ]);
        }

        if ($user->hasRole('Teacher')) {
            $sectionIds = $user->allocations()->pluck('section_id')->toArray();

            $grades = GradeRecord::with(['subject', 'student'])
                ->where('term_id', $activeTerm->id)
                ->whereIn('section_id', $sectionIds)
                ->get()
                ->groupBy('section_id')
                ->map(fn($records) => GradeResource::collection($records));

            return $this->success([
                'term' => $activeTerm->name,
                'grades' => $grades,
            ]);
        }

        if ($user->hasRole('Parent')) {
            $children = User::role('Student')
                ->whereHas('studentProfile', fn($q) => $q->where('parent_id', $user->id))
                ->with([
                    'grades' => fn($q) => $q->where('term_id', $activeTerm->id)
                        ->where('is_locked', true)->with('subject')
                ])
                ->get()
                ->map(fn($child) => [
                    'student' => $child->name,
                    'grades' => GradeResource::collection($child->grades),
                    'average' => round($child->grades->avg('total_score'), 2),
                ]);

            return $this->success([
                'term' => $activeTerm->name,
                'children' => $children,
            ]);
        }

        return $this->unauthorized();
    }
}
