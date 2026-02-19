<?php

use App\Http\Controllers\Academic\AcademicSettingsController;
use App\Http\Controllers\Academic\AttendanceController;
use App\Http\Controllers\Academic\ClassroomAssignmentController;
use App\Http\Controllers\Academic\StudentAdmissionController;
use App\Http\Controllers\Academic\TimetableController;
use App\Http\Controllers\Bursar\BursarController;
use App\Http\Controllers\ClassLevel\ClassLevelController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\School\SchoolController;
use App\Http\Controllers\SchoolAdmin\SchoolAdminController;
use App\Http\Controllers\SchoolContextController;
use App\Http\Controllers\Section\SectionController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Subject\SubjectController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\Teacher\TeacherController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', function () {
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
        Route::post('/school-context', SchoolContextController::class)->name('school.context');


    });

// SchoolAdmin routes
Route::middleware(['auth', 'role:SchoolAdmin', 'tenant'])
    ->prefix('schooladmin')
    ->group(function () {

        Route::get('/dashboard', [SchoolAdminController::class, 'index'])->name('schooladmin.dashboard');
        Route::resource('classLevel', ClassLevelController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('section', SectionController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('subject', SubjectController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('students/export', [StudentAdmissionController::class, 'export'])->name('students.export');
        Route::post('students/import', [StudentAdmissionController::class, 'import'])->name('students.import');
        Route::get('students/template', [StudentAdmissionController::class, 'downloadTemplate'])->name('students.template');
        Route::resource('student', StudentAdmissionController::class)->only('index', 'create', 'store', 'destroy');
        Route::resource('classassignment', ClassroomAssignmentController::class)->only('index', 'create', 'destroy');
        // Academic Settings
        Route::get('/academic-settings', [AcademicSettingsController::class, 'index'])->name('academic-settings.index');

        Route::post('/academic-sessions', [AcademicSettingsController::class, 'storeSession'])->name('academic-sessions.store');
        Route::post('/academic-sessions/{academicSession}/activate', [AcademicSettingsController::class, 'activateSession'])->name('academic-sessions.activate');

        Route::post('/terms', [AcademicSettingsController::class, 'storeTerm'])->name('terms.store');
        Route::post('/terms/{term}/activate', [AcademicSettingsController::class, 'activateTerm'])->name('terms.activate');
        // Timetable Routes
        Route::get('/timetable', [TimetableController::class, 'index'])->name('timetable.index');
        Route::post('/timetable', [TimetableController::class, 'store'])->name('timetable.store');
        Route::delete('/timetable/{timetable}', [TimetableController::class, 'destroy'])->name('timetable.destroy');
        // Attendance Routes
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    });
