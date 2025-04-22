<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlertThreshold;
use Illuminate\Http\Request;

class AlertThresholdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $thresholds = AlertThreshold::orderBy('min_value')->get();
        return view('admin.alerts.index', compact('thresholds'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.alerts.create');
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
            'name' => 'required|string|max:255',
            'level_name' => 'required|string|max:255',
            'min_value' => 'required|integer|min:0',
            'max_value' => 'required|integer|min:0|gt:min_value',
            'color' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'send_notification' => 'boolean',
        ]);
        
        AlertThreshold::create($request->all());
        
        return redirect()->route('admin.alerts.index')
            ->with('success', 'Alert threshold created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $threshold = AlertThreshold::findOrFail($id);
        return view('admin.alerts.edit', compact('threshold'));
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
            'name' => 'required|string|max:255',
            'level_name' => 'required|string|max:255',
            'min_value' => 'required|integer|min:0',
            'max_value' => 'required|integer|min:0|gt:min_value',
            'color' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'send_notification' => 'boolean',
        ]);
        
        $threshold = AlertThreshold::findOrFail($id);
        $threshold->update($request->all());
        
        return redirect()->route('admin.alerts.index')
            ->with('success', 'Alert threshold updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $threshold = AlertThreshold::findOrFail($id);
        $threshold->delete();
        
        return redirect()->route('admin.alerts.index')
            ->with('success', 'Alert threshold deleted successfully.');
    }
    
    /**
     * Toggle the send notification status.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function toggleNotification($id)
    {
        $threshold = AlertThreshold::findOrFail($id);
        $threshold->send_notification = !$threshold->send_notification;
        $threshold->save();
        
        return redirect()->route('admin.alerts.index')
            ->with('success', 'Notification setting updated.');
    }
}
