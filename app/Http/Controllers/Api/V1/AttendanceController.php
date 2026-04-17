<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AttendanceResource;
use App\Models\Attendance;
use App\Models\Term;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
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
            $records = Attendance::where('student_id', $user->id)
                ->where('term_id', $activeTerm->id)
                ->orderBy('date', 'desc')
                ->get();

            $total = $records->count();
            $present = $records->where('status', 'PRESENT')->count();
            $absent = $records->where('status', 'ABSENT')->count();
            $late = $records->where('status', 'LATE')->count();
            $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            return $this->success([
                'term' => $activeTerm->name,
                'summary' => compact('total', 'present', 'absent', 'late', 'rate'),
                'records' => AttendanceResource::collection($records),
            ]);
        }

        if ($user->hasRole('Parent')) {
            $children = User::role('Student')
                ->whereHas('studentProfile', fn($q) => $q->where('parent_id', $user->id))
                ->get()
                ->map(function ($child) use ($activeTerm) {
                    $records = Attendance::where('student_id', $child->id)
                        ->where('term_id', $activeTerm->id)
                        ->orderBy('date', 'desc')
                        ->get();

                    $total = $records->count();
                    $present = $records->where('status', 'PRESENT')->count();
                    $absent = $records->where('status', 'ABSENT')->count();
                    $late = $records->where('status', 'LATE')->count();
                    $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

                    return [
                        'student' => $child->name,
                        'summary' => compact('total', 'present', 'absent', 'late', 'rate'),
                        'records' => AttendanceResource::collection($records),
                    ];
                });

            return $this->success([
                'term' => $activeTerm->name,
                'children' => $children,
            ]);
        }

        if ($user->hasRole('Teacher')) {
            $sectionIds = $user->allocations()->pluck('section_id')->toArray();

            $summary = [];
            foreach ($sectionIds as $sectionId) {
                $records = Attendance::where('section_id', $sectionId)
                    ->where('term_id', $activeTerm->id)
                    ->get();

                $total = $records->count();
                $present = $records->where('status', 'PRESENT')->count();
                $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

                $summary[] = [
                    'section_id' => $sectionId,
                    'total' => $total,
                    'present' => $present,
                    'rate' => $rate,
                ];
            }

            return $this->success([
                'term' => $activeTerm->name,
                'summary' => $summary,
            ]);
        }

        return $this->unauthorized();
    }
}
