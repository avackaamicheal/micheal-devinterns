@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Add School Admin</h1>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('superadmin.admins.index') }}" class="btn btn-secondary float-right">
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

                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form action="{{ route('superadmin.admins.store') }}" method="POST">
                            @csrf

                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Account Information</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name') }}" placeholder="e.g. John Smith" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email') }}" placeholder="admin@school.com" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ old('phone') }}" placeholder="e.g. 08012345678">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Password <span class="text-danger">*</span></label>
                                                <input type="password" name="password" class="form-control" required>
                                                <small class="text-muted">Min. 8 characters.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Confirm Password <span class="text-danger">*</span></label>
                                                <input type="password" name="password_confirmation" class="form-control"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-outline card-success">
                                <div class="card-header">
                                    <h3 class="card-title">School Assignment</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Assign to School <span class="text-danger">*</span></label>
                                        <select name="school_id" class="form-control" required>
                                            <option value="">-- Select School --</option>
                                            @foreach ($schools as $school)
                                                <option value="{{ $school->id }}"
                                                    {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                                    {{ $school->name }}
                                                    {{ $school->is_active ? '' : '(Inactive)' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">
                                            This admin will only have access to the selected school.
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <a href="{{ route('superadmin.admins.index') }}"
                                        class="btn btn-secondary mr-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-user-plus"></i> Create Admin
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
