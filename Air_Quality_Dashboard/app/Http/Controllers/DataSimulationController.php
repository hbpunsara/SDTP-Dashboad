<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataSimulationController extends Controller
{
    public function start()
{
    // Logic to start data simulation
    return redirect()->route('admin.data-simulation')->with('success', 'Data simulation started.');
}

public function stop()
{
    // Logic to stop data simulation
    return redirect()->route('admin.data-simulation')->with('success', 'Data simulation stopped.');
}
}
