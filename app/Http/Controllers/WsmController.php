<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WsmController extends Controller
{
    
    public function index()
    {
        return view('wsm.index');
    }
}
