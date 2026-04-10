@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">My Profile</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('teacher.profile.update') }}"
                method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="row">
                    {{-- Left: Account & Photo --}}
                    <div class="col-md-4">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Account Info</h3>
                            </div>
                            <div class="card-body text-center">
                                <img src="{{ $teacher->teacherProfile->profile_picture
                                    ? asset('storage/' . $teacher->teacherProfile->profile_picture)
                                    : asset('dist/img/user2-160x160.jpg') }}"
                                    id="picturePreview"
                                    class="img-circle elevation-2 mb-3"
                                    style="width: 120px; height: 120px; object-fit: cover;">

                                <div class="mb-3">
                                    <label class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-camera"></i> Change Photo
                                        <input type="file" name="profile_picture"
                                            id="profilePicInput" class="d-none" accept="image/*">
                                    </label>
                                </div>

                                <div class="text-left">
                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $teacher->name) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', $teacher->email) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Employee ID</label>
                                        <input type="text" class="form-control bg-light"
                                            value="{{ $teacher->teacherProfile->employee_id }}"
                                            readonly disabled>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <label>New Password
                                            <small class="text-muted">(leave blank to keep current)</small>
                                        </label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input type="password" name="password_confirmation"
                                            class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Personal & Professional --}}
                    <div class="col-md-8">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">Personal Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone Number</label>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ old('phone', $teacher->teacherProfile->phone) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date of Birth</label>
                                            <input type="date" name="date_of_birth" class="form-control"
                                                value="{{ old('date_of_birth', $teacher->teacherProfile->date_of_birth?->format('Y-m-d')) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select name="gender" class="form-control">
                                                <option value="">-- Select --</option>
                                                @foreach(['Male', 'Female', 'Other'] as $gender)
                                                    <option value="{{ $gender }}"
                                                        {{ old('gender', $teacher->teacherProfile->gender) == $gender ? 'selected' : '' }}>
                                                        {{ $gender }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Marital Status</label>
                                            <select name="marital_status" class="form-control">
                                                <option value="">-- Select --</option>
                                                @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                                                    <option value="{{ $status }}"
                                                        {{ old('marital_status', $teacher->teacherProfile->marital_status) == $status ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea name="address" class="form-control" rows="2">{{ old('address', $teacher->teacherProfile->address) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">Professional Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Qualification</label>
                                            <input type="text" class="form-control bg-light"
                                                value="{{ $teacher->teacherProfile->qualification ?? 'N/A' }}"
                                                readonly disabled>
                                            <small class="text-muted">
                                                Contact admin to update qualification.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Hire Date</label>
                                            <input type="text" class="form-control bg-light"
                                                value="{{ $teacher->teacherProfile->hire_date?->format('M d, Y') ?? 'N/A' }}"
                                                readonly disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </section>
</div>

<script>
    document.getElementById('profilePicInput').addEventListener('change', function() {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('picturePreview').src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    });
</script>
@endsection
