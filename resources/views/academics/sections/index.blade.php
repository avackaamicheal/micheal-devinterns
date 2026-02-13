@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Sections Management</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button class="btn btn-primary" onclick="openCreateModal()">
                            <i class="fas fa-plus"></i> Add New Section
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">List of Sections</h3>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-striped projects">
                            <thead>
                                <tr>
                                    <th>Class Level</th>
                                    <th>Section Name</th>
                                    <th>Capacity</th>
                                    <th>Status</th>
                                    <th style="width: 20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sections as $section)
                                    <tr>
                                        <td>
                                            <strong>{{ $section->classLevel->name ?? 'No Class' }}</strong>
                                        </td>
                                        <td>{{ $section->name }}</td>
                                        <td>{{ $section->capacity }} Students</td>
                                        <td>
                                            @if ($section->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="project-actions">
                                            <button class="btn btn-info btn-sm"
                                                onclick="openEditModal(
                                                {{ $section->id }},
                                                '{{ $section->name }}',
                                                '{{ $section->class_level_id }}',
                                                '{{ $section->capacity }}'
                                            )">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </button>

                                            <button class="btn btn-danger btn-sm"
                                                onclick="handleDelete({{ $section->id }})">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                            <form id="delete-form-{{ $section->id }}"
                                                action="{{ route('section.destroy', $section->id) }}" method="POST"
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
                        {{ $sections->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="modal-section">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Add New Section</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="section-form" onsubmit="handleFormSubmit(event)">
                    @csrf
                    <input type="hidden" id="section_id" name="section_id">

                    <div class="modal-body">
                        <div id="error-box" class="alert alert-danger d-none">
                            <ul id="error-list" class="mb-0 pl-3"></ul>
                        </div>

                        <div class="form-group">
                            <label for="class_level_id">Class Level</label>
                            <select name="class_level_id" id="class_level_id" class="form-control">
                                <option value="">Select Class...</option>
                                @foreach ($classLevels as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="name">Section Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="e.g. Gold, A, B">
                            <small class="text-muted">Must be unique for the selected class.</small>
                        </div>

                        <div class="form-group">
                            <label for="capacity">Max Capacity</label>
                            <input type="number" name="capacity" id="capacity" class="form-control" value="40">
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="save-btn">Save Section</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // 1. OPEN CREATE MODAL
        function openCreateModal() {

            // Reset the form
            document.getElementById('section-form').reset();
            document.getElementById('section_id').value = '';
            document.getElementById('modal-title').innerText = 'Add New Section';

            // Hide errors
            document.getElementById('error-box').classList.add('d-none');

            // Show Bootstrap Modal
            $('#modal-section').modal('show');
        }

        // 2. OPEN EDIT MODAL (Populate Data)
        function openEditModal(id, name, classId, capacity) {
            // Fill values
            document.getElementById('section_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('class_level_id').value = classId;
            document.getElementById('capacity').value = capacity;

            // Update Title
            document.getElementById('modal-title').innerText = 'Edit Section';

            // Hide errors
            document.getElementById('error-box').classList.add('d-none');

            // Show Modal
            $('#modal-section').modal('show');
        }

        // 3. HANDLE AJAX SUBMISSION
        window.handleFormSubmit = async function(e) {
            e.preventDefault();
            let form = e.target;
            let formData = new FormData(form);
            let id = document.getElementById('section_id').value;
            let url = id ? `/schooladmin/section/${id}` : `{{ route('section.store') }}`;

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
                    let errorBox = document.getElementById('error-box');
                    let errorList = document.getElementById('error-list');
                    errorBox.classList.remove('d-none');
                    errorList.innerHTML = Object.values(data.errors).flat().map(msg => `<li>${msg}</li>`).join('');
                } else if (response.ok) {
                    // Success Logic
                    $('#modal-section').modal('hide');
                    window.showFlash('success', data.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    window.showFlash('error', 'Server Error: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                window.showFlash('error', 'Network Error: Check your connection.');
            }
        }

        // 4. HANDLE DELETE
        window.handleDelete = async function(id) {
            if (!confirm('Are you sure you want to delete this section?')) {
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
                    window.showFlash('success', data.message || 'Section deleted successfully.');

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
