<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DessertsController extends Controller
{
    public function index()
    {
        return view('desserts');
    }
}