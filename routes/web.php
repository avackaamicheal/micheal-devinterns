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
use App\Http\Controllers\Parent\DashboardController as ParentDashboard;
use App\Http\Controllers\School\SchoolController;
use App\Http\Controllers\SchoolAdmin\SchoolAdminController;
use App\Http\Controllers\SchoolContextController;
use App\Http\Controllers\Section\SectionController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
//use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Subject\SubjectController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Teacher\StudentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', function () {
    return view('auth.login');
});

// SuperAdmin routes (no school prefix)
Route::middleware(['auth', 'role:SuperAdmin', 'tenant'])
    ->prefix('superadmin')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('superadmin.dashboard');
        Route::resource('school', SchoolController::class);
        Route::post('/school-context', SchoolContextController::class)->name('school.context');
        // School Admins
        Route::get('/admins', [SchoolAdminController::class, 'index'])->name('superadmin.admins.index');
        Route::get('/admins/create', [SchoolAdminController::class, 'create'])->name('superadmin.admins.create');
        Route::post('/admins', [SchoolAdminController::class, 'store'])->name('superadmin.admins.store');
        Route::get('/admins/{admin}/edit', [SchoolAdminController::class, 'edit'])->name('superadmin.admins.edit');
        Route::put('/admins/{admin}', [SchoolAdminController::class, 'update'])->name('superadmin.admins.update');
        Route::delete('/admins/{admin}', [SchoolAdminController::class, 'destroy'])->name('superadmin.admins.destroy');
    });

Route::middleware(['auth', 'active'])
    ->prefix('{school}')
    ->group(function () {

        // -----------------------------------------------
        // SCHOOL ADMIN ONLY ROUTES
        // -----------------------------------------------
        Route::middleware(['role:SchoolAdmin'])
            ->group(function () {
            Route::get('/dashboard', [SchoolAdminController::class, 'dashboard'])->name('schooladmin.dashboard');
            Route::resource('classLevel', ClassLevelController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::resource('section', SectionController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::resource('subject', SubjectController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::get('/students/generate-admission-number', [StudentAdmissionController::class, 'generateAdmissionNumber'])->name('students.generate-admission');
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
            // Finance
            Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
            Route::post('/fees', [FeeController::class, 'store'])->name('fees.store');
            Route::post('/invoices/generate', [FeeController::class, 'generateInvoices'])->name('invoices.generate');
            Route::get('/invoices', [PaymentController::class, 'index'])->name('invoices.index');
            Route::post('/invoices/{invoice}/pay', [PaymentController::class, 'store'])->name('payments.store');
            Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
            Route::get('/finance/reports', [ReportController::class, 'index'])->name('finance.reports.index');
            Route::get('/finance/reports/export', [ReportController::class, 'export'])->name('finance.reports.export');
            // Teachers management
            Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
            Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
            Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
            Route::get('/teachers/assignments', [TeacherController::class, 'assignments'])->name('teachers.assignments');
            Route::post('/teachers/{teacher}/assign', [TeacherController::class, 'assign'])->name('teachers.assign');
            Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
            Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
            Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
            Route::delete('/teachers/{teacher}/allocations/{allocation}', [TeacherController::class, 'destroyAllocation'])->name('teachers.allocations.destroy');

            //school
            Route::get('/school-profile', [SchoolController::class, 'show'])->name('school.profile');
            Route::put('/school-profile', [SchoolController::class, 'update'])->name('school.profile.update');
            // Bursar
            Route::get('/bursar/dashboard', [BursarController::class, 'index'])->name('bursar.dashboard');
        });

        // -----------------------------------------------
        // SCHOOL ADMIN PREFIXED ROUTES
        // URL: /{school}/admin/...
        // -----------------------------------------------
        Route::middleware(['role:SchoolAdmin'])
            ->prefix('admin')
            ->group(function () {
            Route::get('/timetable', [TimetableController::class, 'index'])->name('admin.timetable.index');
            Route::post('/timetable', [TimetableController::class, 'store'])->name('admin.timetable.store');
            Route::delete('/timetable/{timetable}', [TimetableController::class, 'destroy'])->name('admin.timetable.destroy');
            Route::get('/attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
            Route::post('/attendance', [AttendanceController::class, 'store'])->name('admin.attendance.store');
            Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('admin.attendance.export');
            Route::get('/grades', [GradeEntryController::class, 'index'])->name('admin.grades.index');
            Route::post('/grades', [GradeEntryController::class, 'store'])->name('admin.grades.store');
            Route::get('/assessments', [AssessmentWeightController::class, 'index'])->name('admin.assessments.index');
            Route::post('/assessments', [AssessmentWeightController::class, 'store'])->name('admin.assessments.store');
            Route::get('/reports', [ReportCardController::class, 'index'])->name('admin.reports.index');
            Route::get('/reports/student/{id}', [ReportCardController::class, 'downloadSingle'])->name('admin.reports.single');
            Route::get('/reports/batch/{section_id}', [ReportCardController::class, 'downloadBatch'])->name('admin.reports.batch');
            Route::get('/announcements', [AnnouncementController::class, 'index'])->name('admin.announcements.index');
            Route::post('/announcements', [AnnouncementController::class, 'store'])->name('admin.announcements.store');
            Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');
            Route::get('/messages', [MessageController::class, 'index'])->name('admin.messages.index');
            Route::get('/messages/{thread}', [MessageController::class, 'index'])->name('admin.messages.show');
            Route::post('/messages/{thread}', [MessageController::class, 'store'])->name('admin.messages.store');
            Route::post('/messages/thread/create', [MessageController::class, 'createThread'])->name('admin.messages.thread.create');
        });

        // -----------------------------------------------
        // TEACHER PREFIXED ROUTES
        // URL: /{school}/teacher/...
        // -----------------------------------------------
        Route::middleware(['role:Teacher'])
            ->prefix('teacher')
            ->group(function () {
            Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('teacher.dashboard');
            Route::get('/classes', [TeacherDashboard::class, 'myClasses'])->name('teacher.classes');
            Route::get('/my-students', [StudentController::class, 'index'])->name('teacher.students');
            Route::get('/profile', [TeacherDashboard::class, 'profile'])->name('teacher.profile');
            Route::put('/profile', [TeacherDashboard::class, 'updateProfile'])->name('teacher.profile.update');
            Route::get('/timetable', [TimetableController::class, 'index'])->name('teacher.timetable.index');
            Route::get('/attendance', [AttendanceController::class, 'index'])->name('teacher.attendance.index');
            Route::post('/attendance', [AttendanceController::class, 'store'])->name('teacher.attendance.store');
            Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('teacher.attendance.export');
            Route::get('/grades', [GradeEntryController::class, 'index'])->name('teacher.grades.index');
            Route::post('/grades', [GradeEntryController::class, 'store'])->name('teacher.grades.store');
            Route::get('/assessments', [AssessmentWeightController::class, 'index'])->name('teacher.assessments.index');
            Route::post('/assessments', [AssessmentWeightController::class, 'store'])->name('teacher.assessments.store');
            Route::get('/reports', [ReportCardController::class, 'index'])->name('teacher.reports.index');
            Route::get('/reports/student/{id}', [ReportCardController::class, 'downloadSingle'])->name('teacher.reports.single');
            Route::get('/announcements', [AnnouncementController::class, 'index'])->name('teacher.announcements.index');
            Route::post('/announcements', [AnnouncementController::class, 'store'])->name('teacher.announcements.store');
            Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('teacher.announcements.destroy');
            Route::get('/messages', [MessageController::class, 'index'])->name('teacher.messages.index');
            Route::get('/messages/{thread}', [MessageController::class, 'index'])->name('teacher.messages.show');
            Route::post('/messages/{thread}', [MessageController::class, 'store'])->name('teacher.messages.store');
            Route::post('/messages/thread/create', [MessageController::class, 'createThread'])->name('teacher.messages.thread.create');
        });

        // -----------------------------------------------
        // STUDENT ROUTES
        // -----------------------------------------------
        Route::middleware(['role:Student'])
            ->prefix('student')
            ->group(function () {
            Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('student.dashboard');
        });

        // -----------------------------------------------
        // PARENT ROUTES
        // -----------------------------------------------
        Route::middleware(['role:Parent'])
            ->prefix('parent')
            ->group(function () {
            Route::get('/dashboard', [ParentDashboard::class, 'index'])->name('parent.dashboard');
            Route::post('/invoices/{invoice}/pay', [PaymentController::class, 'store'])->name('parent.payments.store');
            Route::get('/reports/student/{id}', [ReportCardController::class, 'downloadSingle'])->name('parent.reports.single');


            Route::get('/announcements', [AnnouncementController::class, 'index'])->name('parent.announcements.index');

        });
    });
