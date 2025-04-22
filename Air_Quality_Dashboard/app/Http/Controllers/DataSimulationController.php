<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\Sensor;
use App\Models\AirQualityReading;
use Illuminate\Support\Facades\Cache;

class DataSimulationController extends Controller
{
    /**
     * Display the data simulation dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $simulationActive = Cache::get('simulation_active', false);
        $simulationInterval = Cache::get('simulation_interval', 15); // Default: 15 minutes
        $simulationVariation = Cache::get('simulation_variation', 10); // Default: 10% variation
        
        $sensors = Sensor::count();
        $readings = AirQualityReading::count();
        $latestReading = AirQualityReading::latest('reading_time')->first();
        
        return view('admin.data-simulation.index', compact(
            'simulationActive', 
            'simulationInterval', 
            'simulationVariation',
            'sensors',
            'readings',
            'latestReading'
        ));
    }

    /**
     * Start the data simulation process
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function start(Request $request)
    {
        $request->validate([
            'interval' => 'required|integer|min:5|max:60',
            'variation' => 'required|integer|min:1|max:50',
        ]);
        
        // Store simulation settings in cache
        Cache::put('simulation_active', true, now()->addDays(1));
        Cache::put('simulation_interval', $request->interval, now()->addDays(1));
        Cache::put('simulation_variation', $request->variation, now()->addDays(1));
        
        // Run the command once immediately
        Artisan::call('simulate:data');
        
        return redirect()->route('admin.data-simulation')->with('success', 'Data simulation started successfully. Data will be generated every ' . $request->interval . ' minutes.');
    }

    /**
     * Stop the data simulation process
     * 
     * @return \Illuminate\Http\Response
     */
    public function stop()
    {
        Cache::put('simulation_active', false, now()->addDays(1));
        
        return redirect()->route('admin.data-simulation')->with('success', 'Data simulation stopped successfully.');
    }
    
    /**
     * Generate a single batch of simulated data
     * 
     * @return \Illuminate\Http\Response
     */
    public function generate()
    {
        Artisan::call('simulate:data');
        
        return redirect()->route('admin.data-simulation')->with('success', 'Simulated data generated successfully for all active sensors.');
    }
}
