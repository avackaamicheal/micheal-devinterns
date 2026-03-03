@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid d-flex justify-content-between align-items-center">
                <h1 class="m-0">Financial Summary: <strong>{{ $activeTerm->name ?? 'No Active Term' }}</strong></h1>
                <a href="{{ route('finance.reports.export') }}" class="btn btn-success font-weight-bold shadow-sm">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </a>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>${{ Number::currency($totalExpected, 'NGN') }}</h3>
                                <p>Total Expected Revenue</p>
                            </div>
                            <div class="icon"><i class="fas fa-file-invoice"></i></div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>${{ Number::currency($totalCollected, 'NGN') }}</h3>
                                <p>Total Collected</p>
                            </div>
                            <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ Number::currency($totalOutstanding, 'NGN') }}</h3>
                                <p>Total Outstanding Balance</p>
                            </div>
                            <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Student Ledger Breakdown</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Student Name</th>
                                    <th>Expected</th>
                                    <th>Collected</th>
                                    <th>Balance Due</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    @php
                                        $paid = $invoice->payments_sum_amount ?? 0;
                                        $balance = $invoice->total_amount - $paid;
                                    @endphp
                                    <tr>
                                        <td class="align-middle font-weight-bold">{{ $invoice->invoice_number }}</td>
                                        <td class="align-middle">{{ $invoice->student->name }}</td>
                                        <td class="align-middle">{{ Number::currency($invoice->total_amount, 'NGN') }}</td>
                                        <td class="align-middle text-success">{{ Number::currency($paid, 'NGN') }}</td>
                                        <td class="align-middle text-danger font-weight-bold">
                                            {{ $balance > 0 ?  Number::currency($balance, 'NGN') : '-' }}
                                        </td>
                                        <td class="align-middle">
                                            @if ($invoice->status == 'PAID')
                                                <span class="badge badge-success">PAID</span>
                                            @elseif($invoice->status == 'PARTIAL')
                                                <span class="badge badge-warning">PARTIAL</span>
                                            @else
                                                <span class="badge badge-danger">UNPAID</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center p-4 text-muted">No invoices generated for the
                                            active term yet.</td>
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
