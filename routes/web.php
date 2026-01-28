<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Bursar\BursarController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\School\SchoolController;
use App\Http\Controllers\SchoolContextController;
use App\Http\Controllers\Section\SectionController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\ClassLevel\ClassLevelController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SchoolAdmin\SchoolAdminController;
use App\Http\Controllers\Subject\SubjectController;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/',function () {
    return view('auth.login');
});

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
    Route::middleware(['auth', 'role:SuperAdmin', 'tenant'])
            ->prefix('superadmin')
            ->group(function () {

        Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');
        Route::resource('school', SchoolController::class);
        Route::post('/school-context',SchoolContextController::class)->name('school.context');


    });

        // SchoolAdmin routes
    Route::middleware(['auth', 'role:SchoolAdmin', 'tenant'])
            ->prefix('schooladmin')
            ->group(function () {

        Route::get('/dashboard', [SchoolAdminController::class, 'index'])->name('schooladmin.dashboard');
        Route::resource('classLevel', ClassLevelController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('section', SectionController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('subject', SubjectController::class)->only(['index', 'store', 'update', 'destroy']);


    });
