/**
 * Dashboard JavaScript for dynamic data loading
 * Uses JWT authentication for API calls
 */

let dashboardData = {
    overview: null,
    analytics: null
};

// Global variable to store the map instance
let networkMap = null;

// Global variable to store all locations for filtering
let allLocations = [];

// Current location filter state
let currentLocationFilter = 'all';

// Global variable to store the data usage chart instance
let dataUsageChart = null;

/**
 * Load dashboard overview data
 */
function loadDashboardOverview() {
    const token = UserManager.getToken();
    
    if (!token) {
        console.error('No authentication token found');
        return;
    }
    
    // Show loading indicators
    showOverviewLoading(true);
    
    $.ajax({
        url: '/api/dashboard/overview',
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                dashboardData.overview = response.data;
                updateOverviewDisplay(response.data);
                updateLocationCards(response.data.locations.data);
            } else {
                console.error('Failed to load dashboard overview:', response.message);
                showOverviewError();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading dashboard overview:', error);
            showOverviewError();
        },
        complete: function() {
            showOverviewLoading(false);
        }
    });
}

/**
 * Load analytics data
 * @param {string} period - Period for analytics (1, 7, 30, 90 days)
 */
function loadAnalytics(period = '7') {
    const token = UserManager.getToken();
    
    if (!token) {
        console.error('No authentication token found');
        return;
    }
    
    // Show loading indicators
    showAnalyticsLoading(true);
    
    $.ajax({
        url: '/api/dashboard/analytics',
        method: 'GET',
        data: { period: period },
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        success: function(response) {
            if (response.success) {
                dashboardData.analytics = response.data;
                updateAnalyticsDisplay(response.data);
            } else {
                console.error('Failed to load analytics:', response.message);
                showAnalyticsError();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading analytics:', error);
            showAnalyticsError();
        },
        complete: function() {
            showAnalyticsLoading(false);
        }
    });
}

/**
 * Load data usage trends from radacct table
 * @param {string} period - Period for data usage (7, 30, 90 days)
 */
function loadDataUsageTrends(period = '7') {
    console.log('Loading data usage trends for period:', period);
    
    const token = UserManager.getToken();
    
    if (!token) {
        console.error('No authentication token found');
        return;
    }
    
    // Show loading indicator
    showDataUsageLoading(true);
    
    $.ajax({
        url: '/api/dashboard/data-usage-trends',
        method: 'GET',
        data: { period: period },
        headers: {
            'Authorization': 'Bearer ' + token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        success: function(response) {
            console.log('Data usage trends API response:', response);
            if (response.success) {
                console.log('Updating chart with real data...');
                updateDataUsageChart(response.data);
                updateDataUsageStats(response.data);
            } else {
                console.error('Failed to load data usage trends:', response.message);
                showDataUsageError();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading data usage trends:', error);
            console.error('XHR details:', xhr);
            showDataUsageError();
        },
        complete: function() {
            console.log('Data usage trends loading complete');
            showDataUsageLoading(false);
        }
    });
}

/**
 * Initialize network map with real location data
 * @param {Array} locations - Array of location data with coordinates
 */
function initializeNetworkMap(locations) {
    // Check if Leaflet is loaded and map container exists
    if (typeof L === 'undefined' || !document.getElementById('network-map')) {
        console.warn('Leaflet not loaded or map container not found');
        return;
    }
    
    // Hide loading indicator
    const loadingElement = document.getElementById('map-loading');
    if (loadingElement) {
        loadingElement.style.display = 'none';
    }
    
    // Clear existing map if it exists
    if (networkMap) {
        networkMap.remove();
        networkMap = null;
    }
    
    // Filter locations that have coordinates
    const locationsWithCoords = locations.filter(location => 
        location.latitude && location.longitude && 
        !isNaN(parseFloat(location.latitude)) && 
        !isNaN(parseFloat(location.longitude))
    );
    
    if (locationsWithCoords.length === 0) {
        // No locations with coordinates, show message
        document.getElementById('network-map').innerHTML = 
            '<div class="d-flex align-items-center justify-content-center h-100">' +
            '<div class="text-center">' +
            '<i data-feather="map-pin" class="font-large-1 text-muted mb-1"></i>' +
            '<p class="text-muted">No locations with coordinates found</p>' +
            '</div></div>';
        
        // Re-initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace({ width: 14, height: 14 });
        }
        return;
    }
    
    // Initialize the map with a default view (will be adjusted after adding markers)
    networkMap = L.map('network-map').setView([0, 0], 2);
    
    // Add tile layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(networkMap);
    
    // Define marker icons - Green for online, Red for offline
    const onlineIcon = L.divIcon({
        className: 'marker-icon marker-icon-online',
        html: '<div style="background-color:#00C851; width: 14px; height: 14px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.4);"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });
    
    const offlineIcon = L.divIcon({
        className: 'marker-icon marker-icon-offline',
        html: '<div style="background-color:#FF3547; width: 14px; height: 14px; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.4);"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });
    
    // Create array to store all markers for bounds calculation
    const markers = [];
    
    // Add markers for each location with coordinates
    locationsWithCoords.forEach(function(location) {
        const lat = parseFloat(location.latitude);
        const lng = parseFloat(location.longitude);
        
        // Choose icon based on online status - Green for online, Red for offline
        const icon = location.online_status === 'online' ? onlineIcon : offlineIcon;
        
        // Debug logging
        console.log(`Location: ${location.name}, Status: ${location.online_status}, Icon: ${location.online_status === 'online' ? 'Green' : 'Red'}`);
        
        // Create marker
        const marker = L.marker([lat, lng], { icon: icon }).addTo(networkMap);
        markers.push(marker);
        
        // Create popup content with location details
        const statusColor = location.online_status === 'online' ? 'success' : 'danger';
        const statusText = location.online_status === 'online' ? 'Online' : 'Offline';
        
        const popupContent = `
            <div class="p-2" style="min-width: 200px;">
                <h6 class="mb-1">${location.name}</h6>
                <p class="mb-1 small text-muted">${location.address || 'No address available'}</p>
                <div class="row mt-2 mb-2">
                    <div class="col-12">
                        <span class="badge badge-${statusColor} badge-pill">${statusText}</span>
                    </div>
                </div>
                <div class="row small">
                    <div class="col-6">
                        <i class="fas fa-users text-info mr-1"></i>
                        ${location.users || 0} Users
                    </div>
                    <div class="col-6">
                        <i class="fas fa-download text-warning mr-1"></i>
                        ${(location.data_usage_gb || 0).toFixed(1)}GB
                    </div>
                </div>
                <div class="mt-2">
                    <a href="/locations/${location.id}" class="btn btn-sm btn-primary btn-block">
                        View Details
                    </a>
                </div>
            </div>
        `;
        
        marker.bindPopup(popupContent);
    });
    
    // Auto-fit map to show all markers with proper bounds
    if (markers.length === 1) {
        // Single location: center on it with reasonable zoom
        const singleMarker = markers[0];
        const latLng = singleMarker.getLatLng();
        networkMap.setView([latLng.lat, latLng.lng], 13);
    } else if (markers.length > 1) {
        // Multiple locations: fit bounds to show all markers
        const group = new L.featureGroup(markers);
        const bounds = group.getBounds();
        networkMap.fitBounds(bounds, {
            padding: [20, 20], // Add padding around the bounds
            maxZoom: 15 // Prevent zooming in too much
        });
    }
    
    console.log(`Network map initialized with ${locationsWithCoords.length} location markers`);
}

/**
 * Update overview display with real data
 * @param {Object} data - Overview data from API
 */
function updateOverviewDisplay(data) {
    const locations = data.locations;
    const networkStats = data.network_stats;
    
    // Update welcome card
    $('#welcome-total-locations').text(locations.total + ' Location' + (locations.total !== 1 ? 's' : ''));
    $('#welcome-active-count').text(locations.online);
    $('#welcome-offline-count').text(locations.offline);
    
    // Update network statistics
    $('#routers-online-count').text(networkStats.routers_online + '/' + networkStats.routers_total);
    $('#active-users-count').text(networkStats.active_users.toLocaleString());
    $('#data-used-count').text(networkStats.data_used_tb + 'TB');
    $('#uptime-percentage').text(networkStats.uptime_percentage + '%');
    
    // Initialize network map with real location data
    initializeNetworkMap(locations.data);
    
    console.log('Dashboard overview updated successfully');
}

/**
 * Update analytics display with real data
 * @param {Object} data - Analytics data from API
 */
function updateAnalyticsDisplay(data) {
    const analytics = data.analytics;
    
    // Update analytics metrics
    $('#analytics-total-users').text(analytics.total_users.toLocaleString());
    $('#analytics-data-usage').text(analytics.data_usage_gb + ' GB');
    $('#analytics-uptime').text(analytics.uptime + '%');
    $('#analytics-sessions').text(analytics.total_sessions.toLocaleString());
    
    console.log('Analytics updated for period:', data.period + ' days');
}

/**
 * Update location cards with real data
 * @param {Array} locations - Array of location data
 */
function updateLocationCards(locations) {
    // Store all locations for filtering
    allLocations = locations;
    
    // Apply current filter
    renderLocationCards();
}

/**
 * Filter locations based on current filter
 * @param {string} filter - Filter type: 'all', 'online', 'offline'
 */
function filterLocations(filter) {
    currentLocationFilter = filter;
    
    // Update dropdown text
    let filterText = 'All Locations';
    switch(filter) {
        case 'online':
            filterText = 'Online Only';
            break;
        case 'offline':
            filterText = 'Offline Only';
            break;
    }
    $('#locationDropdown').text(filterText);
    
    // Re-render cards with filter
    renderLocationCards();
}

/**
 * Render location cards based on current filter
 */
function renderLocationCards() {
    const $container = $('#locations-container');
    
    if (!$container.length) {
        console.warn('Location cards container not found');
        return;
    }
    
    // Clear existing cards
    $container.empty();
    
    // Filter locations based on current filter
    let filteredLocations = allLocations;
    
    if (currentLocationFilter === 'online') {
        filteredLocations = allLocations.filter(location => location.online_status === 'online');
    } else if (currentLocationFilter === 'offline') {
        filteredLocations = allLocations.filter(location => location.online_status === 'offline');
    }
    
    // Show message if no locations match filter
    if (filteredLocations.length === 0) {
        let message = 'No locations found';
        if (currentLocationFilter === 'online') {
            message = 'No online locations found';
        } else if (currentLocationFilter === 'offline') {
            message = 'No offline locations found';
        }
        
        $container.html(`
            <div class="col-12">
                <div class="text-center py-4">
                    <i data-feather="map-pin" class="font-large-1 text-muted mb-2"></i>
                    <p class="text-muted">${message}</p>
                </div>
            </div>
        `);
        
        // Re-initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace({ width: 14, height: 14 });
        }
        return;
    }
    
    // Create cards for filtered locations
    filteredLocations.forEach(function(location, index) {
        if (index >= 6) return; // Limit to 6 cards for dashboard
        
        const statusClass = location.online_status === 'online' ? 'badge-light-success' : 'badge-light-danger';
        const statusText = location.online_status === 'online' ? 'Online' : 'Offline';
        
        const cardHtml = `
            <div class="col-md-6 col-lg-4 mb-2">
                <div class="card shadow-none border">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-light-primary p-50 mr-1">
                                    <div class="avatar-content">
                                        <i data-feather="map-pin" class="font-medium-4"></i>
                                    </div>
                                </div>
                                <div>
                                    <h5 class="mb-0">${location.name}</h5>
                                    <small class="text-muted">${location.address || 'No address'}</small>
                                </div>
                            </div>
                            <span class="badge badge-pill ${statusClass}">${statusText}</span>
                        </div>
                        
                        <div class="row mt-1">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i data-feather="users" class="text-info mr-1" style="width: 14px; height: 14px;"></i>
                                    <small>${location.users} Users</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i data-feather="download" class="text-warning mr-1" style="width: 14px; height: 14px;"></i>
                                    <small>${location.data_usage_gb.toFixed(2)}GB Used</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="d-flex align-items-center">
                                <i data-feather="hard-drive" class="text-primary mr-1" style="width: 14px; height: 14px;"></i>
                                <small>Router ${statusText}</small>
                            </div>
                            <a href="/location-details-v2/${location.id}" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $container.append(cardHtml);
    });
    
    // Re-initialize feather icons for new cards
    if (typeof feather !== 'undefined') {
        feather.replace({
            width: 14,
            height: 14
        });
    }
}

/**
 * Update data usage chart with real data
 * @param {Object} data - Data usage trends data
 */
function updateDataUsageChart(data) {
    console.log('Updating data usage chart with data:', data);
    
    // Check if ApexCharts is available
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts is not loaded!');
        $('#data-usage-chart').html('<div class="d-flex align-items-center justify-content-center" style="height: 270px;"><div class="text-center"><i data-feather="alert-triangle" class="font-large-1 text-muted mb-2"></i><p class="text-muted">Chart library not loaded</p></div></div>');
        return;
    }

    // Get chart container
    const chartContainer = document.querySelector('#data-usage-chart');
    if (!chartContainer) {
        console.error('Chart container #data-usage-chart not found!');
        return;
    }
    
    console.log('Chart container found:', chartContainer);
    console.log('Chart container dimensions:', {
        width: chartContainer.offsetWidth,
        height: chartContainer.offsetHeight,
        clientWidth: chartContainer.clientWidth,
        clientHeight: chartContainer.clientHeight
    });

    // Validate data structure and provide fallback
    if (!data || !data.daily_usage || !Array.isArray(data.daily_usage) || data.daily_usage.length === 0) {
        console.warn('No data available for chart, using sample data');
        // Use sample data as fallback
        data = {
            daily_usage: [
                {date: new Date(Date.now() - 6 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], download_gb: 10, upload_gb: 3},
                {date: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], download_gb: 15, upload_gb: 5},
                {date: new Date(Date.now() - 4 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], download_gb: 12, upload_gb: 4},
                {date: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], download_gb: 18, upload_gb: 6},
                {date: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], download_gb: 14, upload_gb: 5},
                {date: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], download_gb: 16, upload_gb: 7},
                {date: new Date().toISOString().split('T')[0], download_gb: 20, upload_gb: 8}
            ]
        };
    }
    
    // Prepare data for the chart
    const downloadData = data.daily_usage.map(item => parseFloat(item.download_gb) || 0);
    const uploadData = data.daily_usage.map(item => parseFloat(item.upload_gb) || 0);
    const categories = data.daily_usage.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('en-US', { weekday: 'short' });
    });
    
    console.log('Chart data prepared:', {
        downloadData,
        uploadData,
        categories
    });
    
    // Chart options
    const dataUsageOptions = {
        chart: {
            height: 270,
            type: 'area',
            toolbar: {
                show: false
            }
        },
        colors: ['#00CFE8', '#FF9F43'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        series: [{
            name: 'Download',
            data: downloadData
        }, {
            name: 'Upload',
            data: uploadData
        }],
        xaxis: {
            categories: categories,
            labels: {
                style: {
                    fontSize: '12px'
                }
            }
        },
        yaxis: {
            title: {
                text: 'GB'
            },
            labels: {
                formatter: function(val) {
                    return val.toFixed(1) + ' GB';
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 100]
            }
        },
        grid: {
            borderColor: '#e7eef7',
            strokeDashArray: 5
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left'
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val.toFixed(2) + ' GB';
                }
            }
        }
    };
    
    // Comprehensive cleanup to prevent multiple charts
    if (dataUsageChart) {
        try {
            dataUsageChart.destroy();
        } catch (e) {
            console.log('Chart destroy error (safe to ignore):', e);
        }
        dataUsageChart = null;
    }
    
    // Clear any existing chart content
    chartContainer.innerHTML = '';
    
    // Remove any ApexCharts classes or data attributes
    chartContainer.removeAttribute('data-apexcharts-rendered');
    chartContainer.className = chartContainer.className.replace(/apexcharts-\w+/g, '');
    
    try {
        // Create new chart with a single, clean approach
        dataUsageChart = new ApexCharts(chartContainer, dataUsageOptions);
        console.log('ApexCharts instance created:', dataUsageChart);
        
        dataUsageChart.render().then(() => {
            console.log('Data usage chart rendered successfully');
            
            // Verify chart is visible
            setTimeout(() => {
                const svgElements = chartContainer.querySelectorAll('svg');
                console.log('Chart SVG elements found:', svgElements.length);
                if (svgElements.length > 0) {
                    console.log('Chart is visible and working properly');
                } else {
                    console.warn('Chart SVG not found after render');
                }
            }, 100);
            
        }).catch(error => {
            console.error('Error rendering chart:', error);
            // Fallback display
            chartContainer.innerHTML = '<div class="d-flex align-items-center justify-content-center" style="height: 270px;"><div class="text-center"><i data-feather="alert-triangle" class="font-large-1 text-muted mb-2"></i><p class="text-muted">Chart rendering failed</p></div></div>';
        });
        
    } catch (error) {
        console.error('Error creating chart:', error);
        chartContainer.innerHTML = '<div class="d-flex align-items-center justify-content-center" style="height: 270px;"><div class="text-center"><i data-feather="alert-triangle" class="font-large-1 text-muted mb-2"></i><p class="text-muted">Chart creation failed</p></div></div>';
    }
}

/**
 * Update data usage statistics
 * @param {Object} data - Data usage trends data
 */
function updateDataUsageStats(data) {
    // Format numbers for display
    const formatBytes = (bytes) => {
        if (bytes >= 1024) {
            return (bytes / 1024).toFixed(1) + ' TB';
        }
        return bytes.toFixed(1) + ' GB';
    };
    
    // Determine period text based on the period
    let periodText = 'This Week';
    switch(data.period) {
        case '30':
            periodText = 'This Month';
            break;
        case '365':
            periodText = 'This Year';
            break;
        case '7':
        default:
            periodText = 'This Week';
            break;
    }
    
    // Update the statistics
    $('#total-bandwidth-used').text(formatBytes(data.total_usage_gb));
    $('#download-usage').text(formatBytes(data.total_download_gb));
    $('#upload-usage').text(formatBytes(data.total_upload_gb));
    
    // Update the period text
    $('#total-bandwidth-used').next('p').text(`Total Usage ${periodText}`);
    
    console.log('Data usage stats updated:', data);
}

/**
 * Show/hide loading indicators for overview section
 * @param {boolean} show - Whether to show loading
 */
function showOverviewLoading(show) {
    if (show) {
        $('#welcome-card .card-body').append('<div class="overview-loading text-center"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>');
        $('#network-stats .card-body').append('<div class="overview-loading text-center"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>');
    } else {
        $('.overview-loading').remove();
    }
}

/**
 * Show/hide loading indicators for analytics section
 * @param {boolean} show - Whether to show loading
 */
function showAnalyticsLoading(show) {
    if (show) {
        $('#analytics-section .card-body').append('<div class="analytics-loading text-center"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>');
    } else {
        $('.analytics-loading').remove();
    }
}

/**
 * Show error message for overview section
 */
function showOverviewError() {
    const errorHtml = '<div class="alert alert-danger alert-dismissible mt-2" role="alert"><i data-feather="alert-triangle" class="mr-50"></i>Failed to load dashboard data. Please refresh the page.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    $('#dashboard-errors').html(errorHtml);
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

/**
 * Show error message for analytics section
 */
function showAnalyticsError() {
    const errorHtml = '<div class="alert alert-warning alert-dismissible mt-2" role="alert"><i data-feather="alert-circle" class="mr-50"></i>Failed to load analytics data.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
    $('#analytics-errors').html(errorHtml);
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

/**
 * Show/hide loading indicators for data usage section
 * @param {boolean} show - Whether to show loading
 */
function showDataUsageLoading(show) {
    if (show) {
        // Destroy any existing chart first
        if (dataUsageChart) {
            try {
                dataUsageChart.destroy();
            } catch (e) {
                console.log('Chart destroy error during loading (safe to ignore):', e);
            }
            dataUsageChart = null;
        }
        
        // Show loading spinner
        $('#data-usage-chart').html('<div class="d-flex align-items-center justify-content-center" style="height: 270px;"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
        $('#total-bandwidth-used').text('Loading...');
        $('#download-usage').text('Loading...');
        $('#upload-usage').text('Loading...');
        // Don't change the period text while loading
    } else {
        // Don't clear the container content when loading is done
        // This will be handled by updateDataUsageChart function
    }
}

/**
 * Show error message for data usage section
 */
function showDataUsageError() {
    $('#data-usage-chart').html('<div class="d-flex align-items-center justify-content-center" style="height: 270px;"><div class="text-center"><i data-feather="alert-triangle" class="font-large-1 text-muted mb-2"></i><p class="text-muted">Failed to load data usage trends</p></div></div>');
    $('#total-bandwidth-used').text('Error');
    $('#download-usage').text('Error');
    $('#upload-usage').text('Error');
    
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
}

/**
 * Initialize data usage chart with default data
 */
function initializeDataUsageChart() {
    console.log('Initializing data usage chart with default data...');
    
    // Default sample data
    const defaultData = {
        daily_usage: [
            {date: new Date(Date.now() - 6 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], download_gb: 10, upload_gb: 3},
            {date: new Date(Date.now() - 5 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], download_gb: 15, upload_gb: 5},
            {date: new Date(Date.now() - 4 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], download_gb: 12, upload_gb: 4},
            {date: new Date(Date.now() - 3 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], download_gb: 18, upload_gb: 6},
            {date: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], download_gb: 14, upload_gb: 5},
            {date: new Date(Date.now() - 1 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], download_gb: 16, upload_gb: 7},
            {date: new Date().toISOString().split('T')[0], download_gb: 20, upload_gb: 8}
        ],
        total_usage_gb: 163,
        total_download_gb: 105,
        total_upload_gb: 38,
        period: '7'
    };
    
    // Initialize chart immediately
    updateDataUsageChart(defaultData);
    updateDataUsageStats(defaultData);
}

/**
 * Initialize dashboard
 */
function initializeDashboard() {
    console.log('Initializing dashboard...');
    
    // Check authentication
    const user = UserManager.getUser();
    console.log("dashboard user: ", user);
    const token = UserManager.getToken();
    
    if (!token || !user) {
        console.error('User not authenticated');
        window.location.href = '/';
        return;
    }
    
    // Show loading state for data usage chart initially
    showDataUsageLoading(true);
    
    // Load initial data
    loadDashboardOverview();
    loadAnalytics('7'); // Default to 7 days
    loadDataUsageTrends('7'); // Default to 7 days - this will replace the loading state
    
    // Set up event listeners
    setupEventListeners();
    
    console.log('Dashboard initialized successfully');
}

/**
 * Set up event listeners
 */
function setupEventListeners() {
    // Analytics period dropdown
    $(document).on('click', '[data-analytics-period]', function(e) {
        e.preventDefault();
        const period = $(this).data('analytics-period');
        loadAnalytics(period);
        
        // Update dropdown text
        const periodText = $(this).text();
        $(this).closest('.dropdown').find('.dropdown-toggle').text(periodText);
    });
    
    // Location filter dropdown
    $(document).on('click', '[data-location-filter]', function(e) {
        e.preventDefault();
        const filterType = $(this).data('location-filter');
        
        // Apply filter
        filterLocations(filterType);
    });
    
    // Data usage period dropdown
    $(document).on('click', '#dataUsageDropdown + .dropdown-menu .dropdown-item', function(e) {
        e.preventDefault();
        const periodText = $(this).text();
        
        // Determine period based on text
        let period = '7';
        if (periodText.includes('28') || periodText.includes('Month')) {
            period = '30';
        } else if (periodText.includes('Year')) {
            period = '365';
        } else if (periodText.includes('7')) {
            period = '7';
        }
        
        // Load data usage trends
        loadDataUsageTrends(period);
        
        // Update dropdown text
        $('#dataUsageDropdown').text(periodText);
    });
    
    // Refresh button (if exists)
    $(document).on('click', '[data-action="refresh-dashboard"]', function(e) {
        e.preventDefault();
        loadDashboardOverview();
        loadAnalytics('7');
        loadDataUsageTrends('7');
    });
}

// Initialize when document is ready
$(document).ready(function() {
    initializeDashboard();
}); 