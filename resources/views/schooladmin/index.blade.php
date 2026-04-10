@extends('layouts.app')

@section('content')
    <div class="content-wrapper">

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">School Overview</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                {{-- Stats Cards --}}
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $studentCount }}</h3>
                                <p>Total Students</p>
                            </div>
                            <div class="icon"><i class="fas fa-user-graduate"></i></div>
                            <a href="{{ route('student.index') }}" class="small-box-footer">
                                View List <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $staffCount }}</h3>
                                <p>Teaching Staff</p>
                            </div>
                            <div class="icon"><i class="fas fa-chalkboard-teacher"></i></div>
                            <a href="{{ route('teachers.index') }}" class="small-box-footer">
                                Manage Staff <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $classCount }}</h3>
                                <p>Active Classes</p>
                            </div>
                            <div class="icon"><i class="fas fa-layer-group"></i></div>
                            <a href="{{ route('classLevel.index') }}" class="small-box-footer">
                                View Classes <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $subjectCount }}</h3>
                                <p>Subjects Offered</p>
                            </div>
                            <div class="icon"><i class="fas fa-book"></i></div>
                            <a href="{{ route('subject.index') }}" class="small-box-footer">
                                Curriculum <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <section class="col-lg-7 connectedSortable">

                        {{-- Attendance Chart --}}
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-bar mr-1"></i>
                                    Weekly Attendance
                                </h3>
                                <div class="card-tools">
                                    <div class="d-flex align-items-center">
                                        <span class="mr-3">
                                            <i class="fas fa-circle text-success mr-1"></i> Present
                                        </span>
                                        <span class="mr-3">
                                            <i class="fas fa-circle text-danger mr-1"></i> Absent
                                        </span>
                                        <span>
                                            <i class="fas fa-circle text-warning mr-1"></i> Late
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @php
                                    $totalThisWeek = collect($attendanceChart['present'])->sum()
                                        + collect($attendanceChart['absent'])->sum()
                                        + collect($attendanceChart['late'])->sum();
                                    $presentThisWeek = collect($attendanceChart['present'])->sum();
                                    $attendanceRate = $totalThisWeek > 0
                                        ? round(($presentThisWeek / $totalThisWeek) * 100, 1)
                                        : 0;
                                @endphp

                                {{-- Summary badges --}}
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="text-center">
                                        <div class="h4 font-weight-bold text-success mb-0">
                                            {{ collect($attendanceChart['present'])->sum() }}
                                        </div>
                                        <small class="text-muted">Present</small>
                                    </div>
                                    <div class="text-center">
                                        <div class="h4 font-weight-bold text-danger mb-0">
                                            {{ collect($attendanceChart['absent'])->sum() }}
                                        </div>
                                        <small class="text-muted">Absent</small>
                                    </div>
                                    <div class="text-center">
                                        <div class="h4 font-weight-bold text-warning mb-0">
                                            {{ collect($attendanceChart['late'])->sum() }}
                                        </div>
                                        <small class="text-muted">Late</small>
                                    </div>
                                    <div class="text-center">
                                        <div class="h4 font-weight-bold text-primary mb-0">
                                            {{ $attendanceRate }}%
                                        </div>
                                        <small class="text-muted">Rate</small>
                                    </div>
                                </div>

                                <div style="position: relative; height: 250px;">
                                    <canvas id="attendanceChart"></canvas>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route('admin.attendance.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-arrow-right"></i> View Full Attendance
                                </a>
                            </div>
                        </div>

                        {{-- Quick Actions --}}
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-bolt mr-1"></i> Quick Actions
                                </h3>
                            </div>
                            <div class="card-body">
                                <a href="{{ route('student.create') }}">
                                    <button class="btn btn-app bg-success">
                                        <i class="fas fa-user-plus"></i> Add Student
                                    </button>
                                </a>
                                <a href="{{ route('admin.announcements.index') }}">
                                    <button class="btn btn-app bg-info">
                                        <i class="fas fa-bullhorn"></i> Announcement
                                    </button>
                                </a>
                                <a href="{{ route('fees.index') }}">
                                    <button class="btn btn-app bg-warning">
                                        <i class="fas fa-file-invoice"></i> Collect Fees
                                    </button>
                                </a>
                                <a href="{{ route('admin.attendance.index') }}">
                                    <button class="btn btn-app bg-danger">
                                        <i class="fas fa-user-check"></i> Attendance
                                    </button>
                                </a>
                            </div>
                        </div>

                    </section>

                    <section class="col-lg-5 connectedSortable">

                        {{-- Academic Calendar --}}
                        <div class="card bg-gradient-success">
                            <div class="card-header border-0">
                                <h3 class="card-title">
                                    <i class="far fa-calendar-alt"></i> Academic Calendar
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-success btn-sm"
                                        data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div id="calendar" style="width: 100%"></div>
                            </div>
                        </div>

                        {{-- Recent Classes --}}
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Classes Added</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="products-list product-list-in-card pl-2 pr-2">
                                    @forelse($classes as $class)
                                        <li class="item">
                                            <div class="product-info ml-2">
                                                <a href="javascript:void(0)" class="product-title">
                                                    {{ $class->name }}
                                                    <span class="badge badge-primary float-right">
                                                        {{ $class->sections->count() ?? 0 }} Sections
                                                    </span>
                                                </a>
                                                <span class="product-description">
                                                    Added on {{ $class->created_at->format('M d, Y') }}
                                                </span>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="item p-3 text-muted text-center">
                                            No classes added yet.
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route('classLevel.index') }}" class="btn btn-sm btn-outline-primary">
                                    View All Classes
                                </a>
                            </div>
                        </div>

                    </section>
                </div>

            </div>
        </section>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('attendanceChart').getContext('2d');

        const labels = @json($attendanceChart['labels']);
        const presentData = @json($attendanceChart['present']);
        const absentData = @json($attendanceChart['absent']);
        const lateData = @json($attendanceChart['late']);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Present',
                        data: presentData,
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Absent',
                        data: absentData,
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Late',
                        data: lateData,
                        backgroundColor: 'rgba(255, 193, 7, 0.8)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            afterBody: function(context) {
                                const index = context[0].dataIndex;
                                const total = presentData[index] + absentData[index] + lateData[index];
                                const rate = total > 0
                                    ? Math.round((presentData[index] / total) * 100)
                                    : 0;
                                return `Attendance Rate: ${rate}%`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    }
                }
            }
        });
    });
</script>

@endsection
