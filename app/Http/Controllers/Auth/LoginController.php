<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use illuminate\support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
    *
    * @var string
    */
    // protected $redirectTo = '/login';
    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    public function redirectTo($user)
    {
        // 1. Get the currently authenticated user
        $user = auth()->user();

        // 2. Check Roles and Return Specific Routes
        // Note: Make sure these route names exist in your web.php
        if ($user->hasRole('SuperAdmin')) {
            return route('superadmin.dashboard');
        }

        if ($user->hasRole('SchoolAdmin')) {
            return route('schooladmin.dashboard');
        }

        if ($user->hasRole('Teacher')) {
            // return route('teacher.dashboard'); // Uncomment when you build this
            return '/teacher.dashboard';
        }

        if ($user->hasRole('Student')) {
            // return route('student.dashboard'); // Uncomment when you build this
            return '/student/home';
        }

        if ($user->hasRole('Parent')) {
            return '/parent/home';
        }

        // 3. Default Fallback
        return '/home';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
    */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
