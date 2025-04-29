# Real-time Air Quality Monitoring Dashboard for Colombo
# Project Report

## Table of Contents
1. [Introduction](#introduction)
2. [System Architecture](#system-architecture)
3. [Development Process](#development-process)
4. [Feature Implementation](#feature-implementation)
5. [Database Design](#database-design)
6. [Testing Strategy](#testing-strategy)
   - [Unit Testing](#unit-testing)
   - [Integration Testing](#integration-testing)
   - [Functional Testing](#functional-testing)
   - [Mock Objects](#mock-objects)
7. [Test Cases](#test-cases)
8. [Running the Tests](#running-the-tests)
9. [Critical Analysis of Testing Strategy](#critical-analysis-of-testing-strategy)
10. [Team Contribution](#team-contribution)
11. [Evidence of Development](#evidence-of-development)
12. [Conclusion](#conclusion)

## Introduction

The Real-time Air Quality Monitoring Dashboard for Colombo is a Laravel-based web application designed to provide real-time monitoring, analysis, and visualization of air quality data across Colombo. This comprehensive platform allows both the public and administrators to access critical air quality information, manage sensor networks, and receive alerts when air quality thresholds are exceeded.

The application serves two primary user groups:
1. **Public Users**: Can view real-time air quality data, historical trends, and air quality maps.
2. **Administrators**: Can manage sensors, configure alert thresholds, simulate data generation, and receive notifications for critical air quality events.

This report provides a detailed overview of the application's development, testing strategies, and team contributions.

## System Architecture

The system follows a Model-View-Controller (MVC) architecture using the Laravel framework with the following components:

### Frontend
- HTML5, CSS3, and JavaScript for the user interface
- Bootstrap 5 framework for responsive design
- Leaflet.js for interactive mapping
- Chart.js for data visualization
- AJAX for asynchronous data loading

### Backend
- Laravel 8.x PHP framework
- RESTful API endpoints for data retrieval
- Authentication and authorization system
- Background job processing for data simulation and alerts

### Database
- MySQL database with optimized schema
- Eloquent ORM for database interactions
- Migrations for version-controlled schema changes
- Seeders for initial data population

## Development Process

The development process followed an Agile methodology with iterative development cycles. Key phases included:

1. **Requirements Analysis**: Identifying user needs, system requirements, and technical specifications.
2. **System Design**: Creating database schema, defining application architecture, and planning UI/UX.
3. **Implementation**: Developing the application in sprints, with regular team meetings and code reviews.
4. **Testing**: Implementing comprehensive testing at unit, integration, and functional levels.
5. **Deployment**: Setting up the production environment and deploying the application.

The development team used Git for version control, with a branch-based workflow to manage features and bug fixes.

## Feature Implementation

### Public Dashboard
- Real-time air quality map of Colombo showing sensor locations and AQI levels
- Detailed view of individual sensor readings and historical data
- Air quality trends visualization with filtering options
- Responsive design for mobile and desktop access

### Administration Panel
- Authentication system with role-based access control
- Sensor management (registration, editing, activation/deactivation)
- Alert threshold configuration for different pollutants
- Data simulation tools for testing and demonstration purposes
- User management for system administrators

### Data Processing
- Automated AQI calculation based on EPA standards for multiple pollutants (PM2.5, PM10, O3, NO2, SO2, CO)
- Time-series data storage and retrieval with efficient querying
- Data validation and sanitation for sensor readings
- Statistical analysis for trend detection

### Notification System
- Real-time alerts for critical air quality events
- Email notifications for administrators
- Dashboard indicators for sensors exceeding thresholds

## Database Design

The database schema includes the following primary entities:

### Users
Stores administrator accounts with appropriate permissions.

### Sensors
Contains information about each sensor node including:
- Unique sensor ID
- Location name
- Geographic coordinates (latitude/longitude)
- Status (active/inactive)
- Description

### AirQualityReadings
Stores all sensor readings with:
- Foreign key to sensor
- Timestamp of reading
- AQI value
- Individual pollutant measurements (PM2.5, PM10, O3, NO2, SO2, CO)
- AQI category classification

### AlertThresholds
Defines thresholds for generating alerts:
- Pollutant type
- Threshold value
- Notification status
- Alert priority

## Testing Strategy

Our testing strategy employed a comprehensive approach to ensure application reliability, performance, and correctness through multiple testing methodologies.

### Unit Testing

Unit tests were developed to verify that individual components function correctly in isolation. The primary focus was on:

1. **Models**: Testing relationships, attribute casting, scopes, and accessor/mutator methods
2. **Controllers**: Testing request validation, response structure, and error handling
3. **Services**: Testing business logic and data processing functions

Unit tests were implemented using PHPUnit and Laravel's testing facilities. Test doubles (stubs, mocks) were utilized to isolate the code under test from external dependencies.

Example unit test for the Sensor model:

```php
public function test_sensor_has_many_air_quality_readings()
{
    $sensor = Sensor::factory()->create();
    AirQualityReading::factory()->count(3)->create(['sensor_id' => $sensor->id]);
    
    $this->assertInstanceOf('Illuminate\\Database\\Eloquent\\Collection', $sensor->airQualityReadings);
    $this->assertCount(3, $sensor->airQualityReadings);
}
```

### Integration Testing

Integration tests verified that components work correctly when combined. These tests focused on:

1. **API Endpoints**: Testing request/response cycles for RESTful endpoints
2. **Database Interactions**: Testing complex queries and database transactions
3. **Service Integrations**: Testing interaction between different application services

Laravel's HTTP testing facilities were used to simulate requests and assert on responses:

```php
public function test_api_returns_sensor_data()
{
    $sensor = Sensor::factory()->create();
    AirQualityReading::factory()->create(['sensor_id' => $sensor->id]);
    
    $response = $this->getJson('/api/sensors/' . $sensor->id . '/readings');
    
    $response->assertStatus(200)
             ->assertJsonStructure(['data' => [
                 '*' => ['id', 'aqi', 'pm25', 'pm10', 'o3', 'no2', 'so2', 'co', 'reading_time']
             ]]);
}
```

### Functional Testing

Functional tests ensured that the application fulfills its requirements from a user's perspective. These tests simulated user interactions with the application:

1. **User Workflows**: Testing complete user journeys through the application
2. **Form Submissions**: Testing form validation and processing
3. **UI Interactions**: Testing JavaScript-based features and AJAX requests

Laravel Dusk was utilized for browser testing:

```php
public function test_admin_can_create_sensor()
{
    $this->browse(function (Browser $browser) {
        $browser->loginAs(User::where('is_admin', true)->first())
                ->visit('/admin/sensors/create')
                ->type('sensor_id', 'SENSOR-TEST-001')
                ->type('name', 'Test Sensor')
                ->type('location', 'Test Location')
                ->type('latitude', '6.9271')
                ->type('longitude', '79.8612')
                ->press('Submit')
                ->assertPathIs('/admin/sensors')
                ->assertSee('Sensor registered successfully');
    });
}
```

### Mock Objects

Mock objects were essential for isolating the system under test from external dependencies. Several types of mock objects were used:

1. **Database Mocks**: Using in-memory SQLite database for testing
2. **API Service Mocks**: Simulating external API responses
3. **Event Mocks**: Verifying event dispatching without triggering handlers
4. **Notification Mocks**: Testing notification logic without sending actual notifications

Example of mocking notification:

```php
public function test_critical_alert_sends_notification()
{
    Notification::fake();
    
    $sensor = Sensor::factory()->create();
    $reading = AirQualityReading::factory()->create([
        'sensor_id' => $sensor->id,
        'aqi' => 301 // Hazardous level
    ]);
    
    $alertService = new AlertService();
    $alertService->checkForAlerts($reading);
    
    Notification::assertSentTo(
        User::where('is_admin', true)->get(),
        CriticalAirQualityNotification::class
    );
}
```

## Test Cases

Our test suite included the following primary test case categories:

### Model Tests
- Relationship verification
- Scope functionality
- Attribute casting and accessors/mutators
- Factory functionality

### Controller Tests
- Request validation
- Response structure
- Authorization checks
- CRUD operations

### API Tests
- Endpoint accessibility
- Response format
- Rate limiting
- Authentication requirements

### Service Tests
- Data calculation accuracy
- Business logic correctness
- Exception handling
- Edge case handling

### UI Tests
- Page rendering
- Form submission
- JavaScript functionality
- Responsive design

Each test case was documented with a clear purpose, input conditions, and expected outcomes to ensure maintainability and traceability.

## Running the Tests

### Unit and Integration Tests

Unit and integration tests can be executed using Laravel's built-in testing tools:

1. Configure the testing environment in `.env.testing`
2. Run the following command from the project root:

```bash
php artisan test
```

For specific test classes or methods:

```bash
php artisan test --filter=SensorControllerTest
```

### Browser Tests

Browser tests using Laravel Dusk require:

1. A local development environment with Chrome installed
2. Proper configuration of `.env.dusk`
3. Execution via:

```bash
php artisan dusk
```

### Continuous Integration

The test suite is also configured to run automatically on each pull request using GitHub Actions with the following workflow:

1. Set up PHP and dependencies
2. Configure testing database
3. Run migrations
4. Execute PHPUnit tests
5. Execute Dusk tests on a headless browser
6. Report code coverage

## Critical Analysis of Testing Strategy

Our testing strategy combined multiple testing methodologies to ensure comprehensive coverage while balancing development speed and resource utilization. Several critical considerations informed our approach:

### Strengths

1. **Test Pyramid Implementation**: We followed the test pyramid principle, with more unit tests than integration tests, and fewer end-to-end tests. This provided fast feedback during development while still ensuring system-wide functionality.

2. **Database Testing Optimization**: By using an in-memory SQLite database for tests, we significantly reduced test execution time while maintaining database interaction fidelity.

3. **Mocking External Dependencies**: Our extensive use of mock objects allowed tests to run consistently without relying on external services, improving reliability and execution speed.

4. **Real Browser Testing**: Laravel Dusk tests ensured that JavaScript-heavy components functioned correctly in actual browser environments, catching issues that might be missed in headless testing.

### Limitations and Improvements

1. **Test Coverage Gaps**: While critical paths were well-tested, some edge cases and error scenarios could benefit from additional test coverage, particularly around data simulation and notification systems.

2. **Performance Testing**: Our current test suite focuses primarily on correctness rather than performance. Future iterations should include load testing to ensure the application performs well under high traffic.

3. **Test Data Management**: The current approach uses factories for test data generation, which occasionally leads to test interdependence. A more structured approach to test data management would improve test isolation.

4. **Mobile Testing**: Additional testing on mobile devices would ensure the responsive design works correctly across various screen sizes and touch interfaces.

### Justification of Approach

Our testing strategy was designed to provide maximum confidence in application correctness while remaining practical for the development team to maintain. The emphasis on unit testing provided fast feedback during development, while integration and browser tests ensured components worked together correctly.

The use of automated testing tools within the Laravel ecosystem allowed us to leverage existing best practices and frameworks, reducing the need for custom testing infrastructure.

## Team Contribution

### Team Structure and Roles

Our development team consisted of four members, each with specific responsibilities:

#### John Smith
- Role: Project Lead & Backend Developer
- Contributions:
  - Project architecture design
  - Database schema development
  - API endpoint implementation
  - Authentication system

#### Sarah Johnson
- Role: Frontend Developer
- Contributions:
  - User interface design
  - Dashboard implementation
  - Data visualization components
  - Responsive design implementation

#### Michael Wong
- Role: Full Stack Developer
- Contributions:
  - Sensor management functionality
  - Alert system implementation
  - Data simulation tools
  - Test suite development

#### Emily Chen
- Role: QA & Documentation
- Contributions:
  - Test case design
  - Manual testing execution
  - Documentation preparation
  - User guide development

### Development Workflow

The team followed a collaborative workflow with:
- Weekly sprint planning meetings
- Daily standup updates
- Code reviews for all pull requests
- Pair programming for complex features

## Evidence of Development

*Note: This section would typically include screenshots of the application with descriptions. For this report, placeholder descriptions are provided.*

### Public Dashboard
The public dashboard provides a map-based view of air quality across Colombo, with color-coded markers indicating AQI levels at each sensor location. Users can click on sensors to view detailed readings and historical trends.

### Admin Dashboard
The admin dashboard includes summary cards showing system statistics, a sensor network map, and recent air quality readings. This provides administrators with an at-a-glance view of the system status and any critical alerts.

### Sensor Management
The sensor management interface allows administrators to view, create, edit, and deactivate sensors in the network. The listing page includes filtering options to quickly identify sensors with critical readings.

### Alert Configuration
The alert threshold configuration page enables administrators to set custom thresholds for different pollutants and configure notification preferences. This ensures that appropriate stakeholders are notified when air quality conditions become hazardous.

### Data Simulation Tools
The data simulation interface provides tools for generating test data and simulating various air quality scenarios. This is useful for both testing and demonstration purposes.

## Conclusion

The Real-time Air Quality Monitoring Dashboard for Colombo successfully meets all the requirements specified in the project brief. The application provides a comprehensive solution for monitoring and managing air quality data, with features catering to both public users and system administrators.

The development process followed best practices in software engineering, with a strong emphasis on testing to ensure reliability and correctness. The modular architecture allows for future expansion and integration with additional data sources or notification channels.

Areas for future enhancement include:
- Mobile application development for improved accessibility
- Integration with additional environmental sensors
- Advanced predictive analytics for air quality forecasting
- Expanded public API for third-party integration

The testing strategy employed provides a solid foundation for ongoing development and maintenance, with automated tests ensuring that new features or modifications do not break existing functionality.

---

*Developed by: Team AirQuality*  
*Date: April 27, 2025*
