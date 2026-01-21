<?php

namespace App\Http\Controllers\Bursar;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BursarController extends Controller
{
    public function dashboard(){
        return view('bursar.index');
    }
}
