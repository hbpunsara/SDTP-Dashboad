@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-3">Historical Air Quality Data</h1>
            <p class="lead">Explore historical air quality trends across Colombo city.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Select Sensor</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="sensor-select">Choose a sensor location:</label>
                        <select class="form-select" id="sensor-select">
                            <option value="">-- Select a sensor --</option>
                            @foreach($sensors ?? [] as $sensor)
                                <option value="{{ $sensor->id }}">{{ $sensor->name }} ({{ $sensor->location }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="timeframe-select">Time period:</label>
                        <select class="form-select" id="timeframe-select">
                            <option value="24">Last 24 Hours</option>
                            <option value="72">Last 3 Days</option>
                            <option value="168">Last Week</option>
                            <option value="720">Last Month</option>
                        </select>
                    </div>

                    <button class="btn btn-primary" id="load-data-btn">Load Data</button>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0" id="chart-title">Historical Data</h5>
                </div>
                <div class="card-body">
                    <div id="historical-chart-container">
                        <canvas id="historical-chart"></canvas>
                        <p class="text-center text-muted mt-3" id="no-data-message">Select a sensor and timeframe to view historical data</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Data Table</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="data-table">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>AQI</th>
                                    <th>Category</th>
                                    <th>PM2.5</th>
                                    <th>PM10</th>
                                    <th>Other Parameters</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">No data to display</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Chart objects
    let historicalChart = null;
    
    // DOM elements
    const sensorSelect = document.getElementById('sensor-select');
    const timeframeSelect = document.getElementById('timeframe-select');
    const loadDataBtn = document.getElementById('load-data-btn');
    const chartTitle = document.getElementById('chart-title');
    const noDataMessage = document.getElementById('no-data-message');
    const dataTable = document.getElementById('data-table').getElementsByTagName('tbody')[0];
    
    // Function to get marker color based on AQI
    function getColor(aqi) {
        if (aqi <= 50) return "#00e400";
        if (aqi <= 100) return "#ffff00";
        if (aqi <= 150) return "#ff7e00";
        if (aqi <= 200) return "#ff0000";
        if (aqi <= 300) return "#99004c";
        return "#7e0023";
    }
    
    // Function to get AQI category
    function getCategory(aqi) {
        if (aqi <= 50) return "Good";
        if (aqi <= 100) return "Moderate";
        if (aqi <= 150) return "Unhealthy for Sensitive Groups";
        if (aqi <= 200) return "Unhealthy";
        if (aqi <= 300) return "Very Unhealthy";
        return "Hazardous";
    }
    
    // Load data from API
    loadDataBtn.addEventListener('click', function() {
        const sensorId = sensorSelect.value;
        const hours = timeframeSelect.value;
        
        if (!sensorId) {
            alert('Please select a sensor');
            return;
        }
        
        // Show loading state
        chartTitle.textContent = 'Loading data...';
        noDataMessage.textContent = 'Loading...';
        
        // Fetch data from API
        fetch(`/api/sensors/${sensorId}/readings?hours=${hours}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    noDataMessage.textContent = 'No data available for the selected timeframe';
                    chartTitle.textContent = 'No Data Available';
                    dataTable.innerHTML = '<tr><td colspan="6" class="text-center">No data to display</td></tr>';
                    return;
                }
                
                // Sort data chronologically
                data.sort((a, b) => new Date(a.reading_time) - new Date(b.reading_time));
                
                // Get selected sensor name
                const sensorName = sensorSelect.options[sensorSelect.selectedIndex].text;
                
                // Update chart title
                chartTitle.textContent = `${sensorName} - Last ${hours} Hours`;
                
                // Process data for chart
                const labels = data.map(item => {
                    const date = new Date(item.reading_time);
                    return date.toLocaleString();
                });
                
                const aqiData = data.map(item => item.aqi);
                const pm25Data = data.map(item => item.pm25);
                const pm10Data = data.map(item => item.pm10);
                
                // Create or update chart
                if (historicalChart) {
                    historicalChart.destroy();
                }
                
                const ctx = document.getElementById('historical-chart').getContext('2d');
                historicalChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'AQI',
                                data: aqiData,
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderWidth: 2,
                                tension: 0.1
                            },
                            {
                                label: 'PM2.5',
                                data: pm25Data,
                                borderColor: 'rgba(54, 162, 235, 1)',
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderWidth: 2,
                                tension: 0.1,
                                hidden: true
                            },
                            {
                                label: 'PM10',
                                data: pm10Data,
                                borderColor: 'rgba(255, 206, 86, 1)',
                                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                                borderWidth: 2,
                                tension: 0.1,
                                hidden: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Air Quality Trends'
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
                                    text: 'Date & Time'
                                }
                            }
                        }
                    }
                });
                
                // Hide no data message
                noDataMessage.style.display = 'none';
                
                // Update data table
                let tableHtml = '';
                data.forEach(item => {
                    const date = new Date(item.reading_time).toLocaleString();
                    tableHtml += `
                        <tr>
                            <td>${date}</td>
                            <td>${item.aqi}</td>
                            <td>${item.aqi_category}</td>
                            <td>${item.pm25} μg/m³</td>
                            <td>${item.pm10} μg/m³</td>
                            <td>
                                O₃: ${item.o3 || 'N/A'}, 
                                NO₂: ${item.no2 || 'N/A'}, 
                                SO₂: ${item.so2 || 'N/A'}, 
                                CO: ${item.co || 'N/A'}
                            </td>
                        </tr>
                    `;
                });
                dataTable.innerHTML = tableHtml;
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                noDataMessage.textContent = 'Error loading data. Please try again.';
                chartTitle.textContent = 'Error';
            });
    });
</script>
@endsection
