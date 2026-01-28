<?php

namespace App\Models;

use App\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{

    protected $fillable = ['name','email', 'address', 'principal_name', 'phone_number'];


}
