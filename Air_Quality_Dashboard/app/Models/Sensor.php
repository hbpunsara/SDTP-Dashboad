<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sensor_id',
        'name',
        'location',
        'latitude',
        'longitude',
        'is_active',
        'description'
    ];
    
    /**
     * Get the air quality readings for the sensor.
     */
    public function airQualityReadings()
    {
        return $this->hasMany(AirQualityReading::class);
    }
    
    /**
     * Get the latest air quality reading for the sensor.
     */
    public function latestReading()
    {
        return $this->hasOne(AirQualityReading::class)->latest('reading_time');
    }
}
