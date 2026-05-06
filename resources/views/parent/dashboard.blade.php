@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header bg-white border-bottom mb-4 pb-3 pt-4">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="m-0 font-weight-bold text-dark">
                            <i class="fas fa-home text-primary mr-2"></i> Family Portal
                        </h1>
                        <p class="text-muted mb-0">
                            Welcome back, {{ $parent->name }} &nbsp;|&nbsp;
                            {{ now()->format('l, F j, Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        @if ($activeTerm)
                            <span class="badge badge-success px-3 py-2" style="border-radius: 20px;">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                {{ $activeTerm->name }}
                            </span>
                        @endif
                        @if ($unreadMessages > 0)
                            <span class="badge badge-danger px-3 py-2 ml-2" style="border-radius: 20px;">
                                <i class="fas fa-envelope mr-1"></i>
                                {{ $unreadMessages }} Unread
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                {{-- Overall Summary Banner --}}
                @if ($children->count() > 1)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-gradient-primary shadow-sm mb-0">
                                <div class="card-body py-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="text-white mb-0 font-weight-bold">
                                                <i class="fas fa-users mr-2"></i>
                                                {{ $children->count() }} Children Enrolled
                                            </h5>
                                            <small class="text-white-50">
                                                Scroll down to view each child's full report
                                            </small>
                                        </div>
                                        <div class="text-right">
                                            <h4 class="text-white mb-0 font-weight-bold">
                                                ₦{{ number_format($totalOutstanding) }}
                                            </h4>
                                            <small class="text-white-50">Total Outstanding Fees</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Loop through each child --}}
                @foreach ($childrenData as $data)
                    @php
                        $child = $data['student'];
                        $invoices = $data['invoices'];
                    @endphp

                    {{-- Child Header --}}
                    <div class="d-flex align-items-center mb-3 mt-4">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3"
                            style="width: 45px; height: 45px; font-size: 1.2em; font-weight: bold;">
                            {{ strtoupper(substr($child->name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="mb-0 font-weight-bold">{{ $child->name }}</h4>
                            <small class="text-muted">
                                {{ $data['classLevel']->name ?? 'N/A' }} —
                                {{ $data['section']->name ?? 'N/A' }} &nbsp;|&nbsp;
                                Admission: {{ $child->studentProfile->admission_number ?? 'N/A' }}
                            </small>
                        </div>
                    </div>

                    {{-- Stats Cards --}}
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box {{ $data['outstandingBalance'] > 0 ? 'bg-danger' : 'bg-success' }}">
                                <div class="inner">
                                    <h3>₦{{ number_format($data['outstandingBalance']) }}</h3>
                                    <p>{{ $data['outstandingBalance'] > 0 ? 'Outstanding Fees' : 'Fees Cleared' }}</p>
                                </div>
                                <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                <a href="#fees-{{ $child->id }}" class="small-box-footer">
                                    {{ $data['outstandingBalance'] > 0 ? 'Pay Now' : 'View Receipts' }}
                                    <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box {{ $data['termRate'] >= 75 ? 'bg-info' : 'bg-warning' }}">
                                <div class="inner">
                                    <h3>{{ $data['termRate'] }}%</h3>
                                    <p>Term Attendance Rate</p>
                                </div>
                                <div class="icon"><i class="fas fa-user-check"></i></div>
                                <a href="#attendance-{{ $child->id }}" class="small-box-footer">
                                    View Breakdown <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3>{{ $data['average'] ?? 'N/A' }}{{ $data['average'] ? '%' : '' }}</h3>
                                    <p>Term Average</p>
                                </div>
                                <div class="icon"><i class="fas fa-graduation-cap"></i></div>
                                <a href="#grades-{{ $child->id }}" class="small-box-footer">
                                    View Grades <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div
                                class="small-box {{ $data['todayAttendance']?->status == 'PRESENT' ? 'bg-success' : ($data['todayAttendance']?->status == 'ABSENT' ? 'bg-danger' : 'bg-secondary') }}">
                                <div class="inner">
                                    <h3>{{ $data['todayAttendance']?->status ?? 'N/A' }}</h3>
                                    <p>Today's Attendance</p>
                                </div>
                                <div class="icon"><i class="fas fa-calendar-day"></i></div>
                                <a href="#attendance-{{ $child->id }}" class="small-box-footer">
                                    View Log <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        {{-- Left Column --}}
                        <div class="col-md-8">

                            {{-- Today's Schedule --}}
                            <div class="card card-outline card-primary shadow-sm">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-calendar-day mr-1 text-primary"></i>
                                        {{ $child->name }}'s Schedule Today
                                        <span class="badge badge-primary ml-2">
                                            {{ now()->format('l') }}
                                        </span>
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    @if ($data['todayClasses']->count() > 0)
                                        <table class="table table-hover m-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Time</th>
                                                    <th>Subject</th>
                                                    <th>Teacher</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($data['todayClasses'] as $slot)
                                                    @php
                                                        $start = Carbon\Carbon::parse($slot->start_time);
                                                        $end = Carbon\Carbon::parse($slot->end_time);
                                                        $isNow = now()->between($start, $end);
                                                    @endphp
                                                    <tr class="{{ $isNow ? 'table-success' : '' }}">
                                                        <td class="align-middle">
                                                            @if ($isNow)
                                                                <span class="badge badge-success mr-1">NOW</span>
                                                            @endif
                                                            {{ $start->format('h:i A') }} -
                                                            {{ $end->format('h:i A') }}
                                                        </td>
                                                        <td class="align-middle font-weight-bold">
                                                            {{ $slot->subject->name }}
                                                        </td>
                                                        <td class="align-middle">
                                                            {{ $slot->teacher->name ?? 'TBA' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="text-center p-4 text-muted">
                                            <i class="fas fa-coffee fa-2x mb-2"></i>
                                            <p>No classes scheduled for today.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Grades --}}
                            <div class="card card-outline card-info shadow-sm" id="grades-{{ $child->id }}">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-chart-line mr-1 text-info"></i>
                                        Academic Performance
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped table-hover m-0">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th class="text-center">Score</th>
                                                <th class="text-center">Grade</th>
                                                <th class="text-center">Remark</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($data['grades'] as $grade)
                                                @php
                                                    $score = $grade->total_score;
                                                    $letter =
                                                        $score >= 70
                                                            ? 'A'
                                                            : ($score >= 60
                                                                ? 'B'
                                                                : ($score >= 50
                                                                    ? 'C'
                                                                    : ($score >= 40
                                                                        ? 'D'
                                                                        : 'F')));
                                                    $remark =
                                                        $score >= 70
                                                            ? 'Excellent'
                                                            : ($score >= 60
                                                                ? 'Very Good'
                                                                : ($score >= 50
                                                                    ? 'Good'
                                                                    : ($score >= 40
                                                                        ? 'Pass'
                                                                        : 'Needs Improvement')));
                                                    $badgeClass =
                                                        $score >= 70
                                                            ? 'success'
                                                            : ($score >= 50
                                                                ? 'warning'
                                                                : 'danger');
                                                @endphp
                                                <tr>
                                                    <td class="align-middle font-weight-bold">
                                                        {{ $grade->subject->name }}
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        {{ $score }}%
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span class="badge badge-{{ $badgeClass }} p-2">
                                                            {{ $letter }}
                                                        </span>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        {{ $remark }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center p-4 text-muted">
                                                        No published grades yet for this term.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if ($data['grades']->count() > 0)
                                    <div class="card-footer d-flex justify-content-between align-items-center">
                                        <span class="font-weight-bold">
                                            Term Average:
                                            <strong class="text-primary">{{ $data['average'] }}%</strong>
                                        </span>
                                        <a href="{{ resolveRoute('reports.single', $child->id) }}"
                                            class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-file-pdf"></i> Download Report Card
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Attendance Breakdown --}}
                            <div class="card card-outline card-success shadow-sm" id="attendance-{{ $child->id }}">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-user-check mr-1 text-success"></i>
                                        Attendance Breakdown
                                    </h3>
                                </div>
                                <div class="card-body">

                                    {{-- Term Summary --}}
                                    <div class="row text-center mb-4">
                                        <div class="col-4">
                                            <div class="h3 font-weight-bold text-success mb-0">
                                                {{ $data['termPresent'] }}
                                            </div>
                                            <small class="text-muted">Present</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="h3 font-weight-bold text-danger mb-0">
                                                {{ $data['termAbsent'] }}
                                            </div>
                                            <small class="text-muted">Absent</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="h3 font-weight-bold text-primary mb-0">
                                                {{ $data['termRate'] }}%
                                            </div>
                                            <small class="text-muted">Rate</small>
                                        </div>
                                    </div>

                                    {{-- Monthly Chart --}}
                                    <h6 class="font-weight-bold text-muted mb-3">
                                        Monthly Breakdown (Last 6 Months)
                                    </h6>
                                    <table class="table table-sm table-bordered m-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Month</th>
                                                <th class="text-center text-success">Present</th>
                                                <th class="text-center text-danger">Absent</th>
                                                <th class="text-center text-warning">Late</th>
                                                <th class="text-center">Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['monthlyBreakdown'] as $month)
                                                <tr>
                                                    <td class="font-weight-bold">{{ $month['month'] }}</td>
                                                    <td class="text-center text-success">{{ $month['present'] }}</td>
                                                    <td class="text-center text-danger">{{ $month['absent'] }}</td>
                                                    <td class="text-center text-warning">{{ $month['late'] }}</td>
                                                    <td class="text-center">
                                                        <span
                                                            class="badge badge-{{ $month['rate'] >= 75 ? 'success' : 'danger' }}">
                                                            {{ $month['rate'] }}%
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        {{-- Right Column --}}
                        <div class="col-md-4">

                            {{-- Fees & Invoices --}}
                            <div class="card card-outline card-danger shadow-sm" id="fees-{{ $child->id }}">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-file-invoice-dollar mr-1 text-danger"></i>
                                        Fees & Payments
                                    </h3>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @forelse($invoices as $invoice)
                                            @php
                                                $paid = $invoice->payments_sum_amount ?? 0;
                                                $balance = $invoice->total_amount - $paid;
                                            @endphp
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="font-weight-bold text-sm">
                                                            {{ $invoice->invoice_number }}
                                                        </div>
                                                        <small class="text-muted">
                                                            Due:
                                                            {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                                                        </small>
                                                        <div class="mt-1">
                                                            <span
                                                                class="badge badge-{{ $invoice->status == 'PAID' ? 'success' : ($invoice->status == 'PARTIAL' ? 'warning' : 'danger') }}">
                                                                {{ $invoice->status }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-muted text-sm">
                                                            ₦{{ number_format($invoice->total_amount) }}
                                                        </div>
                                                        @if ($balance > 0)
                                                            <div class="text-danger font-weight-bold">
                                                                ₦{{ number_format($balance) }} due
                                                            </div>
                                                            {{-- Pay Now Button --}}
                                                            <button class="btn btn-xs btn-danger mt-1" data-toggle="modal"
                                                                data-target="#payModal-{{ $invoice->id }}">
                                                                Pay Now
                                                            </button>
                                                        @else
                                                            <div class="text-success font-weight-bold text-sm">
                                                                Paid in Full
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                        @empty
                                            <li class="list-group-item text-center text-muted p-4">
                                                No invoices for this term.
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                                @if ($data['outstandingBalance'] > 0)
                                    <div class="card-footer bg-danger text-white text-center">
                                        <strong>Total Outstanding:
                                            ₦{{ number_format($data['outstandingBalance']) }}</strong>
                                    </div>
                                @else
                                    <div class="card-footer bg-success text-white text-center">
                                        <strong><i class="fas fa-check-circle mr-1"></i> All Fees Paid</strong>
                                    </div>
                                @endif
                            </div>

                            {{-- Quick Actions --}}
                            <div class="card card-outline card-warning shadow-sm">
                                <div class="card-header">
                                    <h3 class="card-title font-weight-bold">
                                        <i class="fas fa-bolt mr-1 text-warning"></i> Quick Actions
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <a href="{{ resolveRoute('reports.single', $child->id) }}"
                                        class="btn btn-block btn-outline-danger mb-2 text-left">
                                        <i class="fas fa-file-pdf mr-2"></i> Download Report Card
                                    </a>
                                    <a href="{{ resolveRoute('messages.index') }}"
                                        class="btn btn-block btn-outline-success mb-2 text-left">
                                        <i class="fas fa-comments mr-2"></i> Message Teachers
                                    </a>
                                    <a href="{{ resolveRoute('announcements.index') }}"
                                        class="btn btn-block btn-outline-info mb-2 text-left">
                                        <i class="fas fa-bullhorn mr-2"></i> View Announcements
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Pay Modals for each invoice --}}
                    @foreach ($invoices as $invoice)
                        @php $balance = $invoice->total_amount - ($invoice->payments_sum_amount ?? 0); @endphp
                        @if ($balance > 0)
                            <div class="modal fade" id="payModal-{{ $invoice->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('payments.store', $invoice->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">
                                                    Pay Invoice: {{ $invoice->invoice_number }}
                                                </h5>
                                                <button type="button" class="close text-white"
                                                    data-dismiss="modal"><span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-info">
                                                    Outstanding Balance:
                                                    <strong>₦{{ number_format($balance) }}</strong>
                                                </div>
                                                <div class="form-group">
                                                    <label>Amount Paying</label>
                                                    <input type="number" name="amount" class="form-control"
                                                        max="{{ $balance }}" step="0.01" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Payment Method</label>
                                                    <select name="method" class="form-control" required>
                                                        <option value="Bank Transfer">Bank Transfer</option>
                                                        <option value="Cash">Cash</option>
                                                        <option value="POS / Card">POS / Card</option>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Reference / Teller No.</label>
                                                    <input type="text" name="reference" class="form-control"
                                                        placeholder="e.g. Bank teller number">
                                                </div>
                                                <div class="form-group">
                                                    <label>Payment Date</label>
                                                    <input type="date" name="payment_date" class="form-control"
                                                        value="{{ date('Y-m-d') }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-check"></i> Confirm Payment
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if (!$loop->last)
                        <hr class="my-5" style="border-top: 3px dashed #dee2e6;">
                    @endif
                @endforeach

            </div>
        </section>
    </div>
@endsection
