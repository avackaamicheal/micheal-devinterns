@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0">Student Portal</h1>
            <p class="text-muted">Welcome back, {{ $student->name }} | {{ $student->studentProfile->section->classLevel->name ?? 'Class' }} - {{ $student->studentProfile->section->name ?? '' }}</p>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-7">
                    <div class="card card-outline card-primary shadow-sm">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-award"></i> Current Term Grades</h3></div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($grades as $grade)
                                        <tr>
                                            <td><strong>{{ $grade->subject->name }}</strong></td>
                                            <td>{{ $grade->is_locked ? $grade->total_score . '%' : 'Pending' }}</td>
                                            <td>
                                                @if($grade->is_locked)
                                                    <span class="badge badge-success">Published</span>
                                                @else
                                                    <span class="badge badge-warning">Draft</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center text-muted p-3">No grades posted yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card card-outline card-success shadow-sm">
                        <div class="card-header"><h3 class="card-title"><i class="fas fa-file-invoice-dollar"></i> Fees & Invoices</h3></div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($invoices as $invoice)
                                    @php
                                        $paid = $invoice->payments_sum_amount ?? 0;
                                        $balance = $invoice->total_amount - $paid;
                                    @endphp
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $invoice->invoice_number }}</strong><br>
                                            <small class="text-muted">Due: {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</small>
                                        </div>
                                        <div class="text-right">
                                            <span class="d-block font-weight-bold {{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $balance > 0 ? '$'.number_format($balance, 2).' Due' : 'Paid in Full' }}
                                            </span>
                                            <span class="badge badge-{{ $invoice->status == 'PAID' ? 'success' : ($invoice->status == 'PARTIAL' ? 'warning' : 'danger') }}">
                                                {{ $invoice->status }}
                                            </span>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted">No invoices generated for this term.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection
