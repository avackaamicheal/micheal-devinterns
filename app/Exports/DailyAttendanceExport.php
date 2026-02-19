<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DailyAttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $sectionId;
    protected $date;

    public function __construct($sectionId, $date)
    {
        $this->sectionId = $sectionId;
        $this->date = $date;
    }

    public function collection()
    {
        return Attendance::with('student.studentProfile')
            ->where('section_id', $this->sectionId)
            ->where('date', $this->date)
            ->get();
    }

    // The interface automatically passes each database record into this $row variable
    public function map($row): array
    {
        return [
            $row->student->studentProfile->admission_number ?? 'N/A',
            $row->student->name,
            $row->date,
            $row->status,
            $row->remarks ?? 'None',
        ];
    }

    public function headings(): array
    {
        return [
            'Admission No',
            'Student Name',
            'Date',
            'Attendance Status',
            'Remarks'
        ];
    }

}
