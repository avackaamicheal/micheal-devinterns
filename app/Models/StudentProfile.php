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
}
