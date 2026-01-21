@extends('layouts.app')

@section('content')
   <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Add School</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Add School</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
        @endif
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Add School</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
              <form method="POST" action="{{ route('school.store') }}">
                @csrf
                <div class="card-body">
                  <div class="form-group">
                    <label for="name">School Name <span class="required">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter School Name" value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter School Email" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="address">Address <span class="required">*</span></label>
                    <input type="text" class="form-control" name="address" id="address" placeholder="Enter School Address" value="{{ old('address') }}">
                    @error('address')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="principal_name">Principal Name <span class="required">*</span></label>
                    <input type="text" class="form-control" name="principal_name" id="principal_name" placeholder="Enter Principal Name" value="{{ old('principal_name') }}">
                    @error('principal_name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="phonenumber">Phone Number <span class="required">*</span></label>
                    <input type="phone_number" class="form-control" name="phone_number" id="phonenumber" placeholder="Enter School Phone Number" value="{{ old('phone_number') }}">
                    @error('phone_number')
                        <p class="mt-2 text-sm text-red-200">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
            </div>
            <!-- /.card -->

          </div>

        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection
