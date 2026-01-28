<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use Multitenantable; // Automatically scopes queries to the active school

    protected $fillable = ['name', 'code', 'description', 'is_active'];
}
