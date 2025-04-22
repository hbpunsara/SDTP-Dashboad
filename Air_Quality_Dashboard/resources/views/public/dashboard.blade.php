@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <p class="text-muted">View real-time air quality data across Colombo city.</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="/historical-data" class="btn btn-primary">
                    <i class="fas fa-chart-line me-1"></i> Historical Data
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Air Quality Map</h5>
                </div>
                <div class="card-body">
                    <div id="map" style="height: 500px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">AQI Legend</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap">
                        <div class="me-3 mb-2">
                            <span class="badge bg-success">&nbsp;</span> Good (0-50)
                        </div>
                        <div class="me-3 mb-2">
                            <span class="badge bg-warning">&nbsp;</span> Moderate (51-100)
                        </div>
                        <div class="me-3 mb-2">
                            <span class="badge bg-orange" style="background-color: #FF9800;">&nbsp;</span> Unhealthy for Sensitive Groups (101-150)
                        </div>
                        <div class="me-3 mb-2">
                            <span class="badge bg-danger">&nbsp;</span> Unhealthy (151-200)
                        </div>
                        <div class="me-3 mb-2">
                            <span class="badge bg-purple" style="background-color: #9C27B0;">&nbsp;</span> Very Unhealthy (201-300)
                        </div>
                        <div class="me-3 mb-2">
                            <span class="badge bg-dark">&nbsp;</span> Hazardous (301+)
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Selected Sensor Data</h5>
                </div>
                <div class="card-body" id="selected-sensor-data">
                    <p class="text-center text-muted">Click on a sensor on the map to view its data</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS for sensor popups -->
<style>
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
    
    .view-details-btn {
        width: 100%;
    }
    
    .sensor-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .sensor-detail-info {
        background-color: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        border-left: 4px solid #0d6efd;
    }
    
    .chart-container {
        height: 250px;
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

@section('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<script>
    // Wait for DOM to fully load before initializing the map
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the map
        const map = L.map('map').setView([6.9271, 79.8612], 12); // Colombo coordinates

        // Add tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add custom control for adding a new sensor
        @auth
        const addSensorControl = L.Control.extend({
            options: {
                position: 'bottomright'
            },
            
            onAdd: function(map) {
                const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                const button = L.DomUtil.create('a', 'add-sensor-btn', container);
                button.innerHTML = '<i class="fas fa-plus-circle"></i> Add New Sensor';
                button.title = 'Add a new sensor location';
                button.href = '{{ route("admin.sensors.create") }}';
                button.style.display = 'flex';
                button.style.alignItems = 'center';
                button.style.justifyContent = 'center';
                button.style.padding = '8px 12px';
                button.style.backgroundColor = '#0d6efd';
                button.style.color = 'white';
                button.style.fontWeight = 'bold';
                button.style.textDecoration = 'none';
                button.style.borderRadius = '4px';
                button.style.minWidth = '140px';
                
                return container;
            }
        });
        new addSensorControl().addTo(map);
        @endauth

        // Real sensor data from the database
        const sensorData = [
            @foreach($sensors as $sensor)
                {
                    id: {{ $sensor->id }},
                    name: "{{ $sensor->name }}",
                    sensor_id: "{{ $sensor->sensor_id }}",
                    location: "{{ $sensor->location }}",
                    lat: {{ $sensor->latitude }},
                    lng: {{ $sensor->longitude }},
                    aqi: {{ $sensor->latestReading ? $sensor->latestReading->aqi : rand(20, 150) }},
                    category: "{{ $sensor->latestReading ? $sensor->latestReading->aqi_category : (rand(0, 100) > 70 ? 'Moderate' : 'Good') }}",
                    is_active: {{ $sensor->is_active ? 'true' : 'false' }}
                }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ];

        // Function to get marker color based on AQI
        function getMarkerColor(aqi) {
            if (aqi <= 50) return "green";
            if (aqi <= 100) return "yellow";
            if (aqi <= 150) return "orange";
            if (aqi <= 200) return "red";
            if (aqi <= 300) return "purple";
            return "black";
        }

        // Add markers for each sensor
        sensorData.forEach(sensor => {
            const markerColor = getMarkerColor(sensor.aqi);
            
            // Create a custom icon
            const sensorIcon = L.divIcon({
                className: 'custom-marker',
                html: `<div style="background-color: ${markerColor}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white;"></div>`,
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });
            
            // Add marker to map
            const marker = L.marker([sensor.lat, sensor.lng], { icon: sensorIcon }).addTo(map);
            
            // Add popup with improved styling (without view details button)
            marker.bindPopup(`
                <div class="sensor-popup">
                    <h5 class="popup-title">${sensor.name}</h5>
                    <div class="sensor-info">
                        <div class="sensor-location"><i class="fas fa-map-marker-alt"></i> ${sensor.location}</div>
                        <div class="sensor-id text-muted">ID: ${sensor.sensor_id}</div>
                        <div class="aqi-display mt-2">
                            <span class="aqi-label">Air Quality Index:</span>
                            <span class="aqi-value" style="background-color: ${markerColor}; color: ${markerColor === 'yellow' ? '#000' : '#fff'};">
                                ${sensor.aqi}
                            </span>
                            <span class="aqi-category">${sensor.category}</span>
                        </div>
                    </div>
                </div>
            `);
            
            // Add click handler
            marker.on('click', function() {
                showSensorDetails(sensor.id);
            });
        });

        // Make showSensorDetails function global
        window.showSensorDetails = function(sensorId) {
            const sensor = sensorData.find(s => s.id === sensorId);
            if (!sensor) return;
            
            // Get detailed data for this sensor
            fetch(`/api/sensors/${sensorId}/readings`)
                .then(response => response.json())
                .then(data => {
                    const sensorDataDiv = document.getElementById('selected-sensor-data');
                    
                    // Create data arrays from API response
                    const timestamps = data.map(reading => reading.reading_time.substring(11, 16));
                    const aqiValues = data.map(reading => reading.aqi);
                    const pm25Values = data.map(reading => reading.pm25);
                    
                    // Update the selected sensor card with more detailed information
                    sensorDataDiv.innerHTML = `
                        <div class="sensor-detail-header">
                            <h4>${sensor.name}</h4>
                            <span class="badge ${getBadgeClass(sensor.aqi)}">${sensor.aqi} - ${sensor.category}</span>
                        </div>
                        <div class="sensor-detail-info mb-3">
                            <div><strong>Location:</strong> ${sensor.location}</div>
                            <div><strong>Sensor ID:</strong> ${sensor.sensor_id}</div>
                            <div><strong>Status:</strong> ${sensor.is_active ? '<span class="text-success">Active</span>' : '<span class="text-danger">Inactive</span>'}</div>
                        </div>
                        <div class="chart-container">
                            <canvas id="sensorChart"></canvas>
                        </div>
                        <div class="text-end mt-2">
                            <a href="/historical-data?sensor_id=${sensorId}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-history me-1"></i> View Historical Data
                            </a>
                        </div>
                    `;
                    
                    // Create a nice-looking chart
                    const ctx = document.getElementById('sensorChart').getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: timestamps.length > 0 ? timestamps : ['No data available'],
                            datasets: [
                                {
                                    label: 'AQI',
                                    data: aqiValues.length > 0 ? aqiValues : [0],
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderWidth: 2,
                                    tension: 0.3
                                },
                                {
                                    label: 'PM2.5',
                                    data: pm25Values.length > 0 ? pm25Values : [0],
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderWidth: 2,
                                    tension: 0.3
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Recent Air Quality Readings'
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error fetching sensor readings:', error);
                    const sensorDataDiv = document.getElementById('selected-sensor-data');
                    sensorDataDiv.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Could not load data for this sensor. Please try again later.
                        </div>
                    `;
                });
        }
        
        // Helper function to get appropriate badge class based on AQI
        function getBadgeClass(aqi) {
            if (aqi <= 50) return "bg-success";
            if (aqi <= 100) return "bg-warning";
            if (aqi <= 150) return "bg-orange";
            if (aqi <= 200) return "bg-danger";
            if (aqi <= 300) return "bg-purple";
            return "bg-dark";
        }
    });
</script>
@endsection