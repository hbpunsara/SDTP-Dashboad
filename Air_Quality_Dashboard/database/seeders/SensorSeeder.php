<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sensor;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sensor::create([
            'sensor_id' => 'S001',
            'name' => 'Colombo Central',
            'latitude' => 6.9271,
            'longitude' => 79.8612,
            'is_active' => true,
        ]);
    }
}