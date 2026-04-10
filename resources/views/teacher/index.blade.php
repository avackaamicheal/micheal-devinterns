@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Teacher Directory</h1>
                    </div>
                    <div class="col-sm-6">
                        <div class="float-right">
                            <a href="{{ route('teachers.assignments') }}" class="btn btn-info mr-2">
                                <i class="fas fa-chalkboard"></i> Manage Assignments
                            </a>
                            <a href="{{ route('teachers.create') }}" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Add Teacher
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">All Teachers ({{ $teachers->count() }})</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Contact</th>
                                    <th>Qualification</th>
                                    <th>Assignment</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teachers as $teacher)
                                    <tr>
                                        <td class="align-middle">
                                            <span class="badge badge-secondary">
                                                {{ $teacher->teacherProfile->employee_id ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $teacher->teacherProfile->profile_picture
                                                    ? asset('storage/' . $teacher->teacherProfile->profile_picture)
                                                    : asset('dist/img/user2-160x160.jpg') }}"
                                                    class="img-circle mr-2"
                                                    style="width: 35px; height: 35px; object-fit: cover;" alt="Profile">
                                                <div>
                                                    <div class="font-weight-bold">{{ $teacher->name }}</div>
                                                    <div class="text-muted text-sm">{{ $teacher->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            {{ $teacher->teacherProfile->phone ?? 'N/A' }}
                                        </td>
                                        <td class="align-middle">
                                            {{ $teacher->teacherProfile->qualification ?? 'N/A' }}
                                        </td>
                                        <td class="align-middle">
                                            @if ($teacher->allocations->count() > 0)
                                                @foreach ($teacher->allocations as $allocation)
                                                    <div class="mb-1">
                                                        <span class="badge badge-info">
                                                            {{ $allocation->subject->name }}
                                                        </span>
                                                        <small class="text-muted">
                                                            {{ $allocation->section->classLevel->name ?? '' }}
                                                            - {{ $allocation->section->name ?? '' }}
                                                        </small>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="badge badge-warning">Unassigned</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-right">
                                            <a href="{{ route('teachers.edit', $teacher->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('teachers.destroy', $teacher->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Remove {{ $teacher->name }}? This cannot be undone.')">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center p-4">
                                            No teachers added yet.
                                            <a href="{{ route('teachers.create') }}">Add your first teacher.</a>
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
