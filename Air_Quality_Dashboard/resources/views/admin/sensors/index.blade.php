@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Sensor Management</h1>
            <p class="text-muted">Manage simulated air quality sensors in Colombo</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.sensors.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Register New Sensor
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h5 class="mb-0">Sensor List</h5>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" id="sensor-search" class="form-control" placeholder="Search sensors...">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Filter <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item filter-option" href="#" data-filter="all">All Sensors</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="active">Active Only</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="inactive">Inactive Only</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sensor ID</th>
                            <th>Location</th>
                            <th>Coordinates</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sensors-table">
                        @forelse($sensors as $sensor)
                        <tr data-status="{{ $sensor->is_active ? 'active' : 'inactive' }}" class="sensor-row">
                            <td><strong>{{ $sensor->sensor_id }}</strong></td>
                            <td>{{ $sensor->location }}</td>
                            <td>
                                <small class="text-muted">
                                    {{ number_format($sensor->latitude, 6) }}, {{ number_format($sensor->longitude, 6) }}
                                </small>
                            </td>
                            <td>
                                @if($sensor->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $sensor->updated_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.sensors.edit', $sensor->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('admin.sensors.toggle-status', $sensor->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-{{ $sensor->is_active ? 'warning' : 'success' }}">
                                        @if($sensor->is_active)
                                        <i class="fas fa-pause-circle"></i> Deactivate
                                        @else
                                        <i class="fas fa-play-circle"></i> Activate
                                        @endif
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-sensor fa-2x mb-3 d-block"></i>
                                No sensors registered yet.
                                <a href="{{ route('admin.sensors.create') }}" class="btn btn-sm btn-primary mt-2">Register your first sensor</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Sensor Map Section -->
    <div class="card shadow-sm">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <h5 class="mb-0">Sensor Location Map</h5>
                </div>
                <div class="col-auto">
                    <div class="d-flex align-items-center">
                        <span class="me-2"><i class="fas fa-info-circle"></i> Click on the map or use the "Add New Sensor Location" button to place a new sensor</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="map" class="map-container" style="height: 500px; border-radius: 5px;"></div>
        </div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<style>
    /* Custom styles for the sensor map */
    .map-container {
        border: 1px solid #ddd;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .sensor-label-tooltip {
        background-color: rgba(0, 0, 0, 0.7);
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.4);
        color: white;
        font-weight: bold;
        padding: 3px 8px;
        border-radius: 10px;
    }
    .sensor-placement-popup .leaflet-popup-content-wrapper {
        border-radius: 8px;
        padding: 0;
    }
    .sensor-placement-popup .leaflet-popup-tip {
        background-color: white;
    }
    .new-sensor-marker {
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.3); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('sensor-search');
        searchInput.addEventListener('keyup', filterSensors);
        
        // Filter functionality
        const filterOptions = document.querySelectorAll('.filter-option');
        filterOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                const filter = this.getAttribute('data-filter');
                filterByStatus(filter);
                
                // Update dropdown button text
                const filterText = filter.charAt(0).toUpperCase() + filter.slice(1);
                this.closest('.input-group').querySelector('.dropdown-toggle').textContent = 
                    filterText === 'All' ? 'Filter ' : filterText + ' ';
            });
        });
        
        function filterSensors() {
            const searchTerm = searchInput.value.toLowerCase();
            const rows = document.querySelectorAll('.sensor-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        function filterByStatus(status) {
            const rows = document.querySelectorAll('.sensor-row');
            
            rows.forEach(row => {
                if (status === 'all' || row.getAttribute('data-status') === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        // Map Implementation
        // Initialize the map centered on Colombo
        const map = L.map('map').setView([6.9271, 79.8612], 12); // Colombo coordinates

        // Add tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Function to get marker color based on sensor status
        function getMarkerColor(isActive) {
            return isActive ? "#28a745" : "#6c757d";
        }

        // Add existing sensors to the map
        @foreach($sensors as $sensor)
        L.circleMarker([{{ $sensor->latitude }}, {{ $sensor->longitude }}], {
            color: getMarkerColor({{ $sensor->is_active ? 'true' : 'false' }}),
            fillColor: getMarkerColor({{ $sensor->is_active ? 'true' : 'false' }}),
            fillOpacity: 0.8,
            radius: 8,
            weight: 2
        }).addTo(map).bindPopup(
            "<div class='text-center'>" +
            "<strong>{{ $sensor->sensor_id }}</strong><br>" +
            "{{ $sensor->location }}<br>" +
            "<small>{{ number_format($sensor->latitude, 6) }}, {{ number_format($sensor->longitude, 6) }}</small><br>" +
            "<div class='mt-2'>" +
            "<a href='{{ route('admin.sensors.edit', $sensor->id) }}' class='btn btn-sm btn-primary'>Edit</a> " +
            "<form action='{{ route('admin.sensors.toggle-status', $sensor->id) }}' method='POST' class='d-inline'>" +
            "@csrf @method('PATCH')" +
            "<button type='submit' class='btn btn-sm btn-{{ $sensor->is_active ? 'warning' : 'success' }}'>" +
            "{{ $sensor->is_active ? 'Deactivate' : 'Activate' }}" +
            "</button>" +
            "</form>" +
            "</div>" +
            "</div>"
        );
        @endforeach
        
        // Special markers for the specific sensor points mentioned by the user
        const specialSensorPoints = [
            { id: 53, lat: 6.9103, lng: 79.8612, name: "Sensor #53" },
            { id: 65, lat: 6.9000, lng: 79.8520, name: "Sensor #65" },
            { id: 69, lat: 6.9350, lng: 79.8400, name: "Sensor #69" },
            { id: 112, lat: 6.9200, lng: 79.8700, name: "Sensor #112" }
        ];
        
        // Add special sensor markers with prominent styling and labels
        specialSensorPoints.forEach(point => {
            // Create a pulsing circle marker for highlight
            const highlightMarker = L.circleMarker([point.lat, point.lng], {
                color: '#ff7800',
                fillColor: '#ff7800',
                fillOpacity: 0.6,
                radius: 10,
                weight: 3
            }).addTo(map);
            
            // Add pulsing animation effect
            function pulseMarker() {
                let size = 10;
                let growing = true;
                
                setInterval(() => {
                    if (growing) {
                        size += 0.5;
                        if (size >= 14) growing = false;
                    } else {
                        size -= 0.5;
                        if (size <= 10) growing = true;
                    }
                    highlightMarker.setRadius(size);
                }, 200);
            }
            
            pulseMarker();
            
            // Add a permanent label for these special sensors
            const tooltipContent = `<strong>${point.name}</strong>`;
            highlightMarker.bindTooltip(tooltipContent, {
                permanent: true,
                direction: 'top',
                className: 'sensor-label-tooltip',
                offset: [0, -15]
            });
            
            // Add popup with more information and action buttons
            highlightMarker.bindPopup(
                `<div class="text-center">
                    <strong>${point.name}</strong><br>
                    <small>${point.lat.toFixed(6)}, ${point.lng.toFixed(6)}</small><br>
                    <div class="alert alert-warning mt-2 mb-2 py-1">
                        <i class="fas fa-star"></i> Featured Sensor Location
                    </div>
                    <a href='{{ route('admin.sensors.create') }}?lat=${point.lat}&lng=${point.lng}' class='btn btn-sm btn-primary'>
                        <i class="fas fa-plus-circle"></i> Add Sensor Here
                    </a>
                </div>`
            );
        });
        
        // Add a custom control for adding a new sensor
        const addSensorControl = L.Control.extend({
            options: {
                position: 'bottomright'
            },
            
            onAdd: function(map) {
                const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                const button = L.DomUtil.create('a', 'add-sensor-btn', container);
                button.innerHTML = '<i class="fas fa-plus-circle"></i> Add New Sensor Location';
                button.title = 'Click to add a new sensor location';
                button.href = '#';
                button.style.display = 'flex';
                button.style.alignItems = 'center';
                button.style.justifyContent = 'center';
                button.style.padding = '8px 12px';
                button.style.backgroundColor = '#0d6efd';
                button.style.color = 'white';
                button.style.fontWeight = 'bold';
                button.style.textDecoration = 'none';
                button.style.borderRadius = '4px';
                button.style.minWidth = '200px';
                
                L.DomEvent.on(button, 'click', L.DomEvent.stop)
                    .on(button, 'click', function() {
                        enableMapClickForNewSensor();
                        button.style.backgroundColor = '#dc3545';
                        button.innerHTML = '<i class="fas fa-crosshairs"></i> Click on map to place sensor';
                    });
                
                return container;
            }
        });
        
        new addSensorControl().addTo(map);
        
        // Variable to track if we're in sensor placement mode
        let placingSensor = false;
        let placementMarker = null;
        
        // Function to enable map clicking for new sensor placement
        function enableMapClickForNewSensor() {
            placingSensor = true;
            
            // Change cursor to indicate placement mode
            document.getElementById('map').style.cursor = 'crosshair';
            
            // Show instruction tooltip
            const tooltip = L.tooltip()
                .setLatLng(map.getCenter())
                .setContent("Click anywhere on the map to place a new sensor")
                .openOn(map);
            
            setTimeout(() => {
                map.closeTooltip(tooltip);
            }, 3000);
        }
        
        // Add a clickable zone highlight that follows cursor when in placement mode
        let hoverCircle = null;
        
        map.on('mousemove', function(e) {
            if (!placingSensor) return;
            
            const latlng = e.latlng;
            
            // Create or update hover circle
            if (!hoverCircle) {
                hoverCircle = L.circle(latlng, {
                    color: '#198754',
                    fillColor: '#198754',
                    fillOpacity: 0.2,
                    radius: 200,
                    weight: 2,
                    dashArray: '5,10',
                    interactive: false
                }).addTo(map);
            } else {
                hoverCircle.setLatLng(latlng);
            }
        });
        
        // Map click handler for placing a new sensor
        map.on('click', function(e) {
            if (!placingSensor) return;
            
            const latlng = e.latlng;
            
            // If there's an existing temporary marker, remove it
            if (placementMarker) {
                map.removeLayer(placementMarker);
            }
            
            // Remove hover circle
            if (hoverCircle) {
                map.removeLayer(hoverCircle);
                hoverCircle = null;
            }
            
            // Add a temporary marker with custom icon
            const newSensorIcon = L.divIcon({
                className: 'new-sensor-marker',
                html: `<div style="background-color: #0d6efd; width: 22px; height: 22px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.5);"></div>`,
                iconSize: [22, 22],
                iconAnchor: [11, 11]
            });
            
            placementMarker = L.marker(latlng, {
                draggable: true,
                icon: newSensorIcon
            }).addTo(map);
            
            // Create a popup with a link to the create sensor page, pre-filling coordinates
            const popupContent = `
                <div class="text-center p-2">
                    <h6 class="mb-2">Add New Sensor Here?</h6>
                    <div class="card bg-light mb-2 p-1">
                        <small>${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}</small>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.sensors.create') }}?lat=${latlng.lat}&lng=${latlng.lng}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle"></i> Create Sensor
                        </a>
                        <button class="btn btn-outline-secondary btn-sm cancel-placement">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            `;
            
            const popup = L.popup({
                closeButton: false,
                className: 'sensor-placement-popup'
            })
            .setLatLng(latlng)
            .setContent(popupContent)
            .openOn(map);
            
            // Add event listener for cancel button after popup is added to DOM
            setTimeout(() => {
                document.querySelector('.cancel-placement')?.addEventListener('click', function() {
                    map.closePopup();
                    if (placementMarker) {
                        map.removeLayer(placementMarker);
                        placementMarker = null;
                    }
                });
            }, 100);
            
            // Update coordinates when marker is dragged
            placementMarker.on('drag', function(e) {
                popup.setLatLng(e.target.getLatLng());
            });
            
            placementMarker.on('dragend', function(e) {
                const newPos = e.target.getLatLng();
                popup.setContent(`
                    <div class="text-center p-2">
                        <h6 class="mb-2">Add New Sensor Here?</h6>
                        <div class="card bg-light mb-2 p-1">
                            <small>${newPos.lat.toFixed(6)}, ${newPos.lng.toFixed(6)}</small>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.sensors.create') }}?lat=${newPos.lat}&lng=${newPos.lng}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus-circle"></i> Create Sensor
                            </a>
                            <button class="btn btn-outline-secondary btn-sm cancel-placement">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                `);
                
                // Re-add event listener for cancel button
                setTimeout(() => {
                    document.querySelector('.cancel-placement')?.addEventListener('click', function() {
                        map.closePopup();
                        if (placementMarker) {
                            map.removeLayer(placementMarker);
                            placementMarker = null;
                        }
                    });
                }, 100);
            });
            
            // Reset placement mode
            placingSensor = false;
            document.getElementById('map').style.cursor = '';
            document.querySelector('.add-sensor-btn').style.backgroundColor = '#0d6efd';
            document.querySelector('.add-sensor-btn').innerHTML = '<i class="fas fa-plus-circle"></i> Add New Sensor Location';
        });
    });
</script>
@endsection

@section('content')
<div class="container">
    <h1>Sensor Management</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Sensor ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sensors as $sensor)
            <tr>
                <td>{{ $sensor->sensor_id }}</td>
                <td>{{ $sensor->name }}</td>
                <td>{{ $sensor->location }}</td>
                <td>{{ $sensor->is_active ? 'Active' : 'Inactive' }}</td>
                <td>
                    <a href="{{ route('admin.sensors.edit', $sensor->id) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('admin.sensors.toggle-active', $sensor->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            {{ $sensor->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection