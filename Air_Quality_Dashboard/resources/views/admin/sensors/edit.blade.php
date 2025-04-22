@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Sensor</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.sensors.index') }}">Sensors</a></li>
        <li class="breadcrumb-item active">Edit Sensor - {{ $sensor->sensor_id }}</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-map-marker-alt me-1"></i>
            Sensor Details
        </div>
        <div class="card-body">
            <form action="{{ route('admin.sensors.update', $sensor->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="sensor_id" class="form-label">Sensor ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sensor_id') is-invalid @enderror" 
                                   id="sensor_id" name="sensor_id" value="{{ old('sensor_id', $sensor->sensor_id) }}" required>
                            @error('sensor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Must be unique. Example: SN-COL-001</div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Sensor Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $sensor->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location', $sensor->location) }}" required>
                            @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Descriptive location name (e.g., "Fort Railway Station")</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude', $sensor->latitude) }}" required readonly>
                                    @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude', $sensor->longitude) }}" required readonly>
                                    @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-text mt-1">Click on the map to update coordinates</div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $sensor->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                  {{ $sensor->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                            <div class="form-text">Toggle to activate or deactivate this sensor</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Sensor Location (Click to update position) <span class="text-danger">*</span></label>
                        <div id="sensorEditMap" style="height: 400px; width: 100%;" class="mb-3"></div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-1"></i> Click anywhere on the map to update the sensor location. The map is bounded to Colombo area.
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.sensors.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Sensor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize sensor coordinates from existing data
        var sensorLat = {{ $sensor->latitude }};
        var sensorLng = {{ $sensor->longitude }};
        
        // Initialize the map centered on the sensor location
        var map = L.map('sensorEditMap').setView([sensorLat, sensorLng], 13);
        
        // Add the OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
        }).addTo(map);
        
        // Set bounds to Colombo area (approximately)
        var colomboBounds = L.latLngBounds(
            L.latLng(6.85, 79.75), // Southwest corner
            L.latLng(7.00, 79.95)  // Northeast corner
        );
        map.setMaxBounds(colomboBounds);
        map.on('drag', function() {
            map.panInsideBounds(colomboBounds, { animate: false });
        });
        
        // Create a marker at current sensor position
        var marker = L.marker([sensorLat, sensorLng], {
            draggable: true
        }).addTo(map);
        
        // Update marker position and form fields when map is clicked
        map.on('click', function(e) {
            // Move marker to clicked position
            marker.setLatLng(e.latlng);
            
            // Update form fields
            document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
            document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
        });
        
        // Update form fields when marker is dragged
        marker.on('dragend', function() {
            var position = marker.getLatLng();
            document.getElementById('latitude').value = position.lat.toFixed(6);
            document.getElementById('longitude').value = position.lng.toFixed(6);
        });
    });
</script>
@endsection
