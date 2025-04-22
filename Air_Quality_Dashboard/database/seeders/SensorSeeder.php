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
        // Array of sensor data for Colombo locations
        $sensors = [
            [
                'sensor_id' => 'SN-COL-001',
                'name' => 'Colombo Fort',
                'location' => 'Colombo Fort Railway Station',
                'latitude' => 6.9344,
                'longitude' => 79.8428,
                'description' => 'Air quality sensor near Colombo Fort Railway Station',
                'is_active' => true,
            ],
            [
                'sensor_id' => 'SN-COL-002',
                'name' => 'Pettah Market',
                'location' => 'Main Street, Pettah',
                'latitude' => 6.9370,
                'longitude' => 79.8485,
                'description' => 'Monitoring air quality in busy Pettah Market area',
                'is_active' => true,
            ],
            [
                'sensor_id' => 'SN-COL-003',
                'name' => 'Colombo Port',
                'location' => 'Colombo Port Area',
                'latitude' => 6.9422,
                'longitude' => 79.8418,
                'description' => 'Industrial area sensor near Colombo Port',
                'is_active' => true,
            ],
            [
                'sensor_id' => 'SN-COL-004',
                'name' => 'Town Hall',
                'location' => 'Colombo Town Hall',
                'latitude' => 6.9177,
                'longitude' => 79.8644,
                'description' => 'Urban center air quality monitoring',
                'is_active' => true,
            ],
            [
                'sensor_id' => 'SN-COL-005',
                'name' => 'Galle Face Green',
                'location' => 'Galle Face Promenade',
                'latitude' => 6.9271,
                'longitude' => 79.8425,
                'description' => 'Coastal area air quality monitoring',
                'is_active' => true,
            ],
            [
                'sensor_id' => 'SN-COL-006',
                'name' => 'Viharamahadevi Park',
                'location' => 'Viharamahadevi Park',
                'latitude' => 6.9147,
                'longitude' => 79.8631,
                'description' => 'Green space air quality monitoring',
                'is_active' => true,
            ],
            [
                'sensor_id' => 'SN-COL-007',
                'name' => 'Maradana',
                'location' => 'Maradana Railway Station',
                'latitude' => 6.9278,
                'longitude' => 79.8675,
                'description' => 'Transport hub air quality sensor',
                'is_active' => true,
            ],
            [
                'sensor_id' => 'SN-COL-008',
                'name' => 'Dematagoda',
                'location' => 'Dematagoda Industrial Area',
                'latitude' => 6.9361,
                'longitude' => 79.8783,
                'description' => 'Industrial zone monitoring',
                'is_active' => true,
            ],
            [
                'sensor_id' => 'SN-COL-009',
                'name' => 'Slave Island',
                'location' => 'Slave Island Area',
                'latitude' => 6.9222,
                'longitude' => 79.8500,
                'description' => 'Urban residential area monitoring',
                'is_active' => true,
            ],
            [
                'sensor_id' => 'SN-COL-010',
                'name' => 'Wellawatte',
                'location' => 'Wellawatte Beach',
                'latitude' => 6.8778,
                'longitude' => 79.8594,
                'description' => 'Southern coastal area monitoring',
                'is_active' => true,
            ],
            [
                'sensor_id' => 'SN-COL-011',
                'name' => 'Borella',
                'location' => 'Borella Junction',
                'latitude' => 6.9166,
                'longitude' => 79.8773,
                'description' => 'Heavy traffic junction monitoring',
                'is_active' => true,
            ],
            [
                'sensor_id' => 'SN-COL-012',
                'name' => 'Kotahena',
                'location' => 'Kotahena',
                'latitude' => 6.9503,
                'longitude' => 79.8628,
                'description' => 'Northern residential area monitoring',
                'is_active' => false,
            ],
            [
                'sensor_id' => 'SN-COL-013',
                'name' => 'BRC Grounds',
                'location' => 'Colombo Cricket Club',
                'latitude' => 6.9026,
                'longitude' => 79.8535,
                'description' => 'Sports complex area monitoring',
                'is_active' => false,
            ],
            [
                'sensor_id' => 'SN-COL-014',
                'name' => 'Kirulapone Canal',
                'location' => 'Kirulapone Canal Area',
                'latitude' => 6.8853,
                'longitude' => 79.8792,
                'description' => 'Waterway adjacent monitoring',
                'is_active' => false,
            ],
        ];

        // Create all sensors
        foreach ($sensors as $sensorData) {
            Sensor::create($sensorData);
        }
        
        $this->command->info('Created ' . count($sensors) . ' sensors for Colombo locations.');
    }
}