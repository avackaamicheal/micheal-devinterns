<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

        {{-- Dynamic Home Link based on Role --}}
        <li class="nav-item d-none d-sm-inline-block">
            @role('SuperAdmin')
                <a href="{{ route('superadmin.dashboard') }}" class="nav-link">SuperAdmin Dashboard</a>
            @endrole
            @role('SchoolAdmin')
                <a href="{{ route('schooladmin.dashboard') }}" class="nav-link">School Dashboard</a>
            @endrole
            @role('Teacher')
                <a href="{{ route('teacher.dashboard') }}" class="nav-link">My Classroom</a>
            @endrole
            @role('Student')
                <a href="{{ route('student.dashboard') }}" class="nav-link">Student Portal</a>
            @endrole
            @role('Parent')
                <a href="{{ route('parent.dashboard') }}" class="nav-link">Family Portal</a>
            @endrole
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        {{-- Only show this dropdown if the user is the SuperAdmin --}}
        @role('SuperAdmin')
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    @if (session('active_school'))
                        <i class="fas fa-school text-warning"></i>
                        <span class="font-weight-bold text-dark">
                            {{ \App\Models\School::find(session('active_school'))->name ?? 'School Selected' }}
                        </span>
                    @else
                        <i class="fas fa-globe text-primary"></i> Global View
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header">Switch Context</span>
                    <div class="dropdown-divider"></div>

                    {{-- Option: Go Global --}}
                    <form action="{{ route('school.context') }}" method="POST">
                        @csrf
                        <input type="hidden" name="school_id" value="">
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-globe mr-2"></i> State Overview
                        </button>
                    </form>

                    {{-- Option: List Schools (Limit to 5 for UI, add 'View All' link if needed) --}}
                    @foreach (\App\Models\School::limit(5)->get() as $school)
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('school.context') }}" method="POST">
                            @csrf
                            <input type="hidden" name="school_id" value="{{ $school->id }}">
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-school mr-2 text-muted"></i> {{ $school->name }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </li>
        @endrole

        @auth
            @if (session('active_school'))
                @php
                    $activeSession = \App\Models\AcademicSession::where('school_id', session('active_school'))
                        ->where('is_active', true)
                        ->first();
                    $activeTerm = \App\Models\Term::where('school_id', session('active_school'))
                        ->where('is_active', true)
                        ->first();
                @endphp

                <li class="nav-item d-none d-md-flex align-items-center mr-3">
                    @if ($activeSession && $activeTerm)
                        <span class="badge badge-success px-3 py-2 text-sm elevation-1" style="border-radius: 20px;">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ $activeSession->name }} &nbsp;|&nbsp; {{ $activeTerm->name }}
                        </span>
                    @else
                        <span class="badge badge-danger px-3 py-2 text-sm elevation-1" style="border-radius: 20px;">
                            <i class="fas fa-exclamation-triangle mr-1"></i> No Active Term Setup
                        </span>
                    @endif
                </li>
            @endif

            @role('Teacher')
                <li class="nav-item dropdown">
                    <a class="nav-link bg-primary rounded px-3 mr-2 text-white" data-toggle="dropdown" href="#"
                        style="margin-top: 4px; padding-top: 4px; height: 30px; line-height: 22px;">
                        <i class="fas fa-plus mr-1"></i> Quick Add
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow-sm">
                        <span class="dropdown-header">Classroom Actions</span>
                        <div class="dropdown-divider"></div>

                        <a href="#" class="dropdown-item">
                            <i class="fas fa-tasks mr-2 text-primary"></i> New Assignment
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file-pdf mr-2 text-danger"></i> Upload Material
                        </a>
                        <a href="{{ route('teacher.announcements.index') }}" class="dropdown-item">
                            <i class="fas fa-bullhorn mr-2 text-warning"></i> Post Announcement
                        </a>

                        <div class="dropdown-divider"></div>
                        <a href="{{ route('teacher.messages.index') }}" class="dropdown-item">
                            <i class="fas fa-envelope mr-2 text-success"></i> Message Parent
                        </a>
                    </div>
                </li>
            @endrole

            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">3 Notifications</span>
                    <div class="dropdown-divider"></div>

                    @role('Teacher')
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file-alt mr-2"></i> 2 Assignments Submitted
                            <span class="float-right text-muted text-sm">3 mins</span>
                        </a>
                        @elserole('Student')
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-chalkboard mr-2"></i> Homework Due: Math
                            <span class="float-right text-muted text-sm">2 hours</span>
                        </a>
                    @else
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> System Update
                            <span class="float-right text-muted text-sm">1 day</span>
                        </a>
                    @endrole

                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>
        @endauth

        @auth
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="user-image img-circle elevation-2"
                        alt="User Image">
                    <span class="d-none d-md-inline">{{ auth()->user()?->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <li class="user-header bg-primary">
                        <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                            alt="User Image">
                        <p>
                            {{ auth()->user()?->name }}
                            <small>{{ ucwords(str_replace('_', ' ', auth()->user()?->roles->first()->name ?? 'User')) }}</small>
                            @if (session('active_school'))
                                <small
                                    class="d-block mt-1">{{ \App\Models\School::find(session('active_school'))->name }}</small>
                            @endif
                        </p>
                    </li>
                    <li class="user-footer">
                        @role('Teacher')
                            <a href="{{ route('teacher.profile') }}" class="btn btn-default btn-flat">Profile</a>
                        @else
                            <a href="#" class="btn btn-default btn-flat">Profile</a>
                        @endrole
                        <form action="{{ route('logout') }}" method="POST" class="float-right">
                            @csrf
                            <button type="submit" class="btn btn-default btn-flat">Sign out</button>
                        </form>
                    </li>
                </ul>
            </li>
        @endauth
    </ul>
</nav>

@auth
    <aside class="main-sidebar sidebar-dark-primary elevation-4">

        @role('SuperAdmin')
            <a href="{{ route('superadmin.dashboard') }}" class="brand-link">
                <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Axia SMS</span>
            </a>
        @else
            <a href="#" class="brand-link">
                <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="School Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light text-truncate"
                    style="max-width: 150px; display: inline-block; vertical-align: middle;">
                    {{ \App\Models\School::find(session('active_school'))->name ?? 'My School' }}
                </span>
            </a>
        @endrole

        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
                        alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ auth()->user()->name }} ({{ auth()->user()->getRoleNames()->first() }})</a>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">

                    <li class="nav-item menu-open">
                        @role('SuperAdmin')
                            <a href="{{ route('superadmin.dashboard') }}" class="nav-link @activeRoute('superadmin.dashboard')"><i
                                    class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        @endrole
                        @role('SchoolAdmin')
                            <a href="{{ route('schooladmin.dashboard') }}" class="nav-link @activeRoute('schooladmin.dashboard')"><i
                                    class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        @endrole
                        @role('Teacher')
                            <a href="{{ route('teacher.dashboard') }}" class="nav-link @activeRoute('teacher.dashboard')"><i
                                    class="nav-icon fas fa-chalkboard-teacher"></i>
                                <p>My Dashboard</p>
                            </a>
                        @endrole
                        @role('Student')
                            <a href="{{ route('student.dashboard') }}" class="nav-link @activeRoute('student.dashboard')"><i
                                    class="nav-icon fas fa-user-graduate"></i>
                                <p>Student Portal</p>
                            </a>
                        @endrole
                        @role('Parent')
                            <a href="{{ route('parent.dashboard') }}" class="nav-link @activeRoute('parent.dashboard')"><i
                                    class="nav-icon fas fa-user-friends"></i>
                                <p>Parent Portal</p>
                            </a>
                        @endrole
                    </li>

                    @role('SuperAdmin')
                        <li class="nav-item">
                            <a href="{{ route('school.index') }}" class="nav-link @activeRoute('school.*')"><i
                                    class="nav-icon fas fa-school"></i>
                                <p>Schools</p>
                            </a>
                        </li>
                    @endrole

                    @role('SchoolAdmin')
                        <li class="nav-header">ACADEMICS</li>
                        <li class="nav-item"><a href="{{ route('classLevel.index') }}" class="nav-link @activeRoute('classLevel.*')"><i
                                    class="nav-icon fas fa-layer-group"></i>
                                <p>Classes / Grades</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('section.index') }}" class="nav-link @activeRoute('section.*')"><i
                                    class="nav-icon fas fa-puzzle-piece"></i>
                                <p>Sections</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('subject.index') }}" class="nav-link @activeRoute('subject.*')"><i
                                    class="nav-icon fas fa-book"></i>
                                <p>Subjects</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('admin.timetable.index') }}"
                                class="nav-link @activeRoute('timetable.*')"><i class="nav-icon fas fa-calendar-week"></i>
                                <p>Timetable</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('admin.attendance.index') }}"
                                class="nav-link @activeRoute('attendance.*')"><i class="nav-icon fas fa-user-check"></i>
                                <p>Attendance</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('admin.grades.index') }}"
                                class="nav-link @activeRoute('grades.*')"><i class="nav-icon fas fa-graduation-cap"></i>
                                <p>Grade</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('admin.assessments.index') }}"
                                class="nav-link @activeRoute('assessments.*')"><i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Assessment</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('admin.reports.index') }}"
                                class="nav-link @activeRoute('reports.*')"><i class="nav-icon fas fa-file-contract"></i>
                                <p>Report Cards</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('academic-settings.index') }}"
                                class="nav-link @activeRoute('academic-settings.*')"><i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Academic Settings</p>
                            </a></li>

                        <li class="nav-header">PEOPLE</li>
                        <li class="nav-item">
                            <a href="{{ route('teachers.index') }}" class="nav-link @activeRoute('teachers.*')">
                                <i class="nav-icon fas fa-user-tie"></i>
                                <p>Teachers <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('teachers.index') }}" class="nav-link @activeRoute('teachers.index', 'teachers.edit')">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>All Teachers</p>
                                    </a>
                                </li>
                                <li class="nav-item"><a href="{{ route('teachers.create') }}"
                                        class="nav-link @activeRoute('teachers.create')"><i class="far fa-circle nav-icon"></i>
                                        <p>Add Teachers</p>
                                    </a></li>
                                <li class="nav-item"><a href="{{ route('teachers.assignments') }}"
                                        class="nav-link @activeRoute('teachers.assignments')"><i class="far fa-circle nav-icon"></i>
                                        <p>Assignments</p>
                                    </a></li>
                            </ul>
                        </li>
                        <li class="nav-item @menuOpen('student.*')">
                            <a href="{{ route('student.index') }}" class="nav-link @activeRoute('student*')"><i
                                    class="nav-icon fas fa-user-graduate"></i>
                                <p>Students <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="{{ route('student.index') }}"
                                        class="nav-link @activeRoute('student.index')"><i class="far fa-circle nav-icon"></i>
                                        <p>All Students</p>
                                    </a></li>
                                <li class="nav-item"><a href="{{ route('student.create') }}"
                                        class="nav-link @activeRoute('student.create')"><i class="far fa-circle nav-icon"></i>
                                        <p>Admission</p>
                                    </a></li>
                            </ul>
                        </li>

                        <li class="nav-header">FINANCE</li>
                        <li class="nav-item"><a href="{{ route('fees.index') }}" class="nav-link @activeRoute('fees.*')"><i
                                    class="nav-icon fas fa-coins"></i>
                                <p>Fees</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('invoices.index') }}" class="nav-link @activeRoute('invoices.*')"><i
                                    class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Invoice</p>
                            </a></li>
                        <li class="nav-item"><a href="{{ route('finance.reports.index') }}"
                                class="nav-link @activeRoute('finance.reports.*')"><i class="nav-icon fas fa-chart-pie"></i>
                                <p>Reports</p>
                            </a></li>
                    @endrole

                    @role('Teacher')
                        <li class="nav-header">CLASSROOM</li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.classes') }}" class="nav-link @activeRoute('teacher.classes')">
                                <i class="nav-icon fas fa-users"></i>
                                <p>My Classes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.grades.index') }}" class="nav-link @activeRoute('teacher.grades.*')">
                                <i class="nav-icon fas fa-edit"></i>
                                <p>Enter Grades</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.attendance.index') }}" class="nav-link @activeRoute('teacher.attendance.*')">
                                <i class="nav-icon fas fa-user-check"></i>
                                <p>Take Attendance</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.timetable.index') }}" class="nav-link @activeRoute('teacher.timetable.*')">
                                <i class="nav-icon fas fa-calendar-week"></i>
                                <p>My Schedule</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.reports.index') }}" class="nav-link @activeRoute('teacher.reports.*')">
                                <i class="nav-icon fas fa-file-contract"></i>
                                <p>Report Cards</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.assessments.index') }}" class="nav-link @activeRoute('teacher.assessments.*')">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Assessments</p>
                            </a>
                        </li>
                        <li class="nav-header">MY STUDENTS</li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.students') }}" class="nav-link @activeRoute('teacher.students.*')">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Student Directory</p>
                            </a>
                        </li>

                        <li class="nav-header">PERSONAL</li>
                        <li class="nav-item">
                            <a href="{{ route('teacher.profile') }}" class="nav-link @activeRoute('teacher.profile')">
                                <i class="nav-icon fas fa-user-circle"></i>
                                <p>My Profile</p>
                            </a>
                        </li>
                    @endrole

                    @hasanyrole('SchoolAdmin|Teacher|Student|Parent')
                        <li class="nav-header">COMMUNICATION</li>

                        @hasanyrole('SchoolAdmin|Teacher')
                            <li class="nav-item">
                                <a href="{{ resolveRoute('announcements.index') }}" class="nav-link @activeRoute('admin.announcements.*', 'teacher.announcements.*')">
                                    <i class="nav-icon fas fa-bullhorn"></i>
                                    <p>Announcements</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ resolveRoute('messages.index') }}" class="nav-link @activeRoute('admin.messages.*', 'teacher.messages.*')">
                                    <i class="nav-icon fas fa-envelope"></i>
                                    <p>Messages</p>
                                </a>
                            </li>
                        @endhasanyrole

                        @hasanyrole('Student|Parent')
                            {{-- Build student/parent communication routes when needed --}}
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-bullhorn"></i>
                                    <p>Announcements</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-envelope"></i>
                                    <p>Messages</p>
                                </a>
                            </li>
                        @endhasanyrole
                    @endhasanyrole

                    @role('SchoolAdmin')
                        <li class="nav-header">SETTINGS</li>
                        <li class="nav-item"><a href="{{ route('school.profile') }}" class="nav-link"><i
                                    class="nav-icon fas fa-cogs"></i>
                                <p>School Profile</p>
                            </a></li>
                    @endrole

                </ul>
            </nav>
        </div>
    </aside>
@endauth
