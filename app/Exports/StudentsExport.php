<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
       // Get all students with their profiles
        return User::role('Student')
            ->with('studentProfile.section.classLevel')
            ->get();
    }

    public function map($student): array
    {
        return [
            $student->name,
            $student->email,
            $student->studentProfile?->admission_number,
            $student->studentProfile?->section->classLevel->name ?? 'N/A',
            $student->studentProfile?->section->name ?? 'N/A',
            $student->studentProfile?->gender,
        ];
    }

    public function headings(): array
    {
        return ['Name', 'Email', 'Admission No', 'Class', 'Section', 'Gender'];
    }
}
