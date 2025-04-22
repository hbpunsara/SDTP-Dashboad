@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Data Simulation</h1>
    <form action="{{ route('admin.data-simulation.start') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success">Start Simulation</button>
    </form>
    <form action="{{ route('admin.data-simulation.stop') }}" method="POST" class="mt-3">
        @csrf
        <button type="submit" class="btn btn-danger">Stop Simulation</button>
    </form>
</div>
@endsection