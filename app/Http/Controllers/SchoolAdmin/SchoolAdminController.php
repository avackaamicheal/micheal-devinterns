<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ClassLevel;

class SchoolAdminController extends Controller
{
    public function index()  {

        $classes = ClassLevel::latest()->take(4)->get();

        return view('schooladmin.index' , compact('classes'));
    }
}
