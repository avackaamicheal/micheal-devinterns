@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit School Admin</h1>
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
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form action="{{ route('superadmin.admins.update', $admin->id) }}" method="POST">
                            @csrf @method('PUT')

                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Account Information</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $admin->name) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', $admin->email) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ old('phone', $admin->phone ?? '') }}">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>New Password
                                                    <small class="text-muted">(leave blank to keep current)</small>
                                                </label>
                                                <input type="password" name="password" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Confirm Password</label>
                                                <input type="password" name="password_confirmation" class="form-control">
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
                                        <label>Assigned School <span class="text-danger">*</span></label>
                                        <select name="school_id" class="form-control" required>
                                            <option value="">-- Select School --</option>
                                            @foreach ($schools as $school)
                                                <option value="{{ $school->id }}"
                                                    {{ old('school_id', $admin->school_id) == $school->id ? 'selected' : '' }}>
                                                    {{ $school->name }}
                                                    {{ $school->is_active ? '' : '(Inactive)' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <a href="{{ route('superadmin.admins.index') }}"
                                        class="btn btn-secondary mr-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
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
