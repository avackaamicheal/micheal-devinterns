@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">Daily Attendance</h1>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                <div class="card card-default">
                    <div class="card-body">
                        <form action="{{ route('attendance.index') }}" method="GET" class="form-inline">
                            <label class="mr-2">Class Section:</label>
                            <select name="section_id" class="form-control mr-3" required>
                                <option value="">-- Choose Class --</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section->id }}"
                                        {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->classLevel->name }} - {{ $section->name }}
                                    </option>
                                @endforeach
                            </select>

                            <label class="mr-2">Date:</label>
                            <input type="date" name="date" class="form-control mr-3" value="{{ $date }}"
                                max="{{ date('Y-m-d') }}" required>

                            <button type="submit" class="btn btn-primary">Load Register</button>
                        </form>
                    </div>
                </div>

                @if ($selectedSection)
                    <div class="card card-primary card-outline">

                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">
                                Marking Attendance for: <strong>{{ $selectedSection->classLevel->name }} -
                                    {{ $selectedSection->name }}</strong> on
                                <strong>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</strong>
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('attendance.export', ['section_id' => $selectedSection->id, 'date' => $date]) }}"
                                    class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-file-excel"></i> Export to Excel
                                </a>
                            </div>
                        </div>

                        <form action="{{ route('attendance.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="section_id" value="{{ $selectedSection->id }}">
                            <input type="hidden" name="date" value="{{ $date }}">

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">#</th>
                                            <th style="width: 15%">Admission No</th>
                                            <th style="width: 25%">Student Name</th>
                                            <th style="width: 35%">Attendance Status</th>
                                            <th style="width: 20%">Remarks (Optional)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($students as $index => $student)
                                            @php
                                                $currentStatus = isset($attendances[$student->id])
                                                    ? $attendances[$student->id]->status
                                                    : 'PRESENT';
                                                $currentRemark = isset($attendances[$student->id])
                                                    ? $attendances[$student->id]->remarks
                                                    : '';
                                            @endphp
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $student->studentProfile->admission_number ?? 'N/A' }}</td>
                                                <td class="font-weight-bold">{{ $student->name }}</td>
                                                <td>
                                                    <div class="icheck-success d-inline mr-3">
                                                        <input type="radio" name="attendance[{{ $student->id }}]"
                                                            id="present_{{ $student->id }}" value="PRESENT"
                                                            {{ $currentStatus == 'PRESENT' ? 'checked' : '' }}>
                                                        <label for="present_{{ $student->id }}">Present</label>
                                                    </div>
                                                    <div class="icheck-danger d-inline mr-3">
                                                        <input type="radio" name="attendance[{{ $student->id }}]"
                                                            id="absent_{{ $student->id }}" value="ABSENT"
                                                            {{ $currentStatus == 'ABSENT' ? 'checked' : '' }}>
                                                        <label for="absent_{{ $student->id }}">Absent</label>
                                                    </div>
                                                    <div class="icheck-warning d-inline">
                                                        <input type="radio" name="attendance[{{ $student->id }}]"
                                                            id="late_{{ $student->id }}" value="LATE"
                                                            {{ $currentStatus == 'LATE' ? 'checked' : '' }}>
                                                        <label for="late_{{ $student->id }}">Late</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="remarks[{{ $student->id }}]"
                                                        class="form-control form-control-sm"
                                                        placeholder="e.g. Sick, Traffic" value="{{ $currentRemark }}">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted p-4">No students found in
                                                    this class section.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($students->count() > 0)
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-save"></i> Save Attendance
                                    </button>
                                </div>
                            @endif
                        </form>

                    </div>
                @endif

            </div>
        </section>
    </div>
@endsection
