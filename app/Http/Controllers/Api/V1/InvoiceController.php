<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use App\Models\Term;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
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
            $invoices = Invoice::with('items')
                ->where('student_id', $user->id)
                ->where('term_id', $activeTerm->id)
                ->withSum('payments', 'amount')
                ->get();

            return $this->success([
                'term' => $activeTerm->name,
                'invoices' => InvoiceResource::collection($invoices),
            ]);
        }

        if ($user->hasRole('Parent')) {
            $children = User::role('Student')
                ->whereHas('studentProfile', fn($q) => $q->where('parent_id', $user->id))
                ->with([
                    'invoices' => fn($q) => $q->where('term_id', $activeTerm->id)
                        ->withSum('payments', 'amount')->with('items')
                ])
                ->get()
                ->map(fn($child) => [
                    'student' => $child->name,
                    'invoices' => InvoiceResource::collection($child->invoices),
                ]);

            return $this->success([
                'term' => $activeTerm->name,
                'children' => $children,
            ]);
        }

        if ($user->hasRole('Teacher')) {
            return $this->unauthorized('Teachers do not have access to invoices.');
        }

        return $this->unauthorized();
    }
}
