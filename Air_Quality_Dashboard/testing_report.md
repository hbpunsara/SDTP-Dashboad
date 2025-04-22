# Air Quality Monitoring Dashboard for Colombo Metropolitan Area
# Testing and Implementation Report

## Table of Contents
1. [Introduction](#introduction)
2. [Development Evidence](#development-evidence)
3. [Team Contribution Breakdown](#team-contribution-breakdown)
4. [Testing Strategy](#testing-strategy)
   - [Unit Testing](#unit-testing)
   - [Integration Testing](#integration-testing)
   - [Functional Testing](#functional-testing)
5. [Mock Objects and Test Data](#mock-objects-and-test-data)
6. [Running the Tests](#running-the-tests)
7. [Functional Test Plans](#functional-test-plans)
8. [Critical Analysis of Testing Strategy](#critical-analysis-of-testing-strategy)
9. [Conclusion](#conclusion)

## Introduction

This report documents the testing strategy and implementation for the Real-time Air Quality Monitoring Dashboard developed for the Colombo Metropolitan Area. The application was built using Laravel framework for the backend, MySQL for the database, and standard web frontend technologies (HTML, CSS, JavaScript) including the Leaflet mapping library for geographical visualization.

The dashboard serves as a centralized platform for monitoring air quality data from multiple sensors placed throughout the Colombo area, providing both public access to real-time air quality information and an administrative interface for managing sensors, configuring alerts, and monitoring system performance.

## Development Evidence

### Public Dashboard Interface

![Public Dashboard](screenshots/public_dashboard.png)

**Summary**: The public dashboard provides real-time visualization of air quality data across the Colombo Metropolitan Area. Users can view sensor locations on an interactive map, with color-coded markers indicating the Air Quality Index (AQI) levels at each location. The interface includes:
- Interactive map with sensor locations
- Color-coded AQI indicators
- Detail views for individual sensors
- Historical data visualization
- Information about AQI categories and health implications

*Developed by: [Team Member Names]*

### Admin Dashboard

![Admin Dashboard](screenshots/admin_dashboard.png)

**Summary**: The administrative dashboard provides a comprehensive overview of the air quality monitoring system, including system status, critical alerts, and key metrics. The admin dashboard serves as the central hub for monitoring administrators to oversee the entire air quality monitoring network.

*Developed by: [Team Member Names]*

### Sensor Management Interface

![Sensor Management](screenshots/sensor_management.png)

**Summary**: The sensor management interface allows administrators to add, edit, delete, and monitor the status of all sensors in the network. Administrators can:
- View all sensors with their current status
- Add new sensors with location coordinates
- Edit existing sensor details
- Toggle sensor activation status
- Delete sensors with confirmation

*Developed by: [Team Member Names]*

### Alert Configuration System

![Alert Configuration](screenshots/alert_configuration.png)

**Summary**: The alert configuration system allows administrators to define and manage alert thresholds based on AQI levels. The system includes:
- Predefined EPA AQI categories
- Customizable threshold levels
- Visual indicators for threshold ranges
- Notification toggle settings
- Editable descriptions for health implications

*Developed by: [Team Member Names]*

### User Management System

![User Management](screenshots/user_management.png)

**Summary**: The user management interface allows the creation and management of administrator accounts with access to the system. Features include:
- User listing with role indicators
- Add new administrator accounts
- Edit existing user details
- Secure password management
- Protection against self-deletion of accounts

*Developed by: [Team Member Names]*

### Authentication System

![Login Page](screenshots/login_page.png)

**Summary**: The authentication system provides secure access to the administrative features of the dashboard, allowing only authorized personnel to manage the air quality monitoring system.

*Developed by: [Team Member Names]*

## Team Contribution Breakdown

| Team Member | Contributions | Percentage |
|-------------|--------------|------------|
| Member 1    | - Implementation of sensor management system<br>- Integration of Leaflet maps<br>- Development of data visualization components | 33% |
| Member 2    | - Development of alert configuration system<br>- Database design and implementation<br>- API development for sensor data | 33% |
| Member 3    | - User management and authentication<br>- UI/UX design<br>- Public dashboard implementation | 34% |

## Testing Strategy

Our testing approach follows a comprehensive strategy that combines unit testing, integration testing, and functional testing to ensure the quality and reliability of the Air Quality Dashboard. This multi-layered approach allows us to validate individual components in isolation as well as their interactions within the system.

### Unit Testing

Unit tests focus on testing individual components of the application in isolation. For our Laravel application, this primarily involves testing models, controllers, and service classes.

#### Model Testing

We developed unit tests for all database models to ensure data integrity and proper relationship handling:

```php
public function testAlertThresholdRangeCheck()
{
    // Test that an AQI value falls within the correct threshold range
    $threshold = AlertThreshold::factory()->create([
        'min_value' => 51,
        'max_value' => 100,
        'name' => 'Moderate'
    ]);
    
    $this->assertTrue($threshold->containsValue(75));
    $this->assertFalse($threshold->containsValue(101));
}
```

#### Controller Testing

Controller tests verify that each endpoint handles requests correctly and returns appropriate responses:

```php
public function testSensorCreation()
{
    $sensorData = [
        'name' => 'Test Sensor',
        'location' => 'Test Location',
        'latitude' => 6.9271,
        'longitude' => 79.8612,
        'is_active' => true
    ];
    
    $response = $this->actingAs($this->admin)
                     ->post(route('admin.sensors.store'), $sensorData);
    
    $response->assertRedirect(route('admin.sensors.index'));
    $this->assertDatabaseHas('sensors', ['name' => 'Test Sensor']);
}
```

#### Service Classes Testing

We isolated complex business logic into service classes and tested them thoroughly:

```php
public function testAqiCalculation()
{
    $aqiService = new AqiCalculationService();
    
    // Test calculation with known values
    $pm25Value = 35.4;
    $expectedAqi = 102;
    
    $this->assertEquals($expectedAqi, $aqiService->calculateAqiForPm25($pm25Value));
}
```

### Integration Testing

Integration tests verify that different components of the application work together correctly. These tests focus on data flow between components and database interactions.

#### Database Integration Tests

These tests verify that our models correctly interact with the database:

```php
public function testSensorReadingsRelationship()
{
    $sensor = Sensor::factory()->create();
    $readings = SensorReading::factory()->count(5)->create(['sensor_id' => $sensor->id]);
    
    $this->assertCount(5, $sensor->readings);
    $this->assertInstanceOf(SensorReading::class, $sensor->readings->first());
}
```

#### Controller-Service Integration Tests

These tests verify that controllers correctly use service classes:

```php
public function testAlertNotificationSystem()
{
    // Mock the notification service
    $notificationService = $this->mock(NotificationService::class);
    $notificationService->shouldReceive('sendAlertNotification')->once();
    
    // Create a high reading that should trigger an alert
    $reading = SensorReading::factory()->create(['aqi_value' => 301]);
    
    // Call the controller method that processes new readings
    $response = $this->post(route('admin.process-reading'), [
        'reading_id' => $reading->id
    ]);
    
    $response->assertStatus(200);
}
```

### Functional Testing

Functional tests simulate user interactions with the application to verify that the application works correctly from an end-user perspective.

#### Browser Tests

We used Laravel Dusk to perform browser tests that simulate user interactions:

```php
public function testSensorCreationFlow()
{
    $this->browse(function (Browser $browser) {
        $browser->loginAs($this->admin)
                ->visit(route('admin.sensors.index'))
                ->click('@add-sensor-button')
                ->type('name', 'New Test Sensor')
                ->type('location', 'Test Location')
                ->type('latitude', '6.9271')
                ->type('longitude', '79.8612')
                ->check('is_active')
                ->press('Create Sensor')
                ->assertPathIs('/admin/sensors')
                ->assertSee('New Test Sensor');
    });
}
```

#### API Tests

We tested the API endpoints that provide data to the frontend components:

```php
public function testSensorDataApi()
{
    $sensor = Sensor::factory()->create();
    $readings = SensorReading::factory()->count(3)->create(['sensor_id' => $sensor->id]);
    
    $response = $this->getJson(route('api.sensor.readings', $sensor->id));
    
    $response->assertStatus(200)
             ->assertJsonCount(3, 'data');
}
```

## Mock Objects and Test Data

### Mock Sensor Data Generator

For testing the air quality monitoring system without relying on physical sensors, we developed a mock data generator that simulates realistic sensor readings. This generator was crucial for testing the data visualization components, alert system, and historical data analysis features.

```php
class MockSensorDataGenerator
{
    public function generateReading(Sensor $sensor, ?Carbon $timestamp = null)
    {
        $timestamp = $timestamp ?? now();
        
        // Generate realistic PM2.5 values based on time of day and location
        $baseValue = $this->getBaseValueForLocation($sensor->location);
        $timeMultiplier = $this->getTimeOfDayMultiplier($timestamp);
        $randomVariation = rand(-10, 10);
        
        $pm25Value = max(0, $baseValue * $timeMultiplier + $randomVariation);
        $aqiValue = $this->calculateAqiFromPm25($pm25Value);
        
        return [
            'sensor_id' => $sensor->id,
            'timestamp' => $timestamp,
            'pm25_value' => $pm25Value,
            'aqi_value' => $aqiValue,
        ];
    }
    
    // Helper methods...
}
```

### API Response Mocks

We created mock API responses for testing frontend components without making actual API calls:

```php
// Mock response for sensor list
$mockSensorResponse = [
    'data' => [
        [
            'id' => 1,
            'name' => 'Colombo Fort',
            'location' => 'Central Colombo',
            'latitude' => 6.9271,
            'longitude' => 79.8612,
            'is_active' => true,
            'current_aqi' => 75,
            'updated_at' => now()->toIso8601String()
        ],
        // More sensor data...
    ]
];
```

### Database Seeders

We developed comprehensive seeders to populate the test database with realistic data:

```php
public function run()
{
    // Create standard AQI thresholds based on EPA guidelines
    AlertThreshold::create([
        'name' => 'Good Air Quality',
        'level_name' => 'Good',
        'min_value' => 0,
        'max_value' => 50,
        'color' => '#00E400',
        'description' => 'Air quality is considered satisfactory, and air pollution poses little or no risk.',
        'is_active' => true,
        'send_notification' => false
    ]);
    
    // More thresholds...
    
    // Create test sensors
    Sensor::create([
        'name' => 'Colombo Fort',
        'location' => 'Central Colombo',
        'latitude' => 6.9271,
        'longitude' => 79.8612,
        'is_active' => true
    ]);
    
    // More sensors...
}
```

## Running the Tests

All tests can be executed using Laravel's built-in PHPUnit integration. The following steps outline how to run the various test suites:

### Setting Up the Test Environment

1. Configure the `.env.testing` file with test database credentials
2. Run database migrations for the test database:
   ```bash
   php artisan migrate --env=testing
   ```
3. Seed the test database:
   ```bash
   php artisan db:seed --env=testing
   ```

### Running Unit Tests

```bash
php artisan test --testsuite=Unit
```

### Running Integration Tests

```bash
php artisan test --testsuite=Integration
```

### Running Feature Tests

```bash
php artisan test --testsuite=Feature
```

### Running Browser Tests (Laravel Dusk)

```bash
php artisan dusk
```

## Functional Test Plans

### User Authentication Test Plan

| Test ID | Description | Pre-conditions | Test Steps | Expected Results |
|---------|-------------|----------------|------------|------------------|
| AUTH-01 | Admin login with valid credentials | Admin account exists | 1. Navigate to login page<br>2. Enter valid email and password<br>3. Click login button | User is authenticated and redirected to admin dashboard |
| AUTH-02 | Admin login with invalid credentials | Admin account exists | 1. Navigate to login page<br>2. Enter invalid email or password<br>3. Click login button | Error message displayed, user remains on login page |
| AUTH-03 | Admin logout | User is logged in | 1. Click logout button | User is logged out and redirected to public dashboard |

### Sensor Management Test Plan

| Test ID | Description | Pre-conditions | Test Steps | Expected Results |
|---------|-------------|----------------|------------|------------------|
| SENS-01 | Create new sensor | User is logged in as admin | 1. Navigate to sensor management<br>2. Click "Add Sensor"<br>3. Fill form with valid data<br>4. Submit form | New sensor created and displayed in sensor list |
| SENS-02 | Edit existing sensor | Sensor exists in system | 1. Navigate to sensor management<br>2. Click edit button for a sensor<br>3. Modify sensor details<br>4. Save changes | Sensor details updated in database and UI |
| SENS-03 | Delete sensor | Sensor exists in system | 1. Navigate to sensor management<br>2. Click delete button for a sensor<br>3. Confirm deletion | Sensor removed from database and UI |
| SENS-04 | Toggle sensor status | Sensor exists in system | 1. Navigate to sensor management<br>2. Click status toggle for a sensor | Sensor status updated in database and UI |

### Alert Configuration Test Plan

| Test ID | Description | Pre-conditions | Test Steps | Expected Results |
|---------|-------------|----------------|------------|------------------|
| ALRT-01 | Create new alert threshold | User is logged in as admin | 1. Navigate to alert management<br>2. Click "Add Threshold"<br>3. Fill form with valid data<br>4. Submit form | New threshold created and displayed in threshold list |
| ALRT-02 | Edit existing threshold | Threshold exists in system | 1. Navigate to alert management<br>2. Click edit button for a threshold<br>3. Modify threshold details<br>4. Save changes | Threshold details updated in database and UI |
| ALRT-03 | Toggle notification setting | Threshold exists in system | 1. Navigate to alert management<br>2. Click notification toggle for a threshold | Notification setting updated in database and UI |

### Public Dashboard Test Plan

| Test ID | Description | Pre-conditions | Test Steps | Expected Results |
|---------|-------------|----------------|------------|------------------|
| DASH-01 | View sensor data on map | Sensors exist with data | 1. Navigate to public dashboard<br>2. Wait for map to load | Map displays all active sensors with correct AQI color coding |
| DASH-02 | View sensor details | Sensors exist with data | 1. Navigate to public dashboard<br>2. Click on a sensor marker | Popup displays with sensor details and current AQI |
| DASH-03 | View historical data | Sensor has historical readings | 1. Navigate to sensor detail page<br>2. View historical data chart | Chart displays historical AQI values correctly |

## Critical Analysis of Testing Strategy

### Strengths of Our Approach

1. **Comprehensive Test Coverage**: Our multilayered testing approach (unit, integration, functional) ensures that both individual components and the system as a whole are thoroughly tested.

2. **Isolation of Dependencies**: By using mock objects and test doubles, we effectively isolated components during testing, allowing for more reliable and focused tests that aren't affected by external factors.

3. **Realistic Test Data**: Our mock data generators produce realistic air quality data patterns, ensuring that our testing accurately reflects real-world scenarios and edge cases.

4. **Automated Testing Pipeline**: Integration with Laravel's testing framework allows for automated test execution, making continuous integration possible and reducing the likelihood of regression issues.

5. **Test-Driven Development**: For critical features like the alert system, we employed a test-driven development approach, writing tests before implementing features to ensure requirements were met.

### Limitations and Challenges

1. **Performance Testing Constraints**: Due to resource limitations, our performance testing was limited to simulated loads rather than real-world scale testing, which might not fully represent the system's behavior under peak conditions.

2. **Browser Testing Complexity**: Browser-based tests were more brittle than other tests, occasionally failing due to timing issues or browser-specific behavior, requiring additional maintenance.

3. **Mobile Device Testing Scope**: While responsive design was implemented, our testing on mobile devices was limited to emulation rather than comprehensive real-device testing.

4. **Long-term Data Patterns**: Our testing could not fully simulate long-term data patterns (seasonal variations, yearly trends) due to time constraints, which might affect the historical analysis features.

### Future Improvements

1. **Expanded Mobile Testing**: Implement more comprehensive testing on real mobile devices to ensure optimal user experience across all platforms.

2. **Stress Testing**: Develop more robust performance and stress testing to simulate high-load scenarios, particularly for concurrent user access during air quality incidents.

3. **Automated UI Regression Testing**: Expand the use of visual regression testing to automatically detect unintended UI changes.

4. **User Acceptance Testing**: Incorporate formal user acceptance testing with actual stakeholders from environmental monitoring agencies.

5. **Security Testing Enhancement**: Implement more comprehensive security testing, including penetration testing and vulnerability scanning.

### Justification of Approach

Our testing strategy was designed to balance thoroughness with practical constraints. Unit tests provide rapid feedback on code correctness, integration tests verify component interactions, and functional tests validate the user experience. This layered approach allows us to identify issues at the most appropriate level, making debugging more efficient.

The use of mock objects and test data generators was essential for testing environmental monitoring systems, as real sensor data would be impractical to generate in a controlled testing environment. Our mock data generators were carefully designed to simulate realistic air quality patterns, including daily variations and location-specific characteristics.

The emphasis on automated testing supports our continuous integration workflow, allowing us to detect regressions quickly as new features are added. This was particularly important given the critical nature of an air quality monitoring system, where data accuracy and system reliability are paramount.

## Conclusion

Our testing strategy for the Air Quality Monitoring Dashboard has proven effective in ensuring the reliability, accuracy, and usability of the system. Through a combination of unit, integration, and functional testing, we have verified that the application meets all specified requirements while maintaining high code quality and robust error handling.

The mock objects and test data generators developed specifically for this project have allowed us to thoroughly test the system's response to various air quality scenarios without relying on physical sensors, significantly enhancing our testing capabilities.

While we have identified some limitations in our current testing approach, particularly around performance testing and mobile device coverage, the foundation we have established provides a solid framework for ongoing quality assurance as the system evolves.

The Air Quality Monitoring Dashboard for Colombo Metropolitan Area is now ready for deployment, with confidence that it will provide reliable, accurate, and timely air quality information to both the public and administrative users.

---

*Developed by [Team Member Names]*
