<?php

use App\Http\Controllers\Academic\AcademicSettingsController;
use App\Http\Controllers\Academic\AssessmentWeightController;
use App\Http\Controllers\Academic\AttendanceController;
use App\Http\Controllers\Academic\ClassroomAssignmentController;
use App\Http\Controllers\Academic\GradeEntryController;
use App\Http\Controllers\Academic\ReportCardController;
use App\Http\Controllers\Academic\StudentAdmissionController;
use App\Http\Controllers\Academic\TimetableController;
use App\Http\Controllers\Bursar\BursarController;
use App\Http\Controllers\ClassLevel\ClassLevelController;
use App\Http\Controllers\Communication\AnnouncementController;
use App\Http\Controllers\Communication\MessageController;
use App\Http\Controllers\Finance\FeeController;
use App\Http\Controllers\Finance\PaymentController;
use App\Http\Controllers\Finance\ReportController;
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

Route::middleware(['auth', 'active', 'role:SchoolAdmin'])
    // SchoolAdmin routes
    ->prefix('{school}')
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
        // Assessment Settings
        Route::get('/assessments', [AssessmentWeightController::class, 'index'])->name('assessments.index');
        Route::post('/assessments', [AssessmentWeightController::class, 'store'])->name('assessments.store');
        // Grade Entry
        Route::get('/grades', [GradeEntryController::class, 'index'])->name('grades.index');
        Route::post('/grades', [GradeEntryController::class, 'store'])->name('grades.store');
        // Report Cards
        Route::get('/reports', [ReportCardController::class, 'index'])->name('reports.index');
        Route::get('/reports/student/{id}', [ReportCardController::class, 'downloadSingle'])->name('reports.single');
        Route::get('/reports/batch/{section_id}', [ReportCardController::class, 'downloadBatch'])->name('reports.batch');
        // Finance & Fees
        Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
        Route::post('/fees', [FeeController::class, 'store'])->name('fees.store');
        Route::post('/invoices/generate', [FeeController::class, 'generateInvoices'])->name('invoices.generate');
        // Invoices & Payments
        Route::get('/invoices', [PaymentController::class, 'index'])->name('invoices.index');
        Route::post('/invoices/{invoice}/pay', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
        // Finance Reports
        Route::get('/finance/reports', [ReportController::class, 'index'])->name('finance.reports.index');
        Route::get('/finance/reports/export', [ReportController::class, 'export'])->name('finance.reports.export');
        // Announcements
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        // Messaging
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{thread}', [MessageController::class, 'index'])->name('messages.show');
        Route::post('/messages/{thread}', [MessageController::class, 'store'])->name('messages.store');
        Route::post('/messages/thread/create', [MessageController::class, 'createThread'])->name('messages.thread.create');
        // Teachers
        Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
        Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/assignments', [TeacherController::class, 'assignments'])->name('teachers.assignments');
        Route::post('/teachers/{teacher}/assign', [TeacherController::class, 'assign'])->name('teachers.assign');
        Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    });



