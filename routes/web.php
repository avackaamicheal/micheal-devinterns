<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Bursar\BursarController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\School\SchoolController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SchoolAdmin\SchoolAdminController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {

        // SchoolAdmin
        Route::get('/schooladmin/dashboard', [SchoolAdminController::class, 'index'])
        ->middleware('role:SchoolAdmin')
        ->name('schooladmin.dashboard');

        // Teacher
    Route::get('/teacher/dashboard', [TeacherController::class, 'index'])
        ->middleware('role:Teacher')
        ->name('teacher.dashboard');

        // Student
    Route::get('/student/dashboard', [StudentController::class, 'index'])
        ->middleware('role:Student')
        ->name('student.dashboard');

        // parent
    Route::get('/parent/dashboard', [ParentController::class, 'index'])
        ->middleware('role:Parent')
        ->name('parent.dashboard');

        //bursar
        Route::get('/bursar/dashboard', [BursarController::class, 'index'])
        ->middleware('role:Bursar')
        ->name('bursar.dashboard');
    });


        // SuperAdmin routes
    Route::middleware(['auth', 'role:SuperAdmin'])
            ->prefix('superadmin')
            ->group(function () {

        Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');
        Route::resource('school', SchoolController::class);


    });
