<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\ClassroomAssignment;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;



class User extends Authenticatable
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
}
