<?php

namespace Tests\Unit\Models;

use App\Models\AlertThreshold;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlertThresholdTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_determine_if_an_aqi_value_falls_within_its_range()
    {
        // Create a moderate threshold (51-100)
        $threshold = AlertThreshold::create([
            'name' => 'Moderate Air Quality',
            'level_name' => 'Moderate',
            'min_value' => 51,
            'max_value' => 100,
            'color' => '#FFFF00',
            'description' => 'Air quality is acceptable.',
            'is_active' => true,
            'send_notification' => false
        ]);
        
        // Test value within range
        $this->assertTrue($threshold->containsValue(75));
        
        // Test edge cases
        $this->assertTrue($threshold->containsValue(51));
        $this->assertTrue($threshold->containsValue(100));
        
        // Test values outside range
        $this->assertFalse($threshold->containsValue(50));
        $this->assertFalse($threshold->containsValue(101));
    }
    
    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $threshold = new AlertThreshold();
        
        $expectedFillable = [
            'name',
            'level_name',
            'min_value',
            'max_value',
            'color',
            'description',
            'is_active',
            'send_notification'
        ];
        
        $this->assertEquals($expectedFillable, $threshold->getFillable());
    }
    
    /** @test */
    public function it_can_be_toggled_active_status()
    {
        $threshold = AlertThreshold::create([
            'name' => 'Unhealthy Air Quality',
            'level_name' => 'Unhealthy',
            'min_value' => 151,
            'max_value' => 200,
            'color' => '#FF0000',
            'description' => 'Everyone may begin to experience health effects.',
            'is_active' => true,
            'send_notification' => true
        ]);
        
        // Test initial state
        $this->assertTrue($threshold->is_active);
        
        // Toggle status
        $threshold->is_active = false;
        $threshold->save();
        
        // Reload model and check new state
        $this->assertFalse($threshold->fresh()->is_active);
    }
    
    /** @test */
    public function it_can_be_toggled_notification_status()
    {
        $threshold = AlertThreshold::create([
            'name' => 'Unhealthy Air Quality',
            'level_name' => 'Unhealthy',
            'min_value' => 151,
            'max_value' => 200,
            'color' => '#FF0000',
            'description' => 'Everyone may begin to experience health effects.',
            'is_active' => true,
            'send_notification' => true
        ]);
        
        // Test initial state
        $this->assertTrue($threshold->send_notification);
        
        // Toggle status
        $threshold->send_notification = false;
        $threshold->save();
        
        // Reload model and check new state
        $this->assertFalse($threshold->fresh()->send_notification);
    }
}
