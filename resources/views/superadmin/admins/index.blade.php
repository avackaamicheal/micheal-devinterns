@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">School Administrators</h1>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('superadmin.admins.create') }}" class="btn btn-primary float-right">
                            <i class="fas fa-user-plus"></i> Add Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            All Admins
                            <span class="badge badge-info ml-2">{{ $admins->count() }}</span>
                        </h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Assigned School</th>
                                    <th>School Status</th>
                                    <th>Created</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $index => $admin)
                                    <tr>
                                        <td class="align-middle">{{ $index + 1 }}</td>
                                        <td class="align-middle font-weight-bold">
                                            {{ $admin->name }}
                                        </td>
                                        <td class="align-middle">{{ $admin->email }}</td>
                                        <td class="align-middle">
                                            @if ($admin->school)
                                                <span class="badge badge-primary p-2">
                                                    <i class="fas fa-school mr-1"></i>
                                                    {{ $admin->school->name }}
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    No School Assigned
                                                </span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if ($admin->school?->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            {{ $admin->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="align-middle text-right">
                                            <a href="{{ route('superadmin.admins.edit', $admin->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('superadmin.admins.destroy', $admin->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Remove {{ $admin->name }}?')">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-4">
                                            No school admins created yet.
                                            <a href="{{ route('superadmin.admins.create') }}">
                                                Create your first admin.
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection
