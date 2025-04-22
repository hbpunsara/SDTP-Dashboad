<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sensor;
use App\Models\AirQualityReading;
use Illuminate\Support\Facades\Cache;

class SimulateData extends Command
{
    protected $signature = 'simulate:data';
    protected $description = 'Generate simulated AQI data for sensors';

    /**
     * AQI category ranges
     */
    protected $aqiCategories = [
        ['min' => 0, 'max' => 50, 'name' => 'Good'],
        ['min' => 51, 'max' => 100, 'name' => 'Moderate'],
        ['min' => 101, 'max' => 150, 'name' => 'Unhealthy for Sensitive Groups'],
        ['min' => 151, 'max' => 200, 'name' => 'Unhealthy'],
        ['min' => 201, 'max' => 300, 'name' => 'Very Unhealthy'],
        ['min' => 301, 'max' => 500, 'name' => 'Hazardous']
    ];

    public function handle()
    {
        $activeSensors = Sensor::where('is_active', true)->get();
        $variation = Cache::get('simulation_variation', 10); // Default: 10% variation
        
        $this->info('Generating simulated air quality data for ' . $activeSensors->count() . ' active sensors...');
        
        $generatedCount = 0;
        
        foreach ($activeSensors as $sensor) {
            // Get the latest reading for this sensor or create baseline values
            $latestReading = AirQualityReading::where('sensor_id', $sensor->id)
                ->latest('reading_time')
                ->first();
            
            if ($latestReading) {
                // Use the latest reading as a baseline and apply variation
                $aqi = $this->applyVariation($latestReading->aqi, $variation);
                $pm25 = $this->applyVariation($latestReading->pm25, $variation);
                $pm10 = $this->applyVariation($latestReading->pm10, $variation);
                $o3 = $this->applyVariation($latestReading->o3, $variation);
                $no2 = $this->applyVariation($latestReading->no2, $variation);
                $so2 = $this->applyVariation($latestReading->so2, $variation);
                $co = $this->applyVariation($latestReading->co, $variation);
            } else {
                // Create baseline values for a new sensor based on location
                // For simplicity, we'll use random values within reasonable ranges
                $aqi = rand(20, 180);
                $pm25 = $aqi * 0.4 + rand(-5, 5); // PM2.5 often correlates with AQI
                $pm10 = $pm25 * 1.5 + rand(-10, 10);
                $o3 = rand(10, 70) / 10;
                $no2 = rand(5, 60) / 10;
                $so2 = rand(2, 40) / 10;
                $co = rand(1, 20) / 10;
            }

            // Ensure values stay within reasonable bounds
            $aqi = max(0, min(500, $aqi));
            $pm25 = max(0, $pm25);
            $pm10 = max(0, $pm10);
            $o3 = max(0, $o3);
            $no2 = max(0, $no2);
            $so2 = max(0, $so2);
            $co = max(0, $co);
            
            // Determine AQI category
            $aqiCategory = $this->getAqiCategory($aqi);
            
            // Create the air quality reading
            AirQualityReading::create([
                'sensor_id' => $sensor->id,
                'aqi' => round($aqi),
                'pm25' => round($pm25, 2),
                'pm10' => round($pm10, 2),
                'o3' => round($o3, 2),
                'no2' => round($no2, 2),
                'so2' => round($so2, 2),
                'co' => round($co, 2),
                'aqi_category' => $aqiCategory,
                'reading_time' => now(),
            ]);
            
            $generatedCount++;
        }
        
        $this->info("Generated $generatedCount air quality readings successfully.");
    }
    
    /**
     * Apply random variation to a value
     *
     * @param float $value
     * @param int $variation
     * @return float
     */
    protected function applyVariation($value, $variation)
    {
        // Calculate the maximum variation amount
        $maxChange = $value * ($variation / 100);
        
        // Generate a random variation between -maxChange and +maxChange
        $change = mt_rand(-1000, 1000) / 1000 * $maxChange;
        
        return $value + $change;
    }
    
    /**
     * Get the AQI category name based on the AQI value
     *
     * @param int $aqi
     * @return string
     */
    protected function getAqiCategory($aqi)
    {
        foreach ($this->aqiCategories as $category) {
            if ($aqi >= $category['min'] && $aqi <= $category['max']) {
                return $category['name'];
            }
        }
        
        return 'Unknown';
    }
}