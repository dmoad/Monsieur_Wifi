@extends('layouts.app')

@section('title', 'Locations - Monsieur WiFi')

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/rowGroup.bootstrap4.min.css">

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/maps/leaflet.min.css">
<link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/maps/map-leaflet.css">

<style>
.dataTables_paginate {
    margin-top: 1rem !important;
    padding: 1rem !important;
}

.pagination {
    display: flex;
    justify-content: flex-end;
}

.page-link {
    padding: 0.5rem 0.75rem;
    margin-left: -1px;
    border: 1px solid #ddd;
    color: #7367f0;
}

.page-item.active .page-link {
    background-color: #7367f0;
    border-color: #7367f0;
    color: #fff;
}

.page-item.disabled .page-link {
    color: #b9b9c3;
    pointer-events: none;
    background-color: #fff;
    border-color: #ddd;
}

.location-card {
    border-radius: 10px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.location-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.status-badge {
    padding: 5px 10px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-online {
    background-color: rgba(40, 199, 111, 0.12);
    color: #28c76f;
}

.status-offline {
    background-color: rgba(234, 84, 85, 0.12);
    color: #ea5455;
}

.status-warning {
    background-color: rgba(255, 159, 67, 0.12);
    color: #ff9f43;
}

.network-stat-icon {
    height: 45px;
    width: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}

.marker-icon {
    display: flex;
    align-items: center;
    justify-content: center;
}

.leaflet-map {
    z-index: 1;
}

.leaflet-container {
    font-family: inherit;
    font-size: inherit;
}

.leaflet-popup-content {
    margin: 0;
    padding: 0;
}

.custom-div-icon, .marker-icon {
    background: transparent;
    border: none;
}
</style>
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Locations</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                        <li class="breadcrumb-item active">Locations</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
        <div class="form-group breadcrumb-right">
            <button class="btn btn-primary" data-toggle="modal" data-target="#add-location-modal">
                <i data-feather="plus" class="mr-50"></i>
                <span>Add Location</span>
            </button>
        </div>
    </div>
</div>

<div class="content-body">
    <section id="locations-content">
        <!-- Locations Stats -->
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="font-weight-bolder mb-0" id="total-locations"></h2>
                            <p class="card-text">Total Locations</p>
                        </div>
                        <div class="avatar bg-light-primary p-50 m-0" style="pointer-events: none; cursor: default; text-decoration: none;">
                            <div class="avatar-content">
                                <i data-feather="map-pin" class="font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="font-weight-bolder mb-0" id="online-locations"></h2>
                            <p class="card-text">Online Locations</p>
                        </div>
                        <div class="avatar bg-light-success p-50 m-0" style="pointer-events: none; cursor: default; text-decoration: none;">
                            <div class="avatar-content">
                                <i data-feather="check-circle" class="font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="font-weight-bolder mb-0" id="total-users"></h2>
                            <p class="card-text">Total Users</p>
                        </div>
                        <div class="avatar bg-light-info p-50 m-0" style="pointer-events: none; cursor: default; text-decoration: none;">
                            <div class="avatar-content">
                                <i data-feather="users" class="font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="font-weight-bolder mb-0" id="total-data"></h2>
                            <p class="card-text">Total Data Usage</p>
                        </div>
                        <div class="avatar bg-light-warning p-50 m-0" style="pointer-events: none; cursor: default; text-decoration: none;">
                            <div class="avatar-content">
                                <i data-feather="download" class="font-medium-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Locations Table -->
        <div class="row" id="table-hover-row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Locations List</h4>
                        <div class="d-flex align-items-center">
                            <div class="form-group mb-0 mr-1">
                                <select class="form-control" id="status-filter" disabled>
                                    <option value="">All Status</option>
                                    <option value="Online">Online</option>
                                    <option value="Offline">Offline</option>
                                </select>
                            </div>
                            <div class="form-group mb-0">
                                <input type="text" class="form-control" id="search-locations" placeholder="Search locations...">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="locations-table">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Address</th>
                                    <th>Users</th>
                                    <th>Data Usage</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="locations-table-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Location Modal -->
<div class="modal fade" id="add-location-modal" tabindex="-1" role="dialog" aria-labelledby="add-location-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add-location-title">Add New Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-location-form">
                    <div class="form-group" id="owner-select-group" style="display: none;">
                        <label for="owner-select">Owner <span class="text-danger">*</span></label>
                        <select class="form-control" id="owner-select">
                            <option value="">Loading users...</option>
                        </select>
                        <small class="form-text text-muted">Select the owner for this location.</small>
                    </div>
                    <div class="form-group">
                        <label for="location-name">Location Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="location-name" placeholder="Enter location name">
                    </div>
                    <div class="form-group">
                        <label for="location-address">Address</label>
                        <input type="text" class="form-control" id="location-address" placeholder="Enter address">
                    </div>
                    <div class="form-group">
                        <label for="device-select">Select Device <span class="text-danger">*</span></label>
                        <select class="form-control" id="device-select" required>
                            <option value="">Loading devices...</option>
                        </select>
                        <small class="form-text text-muted">Select an existing device to assign to this location.</small>
                    </div>
                    <div class="form-group">
                        <label for="location-notes">Description</label>
                        <textarea class="form-control" id="location-notes" rows="3" placeholder="Enter additional notes or description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="add-location-btn">Add Location</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet -->
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

<!-- DataTables -->
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
<script src="/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
<script src="/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js"></script>
<script src="/app-assets/vendors/js/maps/leaflet.min.js"></script>
<script src="/app-assets/js/scripts/maps/map-leaflet.js"></script>
<script src="/app-assets/js/scripts/tables/table-datatables-basic.js"></script>

<script>
$(document).ready(function() {
    const user = UserManager.getUser();
    const token = UserManager.getToken();
    
    if (!token || !user) {
        window.location.href = '/';
        return;
    }

    console.log("token: " + token);

    // Make API call to get locations
    $.ajax({
        url: APP_CONFIG.API.BASE_URL + '/locations',
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token
        },
        success: function(response) {
            var locations = response.locations;
            var networkTotals = response.network_totals;
            console.log("Locations list response: ", locations);
            console.log("Network totals: ", networkTotals);
            
            var total_locations = locations.length;
            var online_locations = locations.filter(loc => loc.online_status == "online").length;
            var total_users = locations.reduce((sum, loc) => sum + loc.users, 0);
            var total_data_gb = networkTotals.total_data_gb;
            var total_data_formatted = total_data_gb > 1024 ? 
                (total_data_gb / 1024).toFixed(1) + 'TB' : 
                total_data_gb.toFixed(1) + 'GB';

            $("#total-locations").text(total_locations);
            $("#online-locations").text(online_locations);
            $("#total-users").text(total_users);
            $("#total-data").text(total_data_formatted);
            
            // Populate table with locations data
            var table_content = "";
            locations.forEach(function(location) {
                table_content += '<tr>';
                table_content += '<td><div class="d-flex align-items-center"><div class="avatar bg-light-primary mr-1"><div class="avatar-content"><img src="/assets/map-icon-1.png" alt="Marker Icon" width="40" height="40"></div></div><span>' + location.name + '</span></div></td>';
                table_content += '<td>' + location.address + '</td>';
                table_content += '<td>' + location.users + '</td>';
                table_content += '<td>' + location.data_usage + '</td>';
                if (location.online_status == "online") {   
                    table_content += '<td><span class="badge badge-pill badge-light-success">Online</span></td>';
                } else {
                    table_content += '<td><span class="badge badge-pill badge-light-danger">Offline</span></td>';
                }
                table_content += '<td><a href="/en/locations/' + location.id + '" class="btn btn-sm btn-primary">View</a></td>';
                table_content += '</tr>';
            });
            $('#locations-table-body').html(table_content);

            if (typeof feather !== 'undefined') {
                feather.replace();
            }
            
            // Initialize or reinitialize DataTable after data is loaded
            if ($.fn.DataTable.isDataTable('#locations-table')) {
                $('#locations-table').DataTable().destroy();
            }
            
            $('#locations-table').DataTable({
                responsive: true,
                columnDefs: [
                    {
                        targets: [5],
                        orderable: false
                    }
                ],
                dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                language: {
                    paginate: {
                        previous: '&nbsp;',
                        next: '&nbsp;'
                    }
                }
            });
            
            $('#status-filter').prop('disabled', false);
            $('#status-filter').val("");
        },
        error: function(xhr, status, error) {
            console.error('Error fetching locations:', error);
            if (xhr.status === 401) {
                window.location.href = '/';
            }
        }
    });
    
    // Handle add location form submission
    $('#add-location-btn').on('click', function(e) {
        e.preventDefault();
        
        $(this).html('<i data-feather="loader" class="mr-2"></i>Adding Location...');
        $(this).prop('disabled', true);
        
        $('.form-error').remove();
        $('.is-invalid').removeClass('is-invalid');
        
        const locationData = {
            name: $('#location-name').val(),
            address: $('#location-address').val(),
            device_id: $('#device-select').val(),
            description: $('#location-notes').val()
        };
        
        // Set owner_id
        if (UserManager.isAdminOrAbove() && $('#owner-select').val()) {
            // Admin selected a specific owner
            locationData.owner_id = $('#owner-select').val();
        } else {
            // Non-admin or admin without selection - set to current user
            locationData.owner_id = user.id;
        }

        let hasErrors = false;
        if (!locationData.name) {
            showFieldError('location-name', 'Location name is required');
            hasErrors = true;
        }

        if (!locationData.device_id) {
            showFieldError('device-select', 'Device selection is required');
            hasErrors = true;
        }

        if (hasErrors) {
            $('#add-location-btn').html('Add Location');
            $('#add-location-btn').prop('disabled', false);
            return;
        }
        
        $.ajax({
            url: APP_CONFIG.API.BASE_URL + '/locations',
            type: 'POST',
            data: locationData,
            headers: {
                'Authorization': 'Bearer ' + token
            },
            success: function(response) {
                console.log(response);
                $('#add-location-btn').removeClass('btn-primary');
                $('#add-location-btn').addClass('btn-success');
                
                let successMessage = 'Location created successfully';
                if (response.firmware) {
                    successMessage += `<br><small>Assigned firmware: ${response.firmware.name}</small>`;
                }

                $('#add-location-btn').html(successMessage);
                $('#add-location-btn').prop('disabled', true);
                
                setTimeout(function() {
                    $('#add-location-btn').removeClass('btn-success');
                    $('#add-location-btn').addClass('btn-primary');
                    $('#add-location-btn').html('Add Location');
                    $('#add-location-btn').prop('disabled', false);
                    $('#add-location-modal').modal('hide');
                    
                    $('#add-location-form')[0].reset();
                    $('.form-error').remove();
                    $('.is-invalid').removeClass('is-invalid');
                    
                    window.location.reload();
                }, 4000);
            },
            error: function(xhr, status, error) {
                $('#add-location-btn').removeClass('btn-primary');
                $('#add-location-btn').addClass('btn-danger');
                $('#add-location-btn').html('Error creating location');
                $('#add-location-btn').prop('disabled', true);

                setTimeout(function() {
                    $('#add-location-btn').removeClass('btn-danger');
                    $('#add-location-btn').addClass('btn-primary');
                    $('#add-location-btn').html('Add Location');
                    $('#add-location-btn').prop('disabled', false);
                }, 3000);
                console.error('Error creating location:', error);
            }
        });
    });
    
    function showFieldError(fieldId, message) {
        $(`#${fieldId}`)
            .addClass('is-invalid')
            .after(`<div class="invalid-feedback form-error">${message}</div>`);
    }
    
    function isValidMacAddress(mac) {
        return /^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/.test(mac);
    }
});

$('#status-filter').on('change', function() {
    var status = $(this).val();
    var table = $('#locations-table').DataTable();
    
    if (status !== '') {
        table.column(4).search(status).draw();
    } else {
        table.column(4).search('').draw();
    }
});

async function loadUsers() {
    console.log('loadUsers called');
    console.log('isAdminOrAbove:', UserManager.isAdminOrAbove());
    
    if (!UserManager.isAdminOrAbove()) {
        console.log('User is not admin, skipping user load');
        return;
    }
    
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/accounts/users`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error('Failed to load users');
        
        const data = await response.json();
        console.log('Users loaded:', data.users.length);
        const select = $('#owner-select');
        
        let options = '<option value="">Select owner...</option>';
        data.users.forEach(user => {
            options += `<option value="${user.id}">${user.name} (${user.email})</option>`;
        });
        
        select.html(options);
        console.log('Showing owner-select-group');
        $('#owner-select-group').show();
        console.log('owner-select-group display:', $('#owner-select-group').css('display'));
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

async function loadAvailableDevices() {
    const token = UserManager.getToken();
    
    try {
        const response = await fetch(`${APP_CONFIG.API.BASE_URL}/v1/devices/available-for-location`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) throw new Error('Failed to load devices');
        
        const data = await response.json();
        const select = $('#device-select');
        
        let options = '<option value="">Select a device...</option>';
        
        // Unassigned devices first
        if (data.unassigned && data.unassigned.length > 0) {
            options += '<optgroup label="Available Devices">';
            data.unassigned.forEach(device => {
                options += `<option value="${device.id}">${device.serial_number} - ${device.mac_address} (${device.model}) - Available</option>`;
            });
            options += '</optgroup>';
        }
        
        // Assigned devices second
        if (data.assigned && data.assigned.length > 0) {
            options += '<optgroup label="Devices Assigned to Other Locations">';
            data.assigned.forEach(device => {
                const locationName = device.location ? device.location.name : 'Unknown Location';
                options += `<option value="${device.id}">${device.serial_number} - ${device.mac_address} (${device.model}) - Assigned to: ${locationName}</option>`;
            });
            options += '</optgroup>';
        }
        
        if (data.unassigned.length === 0 && data.assigned.length === 0) {
            options = '<option value="">No devices available</option>';
        }
        
        select.html(options);
    } catch (error) {
        console.error('Error loading devices:', error);
        $('#device-select').html('<option value="">Error loading devices</option>');
    }
}

// Load devices and users when modal is shown
$('#add-location-modal').on('show.bs.modal', function() {
    loadAvailableDevices();
    loadUsers();
});
</script>
@endpush

@php
    $locale = 'en';
@endphp
