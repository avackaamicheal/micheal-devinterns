<?php

namespace App\Http\Controllers\Academic;

use App\Models\User;
use App\Models\ClassLevel;
use Illuminate\Http\Request;
use App\Models\ParentProfile;
use App\Models\StudentProfile;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\StoreStudentRequest;

class StudentAdmissionController extends Controller
{
    public function index()
    // Fetch students scoped to the active school
    {
        $schoolId = session('active_school') ?? Auth::user()->school_id;

        if (!$schoolId) {
            return redirect()->back()->with('error', 'No active school found');
        }

        $students = User::query()
            ->role('Student')
            // ->where('school_id', $schoolId)
            // EAGER LOAD: Get Profile, Section (and its Class), and Parents in one go
            ->with(['studentProfile.section.classLevel', 'parents'])
            ->latest()
            ->paginate(10);

        return view('student.index', compact('students'));
    }

    public function create()
    {
        $classLevels = ClassLevel::with('sections')->get(); // Load sections for dynamic dropdown
        return view('student.create', compact('classLevels'));
    }

    public function store(StoreStudentRequest $request)
    {
        // Start Transaction: All or Nothing
        DB::beginTransaction();

        try {
            // 1. Create Student User Account
            $studentUser = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email, // Can be null
                'password' => Hash::make('student123'), // Default password
                'school_id' => session('active_school'),
            ]);

            // 2. ASSIGN SPATIE ROLE (Crucial Step)
            // Make sure "Student" matches the spelling in your database exactly
            $studentUser->assignRole('Student');

            // 3. Create Student Profile
            StudentProfile::create([
                'school_id' => session('active_school') ?? Auth::user()->school_id,
                'user_id' => $studentUser->id,
                'admission_number' => $request->admission_number,
                'class_level_id' => $request->class_level_id,
                'section_id' => $request->section_id,
                'date_of_birth' => $request->dob,
                'gender' => $request->gender, // Ensure gender is in fillable
                'address' => $request->address, // Ensure address is in fillable
            ]);

            // 3. Handle Parent (Find or Create)
            $parentUser = User::where('email', $request->parent_email)->first();

            if (!$parentUser) {
                // Parent doesn't exist, create new
                $parentUser = User::create([
                    'name' => $request->parent_name,
                    'email' => $request->parent_email,
                    'password' => Hash::make('parent123'),
                    'school_id' => session('active_school'), // Or null if global
                ]);

                $parentUser->assignRole('Parent');

                // Create Parent Profile
                ParentProfile::create([
                    'user_id' => $parentUser->id,
                    'occupation' => $request->parent_occupation ?? 'N/A', // Add field to form if needed
                    'alt_phone' => $request->parent_phone,
                ]);
            }

            // 4. Link Parent to Student (The Pivot Table)
            // This allows the parent to see this specific student
            $parentUser->children()->attach($studentUser->id, [
                'relationship' => $request->relationship
            ]);

            DB::commit(); // Save everything

            return response()->json([
                'status' => 'success',
                'message' => 'Student admitted and linked to parent successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack(); // Undo everything if error

            // Log the error for debugging
            Log::error($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Admission failed: ' . $e->getMessage()
            ], 500);
        }
    }


    public function destroy(User $student)
    {
        // Due to 'cascadeOnDelete' in migrations, deleting the User
        // automatically wipes their Profile and Parent links.
        $student->delete();

        if (request()->ajax() || request()->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Student record deleted successfully!'
            ]);
        }

        return back()->with('success', 'Student record deleted.');
    }

    public function export()
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120', // Just ensure it's a file and under 5MB
        ], [
            'file.required' => 'Please select a file to upload.',
            'file.file' => 'The uploaded item must be a valid file.',
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));

            return back()->with('success', 'Students imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            return back()->with('error', 'Import failed: ' . implode(' | ', $errorMessages));
        } catch (\Exception $e) {
            return back()->with('error', 'System Error: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=student_import_template.csv',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0'
        ];

        // The exact headers your Import class is looking for
        $columns = [
            'first_name', 'last_name', 'email', 'admission_number',
            'class_level_id', 'section_id', 'date_of_birth', 'gender', 'address'
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');

            // 1. Write the Header Row
            fputcsv($file, $columns);

            // 2. Write an Example Row (Helps the admin understand the format)
            fputcsv($file, [
                'John', 'Doe', 'johndoe@example.com', 'ADM-2026-001',
                '1', '1', '2010-05-14', 'Male', '123 Learning Ave'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
