<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory;
    protected $fillable = ['name','slug','email', 'address', 'principal_name', 'phone_number'];


    protected static function boot()
    {
        parent::boot();

        static::saving(function ($school) {
            if (empty($school->slug)) {
                $school->slug = Str::slug($school->name);
            }
        });
    }

    // 4. Tell Laravel to use this column for URLs
    public function getRouteKeyName()
    {
        return 'slug';
    }


}
