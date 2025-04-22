<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\Admin\AlertThresholdController;
use App\Models\AlertThreshold;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AlertThresholdControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an admin user
        $this->admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_admin' => true
        ]);
        
        // Set admin session
        session(['is_admin' => true, 'admin_id' => $this->admin->id]);
    }

    /** @test */
    public function it_can_display_a_list_of_alert_thresholds()
    {
        // Create some alert thresholds
        AlertThreshold::create([
            'name' => 'Good Air Quality',
            'level_name' => 'Good',
            'min_value' => 0,
            'max_value' => 50,
            'color' => '#00E400',
            'description' => 'Air quality is considered satisfactory.',
            'is_active' => true,
            'send_notification' => false
        ]);
        
        AlertThreshold::create([
            'name' => 'Moderate Air Quality',
            'level_name' => 'Moderate',
            'min_value' => 51,
            'max_value' => 100,
            'color' => '#FFFF00',
            'description' => 'Air quality is acceptable.',
            'is_active' => true,
            'send_notification' => true
        ]);
        
        // Make request to index route
        $response = $this->get(route('admin.alerts.index'));
        
        // Assert response
        $response->assertStatus(200);
        $response->assertViewIs('admin.alerts.index');
        $response->assertViewHas('thresholds');
        
        // Assert the view contains both thresholds
        $thresholds = $response->viewData('thresholds');
        $this->assertCount(2, $thresholds);
    }
    
    /** @test */
    public function it_can_store_a_new_alert_threshold()
    {
        // Prepare threshold data
        $thresholdData = [
            'name' => 'Test Threshold',
            'level_name' => 'Test',
            'min_value' => 301,
            'max_value' => 500,
            'color' => '#800080',
            'description' => 'Test description',
            'is_active' => true,
            'send_notification' => true
        ];
        
        // Send post request
        $response = $this->post(route('admin.alerts.store'), $thresholdData);
        
        // Assert redirect
        $response->assertRedirect(route('admin.alerts.index'));
        $response->assertSessionHas('success', 'Alert threshold created successfully.');
        
        // Assert database has the threshold
        $this->assertDatabaseHas('alert_thresholds', [
            'name' => 'Test Threshold',
            'level_name' => 'Test',
            'min_value' => 301,
            'max_value' => 500
        ]);
    }
    
    /** @test */
    public function it_validates_required_fields_when_storing()
    {
        // Send post request with missing required fields
        $response = $this->post(route('admin.alerts.store'), [
            'name' => 'Test Threshold',
            // Missing other required fields
        ]);
        
        // Assert validation errors
        $response->assertSessionHasErrors(['level_name', 'min_value', 'max_value', 'color']);
    }
    
    /** @test */
    public function it_can_update_an_existing_alert_threshold()
    {
        // Create a threshold to update
        $threshold = AlertThreshold::create([
            'name' => 'Original Name',
            'level_name' => 'Original',
            'min_value' => 201,
            'max_value' => 300,
            'color' => '#FFA500',
            'description' => 'Original description',
            'is_active' => true,
            'send_notification' => false
        ]);
        
        // New data for the threshold
        $updatedData = [
            'name' => 'Updated Name',
            'level_name' => 'Updated',
            'min_value' => 201,
            'max_value' => 300,
            'color' => '#FFA500',
            'description' => 'Updated description',
            'is_active' => true,
            'send_notification' => true
        ];
        
        // Send put request
        $response = $this->put(route('admin.alerts.update', $threshold->id), $updatedData);
        
        // Assert redirect
        $response->assertRedirect(route('admin.alerts.index'));
        $response->assertSessionHas('success', 'Alert threshold updated successfully.');
        
        // Assert database has updated threshold
        $this->assertDatabaseHas('alert_thresholds', [
            'id' => $threshold->id,
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'send_notification' => true
        ]);
    }
    
    /** @test */
    public function it_can_delete_an_alert_threshold()
    {
        // Create a threshold
        $threshold = AlertThreshold::create([
            'name' => 'Threshold to Delete',
            'level_name' => 'Delete',
            'min_value' => 401,
            'max_value' => 500,
            'color' => '#800080',
            'description' => 'This threshold will be deleted',
            'is_active' => true,
            'send_notification' => true
        ]);
        
        // Send delete request
        $response = $this->delete(route('admin.alerts.destroy', $threshold->id));
        
        // Assert redirect
        $response->assertRedirect(route('admin.alerts.index'));
        $response->assertSessionHas('success', 'Alert threshold deleted successfully.');
        
        // Assert threshold no longer in database
        $this->assertDatabaseMissing('alert_thresholds', [
            'id' => $threshold->id
        ]);
    }
    
    /** @test */
    public function it_can_toggle_notification_setting()
    {
        // Create a threshold
        $threshold = AlertThreshold::create([
            'name' => 'Notification Test',
            'level_name' => 'Test',
            'min_value' => 101,
            'max_value' => 150,
            'color' => '#FF9900',
            'description' => 'Test description',
            'is_active' => true,
            'send_notification' => false
        ]);
        
        // Send patch request to toggle notification
        $response = $this->patch(route('admin.alerts.toggle-notification', $threshold->id));
        
        // Assert redirect
        $response->assertRedirect();
        
        // Assert notification setting is toggled
        $updatedThreshold = AlertThreshold::find($threshold->id);
        $this->assertTrue($updatedThreshold->send_notification);
        
        // Toggle back to false
        $response = $this->patch(route('admin.alerts.toggle-notification', $threshold->id));
        $updatedThreshold = AlertThreshold::find($threshold->id);
        $this->assertFalse($updatedThreshold->send_notification);
    }
}
