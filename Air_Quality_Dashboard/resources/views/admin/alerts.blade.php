@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Alert Configuration</h1>
    <form action="{{ route('admin.alerts.update') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="moderate" class="form-label">Moderate Threshold</label>
            <input type="number" class="form-control" id="moderate" name="moderate" value="{{ $thresholds['moderate'] }}">
        </div>
        <div class="mb-3">
            <label for="unhealthy" class="form-label">Unhealthy Threshold</label>
            <input type="number" class="form-control" id="unhealthy" name="unhealthy" value="{{ $thresholds['unhealthy'] }}">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection