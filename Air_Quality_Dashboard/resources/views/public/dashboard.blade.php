@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-3">Air Quality Monitoring Dashboard - Colombo Metropolitan Area</h1>
            <p class="lead">View real-time air quality data across Colombo city.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Air Quality Map</h5>
                </div>
                <div class="card-body">
                    <div id="map" style="height: 500px;"></div>
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

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<script>
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

    // Sample data (placeholder) - this would be replaced with real data from your backend
    const sensorData = [
        { id: 1, name: "Colombo Fort", lat: 6.9271, lng: 79.8612, aqi: 45, category: "Good" },
        { id: 2, name: "Pettah", lat: 6.9344, lng: 79.8500, aqi: 75, category: "Moderate" },
        { id: 3, name: "Slave Island", lat: 6.9200, lng: 79.8500, aqi: 120, category: "Unhealthy for Sensitive Groups" },
        { id: 4, name: "Borella", lat: 6.9103, lng: 79.8780, aqi: 65, category: "Moderate" },
        { id: 5, name: "Wellawatte", lat: 6.8790, lng: 79.8630, aqi: 35, category: "Good" }
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
        
        // Add popup
        marker.bindPopup(`
            <strong>${sensor.name}</strong><br>
            AQI: ${sensor.aqi} (${sensor.category})<br>
            <a href="#" onclick="showSensorDetails(${sensor.id})">View Details</a>
        `);
        
        // Add click handler
        marker.on('click', function() {
            showSensorDetails(sensor.id);
        });
    });

    // Function to show sensor details (would be implemented with real data)
    function showSensorDetails(sensorId) {
        const sensor = sensorData.find(s => s.id === sensorId);
        if (!sensor) return;
        
        const sensorDataDiv = document.getElementById('selected-sensor-data');
        sensorDataDiv.innerHTML = `
            <h4>${sensor.name}</h4>
            <div class="mb-3">
                <strong>Current AQI:</strong> ${sensor.aqi} (${sensor.category})
            </div>
            <div>
                <canvas id="sensorChart"></canvas>
            </div>
        `;
        
        // Sample chart data (would be replaced with real historical data)
        const ctx = document.getElementById('sensorChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['12AM', '3AM', '6AM', '9AM', '12PM', '3PM', '6PM', '9PM'],
                datasets: [{
                    label: 'AQI Last 24 Hours',
                    data: [40, 35, 30, 45, 55, 65, 70, sensor.aqi],
                    fill: false,
                    borderColor: getMarkerColor(sensor.aqi),
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
</script>
@endsection

<script>
    var map = L.map('map').setView([6.9271, 79.8612], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    @foreach ($sensors as $sensor)
    var marker = L.marker([{{ $sensor->latitude }}, {{ $sensor->longitude }}]).addTo(map);
    marker.bindPopup("<b>{{ $sensor->name }}</b><br>AQI: {{ $sensor->latestReading->aqi }}");
    @endforeach
</script>