<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class ClassLevel extends Model
{

    use Multitenantable;
    protected $fillable = ['name', 'description', 'is_active'];


    public function sections(){
        return $this->hasMany(Section::class);
    }
}
