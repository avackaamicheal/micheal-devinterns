@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header no-print">
        <div class="container-fluid">
            <h1 class="m-0">Timetable Manager</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            @if($errors->any())
                <div class="alert alert-danger no-print">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success no-print">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger no-print">{{ session('error') }}</div>
            @endif

            {{-- Search Forms: Admin Only --}}
            @if(Auth::user()->hasRole('SchoolAdmin'))
                <div class="row no-print">
                    <div class="col-md-6">
                        <div class="card card-outline card-primary">
                            <div class="card-body">
                                <form action="{{ resolveRoute('timetable.index') }}" method="GET" class="d-flex">
                                    <select name="section_id" class="form-control mr-2" required>
                                        <option value="">-- Manage Class Timetable --</option>
                                        @foreach($sections as $section)
                                            <option value="{{ $section->id }}"
                                                {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                                {{ $section->classLevel->name }} - {{ $section->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary">Load</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card card-outline card-info">
                            <div class="card-body">
                                <form action="{{ resolveRoute('timetable.index') }}" method="GET" class="d-flex">
                                    <select name="teacher_id" class="form-control mr-2" required>
                                        <option value="">-- View Teacher Schedule --</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}"
                                                {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-info">Load</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Teacher sees a simple info banner instead --}}
                <div class="alert alert-info no-print">
                    <i class="fas fa-info-circle"></i>
                    Showing your personal timetable for the active term.
                </div>
            @endif

            @if($activeFilter)
                <div class="row">

                    {{-- Add Slot Form: Admin Only, Section View Only --}}
                    @if($activeFilter === 'section' && Auth::user()->hasRole('SchoolAdmin'))
                        <div class="col-md-4 no-print">
                            <div class="card card-info card-outline shadow-sm">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        Add Slot: <strong>{{ $selectedEntity->name }}</strong>
                                    </h3>
                                </div>
                                <form action="{{ resolveRoute('timetable.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="section_id" value="{{ $selectedEntity->id }}">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Subject</label>
                                            <select name="subject_id" class="form-control" required>
                                                <option value="">-- Select Subject --</option>
                                                @foreach($subjects as $subject)
                                                    <option value="{{ $subject->id }}">
                                                        {{ $subject->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Teacher</label>
                                            <select name="teacher_id" class="form-control" required>
                                                <option value="">-- Assign Teacher --</option>
                                                @foreach($teachers as $teacher)
                                                    <option value="{{ $teacher->id }}">
                                                        {{ $teacher->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Day of Week</label>
                                            <select name="day_of_week" class="form-control" required>
                                                @foreach($daysOfWeek as $day)
                                                    <option value="{{ $day }}">{{ $day }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Start Time</label>
                                                    <input type="time" name="start_time"
                                                        class="form-control" required>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>End Time</label>
                                                    <input type="time" name="end_time"
                                                        class="form-control" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-info btn-block">
                                            <i class="fas fa-plus"></i> Add to Timetable
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                    {{-- Timetable Grid --}}
                    <div class="{{ $activeFilter === 'section' && Auth::user()->hasRole('SchoolAdmin') ? 'col-md-8' : 'col-md-12' }}">
                        <div class="card shadow-sm printable-area">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title font-weight-bold">
                                    @if($activeFilter === 'section')
                                        Timetable for:
                                        {{ $selectedEntity->classLevel->name }} - {{ $selectedEntity->name }}
                                    @else
                                        Schedule for: {{ $selectedEntity->name }}
                                        @if(Auth::user()->hasRole('Teacher'))
                                            <span class="badge badge-info ml-2">My Timetable</span>
                                        @else
                                            <span class="badge badge-secondary ml-2">Teacher</span>
                                        @endif
                                    @endif
                                </h3>
                                <div class="card-tools no-print">
                                    <button onclick="window.print()" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-print"></i> Print
                                    </button>
                                    @if(Auth::user()->hasRole('SchoolAdmin'))
                                        <a href="{{ resolveRoute('timetable.index') }}"
                                            class="btn btn-sm btn-default ml-1">Clear</a>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-bordered table-striped m-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 15%" class="text-center">Day</th>
                                            <th>Scheduled Classes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($daysOfWeek as $day)
                                            <tr>
                                                <td class="align-middle text-center font-weight-bold bg-light">
                                                    {{ strtoupper($day) }}
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap">
                                                        @if(isset($timetableGrid[$day]))
                                                            @foreach($timetableGrid[$day] as $slot)
                                                                <div class="border rounded p-2 m-1 bg-white shadow-sm"
                                                                    style="min-width: 160px; border-left: 4px solid #007bff !important;">
                                                                    <strong class="d-block text-primary">
                                                                        {{ $slot->subject->name }}
                                                                    </strong>
                                                                    <small class="text-muted d-block">
                                                                        <i class="far fa-clock"></i>
                                                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} -
                                                                        {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                                    </small>
                                                                    <small class="d-block mt-1 font-weight-bold">
                                                                        @if($activeFilter === 'section')
                                                                            <i class="fas fa-user-tie text-secondary"></i>
                                                                            {{ $slot->teacher->name }}
                                                                        @else
                                                                            <i class="fas fa-users text-secondary"></i>
                                                                            {{ $slot->section->classLevel->name }} -
                                                                            {{ $slot->section->name }}
                                                                        @endif
                                                                    </small>

                                                                    {{-- Delete: Admin Only --}}
                                                                    @if($activeFilter === 'section' && Auth::user()->hasRole('SchoolAdmin'))
                                                                        <form action="{{ resolveRoute('timetable.destroy', $slot->id) }}"
                                                                            method="POST" class="mt-2 no-print">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-xs btn-outline-danger w-100"
                                                                                onclick="return confirm('Remove this slot?')">
                                                                                <i class="fas fa-trash"></i> Remove
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted font-italic p-2">Free slot</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            @else
                <div class="alert alert-default-info text-center py-5 mt-4 no-print shadow-sm">
                    <i class="fas fa-calendar-alt fa-3x text-info mb-3"></i>
                    @if(Auth::user()->hasRole('Teacher'))
                        <h5>You have no timetable entries for the active term yet.</h5>
                        <p class="text-muted">
                            Contact your School Admin to get your schedule set up.
                        </p>
                    @else
                        <h5>Select a Class or Teacher to view and manage their timetable.</h5>
                    @endif
                </div>
            @endif

        </div>
    </section>
</div>

<style>
    @media print {
        .no-print, .main-header, .main-sidebar, .card-footer, .alert {
            display: none !important;
        }
        .content-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
            background-color: white !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .bg-light {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
        }
        .bg-white {
            background-color: #ffffff !important;
            -webkit-print-color-adjust: exact;
        }
        @page { size: landscape; margin: 1cm; }
    }
</style>
@endsection
