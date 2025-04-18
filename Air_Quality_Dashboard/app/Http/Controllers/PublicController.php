<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\AirQualityReading;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Display the main public dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sensors = Sensor::with('latestReading')->where('is_active', true)->get();
        return view('public.dashboard', compact('sensors'));
    }

    /**
     * Display historical data view
     *
     * @return \Illuminate\Http\Response
     */
    public function historicalData()
    {
        $sensors = Sensor::where('is_active', true)->get();
        return view('public.historical-data', compact('sensors'));
    }

    /**
     * Get sensor data for the API
     *
     * @return \Illuminate\Http\Response
     */
    public function getSensors()
    {
        return Sensor::with('latestReading')->where('is_active', true)->get();
    }

    /**
     * Get historical readings for a specific sensor
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getSensorReadings($id, Request $request)
    {
        $hours = $request->get('hours', 24);
        
        return AirQualityReading::where('sensor_id', $id)
            ->orderBy('reading_time', 'desc')
            ->limit($hours)
            ->get();
    }
}
