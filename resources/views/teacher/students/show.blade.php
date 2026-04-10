@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <h1 class="m-0"><a href="{{ route('teacher.students.index') }}" class="text-muted"><i
                            class="fas fa-arrow-left"></i></a> Student Profile</h1>

                <div>
                    @if ($student->studentProfile->parent_id)
                        <form action="{{ route('messages.index', $student->studentProfile->parent_id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-envelope mr-1"></i>
                                Message Parent</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <section class="content mt-3">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-4">
                        <div class="card card-primary card-outline shadow-sm">
                            <div class="card-body box-profile text-center">
                                <img class="profile-user-img img-fluid img-circle"
                                    src="{{ asset('dist/img/user1-128x128.jpg') }}" alt="User profile picture">
                                <h3 class="profile-username">{{ $student->name }}</h3>
                                <p class="text-muted">{{ $student->studentProfile->admission_number ?? 'No ID Assigned' }}
                                </p>

                                <ul class="list-group list-group-unbordered mb-3 text-left mt-4">
                                    <li class="list-group-item">
                                        <b>Class</b> <a
                                            class="float-right text-dark">{{ $student->studentProfile->section->classLevel->name ?? '' }}
                                            - {{ $student->studentProfile->section->name ?? '' }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Gender</b> <a
                                            class="float-right text-dark">{{ $student->studentProfile->gender ?? 'N/A' }}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Date of Birth</b> <a
                                            class="float-right text-dark">{{ $student->studentProfile->date_of_birth ?? 'N/A' }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card shadow-sm border-danger" style="border-top: 3px solid #dc3545;">
                            <div class="card-header bg-white">
                                <h3 class="card-title text-danger font-weight-bold"><i
                                        class="fas fa-notes-medical mr-1"></i> Health & Needs</h3>
                            </div>
                            <div class="card-body text-sm">
                                <p><strong>Allergies:</strong> Penicillin, Peanuts</p>
                                <p class="mb-0"><strong>Notes:</strong> Student needs to sit near the front of the class
                                    due to mild visual impairment.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">

                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h3 class="card-title font-weight-bold"><i class="fas fa-phone-alt text-success mr-2"></i>
                                    Primary Contact</h3>
                            </div>
                            <div class="card-body">
                                @if ($student->studentProfile->parent)
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <p class="mb-1 text-muted">Parent / Guardian Name</p>
                                            <h5>{{ $student->studentProfile->parent->name }}</h5>
                                        </div>
                                        <div class="col-sm-6">
                                            <p class="mb-1 text-muted">Contact Email</p>
                                            <h5>{{ $student->studentProfile->parent->email }}</h5>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-light text-muted border mb-0"><i
                                            class="fas fa-info-circle mr-2"></i> No parent account linked to this student
                                        yet.</div>
                                @endif
                            </div>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h3 class="card-title font-weight-bold"><i class="fas fa-graduation-cap text-info mr-2"></i>
                                    My Subjects Snapshot</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Subject You Teach</th>
                                            <th>Current Grade</th>
                                            <th>Recent Attendance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($teacherSubjects as $assignment)
                                            <tr>
                                                <td class="font-weight-bold">{{ $assignment->subject->name }}</td>
                                                <td><span class="badge badge-warning">Awaiting Exams</span></td>
                                                <td><span class="text-success"><i class="fas fa-check-circle"></i>
                                                        95%</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-muted text-center p-3">You do not teach this
                                                    student directly.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
