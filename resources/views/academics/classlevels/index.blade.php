@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Class Levels / Grades</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button class="btn btn-primary" onclick="openCreateModal()">
                            <i class="fas fa-plus"></i> Add New Class
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">List of Classes</h3>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-striped projects">
                            <thead>
                                <tr>
                                    <th style="width: 20%">Class Name</th>
                                    <th>Description</th>
                                    <th>Sections</th>
                                    <th>Status</th>
                                    <th style="width: 20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classLevels as $class)
                                    <tr>
                                        <td>
                                            <strong>{{ $class->name }}</strong>
                                            <br>
                                            <small>Created {{ $class->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>{{ Str::limit($class->description, 50) }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $class->sections_count }} Sections</span>
                                        </td>
                                        <td>
                                            @if ($class->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="project-actions">
                                            <button class="btn btn-info btn-sm"
                                                onclick="openEditModal(
                                                {{ $class->id }},
                                                '{{ $class->name }}',
                                                '{{ $class->description }}'
                                            )">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </button>

                                            <button class="btn btn-danger btn-sm"
                                                onclick="handleDelete({{ $class->id }})">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                            <form id="delete-form-{{ $class->id }}" action="{{ route('classLevel.destroy', $class->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-footer clearfix">
                        {{ $classLevels->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-class">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Add New Class</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="class-form" onsubmit="handleFormSubmit(event)">
                    @csrf
                    <input type="hidden" id="class_id" name="class_id">

                    <div class="modal-body">
                        <div id="error-box" class="alert alert-danger d-none">
                            <ul id="error-list" class="mb-0 pl-3"></ul>
                        </div>

                        <div class="form-group">
                            <label for="name">Class Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="e.g. Grade 1, SS 3, Kindergarten">
                            <small class="text-muted">Must be unique within the school.</small>
                        </div>

                        <div class="form-group">
                            <label for="description">Description (Optional)</label>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Brief description..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Attached to window to prevent ReferenceError
        window.openCreateModal = function() {
            document.getElementById('class-form').reset();
            document.getElementById('class_id').value = '';
            document.getElementById('modal-title').innerText = 'Add New Class';
            document.getElementById('error-box').classList.add('d-none');
            $('#modal-class').modal('show');
        }

        window.openEditModal = function(id, name, description) {
            document.getElementById('class_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('description').value = description || ''; // Handle null description

            document.getElementById('modal-title').innerText = 'Edit Class';
            document.getElementById('error-box').classList.add('d-none');
            $('#modal-class').modal('show');
        }

        window.handleFormSubmit = async function(e) {
            e.preventDefault();
            let form = e.target;
            let formData = new FormData(form);
            let id = document.getElementById('class_id').value;
            let url = id ? `/schooladmin/classLevel/${id}` : `{{ route('classLevel.store') }}`;

            if (id) formData.append('_method', 'PUT');

            try {
                let response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                let data = await response.json();

                if (response.status === 422) {
                    // Validation errors stay in the modal error-box
                    let errorBox = document.getElementById('error-box');
                    let errorList = document.getElementById('error-list');
                    errorBox.classList.remove('d-none');
                    errorList.innerHTML = Object.values(data.errors).flat().map(msg => `<li>${msg}</li>`).join('');
                } else if (response.ok) {
                    // 1. Hide the Modal
                    $('#modal-class').modal('hide');

                    // 2. Trigger the Global Flash Message
                    window.showFlash('success', data.message);

                    // 3. Reload after the user has had time to read it
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    window.showFlash('error', data.message || 'A server error occurred.');
                }
            } catch (error) {
                window.showFlash('error', 'System Error: Could not connect to server.');
            }
        }

        window.handleDelete = async function(id) {
            if (!confirm('Are you sure you want to delete this class?')) {
                return;
            }

            const form = document.getElementById(`delete-form-${id}`);
            const url = form.action;
            const formData = new FormData(form);

            try {
                const response = await fetch(url, {
                    method: 'POST', // Form specifies DELETE via _method, but we send as POST
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    // 1. Trigger the Global Flash Message
                    window.showFlash('success', data.message || 'Class deleted successfully.');

                    // 2. Remove the row from the table immediately for a "snappy" feel
                    // Or reload after a delay if you prefer
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    window.showFlash('error', 'Could not delete: ' + (data.message || 'Server error'));
                }
            } catch (error) {
                window.showFlash('error', 'System error occurred during deletion.');
            }
        }
    </script>
@endpush
