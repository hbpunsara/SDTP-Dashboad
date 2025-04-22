@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Admin Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <!-- Status Cards Section -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Sensors</div>
                            <div class="fs-3">{{ $sensorStats['total'] }}</div>
                        </div>
                        <i class="fas fa-map-marker-alt fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>Active: {{ $sensorStats['active'] }} | Inactive: {{ $sensorStats['inactive'] }}</div>
                    <a class="small text-white stretched-link" href="{{ route('admin.sensors.index') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Critical Alerts</div>
                            <div class="fs-3">{{ $sensorStats['critical_alerts'] }}</div>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>Requires immediate attention</div>
                    <a class="small text-white stretched-link" href="{{ route('admin.sensors.index') }}?filter=critical">View Alerts</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">System Status</div>
                            <div class="fs-5">{{ $systemStats['system_status'] }}</div>
                        </div>
                        <i class="fas fa-server fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>Uptime: {{ $systemStats['system_uptime'] }}</div>
                    <a class="small text-white stretched-link" href="{{ route('admin.data-simulation') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Data Points</div>
                            <div class="fs-5">{{ $systemStats['data_points_collected'] }}</div>
                        </div>
                        <i class="fas fa-database fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div>Last Sync: {{ substr($systemStats['last_data_sync'], 0, 16) }}</div>
                    <a class="small text-white stretched-link" href="{{ route('admin.data-simulation') }}">Simulate Data</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sensor Map -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-map-marked-alt me-1"></i>
                    Sensor Network Map
                </div>
                <div class="card-body">
                    <div id="adminMap" style="height: 400px; width: 100%;"></div>
                </div>
            </div>
        </div>
        
        <!-- Recent Readings Section -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Recent Air Quality Readings
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sensor ID</th>
                                    <th>Location</th>
                                    <th>AQI</th>
                                    <th>Status</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentReadings as $reading)
                                <tr>
                                    <td><strong>{{ $reading['sensor_id'] }}</strong></td>
                                    <td>{{ $reading['location'] }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $color = 'success';
                                                if ($reading['aqi'] > 100) {
                                                    $color = 'danger';
                                                } elseif ($reading['aqi'] > 50) {
                                                    $color = 'warning';
                                                }
                                            @endphp
                                            <div class="me-2 bg-{{ $color }}" style="width: 12px; height: 12px; border-radius: 50%;"></div>
                                            {{ $reading['aqi'] }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $badge = 'success';
                                            if ($reading['status'] == 'Unhealthy for Sensitive Groups') {
                                                $badge = 'warning';
                                            } elseif ($reading['status'] == 'Unhealthy' || $reading['status'] == 'Very Unhealthy') {
                                                $badge = 'danger';
                                            } elseif ($reading['status'] == 'Moderate') {
                                                $badge = 'info';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $badge }}">{{ $reading['status'] }}</span>
                                    </td>
                                    <td><small class="text-muted">{{ $reading['timestamp'] }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the map
        const adminMap = L.map('adminMap').setView([6.9271, 79.8612], 12); // Colombo coordinates

        // Add tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(adminMap);
        
        // Fetch sensors data from API
        fetch('/api/sensors')
            .then(response => response.json())
            .then(sensors => {
                // Function to get marker color based on AQI
                function getMarkerColor(aqi) {
                    if (!aqi) return "gray";
                    if (aqi <= 50) return "green";
                    if (aqi <= 100) return "yellow";
                    if (aqi <= 150) return "orange";
                    if (aqi <= 200) return "red";
                    if (aqi <= 300) return "purple";
                    return "black";
                }
                
                // Add markers for each sensor
                sensors.forEach(sensor => {
                    // Skip inactive sensors or sensors without readings
                    if (!sensor.is_active) return;
                    
                    const aqi = sensor.latest_reading ? sensor.latest_reading.aqi : null;
                    const markerColor = getMarkerColor(aqi);
                    
                    // Create a custom icon
                    const sensorIcon = L.divIcon({
                        className: 'custom-marker',
                        html: `<div style="background-color: ${markerColor}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white;"></div>`,
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    });
                    
                    // Add marker to map
                    const marker = L.marker([sensor.latitude, sensor.longitude], { icon: sensorIcon }).addTo(adminMap);
                    
                    // Get AQI category
                    let category = "Unknown";
                    if (aqi !== null) {
                        if (aqi <= 50) category = "Good";
                        else if (aqi <= 100) category = "Moderate";
                        else if (aqi <= 150) category = "Unhealthy for Sensitive Groups";
                        else if (aqi <= 200) category = "Unhealthy";
                        else if (aqi <= 300) category = "Very Unhealthy";
                        else category = "Hazardous";
                    }
                    
                    // Add popup with improved styling
                    marker.bindPopup(`
                        <div class="sensor-popup">
                            <h5 class="popup-title">${sensor.name}</h5>
                            <div class="sensor-info">
                                <div class="sensor-location"><i class="fas fa-map-marker-alt"></i> ${sensor.location}</div>
                                <div class="sensor-id text-muted">ID: ${sensor.sensor_id}</div>
                                <div class="aqi-display mt-2">
                                    <span class="aqi-label">Air Quality Index:</span>
                                    <span class="aqi-value" style="background-color: ${markerColor}; color: ${markerColor === 'yellow' ? '#000' : '#fff'};">
                                        ${aqi || 'N/A'}
                                    </span>
                                    <span class="aqi-category">${category}</span>
                                </div>
                            </div>
                        </div>
                    `);
                });
            })
            .catch(error => {
                console.error('Error fetching sensors:', error);
                document.getElementById('adminMap').innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Could not load sensor data. Please refresh the page or try again later.
                    </div>
                `;
            });
    });
</script>

<style>
    /* Custom styles for the sensor popups */
    .sensor-popup {
        padding: 5px;
    }
    
    .popup-title {
        font-weight: bold;
        margin-bottom: 8px;
        color: #0d6efd;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 5px;
    }
    
    .sensor-info {
        margin-bottom: 10px;
    }
    
    .sensor-location {
        font-weight: 500;
    }
    
    .sensor-id {
        font-size: 0.85rem;
    }
    
    .aqi-display {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 5px;
    }
    
    .aqi-label {
        font-weight: 500;
        flex-basis: 100%;
    }
    
    .aqi-value {
        display: inline-block;
        width: 35px;
        height: 35px;
        line-height: 35px;
        text-align: center;
        border-radius: 50%;
        font-weight: bold;
    }
    
    .aqi-category {
        font-size: 0.9rem;
    }
    
    /* Customize Leaflet popup */
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
        box-shadow: 0 3px 14px rgba(0,0,0,0.2);
    }
    
    .leaflet-popup-content {
        margin: 10px;
        min-width: 200px;
    }
</style>
@endsection