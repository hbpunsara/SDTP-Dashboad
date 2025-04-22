@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Alert Threshold</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.alerts.index') }}">Alert Configuration</a></li>
        <li class="breadcrumb-item active">Edit Threshold</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Edit Alert Threshold
        </div>
        <div class="card-body">
            <form action="{{ route('admin.alerts.update', $threshold->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Threshold Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $threshold->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Example: "Moderate Air Quality"</div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="level_name" class="form-label">Level Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('level_name') is-invalid @enderror" 
                                   id="level_name" name="level_name" value="{{ old('level_name', $threshold->level_name) }}" required>
                            @error('level_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Short display name, e.g., "Moderate"</div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_value" class="form-label">Minimum AQI <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('min_value') is-invalid @enderror" 
                                           id="min_value" name="min_value" value="{{ old('min_value', $threshold->min_value) }}" required min="0">
                                    @error('min_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_value" class="form-label">Maximum AQI <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('max_value') is-invalid @enderror" 
                                           id="max_value" name="max_value" value="{{ old('max_value', $threshold->max_value) }}" required min="0">
                                    @error('max_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="color" class="form-label">Color <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color-picker" value="{{ old('color', $threshold->color) }}">
                                <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                       id="color" name="color" value="{{ old('color', $threshold->color) }}" required>
                            </div>
                            @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Hex color code for this threshold level</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $threshold->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Health implications and cautionary statements</div>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                  {{ $threshold->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                            <div class="form-text">Only active thresholds are used for alerts</div>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="send_notification" name="send_notification" value="1"
                                  {{ $threshold->send_notification ? 'checked' : '' }}>
                            <label class="form-check-label" for="send_notification">Send Notifications</label>
                            <div class="form-text">Enable notifications when AQI enters this range</div>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Preview:</strong>
                            <div class="mt-2 text-center">
                                <div id="preview-badge" class="py-2 px-3 rounded d-inline-block" style="background-color: {{ $threshold->color }}; color: {{ in_array($threshold->color, ['#FFFF00', '#00E400']) ? 'black' : 'white' }}; font-weight: bold; min-width: 100px;">
                                    {{ $threshold->level_name }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.alerts.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Threshold
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
        const colorPicker = document.getElementById('color-picker');
        const colorInput = document.getElementById('color');
        const previewBadge = document.getElementById('preview-badge');
        const levelNameInput = document.getElementById('level_name');
        
        // Update color input when picker changes
        colorPicker.addEventListener('input', function() {
            colorInput.value = this.value;
            previewBadge.style.backgroundColor = this.value;
            
            // Set text color based on background brightness
            const rgb = hexToRgb(this.value);
            const brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
            previewBadge.style.color = brightness > 125 ? 'black' : 'white';
        });
        
        // Update picker when input changes
        colorInput.addEventListener('input', function() {
            if (isValidHexColor(this.value)) {
                colorPicker.value = this.value;
                previewBadge.style.backgroundColor = this.value;
                
                // Set text color based on background brightness
                const rgb = hexToRgb(this.value);
                const brightness = (rgb.r * 299 + rgb.g * 587 + rgb.b * 114) / 1000;
                previewBadge.style.color = brightness > 125 ? 'black' : 'white';
            }
        });
        
        // Update preview level name
        levelNameInput.addEventListener('input', function() {
            previewBadge.textContent = this.value || 'Level Name';
        });
        
        // Helper functions
        function hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : { r: 0, g: 0, b: 0 };
        }
        
        function isValidHexColor(hex) {
            return /^#[0-9A-F]{6}$/i.test(hex);
        }
    });
</script>
@endsection
