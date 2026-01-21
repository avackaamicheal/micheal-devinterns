@extends('layouts.app')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>School List</h1>
                    </div>
                    <div class="col-sm-6" style="text-align: right">
                        <a href="{{ route('school.create') }}" class="btn btn-primary">Add School</a>
                    </div>
                    {{-- <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">School</li>
                        </ol>
                    </div> --}}
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- /.col -->
                    <div class="col-md-12">

                        <div class="card">
                            <!-- /.card-header -->
                            <div class="card-body p-0">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>School Name</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th>Principal Name</th>
                                            <th>Phone Number</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($schools as $school)
                                            <tr>
                                                <td>{{ $school->id }}</td>
                                                <td>{{ $school->name }}</td>
                                                <td>{{ $school->email }}</td>
                                                <td>{{ $school->address }}</td>
                                                <td>{{ $school->principal_name }}</td>
                                                <td>{{ $school->phone_number }}</td>
                                                <td>
                                                    <a href=""><button class="btn btn-primary btn-sm btn-rounded"><span class="fas fa-eye"></span></button></a>
                                                    <a href="{{ route('school.edit', $school->id) }}"><button class="btn btn-primary btn-sm btn-rounded"><span class="fas fa-pen"></span></button></a>
                                                    <button form="delete-form" class="btn btn-danger btn-sm btn-rounded"><span class="fas fa-times"></span></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                    <form method="POST" action="{{ route('school.destroy', $school->id) }}" id="delete-form" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                    </form>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>

                </div>

            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
