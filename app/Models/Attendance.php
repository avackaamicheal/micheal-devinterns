<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory, Multitenantable;
    protected $fillable = [
        'term_id',
        'section_id',
        'student_id',
        'date',
        'status',
        'remarks'
    ] ;

    public function term()  {
        return $this->belongsTo(Term::class);
    }

    public function section(){
      return $this->belongsTo(Section::class);
    }

    public function user() {
        return $this->belongsTo(User::class,'student_id');
    }
}
