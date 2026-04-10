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
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Assign Teachers to Subjects & Classes</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 20%">Teacher</th>
                                <th style="width: 20%">Current Assignments</th>
                                <th style="width: 60%">Add New Assignment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $teacher)
                                <tr>
                                    <td class="align-middle">
                                        <div class="font-weight-bold">{{ $teacher->name }}</div>
                                        <div class="text-muted text-sm">
                                            {{ $teacher->teacherProfile->employee_id ?? 'N/A' }}
                                        </div>
                                        <div class="text-muted text-sm">
                                            {{ $teacher->teacherProfile->qualification ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        @forelse($teacher->allocations as $allocation)
                                            <div class="mb-1">
                                                <span class="badge badge-info">
                                                    {{ $allocation->subject->name }}
                                                </span>
                                                <small class="text-muted">
                                                    {{ $allocation->section->classLevel->name ?? '' }}
                                                    - {{ $allocation->section->name ?? '' }}
                                                </small>
                                                <form action="{{ route('teachers.allocations.destroy', [$teacher->id, $allocation->id]) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-xs btn-danger ml-1"
                                                        onclick="return confirm('Remove this assignment?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @empty
                                            <span class="badge badge-warning">Unassigned</span>
                                        @endforelse
                                    </td>
                                    <td class="align-middle">
                                        <form action="{{ route('teachers.assign', $teacher->id) }}"
                                            method="POST">
                                            @csrf
                                            <div class="d-flex align-items-start flex-wrap gap-2">

                                                {{-- Subject --}}
                                                <div class="mr-2" style="min-width: 150px;">
                                                    <label class="text-muted text-sm mb-1">Subject</label>
                                                    <select name="subject_id" class="form-control form-control-sm" required>
                                                        <option value="">-- Subject --</option>
                                                        @foreach($subjects as $subject)
                                                            <option value="{{ $subject->id }}">
                                                                {{ $subject->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Class & Section --}}
                                                <div class="mr-2" style="min-width: 180px;">
                                                    <label class="text-muted text-sm mb-1">Class & Section</label>
                                                    <select name="section_id" class="form-control form-control-sm" required>
                                                        <option value="">-- Section --</option>
                                                        @foreach($classLevels as $class)
                                                            <optgroup label="{{ $class->name }}">
                                                                @foreach($class->sections as $section)
                                                                    <option value="{{ $section->id }}">
                                                                        {{ $class->name }} - {{ $section->name }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Save --}}
                                                <div style="margin-top: 22px;">
                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-plus"></i> Assign
                                                    </button>
                                                </div>

                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center p-4">
                                        No teachers found.
                                        <a href="{{ route('teachers.create') }}">Add teachers first.</a>
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
@endsection
