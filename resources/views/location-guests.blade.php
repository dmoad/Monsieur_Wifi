<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0,minimal-ui">
    <meta name="description" content="monsieur-wifi cloud controller dashboard for managing and monitoring WiFi networks.">
    <meta name="keywords" content="wifi, cloud controller, network management, monsieur-wifi">
    <meta name="author" content="monsieur-wifi">
    <title>Location Guests - monsieur-wifi Controller</title>
    <link rel="apple-touch-icon" href="/app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="/app-assets/mrwifi-assets/MrWifi.png">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500;1,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/extensions/toastr.min.css">
    <!-- END: Vendor CSS-->
    
    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/bordered-layout.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="/app-assets/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/plugins/extensions/ext-component-toastr.css">
    <!-- END: Page CSS-->
    
    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <!-- END: Custom CSS-->
    
    <script src="/app-assets/vendors/js/jquery/jquery.min.js"></script>

    <!-- BEGIN: Page Vendor JS-->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css">
</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
<body class="vertical-layout vertical-menu-modern navbar-floating footer-static menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="">
    <!-- BEGIN: Header-->
    <nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow">
        <div class="navbar-container d-flex content">
            <div class="bookmark-wrapper d-flex align-items-center">
                <ul class="nav navbar-nav d-xl-none">
                    <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i data-feather="menu"></i></a></li>
                </ul>
            </div>
            <ul class="nav navbar-nav align-items-center ml-auto">
                <!-- Language dropdown -->
                <li class="nav-item dropdown dropdown-language">
                    <a class="nav-link dropdown-toggle" id="dropdown-flag" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="flag-icon flag-icon-us"></i>
                        <span class="selected-language">English</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-flag">
                        <a class="dropdown-item" href="/en/locations" data-language="en">
                            <i class="flag-icon flag-icon-us"></i> English
                        </a>
                        <a class="dropdown-item" href="/fr/locations" data-language="fr">
                            <i class="flag-icon flag-icon-fr"></i> Français
                        </a>
                    </div>
                </li>
                
                <!-- User dropdown -->
                <li class="nav-item dropdown dropdown-user">
                    <a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder"></span><span class="user-status"></span></div>
                        <span class="avatar"><img class="round user-profile-picture" src="/assets/avatar-default.jpg" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">
                        <a class="dropdown-item" href="/en/profile"><i class="mr-50" data-feather="user"></i> Profile</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/logout"><i class="mr-50" data-feather="power"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="navbar-header">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item mr-auto">
                    <a class="navbar-brand" href="dashboard.html">
                        <span class="brand-logo">
                            <img src="../../../app-assets/mrwifi-assets/Mr-Wifi.PNG" alt="monsieur-wifi logo">
                        </span>
                        <h2 class="brand-text">monsieur-wifi</h2>
                    </a>
                </li>
                <li class="nav-item nav-toggle">
                    <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
                        <i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i>
                        <i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-primary" data-feather="disc" data-ticon="disc"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="shadow-bottom"></div>
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <!-- Management Section -->
                <li class="navigation-header"><span>Management</span></li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/dashboard">
                        <i data-feather="home"></i>
                        <span class="menu-title text-truncate">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a class="d-flex align-items-center" href="/en/locations">
                        <i data-feather="map-pin"></i>
                        <span class="menu-title text-truncate">Locations</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="d-flex align-items-center" href="/en/captive-portals">
                        <i data-feather="layout"></i>
                        <span class="menu-title text-truncate">Captive Portals</span>
                    </a>
                </li>
                
                <!-- For Admin Section -->
                <li class="navigation-header only_admin hidden"><span>For Admin</span></li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/accounts">
                        <i data-feather="users"></i>
                        <span class="menu-title text-truncate">Accounts</span>
                    </a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/domain-blocking">
                        <i data-feather="slash"></i>
                        <span class="menu-title text-truncate">Domain Blocking</span>
                    </a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/firmware">
                        <i data-feather="download"></i>
                        <span class="menu-title text-truncate">Firmware</span>
                    </a>
                </li>
                <li class="nav-item only_admin hidden">
                    <a class="d-flex align-items-center" href="/en/system-settings">
                        <i data-feather="settings"></i>
                        <span class="menu-title text-truncate">System Settings</span>
                    </a>
                </li>
                <!-- Account Section -->
                <li class="navigation-header"><span>Account</span></li>
                <li class="nav-item">
                     <a class="d-flex align-items-center" href="/en/profile">
                         <i data-feather="user"></i>
                         <span class="menu-title text-truncate">Profile</span>
                     </a>
                </li>
                <li class="nav-item">
                     <a class="d-flex align-items-center" href="/logout">
                         <i data-feather="power"></i>
                         <span class="menu-title text-truncate">Logout</span>
                     </a>
                </li> 
            </ul>
        </div>
    </div>
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0" id="location-name">Location Guests</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/en/dashboard">Home</a></li>
                                    <li class="breadcrumb-item"><a href="/en/locations">Locations</a></li>
                                    <li class="breadcrumb-item active">Guests</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrumb-right">
                        <button class="btn btn-primary" id="export-csv-btn">
                            <i data-feather="download" class="mr-50"></i>
                            <span>Export CSV</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Guests Content Starts -->
                <section id="guests-content">
                    <!-- Guests Table -->
                    <div class="row" id="table-hover-row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Guest List</h4>
                                    
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="guests-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>MAC Address</th>
                                                <th>Email</th>
                                                <th>Phone Number</th>
                                                <th>First Login</th>
                                            </tr>
                                        </thead>
                                        <tbody id="guests-table-body">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Guests Content Ends -->
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    <footer class="footer footer-static footer-light">
        <p class="clearfix mb-0"><span class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2025<a class="ml-25" href="https://mrwifi.com" target="_blank">monsieur-wifi</a><span class="d-none d-sm-inline-block">, All rights Reserved</span></span><span class="float-md-right d-none d-md-block">Hand-crafted & Made with<i data-feather="heart"></i></span></p>
    </footer>
    <button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="/app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Page Vendor JS-->
    <script src="/app-assets/vendors/js/extensions/toastr.min.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="/app-assets/js/core/app-menu.js"></script>
    <script src="/app-assets/js/core/app.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js"></script>
    <script src="/app-assets/vendors/js/tables/datatable/responsive.bootstrap4.js"></script>
    <!-- END: Page Vendor JS-->

    <!-- Include config.js before other custom scripts -->
    <script src="/assets/js/config.js?v=1"></script>

    <script>
        let guestsTable;
        let locationId = '{{ $location }}';
        let currentSearch = '';

        $(window).on('load', function() {
            if (feather) {
                feather.replace({
                    width: 14,
                    height: 14
                });
            }
        });

        $(document).ready(function() {
            // Check if user is logged in using UserManager from config.js
            const user = UserManager.getUser();
            const token = UserManager.getToken();
            
            if (!token || !user) {
                window.location.href = '/';
                return;
            }

            // Update user display in the top right dropdown
            $('.user-name').text(user.name);
            $('.user-status').text(user.role);
            var profile_picture = localStorage.getItem('profile_picture');
            if (profile_picture) {
                $('.user-profile-picture').attr('src', '/uploads/profile_pictures/' + profile_picture);
            }

            // Load location data
            loadLocationData();

            // Initialize DataTable
            guestsTable = $('#guests-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: false,
                searching: true,
                order: [[4, 'desc']], // Order by Last Login (updated_at) descending
                columns: [
                    { 
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    { data: 'mac_address', name: 'mac_address', defaultContent: '-' },
                    { data: 'email', name: 'email', defaultContent: '-' },
                    { data: 'phone', name: 'phone', defaultContent: '-' },
                    { 
                        data: 'updated_at', 
                        name: 'updated_at', 
                        defaultContent: '-',
                        render: function(data, type, row) {
                            if (!data || data === '-') {
                                return '-';
                            }
                            
                            // For sorting, return the raw date
                            if (type === 'sort') {
                                return data;
                            }
                            
                            // Format the date for display
                            const date = new Date(data);
                            const now = new Date();
                            const diffMs = now - date;
                            const diffMins = Math.floor(diffMs / 60000);
                            const diffHours = Math.floor(diffMs / 3600000);
                            const diffDays = Math.floor(diffMs / 86400000);
                            
                            // Show relative time for recent logins
                            if (diffMins < 1) {
                                return '<span class="badge badge-light-success">Just now</span>';
                            } else if (diffMins < 60) {
                                return '<span class="badge badge-light-success">' + diffMins + ' min' + (diffMins > 1 ? 's' : '') + ' ago</span>';
                            } else if (diffHours < 24) {
                                return '<span class="badge badge-light-info">' + diffHours + ' hour' + (diffHours > 1 ? 's' : '') + ' ago</span>';
                            } else if (diffDays < 7) {
                                return '<span class="badge badge-light-warning">' + diffDays + ' day' + (diffDays > 1 ? 's' : '') + ' ago</span>';
                            } else {
                                // Show formatted date for older entries
                                return '<span class="badge badge-light-secondary">' + 
                                       date.toLocaleDateString() + ' ' + 
                                       date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + 
                                       '</span>';
                            }
                        }
                    }
                ],
                language: {
                    emptyTable: 'No guests found',
                    zeroRecords: 'No matching guests found',
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>'
                }
            });

            // Load guests data
            loadGuestsData();

            // Search functionality
            $('#search-guests').on('keyup', function() {
                currentSearch = $(this).val();
                guestsTable.search(currentSearch).draw();
            });

            // Export CSV button
            $('#export-csv-btn').on('click', function() {
                exportToCSV();
            });
        });

        function loadLocationData() {
            const token = UserManager.getToken();
            
            $.ajax({
                url: `/api/locations/${locationId}`,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    let location = null;
                    console.log('Location data response:', response);
                    if (response.data) {
                        location = response.data;
                    } else if (response.location) {
                        location = response.location;
                    }

                    if (location && location.name) {
                        $('#location-name').text(location.name + ' - Guests');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading location data:', error);
                    if (xhr.status === 401) {
                        window.location.href = '/';
                    }
                }
            });
        }

        function loadGuestsData() {
            const token = UserManager.getToken();
            
            $.ajax({
                url: `/api/locations/${locationId}/guest-users`,
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                success: function(response) {
                    console.log('Guest users response:', response);
                    
                    if (response.success && response.data) {
                        const guests = response.data;
                        
                        // Clear existing data using DataTables API
                        guestsTable.clear();
                        
                        // Add data to table using DataTables API
                        if (guests.length > 0) {
                            guests.forEach(function(guest) {
                                guestsTable.row.add({
                                    mac_address: guest.mac_address || '-',
                                    email: guest.email || '-',
                                    phone: guest.phone || '-',
                                    updated_at: guest.created_at || '-'
                                });
                            });
                        }
                        
                        // Draw the table (this will show emptyTable message if no data)
                        guestsTable.draw();
                    } else {
                        guestsTable.clear().draw();
                        toastr.error('Error loading guests data');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading guests:', error);
                    
                    // Clear and redraw to show empty state
                    guestsTable.clear().draw();
                    
                    if (xhr.status === 401) {
                        window.location.href = '/';
                    } else {
                        toastr.error('Failed to load guests data');
                    }
                }
            });
        }

        function exportToCSV() {
            const token = UserManager.getToken();
            const searchParam = currentSearch ? `?search=${encodeURIComponent(currentSearch)}` : '';
            
            // Create a temporary link to trigger download
            const url = `/api/locations/${locationId}/guest-users/export${searchParam}`;
            
            // Create a temporary anchor element
            const link = document.createElement('a');
            link.href = url;
            link.style.display = 'none';
            
            // Add authorization header via fetch
            fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'text/csv'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Export failed');
                }
                return response.blob();
            })
            .then(blob => {
                const downloadUrl = window.URL.createObjectURL(blob);
                link.href = downloadUrl;
                link.download = `location_${locationId}_guests_${new Date().toISOString().slice(0,10)}.csv`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(downloadUrl);
                
                toastr.success('CSV export started');
            })
            .catch(error => {
                console.error('Export error:', error);
                toastr.error('Failed to export CSV');
            });
        }
    </script>
</body>
</html>

