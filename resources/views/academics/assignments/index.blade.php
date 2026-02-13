@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Teacher Assignments</h1></div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#modal-assign">
                        <i class="fas fa-plus"></i> Assign Teacher
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Teacher</th>
                                <th>Class / Section</th>
                                <th>Subject</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $assign)
                            <tr id="row-{{ $assign->id }}">
                                <td>
                                    <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle img-sm mr-2">
                                    {{ $assign->teacher->name }}
                                </td>
                                <td>
                                    {{ $assign->section->classLevel->name }} - {{ $assign->section->name }}
                                </td>
                                <td><span class="badge badge-info">{{ $assign->subject->name }}</span></td>
                                <td>
                                    <button class="btn btn-danger btn-sm" onclick="deleteAssignment({{ $assign->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    {{-- Hidden Delete Form --}}
                                    <form id="delete-form-{{ $assign->id }}" action="{{ route('assignments.destroy', $assign->id) }}" method="POST" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modal-assign">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Assign Teacher</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="assign-form" onsubmit="handleAssign(event)">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Teacher</label>
                        <select name="teacher_id" class="form-control" required>
                            <option value="">-- Choose Teacher --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Section</label>
                        <select name="section_id" class="form-control" required>
                            <option value="">-- Choose Section --</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}">
                                    {{ $section->classLevel->name }} - {{ $section->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Subject</label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">-- Choose Subject --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Assignment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 1. Handle Assignment (Create)
    window.handleAssign = async function(e) {
        e.preventDefault();
        let form = e.target;
        let formData = new FormData(form);

        try {
            let response = await fetch("{{ route('assignments.store') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            });
            let data = await response.json();

            if (response.ok) {
                $('#modal-assign').modal('hide');
                window.showFlash('success', data.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                window.showFlash('error', data.message);
            }
        } catch (error) { window.showFlash('error', 'System Error'); }
    };

    // 2. Handle Delete (AJAX)
    window.deleteAssignment = async function(id) {
        if (!confirm('Remove this assignment?')) return;

        let form = document.getElementById(`delete-form-${id}`);
        let row = document.getElementById(`row-${id}`);

        try {
            let response = await fetch(form.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: new FormData(form)
            });

            if (response.ok) {
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 500);
                window.showFlash('success', 'Assignment removed.');
            }
        } catch (error) { window.showFlash('error', 'System Error'); }
    };
</script>
@endpush
