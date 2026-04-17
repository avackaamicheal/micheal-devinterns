<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\ClassroomAssignment;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;



class User extends Authenticatable
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'school_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    // Link to Student Profile
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    // Link to Teacher Profile
    public function teacherProfile()
    {
        return $this->hasOne(TeacherProfile::class);
    }

    // Teacher -> Assignments Relationship
    public function assignments()
    {
        return $this->hasMany(ClassroomAssignment::class, 'teacher_id');
    }

    // app/Models/User.php

    // A User (Parent) can have many students (Users)
    public function children()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot('relationship')
            ->withTimestamps();
    }

    // A User (Student) can have many parents (Users)
    public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id')
            ->withPivot('relationship')
            ->withTimestamps();
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get all grade records for a student.
     * This is the engine that will power our Report Cards.
     */
    public function grades()
    {
        return $this->hasMany(GradeRecord::class, 'student_id');
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class, 'student_id');
    }

    public function allocations()
    {
        return $this->hasMany(ClassroomAssignment::class, 'teacher_id');
    }


    // In User.php
    public function allowedSectionIds(): array
    {
        if ($this->hasRole('SchoolAdmin')) {
            return Section::pluck('id')->toArray();
        }

        if ($this->hasRole('Teacher')) {
            return $this->allocations()->pluck('section_id')->toArray();
        }

        return [];
    }
}
