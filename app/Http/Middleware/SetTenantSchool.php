<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetTenantSchool
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Grab the school from the route parameter (e.g., 'springfield-high')
        $schoolSlug = $request->route('school');
        //dd($schoolSlug, gettype($schoolSlug));

    // Explicitly resolve from DB instead of relying on route model binding
    if ($schoolSlug) {
        // Route model binding already resolved it to a model
        $school = $schoolSlug instanceof \App\Models\School
            ? $schoolSlug
            : \App\Models\School::where('slug', $schoolSlug)->firstOrFail();
        session(['active_school' => $school->id]);
        URL::defaults(['school' => $school->slug]);

        // Rebind the resolved model back onto the route
        $request->route()->setParameter('school', $school);
    }
        return $next($request);
    }
}
