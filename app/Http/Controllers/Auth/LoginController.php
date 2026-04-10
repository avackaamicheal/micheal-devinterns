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
    public function redirectTo()
    {
        // 1. Get the currently authenticated user
       $user = Auth::user();

        if ($user->hasRole('SuperAdmin')) {
            return route('superadmin.dashboard');
        }

        $slug = trim($user->school->slug ?? '');

        if (!$slug) {
            Auth::logout();
            return route('login');
        }

        if ($user->hasRole('SchoolAdmin')) {
            return route('schooladmin.dashboard', ['school' => $slug]);
        }

        if ($user->hasRole('Teacher')) {
            return route('teacher.dashboard', ['school' => $slug]);
        }

        if ($user->hasRole('Student')) {
            return route('student.dashboard', ['school' => $slug]);
        }

        if ($user->hasRole('Parent')) {
            return route('parent.dashboard', ['school' => $slug]);
        }

        Auth::logout();
        return route('login');
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
