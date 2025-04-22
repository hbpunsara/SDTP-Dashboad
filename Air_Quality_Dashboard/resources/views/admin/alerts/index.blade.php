@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Alert Configuration</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Alert Configuration</li>
    </ol>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-exclamation-triangle me-1"></i>
                Alert Thresholds
            </div>
            <a href="{{ route('admin.alerts.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Add New Threshold
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">Level</th>
                            <th width="15%">Name</th>
                            <th width="20%">Range</th>
                            <th width="30%">Description</th>
                            <th width="10%">Status</th>
                            <th width="10%">Notifications</th>
                            <th width="10%" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($thresholds as $threshold)
                        <tr>
                            <td>
                                <div class="aqi-badge" style="background-color: {{ $threshold->color }}; color: {{ in_array($threshold->color, ['#FFFF00', '#00E400']) ? '#000' : '#fff' }};">
                                    {{ $threshold->level_name }}
                                </div>
                            </td>
                            <td>{{ $threshold->name }}</td>
                            <td>{{ $threshold->min_value }} - {{ $threshold->max_value }}</td>
                            <td>{{ $threshold->description }}</td>
                            <td>
                                @if($threshold->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.alerts.toggle-notification', $threshold->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $threshold->send_notification ? 'btn-success' : 'btn-outline-secondary' }}">
                                        <i class="fas {{ $threshold->send_notification ? 'fa-bell' : 'fa-bell-slash' }}"></i>
                                        {{ $threshold->send_notification ? 'Enabled' : 'Disabled' }}
                                    </button>
                                </form>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.alerts.edit', $threshold->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                @if($threshold->id > 6) <!-- Prevent deletion of default EPA thresholds -->
                                <form action="{{ route('admin.alerts.destroy', $threshold->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this threshold?');">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">No alert thresholds defined yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i>
            About Air Quality Index (AQI)
        </div>
        <div class="card-body">
            <p>The Air Quality Index (AQI) is used for reporting daily air quality. It tells you how clean or polluted your air is, and what associated health effects might be a concern for you.</p>
            
            <p>The AQI scale ranges from 0 to 500. The higher the AQI value, the greater the level of air pollution and the greater the health concern:</p>
            
            <ul>
                <li><strong>0-50 (Good)</strong>: Air quality is considered satisfactory, and air pollution poses little or no risk.</li>
                <li><strong>51-100 (Moderate)</strong>: Air quality is acceptable; however, for some pollutants there may be a moderate health concern for a very small number of people.</li>
                <li><strong>101-150 (Unhealthy for Sensitive Groups)</strong>: Members of sensitive groups may experience health effects. The general public is not likely to be affected.</li>
                <li><strong>151-200 (Unhealthy)</strong>: Everyone may begin to experience health effects; members of sensitive groups may experience more serious health effects.</li>
                <li><strong>201-300 (Very Unhealthy)</strong>: Health warnings of emergency conditions. The entire population is more likely to be affected.</li>
                <li><strong>301-500 (Hazardous)</strong>: Health alert: everyone may experience more serious health effects.</li>
            </ul>
            
            <p class="mb-0"><small class="text-muted">Source: U.S. Environmental Protection Agency (EPA)</small></p>
        </div>
    </div>
</div>

<style>
    .aqi-badge {
        padding: 6px 8px;
        border-radius: 4px;
        font-weight: bold;
        text-align: center;
        display: inline-block;
        min-width: 80px;
    }
</style>
@endsection
