@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Report Card Generator</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-default">
                <div class="card-body">
                    <form action="{{ resolveRoute('reports.index') }}" method="GET" class="form-inline">
                        <label class="mr-2">Class Section:</label>
                        <select name="section_id" class="form-control mr-4" required>
                            <option value="">-- Choose Class --</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}"
                                    {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                    {{ $section->classLevel->name }} - {{ $section->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Load Class</button>
                    </form>
                </div>
            </div>

            @if($selectedSection)
                <div class="card card-outline card-success">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            Students in: <strong>{{ $selectedSection->name }}</strong>
                        </h3>
                        <div class="card-tools">
                            <a href="{{ resolveRoute('reports.batch', $selectedSection->id) }}"
                                class="btn btn-success btn-sm font-weight-bold shadow-sm">
                                <i class="fas fa-file-pdf"></i> Batch Download Class PDF
                            </a>
                        </div>
                    </div>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped m-0">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th>Admission No</th>
                                    <th>Student Name</th>
                                    <th>Subjects Graded</th>
                                    <th>Average</th>
                                    <th>Overall Grade</th>
                                    <th class="text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $student)
                                    @php
                                        $grades = $student->grades ?? collect();
                                        $lockedGrades = $grades->where('is_locked', true);
                                        $average = $lockedGrades->count() > 0
                                            ? round($lockedGrades->avg('total_score'), 2)
                                            : null;
                                        $overallGrade = null;
                                        if ($average !== null) {
                                            if ($average >= 70) $overallGrade = ['label' => 'A', 'class' => 'success'];
                                            elseif ($average >= 60) $overallGrade = ['label' => 'B', 'class' => 'info'];
                                            elseif ($average >= 50) $overallGrade = ['label' => 'C', 'class' => 'primary'];
                                            elseif ($average >= 40) $overallGrade = ['label' => 'D', 'class' => 'warning'];
                                            else $overallGrade = ['label' => 'F', 'class' => 'danger'];
                                        }
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
                                            @if($lockedGrades->count() > 0)
                                                <span class="badge badge-success">
                                                    {{ $lockedGrades->count() }} Published
                                                </span>
                                                @if($grades->where('is_locked', false)->count() > 0)
                                                    <span class="badge badge-warning ml-1">
                                                        {{ $grades->where('is_locked', false)->count() }} Pending
                                                    </span>
                                                @endif
                                            @elseif($grades->count() > 0)
                                                <span class="badge badge-warning">
                                                    {{ $grades->count() }} Pending
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">No Grades</span>
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
                                            @if($overallGrade)
                                                <span class="badge badge-{{ $overallGrade['class'] }} p-2">
                                                    {{ $overallGrade['label'] }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-right align-middle">
                                            <a href="{{ resolveRoute('reports.single', $student->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-pdf"></i> Print Card
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-4">
                                            No students found in this class.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
