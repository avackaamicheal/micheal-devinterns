@extends('layouts.app')

@section('content')
    <div class="content-wrapper">

        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">School Overview</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>150</h3> <p>Total Students</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <a href="#" class="small-box-footer">View List <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>12</h3> <p>Teaching Staff</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <a href="#" class="small-box-footer">Manage Staff <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $count }}</h3> <p>Active Classes</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <a href="{{ route('classLevel.index', ['school' => $school->slug]) }}" class="small-box-footer">View Classes <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>24</h3> <p>Subjects Offered</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <a href="{{ route('subject.index') }}" class="small-box-footer">Curriculum <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <section class="col-lg-7 connectedSortable">

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-bar mr-1"></i>
                                    Weekly Attendance
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="tab-content p-0">
                                    <div class="chart" id="attendance-chart" style="position: relative; height: 300px;">
                                        <canvas id="revenue-chart-canvas" height="300" style="height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-bolt mr-1"></i>
                                    Quick Actions
                                </h3>
                            </div>
                            <div class="card-body">
                                <button class="btn btn-app bg-success" data-toggle="modal" data-target="#modal-add-student">
                                    <i class="fas fa-user-plus"></i> Add Student
                                </button>
                                <button class="btn btn-app bg-info">
                                    <i class="fas fa-bullhorn"></i> Announcement
                                </button>
                                <button class="btn btn-app bg-warning">
                                    <i class="fas fa-file-invoice"></i> Collect Fees
                                </button>
                            </div>
                        </div>
                    </section>

                    <section class="col-lg-5 connectedSortable">
                        <div class="card bg-gradient-success">
                            <div class="card-header border-0">
                                <h3 class="card-title">
                                    <i class="far fa-calendar-alt"></i>
                                    Academic Calendar
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div id="calendar" style="width: 100%"></div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Classes Added</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="products-list product-list-in-card pl-2 pr-2">
                                    {{-- Loop through recent classes --}}
                                    @foreach($classes as $class)
                                    <li class="item">
                                        <div class="product-info ml-2">
                                            <a href="javascript:void(0)" class="product-title">{{ $class->name }}
                                                <span class="badge badge-primary float-right">{{ $class->sections->count() ?? 0 }} Sections</span>
                                            </a>
                                            <span class="product-description">
                                                Added on {{ $class->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                    </section>
                </div>
            </div>
        </section>
    </div>
@endsection
