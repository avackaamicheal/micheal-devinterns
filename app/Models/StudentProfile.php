<?php

namespace App\Models;

use App\Models\ClassLevel;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'admission_number',
        'class_level_id',
        'section_id', // This field exists in your DB
        'date_of_birth',
        'gender',
        'address',
    ];

    // --- ADD THIS METHOD ---
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // You likely need this one too for the Class Level name
    public function classLevel()
    {
        return $this->belongsTo(ClassLevel::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public static function generateAdmissionNumber(): string
    {
        $school = School::find(session('active_school'));

        $schoolInitials = collect(explode(' ', $school->name))
            ->map(fn($word) => strtoupper($word[0]))
            ->implode('');

        $prefix = $schoolInitials . '-STU';
        $year = date('Y');

        $last = static::withoutGlobalScopes()
            ->where('school_id', session('active_school'))
            ->whereYear('created_at', $year)
            ->where('admission_number', 'like', "{$prefix}%")
            ->latest()
            ->first();

        if ($last && $last->admission_number) {
            $lastNumber = (int) substr($last->admission_number, -4);
            $next = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        return "{$prefix}-{$year}-{$next}";
    }
}
