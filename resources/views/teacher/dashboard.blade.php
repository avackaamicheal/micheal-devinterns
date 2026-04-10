@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="m-0">Welcome, {{ $teacher->name }}!</h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ $activeTerm?->name ?? 'No Active Term' }} &nbsp;|&nbsp;
                            {{ now()->format('l, F j, Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                {{-- AT A GLANCE STATS --}}
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $totalStudents }}</h3>
                                <p>Total Students</p>
                            </div>
                            <div class="icon"><i class="fas fa-users"></i></div>
                            <a href="{{ route('teacher.grades.index') }}" class="small-box-footer">
                                View Grades <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $totalClasses }}</h3>
                                <p>Assigned Classes</p>
                            </div>
                            <div class="icon"><i class="fas fa-chalkboard"></i></div>
                            <a href="{{ route('teacher.timetable.index') }}" class="small-box-footer">
                                View Timetable <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box {{ $pendingGrades > 0 ? 'bg-warning' : 'bg-success' }}">
                            <div class="inner">
                                <h3>{{ $pendingGrades > 0 ? $pendingGrades : 'All Done' }}</h3>
                                <p>Pending Grade Entries</p>
                            </div>
                            <div class="icon"><i class="fas fa-edit"></i></div>
                            <a href="{{ route('teacher.grades.index') }}" class="small-box-footer">
                                Enter Grades <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box {{ $attendanceRate >= 75 ? 'bg-success' : 'bg-danger' }}">
                            <div class="inner">
                                <h3>{{ $attendanceRate }}%</h3>
                                <p>Attendance Rate (This Month)</p>
                            </div>
                            <div class="icon"><i class="fas fa-user-check"></i></div>
                            <a href="{{ route('teacher.attendance.index') }}" class="small-box-footer">
                                Take Attendance <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">

                    {{-- LEFT COLUMN --}}
                    <div class="col-md-8">

                        {{-- TODAY'S SCHEDULE --}}
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-calendar-day mr-1"></i>
                                    Today's Schedule
                                    <span class="badge badge-primary ml-2">{{ now()->format('l') }}</span>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                @if ($todayClasses->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover m-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Time</th>
                                                    <th>Subject</th>
                                                    <th>Class</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($todayClasses as $slot)
                                                    @php
                                                        $start = \Carbon\Carbon::parse($slot->start_time);
                                                        $end = \Carbon\Carbon::parse($slot->end_time);
                                                        $isNow = now()->between($start, $end);
                                                    @endphp
                                                    <tr class="{{ $isNow ? 'table-success' : '' }}">
                                                        <td class="align-middle">
                                                            @if ($isNow)
                                                                <span class="badge badge-success mr-1">NOW</span>
                                                            @endif
                                                            {{ $start->format('h:i A') }} -
                                                            {{ $end->format('h:i A') }}
                                                        </td>
                                                        <td class="align-middle font-weight-bold">
                                                            {{ $slot->subject->name }}
                                                        </td>
                                                        <td class="align-middle">
                                                            {{ $slot->section->classLevel->name }} -
                                                            {{ $slot->section->name }}
                                                        </td>
                                                        <td class="align-middle">
                                                            <a href="{{ route('teacher.attendance.index', ['section_id' => $slot->section_id]) }}"
                                                                class="btn btn-xs btn-outline-success mr-1">
                                                                <i class="fas fa-user-check"></i> Attendance
                                                            </a>
                                                            <a href="{{ route('teacher.grades.index', ['section_id' => $slot->section_id, 'subject_id' => $slot->subject_id]) }}"
                                                                class="btn btn-xs btn-outline-primary">
                                                                <i class="fas fa-edit"></i> Grades
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center p-4 text-muted">
                                        <i class="fas fa-coffee fa-2x mb-2"></i>
                                        <p>No classes scheduled for today.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ASSIGNED CLASSES --}}
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chalkboard-teacher mr-1"></i>
                                    My Assigned Classes
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover m-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Subject</th>
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th>Attendance Rate</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($allocations as $allocation)
                                                @php
                                                    $summary = $attendanceSummary[$allocation->section_id] ?? [
                                                        'rate' => 0,
                                                        'total' => 0,
                                                    ];
                                                    $rate = $summary['rate'];
                                                    $rateClass =
                                                        $rate >= 75 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
                                                @endphp
                                                <tr>
                                                    <td class="align-middle">
                                                        <span class="badge badge-info p-2">
                                                            {{ $allocation->subject->name }}
                                                        </span>
                                                    </td>
                                                    <td class="align-middle">
                                                        {{ $allocation->section->classLevel->name }}
                                                    </td>
                                                    <td class="align-middle">
                                                        {{ $allocation->section->name }}
                                                    </td>
                                                    <td class="align-middle" style="min-width: 150px;">
                                                        <div class="progress" style="height: 8px;">
                                                            <div class="progress-bar bg-{{ $rateClass }}"
                                                                style="width: {{ $rate }}%"></div>
                                                        </div>
                                                        <small class="text-{{ $rateClass }}">
                                                            {{ $rate }}%
                                                            ({{ $summary['total'] }} records)
                                                        </small>
                                                    </td>
                                                    <td class="align-middle">
                                                        <a href="{{ route('teacher.attendance.index', ['section_id' => $allocation->section_id]) }}"
                                                            class="btn btn-xs btn-outline-success mr-1">
                                                            <i class="fas fa-user-check"></i>
                                                        </a>
                                                        <a href="{{ route('teacher.grades.index', ['section_id' => $allocation->section_id, 'subject_id' => $allocation->subject_id]) }}"
                                                            class="btn btn-xs btn-outline-primary">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center p-4">
                                                        <div class="alert alert-warning mb-0">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                                            No classes assigned yet. Contact your School Admin.
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT COLUMN --}}
                    <div class="col-md-4">

                        {{-- QUICK ACTIONS --}}
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-bolt mr-1"></i> Quick Actions
                                </h3>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('teacher.attendance.index') }}" class="btn btn-block btn-outline-success mb-2">
                                    <i class="fas fa-user-check mr-2"></i> Take Attendance
                                </a>
                                <a href="{{ route('teacher.grades.index') }}" class="btn btn-block btn-outline-primary mb-2">
                                    <i class="fas fa-edit mr-2"></i> Enter Grades
                                </a>
                                <a href="{{ route('teacher.timetable.index') }}" class="btn btn-block btn-outline-info mb-2">
                                    <i class="fas fa-calendar-week mr-2"></i> View Timetable
                                </a>
                                <a href="{{ route('teacher.announcements.index') }}"
                                    class="btn btn-block btn-outline-warning mb-2">
                                    <i class="fas fa-bullhorn mr-2"></i> Post Announcement
                                </a>
                                <a href="{{ route('teacher.messages.index') }}" class="btn btn-block btn-outline-secondary">
                                    <i class="fas fa-envelope mr-2"></i> Messages
                                    @if ($unreadMessages > 0)
                                        <span class="badge badge-danger ml-1">{{ $unreadMessages }}</span>
                                    @endif
                                </a>
                            </div>
                        </div>

                        {{-- RECENT ACTIVITY --}}
                        <div class="card card-outline card-warning">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-history mr-1"></i> Recent Activity
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">

                                    {{-- Last Attendance --}}
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-success mr-3 p-2">
                                                <i class="fas fa-user-check"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-bold text-sm">Last Attendance Taken</div>
                                                @if ($lastAttendance)
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($lastAttendance->date)->format('M d, Y') }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">No attendance recorded yet</small>
                                                @endif
                                            </div>
                                        </div>
                                    </li>

                                    {{-- Last Grade Entry --}}
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-primary mr-3 p-2">
                                                <i class="fas fa-edit"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-bold text-sm">Last Grade Submitted</div>
                                                @if ($lastGrade)
                                                    <small class="text-muted">
                                                        {{ $lastGrade->subject->name }} &mdash;
                                                        {{ $lastGrade->updated_at->diffForHumans() }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">No grades submitted yet</small>
                                                @endif
                                            </div>
                                        </div>
                                    </li>

                                    {{-- Unread Messages --}}
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="badge badge-{{ $unreadMessages > 0 ? 'danger' : 'secondary' }} mr-3 p-2">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-bold text-sm">Unread Messages</div>
                                                <small class="text-muted">
                                                    @if ($unreadMessages > 0)
                                                        <a href="{{ route('messages.index') }}">
                                                            {{ $unreadMessages }} unread message(s)
                                                        </a>
                                                    @else
                                                        All caught up!
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
