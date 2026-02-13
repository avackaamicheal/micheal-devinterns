@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <h1>New Student Admission</h1>
        </div>

        <section class="content">
            <div class="container-fluid">
                <form id="admission-form" onsubmit="handleAdmission(event)">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Student Details</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label>First Name</label>
                                            <input type="text" name="first_name" class="form-control" required>
                                        </div>
                                        <div class="col-6">
                                            <label>Last Name</label>
                                            <input type="text" name="last_name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Admission Number</label>
                                        <input type="text" name="admission_number" class="form-control"
                                            placeholder="e.g. 2024/SMS/001" required>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label>Class Level</label>
                                            <select name="class_level_id" id="class_select" class="form-control"
                                                onchange="filterSections()" required>
                                                <option value="">Select Class...</option>
                                                @foreach ($classLevels as $class)
                                                    <option value="{{ $class->id }}"
                                                        data-sections="{{ $class->sections }}">{{ $class->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label>Section</label>
                                            <select name="section_id" id="section_select" class="form-control" required>
                                                <option value="">Select Class First</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label>Date of Birth</label>
                                            <input type="date" name="dob" class="form-control" required>
                                        </div>
                                        <div class="col-6">
                                            <label>Gender</label>
                                            <select name="gender" class="form-control" required>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Parent / Guardian Info</h3>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> If the parent email exists, we will link this
                                        student to their existing account.
                                    </div>
                                    <div class="form-group">
                                        <label>Parent Email</label>
                                        <input type="email" name="parent_email" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Parent Name</label>
                                        <input type="text" name="parent_name" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="parent_phone" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Relationship</label>
                                        <select name="relationship" class="form-control">
                                            <option value="Father">Father</option>
                                            <option value="Mother">Mother</option>
                                            <option value="Guardian">Guardian</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-body">
                                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                                        <i class="fas fa-user-check"></i> Complete Admission
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        // 1. Dynamic Section Filtering (Cascading Dropdown)
        function filterSections() {
            let classSelect = document.getElementById('class_select');
            let sectionSelect = document.getElementById('section_select');
            let selectedOption = classSelect.options[classSelect.selectedIndex];

            // Parse the JSON string stored in the data attribute
            let sections = selectedOption.getAttribute('data-sections');

            sectionSelect.innerHTML = '<option value="">Select Section...</option>';

            if (sections) {
                JSON.parse(sections).forEach(section => {
                    sectionSelect.innerHTML += `<option value="${section.id}">${section.name}</option>`;
                });
            }
        }

        // 2. Handle Submission
        window.handleAdmission = async function(e) {
            e.preventDefault();
            let formData = new FormData(e.target);

            try {
                let response = await fetch(" {{ route('student.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                let data = await response.json();

                if (response.ok) {
                    window.showFlash('success', data.message);
                    // Reset form or redirect
                    setTimeout(() => window.location.href = "{{ route('student.index') }}", 1500);
                } else if (response.status === 422) {
                    // Simple alert for validation errors for now, or use your error list logic
                    alert(Object.values(data.errors).flat().join('\n'));
                } else {
                    window.showFlash('error', data.message);
                }
            } catch (error) {
                window.showFlash('error', 'System Error');
            }
        }
    </script>
@endpush
