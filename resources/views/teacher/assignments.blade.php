@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Class Assignments</h1></div>
                <div class="col-sm-6">
                    <a href="{{ route('teachers.index') }}" class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Back to Teachers
                    </a>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Assign Teachers to Classes</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 25%">Teacher</th>
                                <th style="width: 25%">Current Assignment</th>
                                <th style="width: 50%">Update Assignment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $teacher)
                                <tr>
                                    <td class="align-middle">
                                        <div class="font-weight-bold">{{ $teacher->name }}</div>
                                        <div class="text-muted text-sm">{{ $teacher->email }}</div>
                                    </td>
                                    <td class="align-middle">
                                        @if($teacher->teacherProfile?->classLevel)
                                            <span class="badge badge-success p-2">
                                                {{ $teacher->teacherProfile->classLevel->name }}
                                                @if($teacher->teacherProfile->section)
                                                    - {{ $teacher->teacherProfile->section->name }}
                                                @endif
                                            </span>
                                        @else
                                            <span class="badge badge-warning">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <form action="{{ route('teachers.assign', $teacher->id) }}" method="POST">
                                            @csrf
                                            <div class="d-flex align-items-center gap-2">
                                                <select name="class_level_id" class="form-control form-control-sm mr-2 class-select" data-teacher="{{ $teacher->id }}" required>
                                                    <option value="">-- Class --</option>
                                                    @foreach($classLevels as $class)
                                                        <option value="{{ $class->id }}"
                                                            {{ $teacher->teacherProfile?->class_level_id == $class->id ? 'selected' : '' }}>
                                                            {{ $class->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <select name="section_id" class="form-control form-control-sm mr-2 section-select" id="sections-{{ $teacher->id }}">
                                                    <option value="">-- Section (optional) --</option>
                                                    @foreach($classLevels as $class)
                                                        @foreach($class->sections as $section)
                                                            <option value="{{ $section->id }}"
                                                                data-class="{{ $class->id }}"
                                                                {{ $teacher->teacherProfile?->section_id == $section->id ? 'selected' : '' }}>
                                                                {{ $section->name }}
                                                            </option>
                                                        @endforeach
                                                    @endforeach
                                                </select>

                                                <button type="submit" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-save"></i> Save
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center p-4">
                                        No teachers found. <a href="{{ route('teachers.index') }}">Add teachers first.</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
const classData = @json($classLevels->keyBy('id'));

// Filter sections when class changes
document.querySelectorAll('.class-select').forEach(function(select) {
    select.addEventListener('change', function() {
        const teacherId = this.dataset.teacher;
        const classId = this.value;
        const sectionSelect = document.getElementById('sections-' + teacherId);

        // Reset sections
        sectionSelect.innerHTML = '<option value="">-- Section (optional) --</option>';

        if (classId && classData[classId]?.sections?.length > 0) {
            classData[classId].sections.forEach(function(section) {
                sectionSelect.innerHTML += `<option value="${section.id}">${section.name}</option>`;
            });
        }
    });
});
</script>
@endsection
