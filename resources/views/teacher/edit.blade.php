@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Teacher</h1>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('teachers.index') }}" class="btn btn-secondary float-right">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Account Info</h3>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-3">
                                        <img src="{{ $teacher->teacherProfile->profile_picture
                                            ? asset('storage/' . $teacher->teacherProfile->profile_picture)
                                            : asset('dist/img/user2-160x160.jpg') }}"
                                            class="img-circle elevation-2"
                                            style="width: 100px; height: 100px; object-fit: cover;" alt="Profile Picture">
                                    </div>
                                    <div class="form-group">
                                        <label>Profile Picture</label>
                                        <input type="file" name="profile_picture" class="form-control-file">
                                    </div>
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
                                        <label>New Password <small class="text-muted">(leave blank to keep
                                                current)</small></label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Employee ID</label>
                                        <input type="text" class="form-control"
                                            value="{{ $teacher->teacherProfile->employee_id }}" readonly disabled>
                                        <small class="text-muted">Auto-generated, cannot be changed.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Personal & Professional Info</h3>
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
                                                    @foreach (['Male', 'Female', 'Other'] as $gender)
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
                                                    @foreach (['Single', 'Married', 'Divorced', 'Widowed'] as $status)
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
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Qualification</label>
                                                <input type="text" name="qualification" class="form-control"
                                                    value="{{ old('qualification', $teacher->teacherProfile->qualification) }}"
                                                    placeholder="e.g. B.Sc, M.Ed">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Hire Date</label>
                                                <input type="date" name="hire_date" class="form-control"
                                                    value="{{ old('hire_date', $teacher->teacherProfile->hire_date?->format('Y-m-d')) }}">
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
@endsection
