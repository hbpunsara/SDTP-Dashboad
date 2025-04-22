<?php

namespace Database\Seeders;

use App\Models\AlertThreshold;
use Illuminate\Database\Seeder;

class AlertThresholdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing alert thresholds
        AlertThreshold::truncate();
        
        // Define standard EPA AQI thresholds
        $thresholds = [
            [
                'name' => 'Good Air Quality',
                'level_name' => 'Good',
                'min_value' => 0,
                'max_value' => 50,
                'color' => '#00E400', // Green
                'description' => 'Air quality is considered satisfactory, and air pollution poses little or no risk.',
                'is_active' => true,
                'send_notification' => false
            ],
            [
                'name' => 'Moderate Air Quality',
                'level_name' => 'Moderate',
                'min_value' => 51,
                'max_value' => 100,
                'color' => '#FFFF00', // Yellow
                'description' => 'Air quality is acceptable; however, for some pollutants there may be a moderate health concern for a very small number of people.',
                'is_active' => true,
                'send_notification' => false
            ],
            [
                'name' => 'Unhealthy for Sensitive Groups',
                'level_name' => 'Unhealthy for Sensitive Groups',
                'min_value' => 101,
                'max_value' => 150,
                'color' => '#FF7E00', // Orange
                'description' => 'Members of sensitive groups may experience health effects. The general public is not likely to be affected.',
                'is_active' => true,
                'send_notification' => true
            ],
            [
                'name' => 'Unhealthy Air Quality',
                'level_name' => 'Unhealthy',
                'min_value' => 151,
                'max_value' => 200,
                'color' => '#FF0000', // Red
                'description' => 'Everyone may begin to experience health effects; members of sensitive groups may experience more serious health effects.',
                'is_active' => true,
                'send_notification' => true
            ],
            [
                'name' => 'Very Unhealthy Air Quality',
                'level_name' => 'Very Unhealthy',
                'min_value' => 201,
                'max_value' => 300,
                'color' => '#99004C', // Purple
                'description' => 'Health warnings of emergency conditions. The entire population is more likely to be affected.',
                'is_active' => true,
                'send_notification' => true
            ],
            [
                'name' => 'Hazardous Air Quality',
                'level_name' => 'Hazardous',
                'min_value' => 301,
                'max_value' => 500,
                'color' => '#7E0023', // Maroon
                'description' => 'Health alert: everyone may experience more serious health effects.',
                'is_active' => true,
                'send_notification' => true
            ],
        ];
        
        // Create the alert thresholds
        foreach ($thresholds as $threshold) {
            AlertThreshold::create($threshold);
        }
    }
}
