<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get sensor statistics
        $sensorStats = $this->getSensorStats();
        
        // Get recent air quality data
        $recentReadings = $this->getRecentReadings();
        
        // Get system stats
        $systemStats = $this->getSystemStats();
        
        return view('admin.dashboard', compact('sensorStats', 'recentReadings', 'systemStats'));
    }
    
    /**
     * Get sensor statistics.
     *
     * @return array
     */
    private function getSensorStats()
    {
        // In a real app, this would come from the database
        return [
            'total' => 14,
            'active' => 10,
            'inactive' => 4,
            'critical_alerts' => 2,
            'latest_added' => 'Sensor #112 (Colombo Port)',
        ];
    }
    
    /**
     * Get recent air quality readings.
     *
     * @return array
     */
    private function getRecentReadings()
    {
        // In a real app, this would come from the database
        return [
            [
                'sensor_id' => 'SN-COL-053',
                'location' => 'Town Hall',
                'aqi' => 72,
                'status' => 'Moderate',
                'timestamp' => now()->subHours(1)->format('Y-m-d H:i'),
            ],
            [
                'sensor_id' => 'SN-COL-065',
                'location' => 'Fort Railway Station',
                'aqi' => 45,
                'status' => 'Good',
                'timestamp' => now()->subHours(2)->format('Y-m-d H:i'),
            ],
            [
                'sensor_id' => 'SN-COL-069',
                'location' => 'Pettah Market',
                'aqi' => 110,
                'status' => 'Unhealthy for Sensitive Groups',
                'timestamp' => now()->subHours(1)->format('Y-m-d H:i'),
            ],
            [
                'sensor_id' => 'SN-COL-112',
                'location' => 'Colombo Port',
                'aqi' => 85,
                'status' => 'Moderate',
                'timestamp' => now()->subHours(3)->format('Y-m-d H:i'),
            ],
        ];
    }
    
    /**
     * Get system statistics.
     *
     * @return array
     */
    private function getSystemStats()
    {
        // In a real app, this would come from server monitoring
        return [
            'last_data_sync' => now()->subMinutes(15)->format('Y-m-d H:i:s'),
            'system_status' => 'Operational',
            'data_points_collected' => '14,532',
            'system_uptime' => '7 days, 14 hours',
        ];
    }
}
