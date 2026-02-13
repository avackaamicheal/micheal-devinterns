<?php

namespace App\Models;

use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class ClassroomAssignment extends Model
{

    use Multitenantable; // Automatically saves school_id
    protected $table = 'classroom_assignments';

    protected $fillable = [
        'school_id',
        'teacher_id',
        'section_id',
        'subject_id'
    ];

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
