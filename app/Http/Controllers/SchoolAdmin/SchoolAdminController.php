<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SchoolAdminController extends Controller
{
    public function index()  {
        return view('schooladmin.index');
    }
}
