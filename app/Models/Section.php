<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{

    use Multitenantable;
    protected $fillable = ['class_level_id', 'name', 'capacity', 'is_active'];


    public function classLevel(){
        return $this->belongsTo(ClassLevel::class);
    }
}
