<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AirQualityReading extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sensor_id',
        'aqi',
        'pm25',
        'pm10',
        'o3',
        'no2',
        'so2',
        'co',
        'aqi_category',
        'reading_time'
    ];
    
    /**
     * Get the sensor that owns the reading.
     */
    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
    
    /**
     * Scope a query to only include recent readings.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $hours
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('reading_time', '>=', now()->subHours($hours));
    }
}
