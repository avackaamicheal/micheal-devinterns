@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">My Classes</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            @forelse($sections as $section)
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-users mr-2 text-primary"></i>
                            {{ $section->classLevel->name }} - {{ $section->name }}
                            <span class="badge badge-info ml-2">
                                {{ $section->students->count() }} Students
                            </span>
                        </h3>
                        <div>
                            {{-- Subjects taught in this section --}}
                            @foreach($allocations->where('section_id', $section->id) as $allocation)
                                <span class="badge badge-success mr-1">
                                    {{ $allocation->subject->name }}
                                </span>
                            @endforeach
                            <a href="{{ route('teacher.attendance.index', ['section_id' => $section->id]) }}"
                                class="btn btn-sm btn-outline-success ml-2">
                                <i class="fas fa-user-check"></i> Attendance
                            </a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Admission No</th>
                                    <th>Student Name</th>
                                    <th>Gender</th>
                                    <th>Subjects Graded</th>
                                    <th>Average</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($section->students as $index => $student)
                                    @php
                                        $lockedGrades = $student->grades->where('is_locked', true);
                                        $average = $lockedGrades->count() > 0
                                            ? round($lockedGrades->avg('total_score'), 2)
                                            : null;
                                    @endphp
                                    <tr>
                                        <td class="align-middle">{{ $index + 1 }}</td>
                                        <td class="align-middle">
                                            {{ $student->studentProfile->admission_number ?? 'N/A' }}
                                        </td>
                                        <td class="align-middle font-weight-bold">
                                            {{ $student->name }}
                                        </td>
                                        <td class="align-middle">
                                            {{ $student->studentProfile->gender ?? 'N/A' }}
                                        </td>
                                        <td class="align-middle">
                                            @if($lockedGrades->count() > 0)
                                                <span class="badge badge-success">
                                                    {{ $lockedGrades->count() }} Published
                                                </span>
                                            @elseif($student->grades->count() > 0)
                                                <span class="badge badge-warning">
                                                    {{ $student->grades->count() }} Pending
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">None</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if($average !== null)
                                                <strong>{{ $average }}%</strong>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('teacher.reports.single', $student->id) }}"
                                                class="btn btn-xs btn-outline-danger">
                                                <i class="fas fa-file-pdf"></i> Report Card
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-4 text-muted">
                                            No students in this section.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    You have no classes assigned yet. Contact your School Admin.
                </div>
            @endforelse

        </div>
    </section>
</div>
@endsection
