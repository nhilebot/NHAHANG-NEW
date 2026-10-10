<?php

namespace App\Http\Controllers;

class BeerController extends Controller
{
    public function index()
    {
        return view('beer');
    }
}