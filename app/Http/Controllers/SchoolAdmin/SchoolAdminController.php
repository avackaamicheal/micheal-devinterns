<?php

namespace App\Http\Controllers\SchoolAdmin;


use App\Http\Controllers\Controller;
use App\Models\ClassLevel;
use App\Models\School;

class SchoolAdminController extends Controller
{
    public function index(School $school)  {

    //    dd(
    //     request()->route()->parameters(),
    //     request()->getPathInfo(),
    //     session('active_school')
    // );
        $classes = ClassLevel::latest()->take(4)->get();
        $count = ClassLevel::count();

        return view('schooladmin.index' , compact('classes','count', 'school'));
    }
}
