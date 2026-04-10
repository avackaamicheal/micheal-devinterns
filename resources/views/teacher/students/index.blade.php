@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Student Directory</h1>
                        <p class="text-muted mb-0">
                            Showing students from your assigned classes
                        </p>
                    </div>
                    <div class="col-sm-6">
                        <span class="float-right badge badge-info p-2 mt-2">
                            {{ $students->count() }} Students
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                {{-- Filters --}}
                <div class="card card-default">
                    <div class="card-body">
                        <form action="{{ route('teacher.students') }}" method="GET">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label>Search Student</label>
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Name or admission number..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-4">
                                    <label>Filter by Section</label>
                                    <select name="section_id" class="form-control">
                                        <option value="">-- All My Sections --</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}"
                                                {{ $selectedSectionId == $section->id ? 'selected' : '' }}>
                                                {{ $section->classLevel->name }} - {{ $section->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary mr-2">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    @if (request('search') || request('section_id'))
                                        <a href="{{ route('teacher.students') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Student Table --}}
                <div class="card card-outline card-primary">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="fas fa-users mr-2"></i>
                            @if ($selectedSectionId)
                                @php $selectedSection = $sections->find($selectedSectionId); @endphp
                                {{ $selectedSection?->classLevel->name }} - {{ $selectedSection?->name }}
                            @else
                                All My Students
                            @endif
                        </h3>
                        <div>
                            {{-- Subject badges --}}
                            @foreach ($allocations->unique('subject_id') as $allocation)
                                <span class="badge badge-success mr-1">
                                    {{ $allocation->subject->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 15%">Admission No</th>
                                    <th style="width: 20%">Name</th>
                                    <th style="width: 10%">Gender</th>
                                    <th style="width: 15%">Class / Section</th>
                                    <th style="width: 20%">My Subject Grades</th>
                                    <th style="width: 10%">Average</th>
                                    <th style="width: 10%" class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $student)
                                    @php
                                        $lockedGrades = $student->grades->where('is_locked', true);
                                        $average =
                                            $lockedGrades->count() > 0
                                                ? round($lockedGrades->avg('total_score'), 2)
                                                : null;
                                        $averageClass =
                                            $average >= 70 ? 'success' : ($average >= 50 ? 'warning' : 'danger');
                                        $studentSectionId = $student->studentProfile->section_id;
                                    @endphp
                                    <tr>
                                        <td class="align-middle">{{ $index + 1 }}</td>
                                        <td class="align-middle">
                                            <span class="badge badge-secondary">
                                                {{ $student->studentProfile->admission_number ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="align-middle font-weight-bold">
                                            {{ $student->name }}
                                        </td>
                                        <td class="align-middle">
                                            <span
                                                class="badge badge-{{ $student->studentProfile->gender == 'Male' ? 'info' : 'danger' }}">
                                                {{ $student->studentProfile->gender ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge badge-primary p-2">
                                                {{ $student->studentProfile->section->classLevel->name ?? 'N/A' }}
                                                - {{ $student->studentProfile->section->name ?? '' }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            @forelse($student->grades as $grade)
                                                <div class="mb-1">
                                                    <small class="text-muted">
                                                        {{ $grade->subject->name ?? '' }}:
                                                    </small>
                                                    @if ($grade->is_locked)
                                                        <span class="badge badge-success">
                                                            {{ $grade->total_score }}%
                                                        </span>
                                                    @else
                                                        <span class="badge badge-warning">
                                                            Draft
                                                        </span>
                                                    @endif
                                                </div>
                                            @empty
                                                <span class="text-muted text-sm">No grades yet</span>
                                            @endforelse
                                        </td>
                                        <td class="align-middle">
                                            @if ($average !== null)
                                                <span class="badge badge-{{ $averageClass }} p-2">
                                                    {{ $average }}%
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-right">
                                            {{-- Enter grades --}}
                                            @foreach ($subjectsBySection[$studentSectionId] ?? [] as $allocation)
                                                <a href="{{ route('teacher.grades.index', [
                                                    'section_id' => $studentSectionId,
                                                    'subject_id' => $allocation->subject_id,
                                                ]) }}"
                                                    class="btn btn-xs btn-outline-primary mb-1"
                                                    title="Enter {{ $allocation->subject->name }} grades">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endforeach

                                            {{-- Message parent --}}
                                            @if ($student->parents->count() > 0)
                                                <a href="{{ route('teacher.messages.index') }}"
                                                    class="btn btn-xs btn-outline-success mb-1" title="Message Parent">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                            @endif

                                            {{-- Report card --}}
                                            <a href="{{ route('teacher.reports.single', $student->id) }}"
                                                class="btn btn-xs btn-outline-primary mb-1" title="Download Report Card">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center p-4">
                                            @if (request('search'))
                                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                                <p class="text-muted">
                                                    No students found matching
                                                    <strong>"{{ request('search') }}"</strong>
                                                </p>
                                            @else
                                                <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                                <p class="text-muted">No students found.</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer Stats --}}
                    @if ($students->count() > 0)
                        @php
                            $withGrades = $students
                                ->filter(fn($s) => $s->grades->where('is_locked', true)->count() > 0)
                                ->count();
                            $withParents = $students->filter(fn($s) => $s->parents->count() > 0)->count();
                            $maleCount = $students->filter(fn($s) => $s->studentProfile->gender == 'Male')->count();
                            $femaleCount = $students->filter(fn($s) => $s->studentProfile->gender == 'Female')->count();
                        @endphp
                        <div class="card-footer bg-light d-flex justify-content-between text-sm text-muted">
                            <span>
                                <i class="fas fa-graduation-cap mr-1 text-success"></i>
                                {{ $withGrades }} / {{ $students->count() }} graded
                            </span>
                            <span>
                                <i class="fas fa-user-friends mr-1 text-primary"></i>
                                {{ $withParents }} / {{ $students->count() }} have parent accounts
                            </span>
                            <span>
                                <i class="fas fa-mars mr-1 text-info"></i> {{ $maleCount }} Male
                                &nbsp;|&nbsp;
                                <i class="fas fa-venus mr-1 text-danger"></i> {{ $femaleCount }} Female
                            </span>
                        </div>
                    @endif
                </div>

            </div>
        </section>
    </div>
@endsection
