<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

        {{-- Dynamic Home Link based on Role --}}
        <li class="nav-item d-none d-sm-inline-block">
            @if(auth()->user()?->hasRole('SuperAdmin'))
                <a href="{{ route('superadmin.dashboard') }}" class="nav-link">SuperAdmin Dashboard</a>
            @elseif(auth()->user()?->hasRole('SchoolAdmin'))
                <a href="{{ route('schooladmin.dashboard') }}" class="nav-link">School Dashboard</a>
            {{-- @elseif(auth()->user()->role === 'teacher')
                <a href="{{ route('teacher.dashboard') }}" class="nav-link">My Classroom</a>
            @else
                <a href="{{ route('student.dashboard') }}" class="nav-link">My Learning</a> --}}
            @endif
        </li>

    <ul class="navbar-nav ml-auto">

        {{-- Only show this dropdown if the user is the SuperAdmin --}}
        @if(auth()->user()?->hasRole('SuperAdmin'))
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    @if(session('active_school'))
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
                    @foreach(\App\Models\School::limit(5)->get() as $school)
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
        @endif

        @auth
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">3 Notifications</span>
                    <div class="dropdown-divider"></div>

                    {{-- Example: Teacher Notification --}}
                    @if(auth()->user()?->hasRole('Teacher'))
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-file-alt mr-2"></i> 2 Assignments Submitted
                            <span class="float-right text-muted text-sm">3 mins</span>
                        </a>
                    {{-- Example: Parent/Student Notification --}}
                    @elseif(auth()->user()?->hasRole('Student'))
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-chalkboard mr-2"></i> Homework Due: Math
                            <span class="float-right text-muted text-sm">2 hours</span>
                        </a>
                    {{-- Default --}}
                    @else
                        <a href="#" class="dropdown-item">
                            <i class="fas fa-envelope mr-2"></i> System Update
                            <span class="float-right text-muted text-sm">1 day</span>
                        </a>
                    @endif

                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>
        @endauth

        @auth
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    {{-- User Image --}}
                    <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="user-image img-circle elevation-2" alt="User Image">
                    {{-- User Name --}}
                    <span class="d-none d-md-inline">{{ auth()->user()?->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <li class="user-header bg-primary">
                        <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
                        <p>
                            {{ auth()->user()?->name }}
                            {{-- Display Role Nicely (e.g., 'school_admin' -> 'School Administrator') --}}
                            <small>{{ ucwords(str_replace('_', ' ', auth()->user()?->role)) }}</small>

                            @if(session('active_school'))
                                <small class="d-block mt-1">
                                    {{ \App\Models\School::find(session('active_school'))->name }}
                                </small>
                            @endif
                        </p>
                    </li>

                    <li class="user-footer">
                        <a href="#" class="btn btn-default btn-flat">Profile</a>

                        {{-- Secure Logout Form --}}
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
<!-- /.navbar -->
<!-- Main Sidebar Container -->
@auth
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="index3.html" class="brand-link">
            <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">Axia SMS</span>
        </a>
        @if (auth()->user()->hasRole('SchoolAdmin'))
            <a href="#" class="brand-link">
                <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="School Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                {{-- Display the Active School Name from Session --}}
                <span class="brand-text font-weight-light">
                    {{ \App\Models\School::find(session('active_school'))->name ?? 'My School' }}
                </span>
            </a>
        @endif

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ auth()->user()->name }}</a>
                </div>
            </div>



            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                   with font-awesome or any other icon font library -->
                    <li class="nav-item menu-open">
                        @if (auth()->user()?->hasRole('SuperAdmin'))
                            <a href="{{ route('superadmin.dashboard') }}" class="nav-link active">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>

                        @elseif(auth()->user()?->hasRole('SchoolAdmin'))
                            <a href="{{ route('schooladmin.dashboard') }}" class="nav-link active">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>

                        @endif
                    </li>

                    @if (auth()->user()?->hasRole('SuperAdmin'))
                        <li class="nav-item">
                            <a href="{{ route('school.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-school"></i>
                                <p>
                                    School
                                </p>
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->hasRole('SchoolAdmin'))
                        <li class="nav-header">ACADEMICS</li>

                        <li class="nav-item">
                            <a href="{{ route('classLevel.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>Classes / Grades</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('section.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-puzzle-piece"></i>
                                <p>Sections</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('subject.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Subjects</p>
                            </a>
                        </li>

                        <li class="nav-header">PEOPLE</li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-chalkboard-teacher"></i>
                                <p>
                                    Teachers
                                    <span class="badge badge-info right">12</span>
                                </p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-user-graduate"></i>
                                <p>
                                    Students
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>All Students</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Admission</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-header">SETTINGS</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>School Profile</p>
                            </a>
                        </li>
                    @endif

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
@endauth
