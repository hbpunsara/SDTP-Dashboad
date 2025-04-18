<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Air Quality Monitoring - Colombo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            padding-top: 20px;
            background-color: #f8f9fa;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        /* AQI Category colors */
        .aqi-good { background-color: #00e400; color: #000; }
        .aqi-moderate { background-color: #ffff00; color: #000; }
        .aqi-unhealthy-sensitive { background-color: #ff7e00; color: #000; }
        .aqi-unhealthy { background-color: #ff0000; color: white; }
        .aqi-very-unhealthy { background-color: #99004c; color: white; }
        .aqi-hazardous { background-color: #7e0023; color: white; }
        
        .sensor-marker {
            border-radius: 50%;
            width: 20px;
            height: 20px;
            border: 2px solid white;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="mb-4">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3 rounded shadow-sm">
                <div class="container-fluid">
                    <a class="navbar-brand fw-bold" href="/">
                        <i class="fas fa-wind me-2"></i>Air Quality Monitoring - Colombo
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">
                                    Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('historical-data') ? 'active' : '' }}" href="/historical-data">
                                    Historical Data
                                </a>
                            </li>
                            @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Admin
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="/admin/dashboard">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="/admin/sensors">Manage Sensors</a></li>
                                    <li><a class="dropdown-item" href="/admin/data-simulation">Data Simulation</a></li>
                                </ul>
                            </li>
                            @endauth
                        </ul>
                        <ul class="navbar-nav ms-auto">
                            @guest
                                <li class="nav-item">
                                    <a class="nav-link" href="/login">Admin Login</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <form action="/logout" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-link nav-link">Logout</button>
                                    </form>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
        </header>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>
</html>
