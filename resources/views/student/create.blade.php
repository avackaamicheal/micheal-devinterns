@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">New Student Admission</h1>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('student.index') }}" class="btn btn-secondary float-right">
                            <i class="fas fa-arrow-left"></i> Back to Students
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                <form id="admission-form" onsubmit="handleAdmission(event)">
                    @csrf
                    <div class="row">

                        {{-- Left Column: Student Details --}}
                        <div class="col-md-6">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Student Details</h3>
                                </div>
                                <div class="card-body">

                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" class="form-control"
                                                placeholder="e.g. John" required>
                                        </div>
                                        <div class="col-6">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" class="form-control"
                                                placeholder="e.g. Doe" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Email Address <small class="text-muted">(optional)</small></label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="student@email.com">
                                    </div>

                                    <div class="form-group">
                                        <label>Admission Number <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" name="admission_number" id="admission_number"
                                                class="form-control bg-light font-weight-bold" placeholder="Generating..."
                                                readonly>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="fetchAdmissionNumber()" title="Regenerate">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            Auto-generated. Click <i class="fas fa-sync-alt"></i> to regenerate.
                                        </small>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label>Class Level <span class="text-danger">*</span></label>
                                            <select name="class_level_id" id="class_select" class="form-control"
                                                onchange="filterSections()" required>
                                                <option value="">-- Select Class --</option>
                                                @foreach ($classLevels as $class)
                                                    <option value="{{ $class->id }}"
                                                        data-sections="{{ $class->sections->toJson() }}">
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label>Section <span class="text-danger">*</span></label>
                                            <select name="section_id" id="section_select" class="form-control" required>
                                                <option value="">-- Select Class First --</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-6">
                                            <label>Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" name="dob" class="form-control" required>
                                        </div>
                                        <div class="col-6">
                                            <label>Gender <span class="text-danger">*</span></label>
                                            <select name="gender" class="form-control" required>
                                                <option value="">-- Select --</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea name="address" class="form-control" rows="2" placeholder="Student home address"></textarea>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Parent Details --}}
                        <div class="col-md-6">
                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Parent / Guardian Info</h3>
                                </div>
                                <div class="card-body">

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        If the parent email already exists in the system,
                                        we will link this student to their existing account
                                        automatically.
                                    </div>

                                    <div class="form-group">
                                        <label>Parent Email <span class="text-danger">*</span></label>
                                        <input type="email" name="parent_email" class="form-control" required
                                            placeholder="parent@email.com">
                                    </div>

                                    <div class="form-group">
                                        <label>Parent Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="parent_name" class="form-control" required
                                            placeholder="e.g. Mr. James Doe">
                                    </div>

                                    <div class="form-group">
                                        <label>Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" name="alt_phone" class="form-control" required
                                            placeholder="e.g. 08012345678">
                                    </div>

                                    <div class="form-group">
                                        <label>Relationship to Student <span class="text-danger">*</span></label>
                                        <select name="relationship" class="form-control" required>
                                            <option value="">-- Select --</option>
                                            <option value="Father">Father</option>
                                            <option value="Mother">Mother</option>
                                            <option value="Guardian">Guardian</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Occupation <small class="text-muted">(optional)</small></label>
                                        <input type="text" name="parent_occupation" class="form-control"
                                            placeholder="e.g. Engineer, Teacher">
                                    </div>

                                    <div class="form-group">
                                        <label>Address<small class="text-muted">(optional)</small></label>
                                        <input type="text" name="address" class="form-control"
                                            placeholder="Enter parent address">
                                    </div>

                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="card">
                                <div class="card-body">
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-block btn-lg">
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
        // 1. Dynamic Section Filtering
        function filterSections() {
            let classSelect = document.getElementById('class_select');
            let sectionSelect = document.getElementById('section_select');
            let selectedOption = classSelect.options[classSelect.selectedIndex];
            let sections = selectedOption.getAttribute('data-sections');

            sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';

            if (sections) {
                JSON.parse(sections).forEach(function(section) {
                    sectionSelect.innerHTML += `<option value="${section.id}">${section.name}</option>`;
                });
            }
        }

        // 2. Fetch Auto-Generated Admission Number
        async function fetchAdmissionNumber() {
            const btn = document.querySelector('[onclick="fetchAdmissionNumber()"]');
            const input = document.getElementById('admission_number');

            input.placeholder = 'Generating...';
            if (btn) btn.disabled = true;

            try {
                const response = await fetch("{{ route('students.generate-admission') }}");
                const data = await response.json();
                input.value = data.admission_number;
            } catch (error) {
                input.placeholder = 'Failed to generate';
                console.error('Failed to generate admission number:', error);
            } finally {
                if (btn) btn.disabled = false;
            }
        }

        // 3. Handle Form Submission
        window.handleAdmission = async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            let formData = new FormData(e.target);

            try {
                let response = await fetch("{{ route('student.store') }}", {
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
                    setTimeout(() => window.location.href = "{{ route('student.index') }}", 1500);
                } else if (response.status === 422) {
                    let errors = Object.values(data.errors).flat().join('\n');
                    alert('Validation Error:\n' + errors);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-user-check"></i> Complete Admission';
                } else {
                    alert('Error: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-user-check"></i> Complete Admission';
                }
            } catch (error) {
                alert('System Error. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-user-check"></i> Complete Admission';
            }
        }

        // 4. Auto-fetch admission number on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetchAdmissionNumber();
        });
    </script>
@endpush
