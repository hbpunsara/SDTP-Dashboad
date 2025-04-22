@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3">Admin Dashboard</h1>
            <p class="text-muted">Monitor and manage the Air Quality system</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('admin.sensors.index') }}" class="btn btn-primary">
                    <i class="fas fa-map-marker-alt me-1"></i> Manage Sensors
                </a>
                <a href="{{ route('admin.data-simulation') }}" class="btn btn-outline-primary">
                    <i class="fas fa-chart-line me-1"></i> Data Simulation
                </a>
            </div>
        </div>
    </div>

    <!-- Status Cards Section -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-4 text-primary mb-2">{{ $sensorStats['total'] }}</div>
                    <h5 class="text-muted">Total Sensors</h5>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between text-muted mb-1">
                            <span>Active: {{ $sensorStats['active'] }}</span>
                            <span>Inactive: {{ $sensorStats['inactive'] }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ ($sensorStats['active'] / $sensorStats['total']) * 100 }}%" 
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <a href="{{ route('admin.sensors.index') }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                        <span>View All Sensors</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="display-4 text-danger mb-2">{{ $sensorStats['critical_alerts'] }}</div>
                    <h5 class="text-muted">Critical Alerts</h5>
                    <div class="mt-3">
                        <div class="alert alert-danger py-2">
                            <small><i class="fas fa-exclamation-triangle me-1"></i> Requires attention</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <a href="#" class="text-decoration-none d-flex justify-content-between align-items-center">
                        <span>View Alerts</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-wrapper mb-3">
                        <i class="fas fa-server text-info fa-2x"></i>
                    </div>
                    <h5 class="text-muted">System Status</h5>
                    <div class="mt-3">
                        <span class="badge bg-success px-3 py-2">{{ $systemStats['system_status'] }}</span>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="text-decoration-none d-flex justify-content-between align-items-center">
                        <span>Uptime: {{ $systemStats['system_uptime'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="icon-wrapper mb-3">
                        <i class="fas fa-database text-success fa-2x"></i>
                    </div>
                    <h5 class="text-muted">Data Points</h5>
                    <div class="mt-3">
                        <h3>{{ $systemStats['data_points_collected'] }}</h3>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="text-decoration-none d-flex justify-content-between align-items-center">
                        <span>Last Sync: {{ $systemStats['last_data_sync'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Readings Section -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Air Quality Readings</h5>
                        <a href="#" class="btn btn-sm btn-outline-secondary">View All</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
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

        <!-- Quick Actions Section -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.sensors.create') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <div class="me-3 bg-primary text-white rounded p-2">
                                <i class="fas fa-plus"></i>
                            </div>
                            Add New Sensor
                        </a>
                        <a href="{{ route('admin.data-simulation') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                            <div class="me-3 bg-success text-white rounded p-2">
                                <i class="fas fa-play"></i>
                            </div>
                            Start Data Simulation
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <div class="me-3 bg-info text-white rounded p-2">
                                <i class="fas fa-download"></i>
                            </div>
                            Download Reports
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <div class="me-3 bg-warning text-white rounded p-2">
                                <i class="fas fa-cog"></i>
                            </div>
                            System Settings
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Latest Activity</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item py-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="bg-primary text-white rounded-circle p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-0">New sensor added: {{ $sensorStats['latest_added'] }}</p>
                                    <small class="text-muted">Today at 09:45 AM</small>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item py-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="bg-warning text-white rounded-circle p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-0">Alert triggered at Pettah Market</p>
                                    <small class="text-muted">Yesterday at 4:23 PM</small>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item py-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <div class="bg-success text-white rounded-circle p-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-sync"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="mb-0">System maintenance completed</p>
                                    <small class="text-muted">April 20, 2025</small>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

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