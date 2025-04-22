<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Sensor::with('latestReading');
        
        // Filter for critical alerts if requested
        if ($request->has('filter') && $request->filter === 'critical') {
            $query->whereHas('latestReading', function($q) {
                $q->where('aqi', '>', 100); // AQI values above 100 are considered unhealthy
            });
        }
        
        $sensors = $query->get();
        $filter = $request->filter ?? null;
        
        return view('admin.sensors.index', compact('sensors', 'filter'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.sensors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'sensor_id' => 'required|string|unique:sensors,sensor_id',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'description' => 'nullable|string'
        ]);
        
        Sensor::create($request->all());
        
        return redirect()->route('admin.sensors.index')
            ->with('success', 'Sensor registered successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sensor = Sensor::with(['airQualityReadings' => function($query) {
            $query->latest('reading_time')->limit(24);
        }])->findOrFail($id);
        
        return view('admin.sensors.show', compact('sensor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sensor = Sensor::findOrFail($id);
        return view('admin.sensors.edit', compact('sensor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'sensor_id' => 'required|string|unique:sensors,sensor_id,' . $id,
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'is_active' => 'required|boolean',
            'description' => 'nullable|string'
        ]);
        
        $sensor = Sensor::findOrFail($id);
        $sensor->update($request->all());
        
        return redirect()->route('admin.sensors.index')
            ->with('success', 'Sensor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sensor = Sensor::findOrFail($id);
        $sensor->delete();
        
        return redirect()->route('admin.sensors.index')
            ->with('success', 'Sensor deleted successfully.');
    }
    
    /**
     * Toggle the active status of a sensor.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        $sensor = Sensor::findOrFail($id);
        $sensor->is_active = !$sensor->is_active;
        $sensor->save();
        
        $statusText = $sensor->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.sensors.index')
            ->with('success', "Sensor {$statusText} successfully.");
    }
}
