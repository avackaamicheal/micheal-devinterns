<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('resolveRoute')) {
    function resolveRoute(string $name, mixed $params = []): string
    {
        $user = Auth::user();

        if (!$user) {
            return route($name, $params);
        }

        // Map of generic route names to role-specific ones
        $map = [
            'attendance.index' => [
                'Teacher' => 'teacher.attendance.index',
                'SchoolAdmin' => 'admin.attendance.index'
            ],
            'attendance.store' => ['Teacher' => 'teacher.attendance.store', 'SchoolAdmin' => 'admin.attendance.store'],
            'attendance.export' => ['Teacher' => 'teacher.attendance.export', 'SchoolAdmin' => 'admin.attendance.export'],
            'grades.index' => ['Teacher' => 'teacher.grades.index', 'SchoolAdmin' => 'admin.grades.index'],
            'grades.store' => ['Teacher' => 'teacher.grades.store', 'SchoolAdmin' => 'admin.grades.store'],
            'assessments.index' => ['Teacher' => 'teacher.assessments.index', 'SchoolAdmin' => 'admin.assessments.index'],
            'assessments.store' => ['Teacher' => 'teacher.assessments.store', 'SchoolAdmin' => 'admin.assessments.store'],
            'timetable.index' => ['Teacher' => 'teacher.timetable.index', 'SchoolAdmin' => 'admin.timetable.index'],
            'timetable.store' => ['Teacher' => 'teacher.timetable.store', 'SchoolAdmin' => 'admin.timetable.store'],
            'timetable.destroy' => ['Teacher' => 'teacher.timetable.index', 'SchoolAdmin' => 'admin.timetable.destroy'],
            'reports.index' => ['Teacher' => 'teacher.reports.index', 'SchoolAdmin' => 'admin.reports.index'],
            'reports.single' => ['Teacher' => 'teacher.reports.single', 'SchoolAdmin' => 'admin.reports.single'],
            'reports.batch' => ['Teacher' => 'teacher.reports.batch', 'SchoolAdmin' => 'admin.reports.batch'],
            'announcements.index' => ['Teacher' => 'teacher.announcements.index', 'SchoolAdmin' => 'admin.announcements.index'],
            'announcements.store' => ['Teacher' => 'teacher.announcements.store', 'SchoolAdmin' => 'admin.announcements.store'],
            'announcements.destroy' => ['Teacher' => 'teacher.announcements.destroy', 'SchoolAdmin' => 'admin.announcements.destroy'],
            'messages.index' => ['Teacher' => 'teacher.messages.index', 'SchoolAdmin' => 'admin.messages.index'],
            'messages.show' => ['Teacher' => 'teacher.messages.show', 'SchoolAdmin' => 'admin.messages.show'],
            'messages.store' => ['Teacher' => 'teacher.messages.store', 'SchoolAdmin' => 'admin.messages.store'],
            'messages.thread.create' => ['Teacher' => 'teacher.messages.thread.create', 'SchoolAdmin' => 'admin.messages.thread.create'],
        ];

        // Check if this route needs role-based resolution
        if (isset($map[$name])) {
            foreach ($map[$name] as $role => $resolvedName) {
                if ($user->hasRole($role)) {
                    return route($resolvedName, $params);
                }
            }
            // Role not in map for this route — return # to avoid crash
            return '#';
        }

        // Not a shared route — use as-is if it exists
        try {
            return route($name, $params);
        } catch (\Exception $e) {
            return '#';
        }
    }
}
