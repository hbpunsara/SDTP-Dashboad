<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Air Quality Dashboard - Colombo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        body { padding-top: 20px; }
        .map-container { height: 500px; }
        .aqi-good { background-color: #00e400; color: black; }
        .aqi-moderate { background-color: #ffff00; color: black; }
        .aqi-unhealthy-sensitive { background-color: #ff7e00; color: black; }
        .aqi-unhealthy { background-color: #ff0000; color: white; }
        .aqi-very-unhealthy { background-color: #99004c; color: white; }
        .aqi-hazardous { background-color: #7e0023; color: white; }
        
        .legend-box {
            display: inline-block;
            width: 20px;
            height: 20px;
            margin-right: 5px;
            border-radius: 4px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mb-3">
            <div class="col-12">
                <h1 class="text-primary">Air Quality Monitoring Dashboard</h1>
                <p class="text-muted">Real-time air quality information for Colombo</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Air Quality Map</h5>
                    </div>
                    <div class="card-body">
                        <!-- Map container -->
                        <div id="map" class="map-container"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">AQI Legend</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <div class="me-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div style="width: 24px; height: 24px; background-color: #00e400; border-radius: 4px;" class="me-2"></div>
                                    <span>Good (0-50)</span>
                                </div>
                            </div>
                            <div class="me-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div style="width: 24px; height: 24px; background-color: #ffff00; border-radius: 4px;" class="me-2"></div>
                                    <span>Moderate (51-100)</span>
                                </div>
                            </div>
                            <div class="me-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div style="width: 24px; height: 24px; background-color: #ff7e00; border-radius: 4px;" class="me-2"></div>
                                    <span>Unhealthy for Sensitive Groups (101-150)</span>
                                </div>
                            </div>
                            <div class="me-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div style="width: 24px; height: 24px; background-color: #ff0000; border-radius: 4px;" class="me-2"></div>
                                    <span>Unhealthy (151-200)</span>
                                </div>
                            </div>
                            <div class="me-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div style="width: 24px; height: 24px; background-color: #99004c; border-radius: 4px;" class="me-2"></div>
                                    <span>Very Unhealthy (201-300)</span>
                                </div>
                            </div>
                            <div class="me-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div style="width: 24px; height: 24px; background-color: #7e0023; border-radius: 4px;" class="me-2"></div>
                                    <span>Hazardous (301+)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Selected Sensor Data</h5>
                    </div>
                    <div class="card-body" id="selected-sensor-data">
                        <div class="text-center py-4">
                            <i class="fas fa-map-marker-alt" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                            <h5>No Sensor Selected</h5>
                            <p class="text-muted">Click on a sensor marker on the map to view air quality data</p>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize map
        const map = L.map('map').setView([6.9271, 79.8612], 12); // Colombo coordinates
        
        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Sample data - similar to the image with location for Pamtipitiya Road, Pelawatte
        const sensorData = [
            { id: 1, name: "Pamtipitiya Road", location: "Pelawatte, Sri Lanka", lat: 6.8723, lng: 79.9001, aqi: 69, category: "Moderate", pm25: 21, pm10: 35 },
            { id: 2, name: "Colombo Fort", location: "Colombo", lat: 6.9271, lng: 79.8612, aqi: 53, category: "Moderate", pm25: 16, pm10: 28 },
            { id: 3, name: "Battaramulla", location: "Colombo", lat: 6.8965, lng: 79.9182, aqi: 112, category: "Unhealthy for Sensitive Groups", pm25: 38, pm10: 56 },
            { id: 4, name: "Borella", location: "Colombo", lat: 6.9103, lng: 79.8780, aqi: 65, category: "Moderate", pm25: 19, pm10: 31 }
        ];
        
        // Function to determine marker color based on AQI
        function getMarkerColor(aqi) {
            if (aqi <= 50) return "#00e400";
            if (aqi <= 100) return "#ffff00";
            if (aqi <= 150) return "#ff7e00";
            if (aqi <= 200) return "#ff0000";
            if (aqi <= 300) return "#99004c";
            return "#7e0023";
        }
        
        // Function to get emoji based on AQI category
        function getCategoryEmoji(category) {
            switch(category) {
                case "Good": return "😊";
                case "Moderate": return "😊";
                case "Unhealthy for Sensitive Groups": return "😐";
                case "Unhealthy": return "😷";
                case "Very Unhealthy": return "🤢";
                case "Hazardous": return "☠️";
                default: return "😊";
            }
        }
        
        // Function to determine progress bar class based on value
        function getProgressBarClass(value) {
            if (value <= 50) return "bg-success";
            if (value <= 100) return "bg-warning";
            if (value <= 150) return "bg-orange" // custom color
            if (value <= 200) return "bg-danger";
            return "bg-dark";
        }
        
        // Function to show selected sensor details
        function showSensorDetails(sensorId) {
            const sensor = sensorData.find(s => s.id === sensorId);
            if (!sensor) return;
            
            const sensorDataDiv = document.getElementById('selected-sensor-data');
            
            // Get appropriate AQI class for styling
            let aqiClass = "";
            if (sensor.aqi <= 50) aqiClass = "aqi-good";
            else if (sensor.aqi <= 100) aqiClass = "aqi-moderate";
            else if (sensor.aqi <= 150) aqiClass = "aqi-unhealthy-sensitive";
            else if (sensor.aqi <= 200) aqiClass = "aqi-unhealthy";
            else if (sensor.aqi <= 300) aqiClass = "aqi-very-unhealthy";
            else aqiClass = "aqi-hazardous";
            
            // Calculate progress percentages (max AQI is 500)
            const pm25Percent = Math.min(100, (sensor.pm25 / 250) * 100);
            const pm10Percent = Math.min(100, (sensor.pm10 / 430) * 100);
            
            // Update the sensor data display
            sensorDataDiv.innerHTML = `
                <div class="card">
                    <div class="card-body ${aqiClass}">
                        <h5>${sensor.name}, ${sensor.location}</h5>
                        <div class="d-flex align-items-center mb-3">
                            <div class="fs-1 me-3">${getCategoryEmoji(sensor.category)}</div>
                            <div>
                                <div class="fs-2 fw-bold">${sensor.aqi} - ${sensor.category}</div>
                                <div class="text-muted">updated 2 minutes ago</div>
                            </div>
                        </div>
                        
                        <h6>PM<sub>2.5</sub></h6>
                        <div class="progress mb-3">
                            <div class="progress-bar ${getProgressBarClass(sensor.pm25)}" style="width: ${pm25Percent}%"></div>
                        </div>
                        
                        <h6>PM<sub>10</sub></h6>
                        <div class="progress mb-3">
                            <div class="progress-bar ${getProgressBarClass(sensor.pm10)}" style="width: ${pm10Percent}%"></div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <button class="btn btn-sm btn-primary" onclick="showDetailedInfo(${sensor.id})">Click for more information</button>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <canvas id="sensor-chart"></canvas>
                </div>
            `;
            
            // Generate sample historical data for a nice chart
            const hours = 12;
            const labels = Array.from({length: hours}, (_, i) => `${hours-i} hour${i > 0 ? 's' : ''} ago`);
            labels.push('Now');
            
            // Generate some semi-random historical data based on current AQI
            const historicalData = [];
            for (let i = 0; i < hours; i++) {
                // Random variation of ±15%
                const variation = 0.85 + (Math.random() * 0.3);
                historicalData.push(Math.round(sensor.aqi * variation));
            }
            historicalData.push(sensor.aqi); // Current value
            
            // Create a chart if the element exists
            const chartElement = document.getElementById('sensor-chart');
            if (chartElement) {
                new Chart(chartElement, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'AQI Trend',
                            data: historicalData,
                            fill: false,
                            borderColor: getMarkerColor(sensor.aqi),
                            tension: 0.1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'AQI Value'
                                }
                            }
                        }
                    }
                });
            }
        }
        
        // Add markers for each sensor
        sensorData.forEach(sensor => {
            // Create custom icon with AQI color
            const markerHtml = `<div style="background-color: ${getMarkerColor(sensor.aqi)}; color: black; font-weight: bold; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white;">${sensor.aqi}</div>`;
            
            const icon = L.divIcon({
                html: markerHtml,
                className: 'sensor-marker',
                iconSize: [30, 30]
            });
            
            // Add marker to map
            const marker = L.marker([sensor.lat, sensor.lng], { icon: icon }).addTo(map);
            
            // Add custom popup with improved styling
            const popupContent = `
                <div style="min-width: 200px;">
                    <div style="background-color: ${getMarkerColor(sensor.aqi)}; color: ${sensor.aqi <= 150 ? 'black' : 'white'}; padding: 8px; border-radius: 4px 4px 0 0;">
                        <div style="font-size: 16px; font-weight: bold;">${sensor.name}</div>
                        <div>${sensor.location || ''}</div>
                    </div>
                    <div style="padding: 10px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 4px 4px;">
                        <div style="display: flex; align-items: center; margin-bottom: 8px;">
                            <div style="font-size: 20px; margin-right: 8px;">${getCategoryEmoji(sensor.category)}</div>
                            <div>
                                <div style="font-size: 18px; font-weight: bold;">${sensor.aqi}</div>
                                <div style="font-size: 14px;">${sensor.category}</div>
                            </div>
                        </div>
                        <div style="margin-bottom: 5px;">
                            <div style="font-size: 13px; color: #666;">PM<sub>2.5</sub>: ${sensor.pm25} μg/m³</div>
                            <div style="font-size: 13px; color: #666;">PM<sub>10</sub>: ${sensor.pm10} μg/m³</div>
                        </div>
                        <div style="text-align: center; margin-top: 10px;">
                            <button onclick="showDetailedInfo(${sensor.id}); return false;" 
                                style="background-color: ${getMarkerColor(sensor.aqi)}; 
                                       color: ${sensor.aqi <= 150 ? 'black' : 'white'}; 
                                       border: none; 
                                       padding: 5px 10px; 
                                       border-radius: 4px; 
                                       cursor: pointer;">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Bind the custom popup
            marker.bindPopup(popupContent, {
                maxWidth: 300,
                className: 'custom-popup'
            });
            
            // Also add click handler to marker itself
            marker.on('click', function() {
                showSensorDetails(sensor.id);
            });
        });
        
        // No default sensor selection - user must click on a marker
    </script>
    <!-- Detailed Information Modal -->
    <div class="modal fade" id="detailedInfoModal" tabindex="-1" aria-labelledby="detailedInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" id="modal-header">
                    <h5 class="modal-title" id="detailedInfoModalLabel">Detailed Air Quality Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body">
                    <!-- Content will be dynamically inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to show detailed information in a modal
        function showDetailedInfo(sensorId) {
            const sensor = sensorData.find(s => s.id === sensorId);
            if (!sensor) return;
            
            // Get modal elements
            const modalHeader = document.getElementById('modal-header');
            const modalTitle = document.getElementById('detailedInfoModalLabel');
            const modalBody = document.getElementById('modal-body');
            
            // Get appropriate AQI class for styling
            let aqiClass = "";
            if (sensor.aqi <= 50) aqiClass = "aqi-good";
            else if (sensor.aqi <= 100) aqiClass = "aqi-moderate";
            else if (sensor.aqi <= 150) aqiClass = "aqi-unhealthy-sensitive";
            else if (sensor.aqi <= 200) aqiClass = "aqi-unhealthy";
            else if (sensor.aqi <= 300) aqiClass = "aqi-very-unhealthy";
            else aqiClass = "aqi-hazardous";
            
            // Style the modal header
            modalHeader.className = `modal-header ${aqiClass}`;
            
            // Set modal title
            modalTitle.textContent = `${sensor.name}, ${sensor.location}`;
            
            // Generate semi-random hourly data for detailed view
            const hourlyData = [];
            for (let i = 0; i < 24; i++) {
                const variation = 0.8 + (Math.random() * 0.4); // Random variation of ±20%
                const hourAqi = Math.round(sensor.aqi * variation);
                const pm25 = Math.round(sensor.pm25 * variation);
                const pm10 = Math.round(sensor.pm10 * variation);
                const hour = (new Date().getHours() - 24 + i + 24) % 24;
                hourlyData.push({
                    hour: `${hour}:00`,
                    aqi: hourAqi,
                    pm25: pm25,
                    pm10: pm10,
                    o3: Math.round(Math.random() * 100) / 10,
                    no2: Math.round(Math.random() * 80) / 10,
                    so2: Math.round(Math.random() * 40) / 10,
                    co: Math.round(Math.random() * 20) / 10
                });
            }
            
            // Calculate average, minimum, and maximum values
            const avgAqi = Math.round(hourlyData.reduce((sum, item) => sum + item.aqi, 0) / hourlyData.length);
            const minAqi = Math.min(...hourlyData.map(item => item.aqi));
            const maxAqi = Math.max(...hourlyData.map(item => item.aqi));
            
            // Get current AQI category description with health implications
            let categoryDescription = "";
            let healthImplications = "";
            
            if (sensor.aqi <= 50) {
                categoryDescription = "Good";
                healthImplications = "Air quality is considered satisfactory, and air pollution poses little or no risk.";
            } else if (sensor.aqi <= 100) {
                categoryDescription = "Moderate";
                healthImplications = "Air quality is acceptable; however, there may be a moderate health concern for a small number of people who are unusually sensitive to air pollution.";
            } else if (sensor.aqi <= 150) {
                categoryDescription = "Unhealthy for Sensitive Groups";
                healthImplications = "Members of sensitive groups may experience health effects. The general public is not likely to be affected.";
            } else if (sensor.aqi <= 200) {
                categoryDescription = "Unhealthy";
                healthImplications = "Everyone may begin to experience health effects; members of sensitive groups may experience more serious health effects.";
            } else if (sensor.aqi <= 300) {
                categoryDescription = "Very Unhealthy";
                healthImplications = "Health warnings of emergency conditions. The entire population is more likely to be affected.";
            } else {
                categoryDescription = "Hazardous";
                healthImplications = "Health alert: everyone may experience more serious health effects.";
            }
            
            // Set modal content
            modalBody.innerHTML = `
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Current Status</h5>
                            </div>
                            <div class="card-body ${aqiClass}">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="display-3 me-3">${getCategoryEmoji(sensor.category)}</div>
                                    <div>
                                        <div class="display-5 fw-bold">${sensor.aqi}</div>
                                        <div class="fs-4">${sensor.category}</div>
                                        <div class="text-muted">Updated 2 minutes ago</div>
                                    </div>
                                </div>
                                <p class="mt-3">${healthImplications}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>24 Hour Summary</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <th>Average AQI</th>
                                            <td>${avgAqi}</td>
                                        </tr>
                                        <tr>
                                            <th>Minimum AQI</th>
                                            <td>${minAqi}</td>
                                        </tr>
                                        <tr>
                                            <th>Maximum AQI</th>
                                            <td>${maxAqi}</td>
                                        </tr>
                                        <tr>
                                            <th>Current PM<sub>2.5</sub></th>
                                            <td>${sensor.pm25} μg/m³</td>
                                        </tr>
                                        <tr>
                                            <th>Current PM<sub>10</sub></th>
                                            <td>${sensor.pm10} μg/m³</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>24 Hour Trends</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="detailed-chart" height="250"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Hourly Data</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Hour</th>
                                                <th>AQI</th>
                                                <th>PM<sub>2.5</sub></th>
                                                <th>PM<sub>10</sub></th>
                                                <th>O<sub>3</sub></th>
                                                <th>NO<sub>2</sub></th>
                                                <th>SO<sub>2</sub></th>
                                                <th>CO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${hourlyData.map(hour => `
                                                <tr>
                                                    <td>${hour.hour}</td>
                                                    <td>${hour.aqi}</td>
                                                    <td>${hour.pm25} μg/m³</td>
                                                    <td>${hour.pm10} μg/m³</td>
                                                    <td>${hour.o3} ppb</td>
                                                    <td>${hour.no2} ppb</td>
                                                    <td>${hour.so2} ppb</td>
                                                    <td>${hour.co} ppm</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Health Recommendations</h5>
                            </div>
                            <div class="card-body">
                                <h6>General Population:</h6>
                                <ul>
                                    ${sensor.aqi <= 50 ? '<li>Enjoy outdoor activities</li>' : ''}
                                    ${sensor.aqi > 50 && sensor.aqi <= 100 ? '<li>Unusually sensitive individuals should consider reducing prolonged or heavy exertion.</li>' : ''}
                                    ${sensor.aqi > 100 && sensor.aqi <= 150 ? '<li>People with respiratory or heart disease, the elderly and children should limit prolonged exertion.</li>' : ''}
                                    ${sensor.aqi > 150 && sensor.aqi <= 200 ? '<li>People with respiratory or heart disease, the elderly and children should avoid prolonged exertion; everyone else should limit prolonged exertion.</li>' : ''}
                                    ${sensor.aqi > 200 && sensor.aqi <= 300 ? '<li>People with respiratory or heart disease, the elderly and children should avoid any outdoor activity; everyone else should avoid prolonged exertion.</li>' : ''}
                                    ${sensor.aqi > 300 ? '<li>Everyone should avoid all physical activities outdoors.</li>' : ''}
                                </ul>
                                
                                <h6>Sensitive Groups:</h6>
                                <ul>
                                    ${sensor.aqi <= 50 ? '<li>No special precautions needed</li>' : ''}
                                    ${sensor.aqi > 50 && sensor.aqi <= 100 ? '<li>Consider reducing prolonged or heavy exertion.</li><li>Watch for symptoms such as coughing or shortness of breath.</li>' : ''}
                                    ${sensor.aqi > 100 ? '<li>Avoid prolonged or heavy exertion.</li><li>Move activities indoors or reschedule to a time when the air quality is better.</li>' : ''}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Create detailed chart
            setTimeout(() => {
                const ctx = document.getElementById('detailed-chart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: hourlyData.map(h => h.hour),
                            datasets: [
                                {
                                    label: 'AQI',
                                    data: hourlyData.map(h => h.aqi),
                                    borderColor: getMarkerColor(sensor.aqi),
                                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                    borderWidth: 2,
                                    tension: 0.3,
                                    fill: true
                                },
                                {
                                    label: 'PM2.5',
                                    data: hourlyData.map(h => h.pm25),
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 2,
                                    tension: 0.3,
                                    hidden: true
                                },
                                {
                                    label: 'PM10',
                                    data: hourlyData.map(h => h.pm10),
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 2,
                                    tension: 0.3,
                                    hidden: true
                                },
                                {
                                    label: 'O3',
                                    data: hourlyData.map(h => h.o3),
                                    borderColor: 'rgba(153, 102, 255, 1)',
                                    borderWidth: 2,
                                    tension: 0.3,
                                    hidden: true
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: '24 Hour Air Quality Data'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: false
                                }
                            }
                        }
                    });
                }
            }, 500); // Short delay to ensure the DOM is ready
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('detailedInfoModal'));
            modal.show();
        }
    </script>
</body>
</html>
