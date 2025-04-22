<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sensor;
use App\Models\AirQualityReading;

class SimulateData extends Command
{
    protected $signature = 'simulate:data';
    protected $description = 'Generate simulated AQI data for sensors';

    public function handle()
    {
        $sensors = Sensor::all();
        foreach ($sensors as $sensor) {
            AirQualityReading::create([
                'sensor_id' => $sensor->id,
                'aqi' => rand(0, 300),
                'reading_time' => now(),
            ]);
        }
        $this->info('Simulated data generated successfully.');
    }
}