@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Subjects</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button class="btn btn-primary" onclick="openCreateModal()">
                            <i class="fas fa-plus"></i> Add Subject
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">List of Subjects</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Subject Name</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th style="width: 20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subjects as $subject)
                                    <tr>
                                        <td>{{ $subject->name }}</td>
                                        <td><span class="badge badge-info">{{ $subject->code }}</span></td>
                                        <td>{{ $subject->description }}</td>
                                        <td>
                                            <button class="btn btn-info btn-sm"
                                                onclick="openEditModal(
                                            {{ $subject->id }},
                                            '{{ $subject->name }}',
                                            '{{ $subject->code }}',
                                            '{{ $subject->description }}')">
                                                <i class="fas fa-pen"></i> Edit
                                            </button>
                                            <form action="{{ route('subject.destroy', $subject->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Delete subject?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
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

    <div class="modal fade" id="modal-subject">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="subject-form" onsubmit="handleFormSubmit(event)">
                    @csrf
                    <input type="hidden" id="subject_id" name="subject_id">
                    <div class="modal-header">
                        <h4 class="modal-title" id="modal-title">Add Subject</h4>
                    </div>
                    <div class="modal-body">
                        <div id="error-box" class="alert alert-danger d-none">
                            <ul id="error-list"></ul>
                        </div>

                        <div class="form-group">
                            <label>Subject Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Subject Code</label>
                            <input type="text" name="code" id="code" class="form-control"
                                placeholder="e.g. MATH101">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.openCreateModal = function() {
            document.getElementById('subject-form').reset();
            document.getElementById('subject_id').value = '';
            document.getElementById('modal-title').innerText = 'Add Subject';
            $('#modal-subject').modal('show');
        };

        window.openEditModal = function(id, name, code, desc) {
            document.getElementById('subject_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('code').value = code;
            document.getElementById('description').value = desc;
            document.getElementById('modal-title').innerText = 'Edit Subject';
            $('#modal-subject').modal('show');
        };

        window.handleFormSubmit = async function(e) {
            e.preventDefault();
            let form = e.target;
            let formData = new FormData(form);
            let id = document.getElementById('subject_id').value;
            let url = id ? `/schooladmin/subject/${id}` : `{{ route('subject.store') }}`;

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
                if (response.ok) {
                    // 1. Hide the Modal
                    $('#modal-subject').modal('hide');

                    // 2. Show the Flash Message
                    window.showFlash('success', data.message);

                    // 3. Optional: Refresh table via AJAX or reload after a short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1000);

                } else {
                    // For non-validation server errors (500, etc.)
                    window.showFlash('error', 'A server error occurred. Please try again.');
                }
            } catch (error) {
                alert('System Error');
            }
        };
    </script>
@endpush
