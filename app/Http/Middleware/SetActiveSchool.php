<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Traits\HasRoles;

class SetActiveSchool
{

use HasRoles;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if the user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // 2. Logic for School-Level Users (Teachers, Admins, Students, etc.)
            // If the user belongs to a specific school and the session is empty
            if ($user->school_id && !session()->has('active_school_id')) {
                session(['active_school_id' => $user->school_id]);
            }

            // 3. Logic for the SuperAdmin (Head of Education)
            // The SuperAdmin can switch schools. If they don't have one selected,
            // the session 'active_school_id' remains null, allowing global access.
            // Note: You can also implement a check here to ensure a non-SuperAdmin
            // hasn't manually injected a school_id they don't own.
            if (!$user->hasRole('Superadmin') && session('active_school_id') !== $user->school_id) {
                // Force the session back to their actual school for security
                session(['active_school_id' => $user->school_id]);
            }
        }
        return $next($request);
    }
}
