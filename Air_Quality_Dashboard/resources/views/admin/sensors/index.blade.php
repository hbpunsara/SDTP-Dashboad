@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">{{ isset($filter) && $filter === 'critical' ? 'Critical Air Quality Sensors' : 'Sensor Management' }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Sensors</li>
    </ol>

    @if(isset($filter) && $filter === 'critical')
    <div class="alert alert-danger mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i> Showing sensors with critical air quality readings (AQI > 100).
        <a href="{{ route('admin.sensors.index') }}" class="alert-link ms-2">View all sensors</a>
    </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ isset($filter) && $filter === 'critical' ? 'Critical Air Quality Sensors' : 'Registered Sensors' }}
                        <span class="badge bg-primary ms-2">{{ $sensors->count() }}</span>
                    </div>
                    <a href="{{ route('admin.sensors.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle me-1"></i> Register New Sensor
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" id="sensor-search" class="form-control" placeholder="Search sensors...">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Filter <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item filter-option" href="{{ route('admin.sensors.index') }}">All Sensors</a></li>
                                    <li><a class="dropdown-item filter-option" href="#" data-filter="active">Active Only</a></li>
                                    <li><a class="dropdown-item filter-option" href="#" data-filter="inactive">Inactive Only</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item filter-option {{ isset($filter) && $filter === 'critical' ? 'active' : '' }}" href="{{ route('admin.sensors.index', ['filter' => 'critical']) }}">Critical Alerts</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
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
                                        <small>
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
                                        <a href="{{ route('admin.sensors.show', $sensor->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.sensors.edit', $sensor->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.sensors.toggle-status', $sensor->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-{{ $sensor->is_active ? 'warning' : 'success' }}">
                                                <i class="fas fa-{{ $sensor->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.sensors.destroy', $sensor->id) }}" method="POST" class="d-inline delete-sensor-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-map-marker-alt fa-2x mb-3 d-block"></i>
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
        </div>
    </div>
    
    <!-- Sensor Map Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-map-marked-alt me-1"></i>
                    Sensor Location Map
                </div>
                <div class="card-body">
                    <div id="sensorsMap" style="height: 500px; width: 100%;"></div>
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
        const map = L.map('sensorsMap').setView([6.9271, 79.8612], 12); // Colombo coordinates

        // Add tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add sensors to the map
        const sensors = [
            @foreach($sensors as $sensor)
            {
                id: {{ $sensor->id }},
                sensor_id: "{{ $sensor->sensor_id }}",
                name: "{{ $sensor->name }}",
                location: "{{ $sensor->location }}",
                lat: {{ $sensor->latitude }},
                lng: {{ $sensor->longitude }},
                is_active: {{ $sensor->is_active ? 'true' : 'false' }}
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ];
        
        // Function to get marker color based on sensor status
        function getMarkerColor(isActive) {
            return isActive ? "#4CAF50" : "#9E9E9E";
        }
        
        // Add markers for each sensor
        sensors.forEach(sensor => {
            const markerColor = getMarkerColor(sensor.is_active);
            
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
                <div class="text-center">
                    <strong>${sensor.name || sensor.location}</strong><br>
                    <small class="text-muted">${sensor.sensor_id}</small><br>
                    <div class="mt-2">
                        <a href="/admin/sensors/${sensor.id}" class="btn btn-sm btn-info">View</a>
                        <a href="/admin/sensors/${sensor.id}/edit" class="btn btn-sm btn-primary">Edit</a>
                    </div>
                </div>
            `);
        });
        
        // Setup sensor delete confirmation
        document.querySelectorAll('.delete-sensor-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const sensorId = this.action.split('/').pop();
                const row = this.closest('tr');
                const sensorName = row.querySelector('td:first-child').textContent.trim();
                
                if (confirm(`Are you sure you want to delete the sensor "${sensorName}"? This action cannot be undone.`)) {
                    this.submit();
                }
            });
        });

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
    });
</script>
@endsection