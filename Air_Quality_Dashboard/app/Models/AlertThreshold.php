<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertThreshold extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'level_name',
        'min_value',
        'max_value',
        'color',
        'description',
        'is_active',
        'send_notification'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'send_notification' => 'boolean',
        'min_value' => 'integer',
        'max_value' => 'integer',
    ];
    
    /**
     * Check if an AQI value falls within this threshold.
     *
     * @param int $aqi
     * @return bool
     */
    public function containsAqi($aqi)
    {
        return $aqi >= $this->min_value && $aqi <= $this->max_value;
    }
}
