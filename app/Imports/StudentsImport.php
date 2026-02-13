<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            DB::transaction(function () use ($row) {

                // 1. Create the User account
                $user = User::create([
                    'name' => $row['first_name'] . ' ' . $row['last_name'],
                    'email' => $row['email'],
                    'password' => Hash::make('student123'),
                    'school_id' => session('active_school') ?? auth()->user()->school_id,
                ]);

                // 2. Assign Spatie Role
                $user->assignRole('Student');

                // --- 3. THE DATE PARSING FIX ---
                $rawDate = $row['date_of_birth'];

                if (is_numeric($rawDate)) {
                    // Scenario A: It's an .xlsx file, and Excel turned the date into an integer (e.g., 43900)
                    $formattedDob = Date::excelToDateTimeObject($rawDate)->format('Y-m-d');
                } else {
                    // Scenario B: It's a CSV string (e.g., "14/05/2010" or "05-14-2010")
                    // Carbon will intelligently read it and translate it to "YYYY-MM-DD"
                    $formattedDob = Carbon::parse($rawDate)->format('Y-m-d');
                }

                // 4. Create the Student Profile
                StudentProfile::create([
                    'user_id' => $user->id,
                    'admission_number' => $row['admission_number'],
                    'class_level_id' => $row['class_level_id'],
                    'section_id' => $row['section_id'],
                    'date_of_birth' => $formattedDob, // Use our newly formatted, safe date
                    'gender' => $row['gender'],
                    'address' => $row['address'],
                ]);
            });
        }
    }

    public function rules(): array
    {
        return [
            '*.first_name' => 'required|string',
            '*.last_name' => 'required|string',
            '*.email' => 'required|email|unique:users,email',
            '*.admission_number' => 'required|unique:student_profiles,admission_number',
            '*.class_level_id' => 'required|exists:class_levels,id',
            '*.section_id' => 'required|exists:sections,id',
            '*.date_of_birth' => 'required',
            '*.gender' => 'required|in:Male,Female',
        ];
    }
}
