@extends('layouts.app')

@section('content')
    {{-- {{dd($students->first())}} --}}
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Student List</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button class="btn btn-success" data-toggle="modal" data-target="#modal-import">
                            <i class="fas fa-file-import"></i> Import CSV
                        </button>
                        <a href="{{ route('students.export') }}" class="btn btn-info">
                            <i class="fas fa-file-download"></i> Export Excel
                        </a>
                        <a href="{{ route('student.create') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> New Admission
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Registered Students</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="table_search" class="form-control float-right"
                                    placeholder="Search by name/adm no...">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Admission No</th>
                                    <th>Class Info</th>
                                    <th>Parent / Guardian</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr id="row-{{ $student->id }}">
                                        <td>
                                            <div class="user-block">
                                                <img class="img-circle img-bordered-sm"
                                                    src="{{ asset('dist/img/user2-160x160.jpg') }}" alt="User Image">
                                                <span class="username">
                                                    <a href="#">{{ $student->name }}</a>
                                                </span>
                                                <span class="description">{{ $student->email ?? 'No Email' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">
                                                {{ $student->studentProfile->admission_number ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($student->studentProfile && $student->studentProfile->section)
                                                <strong>{{ $student->studentProfile->section->classLevel->name }}</strong><br>
                                                <small class="text-muted">Section:
                                                    {{ $student->studentProfile->section->name }}</small>
                                            @else
                                                <span class="text-danger">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($student->parents->isNotEmpty())
                                                {{ $student->parents->first()->name }} <br>
                                                <small class="text-muted">
                                                    {{ $student->parents->first()->pivot->relationship }} -
                                                    {{ $student->parents->first()->email }}
                                                </small>
                                            @else
                                                <span class="text-muted">No Parent Linked</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="deleteStudent({{ $student->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                            {{-- Hidden Delete Form --}}
                                            <form id="delete-form-{{ $student->id }}"
                                                action="{{ route('student.destroy', $student->id) }}" method="POST"
                                                style="display:none;">
                                                @csrf @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fas fa-users-slash fa-2x mb-2"></i><br>
                                            No students found. Click "New Admission" to start.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix">
                        {{ $students->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-import">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Bulk Import Students</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label>Select CSV/Excel File</label>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="alert alert-info text-sm">
                        <strong>Required CSV Headers:</strong><br>
                        first_name, last_name, email, admission_number, class_level_id, section_id, date_of_birth, gender, address
                        <br><br>
                        <em>Note: class_level_id and section_id must be the numeric IDs from your database.</em>
                    </div>
                        <a href="{{ route('students.template') }}" class="btn btn-xs btn-default">Download Sample Template</a>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.deleteStudent = async function(id) {
            if (!confirm(
                    'Are you sure? This will delete the student profile, academic history, and unlink parents.'))
                return;

            const form = document.getElementById(`delete-form-${id}`);
            const row = document.getElementById(`row-${id}`);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                });

                const data = await response.json();

                if (response.ok) {
                    window.showFlash('success', data.message);
                    // Animate and Remove Row
                    row.style.transition = "all 0.5s ease";
                    row.style.opacity = "0";
                    setTimeout(() => row.remove(), 500);
                } else {
                    window.showFlash('error', data.message);
                }
            } catch (error) {
                window.showFlash('error', 'System Error');
            }
        }
    </script>
@endpush
