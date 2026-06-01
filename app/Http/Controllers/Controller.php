<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function index(){
        return view('layout.dashboard');
    }
    public function show(){
        return view('layout.mainpage');
    }
}
