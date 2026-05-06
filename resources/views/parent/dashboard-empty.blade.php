@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header bg-white border-bottom mb-4 pb-3 pt-4">
            <div class="container-fluid">
                <h1 class="m-0 font-weight-bold text-dark"><i class="fas fa-home text-primary mr-2"></i> Family Portal</h1>
                <p class="text-muted mb-0">Welcome back, {{ $parent->name }}</p>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row justify-content-center mt-5">
                    <div class="col-md-6 text-center">
                        <div class="card shadow-sm" style="border-radius: 15px; border-top: 4px solid #007bff;">
                            <div class="card-body p-5">
                                <i class="fas fa-user-graduate fa-5x text-muted mb-4"></i>
                                <h2 class="font-weight-bold text-dark">No Students Linked</h2>
                                <p class="text-muted mb-4" style="font-size: 1.1em;">
                                    We couldn't find any student records linked to your parent account. This usually happens
                                    if your account is brand new or if the school admin hasn't finished processing your
                                    child's admission.
                                </p>

                                <hr class="mb-4">

                                <p class="text-dark font-weight-bold mb-2">What should I do next?</p>
                                <p class="text-muted mb-4">Please contact the school's front desk or administration office
                                    and ask them to link your child to your email address:
                                    <strong>{{ $parent->email }}</strong>.</p>

                                <a href="#" class="btn btn-primary px-4 shadow-sm" style="border-radius: 8px;">
                                    <i class="fas fa-envelope mr-2"></i> Email School Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
