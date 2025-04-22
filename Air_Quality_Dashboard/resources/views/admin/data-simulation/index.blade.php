@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Data Simulation Management</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Data Simulation</li>
    </ol>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cogs me-1"></i>
                    Simulation Settings
                </div>
                <div class="card-body">
                    @if($simulationActive)
                    <div class="alert alert-info">
                        <i class="fas fa-sync fa-spin me-2"></i> Simulation is currently active. Data is being generated every {{ $simulationInterval }} minutes with {{ $simulationVariation }}% variation.
                    </div>
                    <form action="{{ route('admin.data-simulation.stop') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-stop me-1"></i> Stop Simulation
                        </button>
                    </form>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-pause me-2"></i> Simulation is currently inactive. Configure and start the simulation below.
                    </div>
                    <form action="{{ route('admin.data-simulation.start') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="interval" class="form-label">Data Generation Interval (minutes)</label>
                            <input type="number" class="form-control" id="interval" name="interval" value="{{ $simulationInterval }}" min="5" max="60" required>
                            <div class="form-text">How often should new data be generated? (5-60 minutes)</div>
                        </div>
                        <div class="mb-3">
                            <label for="variation" class="form-label">Data Variation Percentage</label>
                            <input type="number" class="form-control" id="variation" name="variation" value="{{ $simulationVariation }}" min="1" max="50" required>
                            <div class="form-text">How much should readings vary from previous values? (1-50%)</div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-play me-1"></i> Start Simulation
                        </button>
                    </form>
                    @endif
                    
                    <hr>
                    
                    <form action="{{ route('admin.data-simulation.generate') }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-bolt me-1"></i> Generate One-Time Data
                        </button>
                        <div class="form-text">Generate a single batch of simulated data for all active sensors immediately.</div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Simulation Statistics
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small">Total Sensors</div>
                                            <div class="fs-3">{{ $sensors }}</div>
                                        </div>
                                        <i class="fas fa-map-marker-alt fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small">Total Readings</div>
                                            <div class="fs-3">{{ $readings }}</div>
                                        </div>
                                        <i class="fas fa-database fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h5>Latest Reading</h5>
                        @if($latestReading)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Sensor</th>
                                    <td>{{ $latestReading->sensor->name }}</td>
                                </tr>
                                <tr>
                                    <th>AQI</th>
                                    <td>
                                        <span class="badge 
                                            @if($latestReading->aqi <= 50) bg-success 
                                            @elseif($latestReading->aqi <= 100) bg-warning 
                                            @elseif($latestReading->aqi <= 150) bg-orange 
                                            @else bg-danger @endif">
                                            {{ $latestReading->aqi }}
                                        </span>
                                        {{ $latestReading->aqi_category }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>PM2.5</th>
                                    <td>{{ $latestReading->pm25 }} μg/m³</td>
                                </tr>
                                <tr>
                                    <th>PM10</th>
                                    <td>{{ $latestReading->pm10 }} μg/m³</td>
                                </tr>
                                <tr>
                                    <th>Time</th>
                                    <td>{{ $latestReading->reading_time }}</td>
                                </tr>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-warning">No readings generated yet.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
