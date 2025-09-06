<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function display() {
        $projects = auth()->user()->projects()->latest()->get();
        return view('dashboard', compact('projects'));
    }
}
