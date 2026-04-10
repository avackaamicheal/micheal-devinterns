@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">School Profile</h1>
                </div>
            </div>
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

            <form action="{{ route('school.profile.update') }}" method="POST">
                @csrf @method('PUT')

                <div class="row">
                    {{-- Left: Basic Info --}}
                    <div class="col-md-6">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Basic Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>School Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $school->name) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $school->email) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control"
                                        value="{{ old('phone_number', $school->phone_number) }}">
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control"
                                        rows="3">{{ old('address', $school->address) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Admin Info --}}
                    <div class="col-md-6">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">Administration</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Principal Name</label>
                                    <input type="text" name="principal_name" class="form-control"
                                        value="{{ old('principal_name', $school->principal_name) }}">
                                </div>
                                <div class="form-group">
                                    <label>School Slug</label>
                                    <input type="text" class="form-control bg-light"
                                        value="{{ $school->slug }}" readonly disabled>
                                    <small class="text-muted">
                                        Auto-generated from school name. Contact SuperAdmin to change.
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label>School Status</label>
                                    <input type="text" class="form-control bg-light"
                                        value="{{ $school->is_active ? 'Active' : 'Inactive' }}"
                                        readonly disabled>
                                    <small class="text-muted">
                                        Contact SuperAdmin to change status.
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Stats --}}
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">Quick Stats</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-user-graduate mr-2 text-info"></i> Total Students</span>
                                        <strong>
                                            {{ \App\Models\User::where('school_id', $school->id)->role('Student')->count() }}
                                        </strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-chalkboard-teacher mr-2 text-success"></i> Total Teachers</span>
                                        <strong>
                                            {{ \App\Models\User::where('school_id', $school->id)->role('Teacher')->count() }}
                                        </strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-layer-group mr-2 text-warning"></i> Total Classes</span>
                                        <strong>
                                            {{ \App\Models\ClassLevel::where('school_id', $school->id)->count() }}
                                        </strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-book mr-2 text-danger"></i> Total Subjects</span>
                                        <strong>
                                            {{ \App\Models\Subject::where('school_id', $school->id)->count() }}
                                        </strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
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
