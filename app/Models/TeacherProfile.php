<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    use Multitenantable;

    protected $fillable = [
        'user_id',
        'school_id',
        'profile_picture',
        'employee_id',
        'qualification',
        'hire_date',
        'phone',
        'date_of_birth',
        'gender',
        'marital_status',
        'address',
        'class_level_id',
        'section_id',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function classLevel()
    {
        return $this->belongsTo(ClassLevel::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public static function generateEmployeeId(): string
{
    // Get the active school
    $school = School::find(session('active_school'));

    // Extract first letter of every word in school name e.g. "Saint Murumba College" → "SMC"
    $schoolInitials = collect(explode(' ', $school->name))
        ->map(fn($word) => strtoupper($word[0]))
        ->implode('');

    $prefix = $schoolInitials . '-TCH';
    $year = date('Y');

   // Use withoutGlobalScopes to avoid SchoolScope interference
    $last = static::withoutGlobalScopes()
        ->where('school_id', session('active_school'))
        ->whereYear('created_at', $year)
        ->where('employee_id', 'like', "{$prefix}%")
        ->latest()
        ->first();

    if ($last && $last->employee_id) {
        $lastNumber = (int) substr($last->employee_id, -4);
        $next = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $next = '0001';
    }

    return "{$prefix}-{$year}-{$next}";
}
}
