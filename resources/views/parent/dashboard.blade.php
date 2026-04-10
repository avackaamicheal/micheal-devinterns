@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header border-bottom mb-4 bg-white">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="m-0 font-weight-bold"><i class="fas fa-home text-primary mr-2"></i> Family Portal</h1>
                        <p class="text-muted mb-0">
                            Welcome back, {{ $parent->name }} &nbsp;|&nbsp; {{ now()->format('l, F j, Y') }}
                        </p>
                    </div>

                    {{-- THE MASTER TOGGLE (CHILD SELECTOR) --}}
                    @if(isset($children) && $children->count() > 1)
                        <form action="{{ route('parent.dashboard') }}" method="GET" class="form-inline m-0">
                            <label class="mr-2 font-weight-bold text-primary"><i class="fas fa-child mr-1"></i> Viewing:</label>
                            <select name="child_id" class="form-control border-primary text-primary font-weight-bold shadow-sm" onchange="this.form.submit()" style="border-radius: 8px;">
                                @foreach($children as $child)
                                    <option value="{{ $child->id }}" {{ $activeChild->id == $child->id ? 'selected' : '' }}>
                                        {{ $child->name }} ({{ $child->studentProfile->section->name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <h5 class="text-primary font-weight-bold m-0"><i class="fas fa-child mr-1"></i> Viewing: {{ $activeChild->name ?? 'Student' }}</h5>
                    @endif
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                {{-- AT A GLANCE STATS (4 Cards) --}}
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box {{ $outstandingBalance > 0 ? 'bg-danger' : 'bg-success' }}">
                            <div class="inner">
                                <h3>₦{{ number_format($outstandingBalance) }}</h3>
                                <p>{{ $outstandingBalance > 0 ? 'Outstanding Fees' : 'All Fees Paid' }}</p>
                            </div>
                            <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                            <a href="#" class="small-box-footer">
                                {{ $outstandingBalance > 0 ? 'Pay Now' : 'View Receipts' }} <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box {{ $attendanceRate >= 75 ? 'bg-info' : 'bg-warning' }}">
                            <div class="inner">
                                <h3>{{ $attendanceRate }}%</h3>
                                <p>Term Attendance Rate</p>
                            </div>
                            <div class="icon"><i class="fas fa-user-check"></i></div>
                            <a href="#" class="small-box-footer">
                                View Attendance Log <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box {{ $pendingTasks > 0 ? 'bg-warning' : 'bg-success' }}">
                            <div class="inner">
                                <h3>{{ $pendingTasks > 0 ? $pendingTasks : 'None' }}</h3>
                                <p>Pending Assignments</p>
                            </div>
                            <div class="icon"><i class="fas fa-tasks"></i></div>
                            <a href="#" class="small-box-footer">
                                View Homework <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $latestGrade->score ?? 'N/A' }}</h3>
                                <p>Latest Grade ({{ $latestGrade->subject->name ?? 'Pending' }})</p>
                            </div>
                            <div class="icon"><i class="fas fa-graduation-cap"></i></div>
                            <a href="#" class="small-box-footer">
                                View Academic Record <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">

                    {{-- LEFT COLUMN (Main Info) --}}
                    <div class="col-md-8">

                        {{-- TODAY'S SCHEDULE (For the Active Child) --}}
                        <div class="card card-outline card-primary shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-calendar-day mr-1 text-primary"></i>
                                    {{ $activeChild->name }}'s Schedule Today
                                    <span class="badge badge-primary ml-2">{{ now()->format('l') }}</span>
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                @if(isset($todayClasses) && $todayClasses->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover m-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Time</th>
                                                    <th>Subject</th>
                                                    <th>Teacher</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($todayClasses as $slot)
                                                    @php
                                                        $start = \Carbon\Carbon::parse($slot->start_time);
                                                        $end = \Carbon\Carbon::parse($slot->end_time);
                                                        $isNow = now()->between($start, $end);
                                                    @endphp
                                                    <tr class="{{ $isNow ? 'table-success' : '' }}">
                                                        <td class="align-middle">
                                                            @if($isNow)
                                                                <span class="badge badge-success mr-1">NOW</span>
                                                            @endif
                                                            {{ $start->format('h:i A') }} - {{ $end->format('h:i A') }}
                                                        </td>
                                                        <td class="align-middle font-weight-bold">{{ $slot->subject->name }}</td>
                                                        <td class="align-middle">{{ $slot->teacher->name ?? 'TBA' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center p-4 text-muted">
                                        <i class="fas fa-school fa-2x mb-2"></i>
                                        <p>No classes scheduled for today.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- RECENT GRADES --}}
                        <div class="card card-outline card-info shadow-sm">
                            <div class="card-header border-0">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-chart-line mr-1 text-info"></i>
                                    Recent Grades
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped table-hover m-0">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Type</th>
                                            <th>Score</th>
                                            <th>Date Uploaded</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentGrades as $grade)
                                            <tr>
                                                <td class="font-weight-bold">{{ $grade->subject->name }}</td>
                                                <td>{{ $grade->assessment_type }}</td>
                                                <td><span class="badge badge-primary" style="font-size: 1em;">{{ $grade->score }}</span></td>
                                                <td class="text-muted">{{ $grade->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center p-4 text-muted">No recent grades posted.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer text-center">
                                <a href="#" class="uppercase font-weight-bold">Download Full Report Card</a>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT COLUMN (Actions & Activity) --}}
                    <div class="col-md-4">

                        {{-- QUICK ACTIONS --}}
                        <div class="card card-outline card-success shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-bolt mr-1 text-success"></i> Quick Actions
                                </h3>
                            </div>
                            <div class="card-body">
                                <a href="#" class="btn btn-block btn-outline-danger mb-2 text-left font-weight-bold">
                                    <i class="fas fa-credit-card mr-2"></i> Pay School Fees
                                </a>
                                {{-- <a href="{{ route('messages.index') }}" class="btn btn-block btn-outline-success mb-2 text-left font-weight-bold">
                                    <i class="fas fa-comments mr-2"></i> Message Teachers
                                </a>
                                <a href="{{ route('announcements.index') }}" class="btn btn-block btn-outline-info mb-2 text-left font-weight-bold">
                                    <i class="fas fa-bullhorn mr-2"></i> View Announcements
                                </a> --}}
                                <a href="#" class="btn btn-block btn-outline-primary text-left font-weight-bold">
                                    <i class="fas fa-calendar-minus mr-2"></i> Report Absence
                                </a>
                            </div>
                        </div>

                        {{-- RECENT ACTIVITY --}}
                        <div class="card card-outline card-warning shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">
                                    <i class="fas fa-history mr-1 text-warning"></i> Recent Activity
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">

                                    {{-- Today's Attendance Pulse --}}
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $attStatus = $lastAttendance->status ?? 'Pending';
                                                $attColor = $attStatus == 'PRESENT' ? 'success' : ($attStatus == 'ABSENT' ? 'danger' : 'secondary');
                                            @endphp
                                            <span class="badge badge-{{ $attColor }} mr-3 p-2">
                                                <i class="fas fa-user-check"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-bold text-sm">Today's Attendance</div>
                                                <small class="text-muted">{{ ucfirst(strtolower($attStatus)) }}</small>
                                            </div>
                                        </div>
                                    </li>

                                    {{-- Last Grade Entry --}}
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-primary mr-3 p-2">
                                                <i class="fas fa-file-alt"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-bold text-sm">New Grade Posted</div>
                                                @if(isset($latestGrade))
                                                    <small class="text-muted">{{ $latestGrade->subject->name ?? 'Subject' }} &mdash; {{ $latestGrade->created_at->diffForHumans() }}</small>
                                                @else
                                                    <small class="text-muted">No new grades</small>
                                                @endif
                                            </div>
                                        </div>
                                    </li>

                                    {{-- Unread Messages --}}
                                    <li class="list-group-item">
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-{{ $unreadMessages > 0 ? 'danger' : 'secondary' }} mr-3 p-2">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <div>
                                                <div class="font-weight-bold text-sm">Unread Messages</div>
                                                <small class="text-muted">
                                                    @if($unreadMessages > 0)
                                                        {{-- <a href="{{ route('messages.index') }}">{{ $unreadMessages }} unread message(s)</a> --}}
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
