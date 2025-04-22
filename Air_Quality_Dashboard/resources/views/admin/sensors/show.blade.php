@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Sensor Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.sensors.index') }}">Sensors</a></li>
        <li class="breadcrumb-item active">{{ $sensor->name }}</li>
    </ol>

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Sensor Information
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Sensor ID:</strong> {{ $sensor->sensor_id }}
                    </div>
                    <div class="mb-3">
                        <strong>Name:</strong> {{ $sensor->name }}
                    </div>
                    <div class="mb-3">
                        <strong>Location:</strong> {{ $sensor->location }}
                    </div>
                    <div class="mb-3">
                        <strong>Coordinates:</strong> {{ $sensor->latitude }}, {{ $sensor->longitude }}
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong>
                        @if($sensor->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-danger">Inactive</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Description:</strong> {{ $sensor->description ?? 'No description provided' }}
                    </div>
                    <div class="mb-4">
                        <strong>Added on:</strong> {{ $sensor->created_at->format('M d, Y') }}
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.sensors.edit', $sensor->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit Sensor
                        </a>
                        <form action="{{ route('admin.sensors.toggle-status', $sensor->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $sensor->is_active ? 'warning' : 'success' }}">
                                <i class="fas fa-{{ $sensor->is_active ? 'pause' : 'play' }} me-1"></i>
                                {{ $sensor->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-map-marked-alt me-1"></i>
                    Sensor Location
                </div>
                <div class="card-body">
                    <div id="sensorMap" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-line me-1"></i>
                    Recent Air Quality Readings
                </div>
                <div class="card-body">
                    @if($sensor->airQualityReadings->count() > 0)
                        <div style="height: 300px;">
                            <canvas id="airQualityChart"></canvas>
                        </div>
                        
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>AQI</th>
                                        <th>Category</th>
                                        <th>PM2.5</th>
                                        <th>PM10</th>
                                        <th>O3</th>
                                        <th>NO2</th>
                                        <th>SO2</th>
                                        <th>CO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sensor->airQualityReadings->sortByDesc('reading_time') as $reading)
                                    <tr>
                                        <td>{{ $reading->reading_time->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($reading->aqi <= 50) bg-success 
                                                @elseif($reading->aqi <= 100) bg-warning 
                                                @elseif($reading->aqi <= 150) bg-orange 
                                                @elseif($reading->aqi <= 200) bg-danger
                                                @elseif($reading->aqi <= 300) bg-purple
                                                @else bg-dark @endif"
                                                style="@if($reading->aqi > 100 && $reading->aqi <= 150) background-color: #FF9800; @elseif($reading->aqi > 200 && $reading->aqi <= 300) background-color: #9C27B0; @endif"
                                            >
                                                {{ $reading->aqi }}
                                            </span>
                                        </td>
                                        <td>{{ $reading->aqi_category }}</td>
                                        <td>{{ $reading->pm25 }}</td>
                                        <td>{{ $reading->pm10 }}</td>
                                        <td>{{ $reading->o3 }}</td>
                                        <td>{{ $reading->no2 }}</td>
                                        <td>{{ $reading->so2 }}</td>
                                        <td>{{ $reading->co }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No air quality readings available for this sensor yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

<script>
    // Initialize the map
    const sensorMap = L.map('sensorMap').setView([{{ $sensor->latitude }}, {{ $sensor->longitude }}], 14);

    // Add tile layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(sensorMap);
    
    // Add marker for this sensor
    const sensorIcon = L.divIcon({
        className: 'custom-marker',
        html: `<div style="background-color: {{ $sensor->is_active ? 'green' : 'red' }}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white;"></div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });
    
    const marker = L.marker([{{ $sensor->latitude }}, {{ $sensor->longitude }}], { icon: sensorIcon }).addTo(sensorMap);
    marker.bindPopup("<strong>{{ $sensor->name }}</strong><br>{{ $sensor->location }}");
    
    @if($sensor->airQualityReadings->count() > 0)
    // Initialize air quality chart
    const ctx = document.getElementById('airQualityChart').getContext('2d');
    const airQualityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                @foreach($sensor->airQualityReadings->sortBy('reading_time') as $reading)
                    "{{ $reading->reading_time->format('H:i') }}",
                @endforeach
            ],
            datasets: [{
                label: 'AQI',
                data: [
                    @foreach($sensor->airQualityReadings->sortBy('reading_time') as $reading)
                        {{ $reading->aqi }},
                    @endforeach
                ],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                tension: 0.2
            },
            {
                label: 'PM2.5',
                data: [
                    @foreach($sensor->airQualityReadings->sortBy('reading_time') as $reading)
                        {{ $reading->pm25 }},
                    @endforeach
                ],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                tension: 0.2
            },
            {
                label: 'PM10',
                data: [
                    @foreach($sensor->airQualityReadings->sortBy('reading_time') as $reading)
                        {{ $reading->pm10 }},
                    @endforeach
                ],
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 2,
                tension: 0.2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Value'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Time'
                    }
                }
            }
        }
    });
    @endif
</script>
@endsection
