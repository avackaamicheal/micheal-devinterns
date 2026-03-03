<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\Term;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FinanceReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct(private $schoolId)
    {
    }

    public function collection()
    {
        $activeTerm = Term::where('is_active', true)->first();

        // Load invoices for the current term and let the database calculate the sum of payments
        return Invoice::with(['student.studentProfile'])
            ->where('school_id', $this->schoolId)  // scope to school
            ->where('term_id', $activeTerm?->id)
            ->withSum('payments', 'amount')
            ->get();
    }

    public function map($invoice): array
    {
        $paid = $invoice->payments_sum_amount ?? 0;
        $balance = $invoice->total_amount - $paid;

        return [
            $invoice->invoice_number,
            $invoice->student->studentProfile->admission_number ?? 'N/A',
            $invoice->student->name,
            $invoice->total_amount,
            $paid,
            $balance,
            $invoice->status,
        ];
    }

    public function headings(): array
    {
        return [
            'Invoice #',
            'Admission No',
            'Student Name',
            'Total Expected ($)',
            'Total Collected ($)',
            'Outstanding Balance ($)',
            'Status'
        ];
    }
}
