<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SchoolScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // DIRECT implementation. No "addGlobalScope" wrapper needed here.
         // Retrieve the ID set by your Middleware
            $activeSchool = session('active_school');

         // LOGIC FLOW:
            // A. If a Teacher is logged in, middleware set their ID -> Filter applied.
            // B. If SuperAdmin selected a school, middleware set that ID -> Filter applied.
            // C. If SuperAdmin is in "Global View" (session is null) -> No Filter (Show All).
        if ($activeSchool) {
            $builder->where('school_id', $activeSchool);
        }
    }
}
