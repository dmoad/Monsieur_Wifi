// Define location_id as a global variable
let location_id;
let currentUsagePeriod = 'today'; // Default to today

$(window).on('load', function() {
    if (feather) {
        feather.replace({
            width: 14,
            height: 14
        });
    }

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // get location_id from url and assign to global variable
    location_id = window.location.pathname.split('/').pop();
    console.log("location_id", location_id);
    
    // load location details and current usage
    loadLocationDetails(location_id);
    loadCurrentUsage(location_id, currentUsagePeriod);

    // Handle usage period dropdown
    $('#usage-period-dropdown .dropdown-item').on('click', function(e) {
        e.preventDefault();
        const selectedPeriod = $(this).data('period');
        const selectedText = $(this).text();
        const $button = $('#usage-period-btn');
        
        // Update button text
        $button.text(selectedText);
        
        // Update current period
        currentUsagePeriod = selectedPeriod;
        
        // Add loading state to button temporarily
        const originalText = $button.text();
        $button.html('<i class="fas fa-spinner fa-spin mr-1"></i>' + originalText);
        
        // Reload usage data with new period
        loadCurrentUsage(location_id, currentUsagePeriod);
        
        // Reset button after a delay
        setTimeout(function() {
            $button.text(originalText);
        }, 1000);
    });

    $('#password-network-modal').on('shown.bs.modal', function() {
        initPasswordNetworkToggle();
    });
});

function loadLocationDetails(location_id) {
    // make ajax request to get location details
    $.ajax({
        url: '/api/locations/' + location_id,
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + UserManager.getToken()
        },
        success: function(response) {
            console.log("Location Details Response", response);
            // populate location details
            let captive_portal_designs = [];
            if (response.success) {
                let location = response.data;

                $.ajax({
                    url: '/api/captive-portal-designs',
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + UserManager.getToken()
                    },
                    success: function(response) {
                        console.log("captive-portal-designs response", response);
                        let captive_portal_designs = response.data;
                        populateLocationDetails(location, captive_portal_designs);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading captive portal designs:", error);
                    }
                });
            } else {
                // if message is Unauthorized access, redirect to dashboard
                if (response.message == 'Unauthorized access') {
                    window.location.href = '/dashboard';
                }
            }
        },
        error: function(xhr, status, error) {
            console.error("Error loading location details:", error);
        }
    });
}

function loadCurrentUsage(location_id, period) {
    console.log("Loading current usage for period:", period);
    
    // Show loading state
    showCurrentUsageLoading();
    
    // Calculate date range based on period
    let startDate, endDate;
    const now = new Date();
    endDate = now.toISOString().split('T')[0]; // Today
    
    switch(period) {
        case 'today':
            startDate = endDate;
            break;
        case '7days':
            const sevenDaysAgo = new Date(now);
            sevenDaysAgo.setDate(now.getDate() - 7);
            startDate = sevenDaysAgo.toISOString().split('T')[0];
            break;
        case '30days':
            const thirtyDaysAgo = new Date(now);
            thirtyDaysAgo.setDate(now.getDate() - 30);
            startDate = thirtyDaysAgo.toISOString().split('T')[0];
            break;
        default:
            startDate = endDate;
    }
    
    // Make API call to get accounting data
    $.ajax({
        url: '/api/locations/' + location_id + '/accounting',
        type: 'GET',
        headers: {
            'Authorization': 'Bearer ' + UserManager.getToken(),
            'Content-Type': 'application/json'
        },
        data: {
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            console.log("Current Usage Response", response);
            if (response.success && response.data) {
                updateCurrentUsageDisplay(response.data, period);
                updateLastUpdatedTime();
            } else {
                console.error("Invalid response format for current usage");
                showCurrentUsageError();
            }
        },
        error: function(xhr, status, error) {
            console.error("Error loading current usage:", error);
            showCurrentUsageError();
        }
    });
}

function updateCurrentUsageDisplay(data, period) {
    const summary = data.summary;
    const activeSessions = data.active_sessions || [];
    
    // Format data usage
    const totalGB = summary.total_gb || 0;
    const inputGB = summary.total_input_bytes ? (summary.total_input_bytes / (1024 * 1024 * 1024)).toFixed(2) : 0;
    const outputGB = summary.total_output_bytes ? (summary.total_output_bytes / (1024 * 1024 * 1024)).toFixed(2) : 0;
    
    // Update download usage (input/download)
    $('#download-usage').html(inputGB + ' GB');
    
    // Update upload usage (output/upload)  
    $('#upload-usage').html(outputGB + ' GB');
    
    // Update total users and sessions during the period
    const totalUsers = summary.unique_users || 0;
    const totalSessions = summary.total_sessions || 0;
    $('#connected-users').html(`${totalUsers} <small class="text-muted">/ ${totalSessions}</small>`);
    
    // Calculate average session time
    const totalSessionHours = summary.total_session_time_hours || 0;
    const sessionCountForAvg = summary.total_sessions || 1; // Avoid division by zero
    const avgSessionHours = sessionCountForAvg > 0 ? (totalSessionHours / sessionCountForAvg).toFixed(1) : 0;
    
    $('#avg-session-time').html(avgSessionHours + ' hrs');
    
    // Update status colors based on activity
    updateUsageStatusColors(totalUsers, totalGB);
    
    // Hide loading, show data
    hideCurrentUsageLoading();
    
    console.log("Updated current usage display", {
        period: period,
        download: inputGB + ' GB',
        upload: outputGB + ' GB', 
        totalUsers: totalUsers,
        totalSessions: totalSessions,
        avgSession: avgSessionHours + ' hrs'
    });
}

function updateUsageStatusColors(totalUsers, totalGB) {
    // Update total users color based on activity level
    const $usersElement = $('#connected-users');
    $usersElement.removeClass('text-primary text-info text-warning text-danger');
    
    if (totalUsers === 0) {
        $usersElement.addClass('text-muted');
    } else if (totalUsers <= 5) {
        $usersElement.addClass('text-info');
    } else if (totalUsers <= 15) {
        $usersElement.addClass('text-primary');
    } else if (totalUsers <= 25) {
        $usersElement.addClass('text-warning');
    } else {
        $usersElement.addClass('text-danger');
    }
    
    // Update data usage colors
    const $downloadElement = $('#download-usage');
    const $uploadElement = $('#upload-usage');
    
    $downloadElement.removeClass('text-primary text-success text-warning text-danger');
    $uploadElement.removeClass('text-primary text-success text-warning text-danger');
    
    if (totalGB < 1) {
        $downloadElement.addClass('text-success');
        $uploadElement.addClass('text-success');
    } else if (totalGB < 5) {
        $downloadElement.addClass('text-primary');
        $uploadElement.addClass('text-info');
    } else if (totalGB < 10) {
        $downloadElement.addClass('text-warning');
        $uploadElement.addClass('text-warning');
    } else {
        $downloadElement.addClass('text-danger');
        $uploadElement.addClass('text-danger');
    }
}

function showCurrentUsageLoading() {
    // Show loading spinners in the stats
    $('#download-usage').html('<i class="fas fa-spinner fa-spin" style="font-size: 1rem;"></i>');
    $('#upload-usage').html('<i class="fas fa-spinner fa-spin" style="font-size: 1rem;"></i>');
    $('#connected-users').html('<i class="fas fa-spinner fa-spin" style="font-size: 1rem;"></i>');
    $('#avg-session-time').html('<i class="fas fa-spinner fa-spin" style="font-size: 1rem;"></i>');
    
    // Update last updated text
    $('#usage-last-updated').text('Chargement des données...');
}

function hideCurrentUsageLoading() {
    // This function will be called after data is loaded
    // The actual data display is handled in updateCurrentUsageDisplay()
}

function updateLastUpdatedTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', { 
        hour12: true,
        hour: 'numeric',
        minute: '2-digit'
    });
    $('#usage-last-updated').text(`Dernière mise à jour: ${timeString}`);
}

function showCurrentUsageError() {
    // Show error state in current usage display
    $('#download-usage').html('Error');
    $('#upload-usage').html('Error');
    $('#connected-users').html('Error');
    $('#avg-session-time').html('Error');
    
    // Add error styling
    $('#download-usage, #upload-usage, #connected-users, #avg-session-time')
        .removeClass('text-primary text-info text-success text-warning text-danger')
        .addClass('text-muted');
    
    // Update timestamp
    $('#usage-last-updated').text('Échec du chargement des données');
}

function populateLocationDetails(location, captive_portal_designs) {
    // Set basic location information
    $('.location_name').text(location.name || 'Unknown Location');
    $('.location_address').text(location.address || 'No address available');
    
    // Update device/router info if available
    if (location.device) {
        const device = location.device;
        
        // Update status badge
        if (device.is_online) {
            $('.status-badge').removeClass('status-offline status-warning').addClass('status-online').text('Online');
            $('#start-scan-btn').removeClass('hidden');
            $('#scan-info-text').addClass('hidden');
        } else {
            $('.status-badge').removeClass('status-online status-warning').addClass('status-offline').text('Offline');
            $('#start-scan-btn').addClass('hidden');
            $('#scan-info-text').removeClass('hidden');
        }
        console.log("device:::::", device);
        // Update router details
        $('.router_model_updated').text(device.model || 'Unknown');
        $('.router_firmware').text(device.firmware_version || 'Unknown');
        
        // // Calculate and display uptime if last_seen exists
        // if (device.last_seen) {
        //     const lastSeen = new Date(device.last_seen);
        //     const now = new Date();
        //     const diffMs = now - lastSeen;
        //     const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        //     const diffHours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        //     $('.uptime').text(`${diffDays} days, ${diffHours} hours`);
        // } else {
        //     $('.uptime').text('Unknown');
        // }
        
        // Display reboot count
        // $('.reboot_count').text(device.reboot_count || '0');
    }

    // This isn't printing because the AJAX call is asynchronous - we need to move this log inside the success callback
    // Moving this console.log into the success callback of the AJAX request above
    
    // Update connected users and usage data
    $('.connected_users').text(location.users || '0');
    $('.daily_usage').text(location.data_usage ? `${(location.data_usage / 1024).toFixed(2)} GB` : '0 GB');
    
    // Populate settings if available
    if (location.settings) {
        const settings = location.settings;
        
        // Populate location settings tab
        $('#location-name').val(location.name);
        $('#location-address').val(location.address);
        $('#location-city').val(location.city);
        $('#location-state').val(location.state);
        $('#location-country').val(location.country);
        $('#location-manager').val(location.manager_name);
        $('#location-contact-email').val(location.contact_email);
        $('#location-contact-phone').val(location.contact_phone);
        $('#location-status').val(location.status);
        $('#location-description').val(location.description);

        // Populate router settings
        $('#wifi-country').val(settings.country_code || 'US');
        $('#power-level-2g').val(settings.transmit_power_2g || '15');
        $('#power-level-5g').val(settings.transmit_power_5g || '17');
        $('#channel-width-2g').val(settings.channel_width_2g || '40');
        $('#channel-width-5g').val(settings.channel_width_5g || '80');
        $('#channel-2g').val(settings.channel_2g || '6');
        $('#channel-5g').val(settings.channel_5g || '36');
        // Populate WAN settings
        if (settings.wan_connection_type == 'STATIC') {
            $('#wan-connection-type').val(settings.wan_connection_type.toUpperCase());
            $('#wan-type-display').text(settings.wan_connection_type.toUpperCase());
            $('#wan-ip-display').text(settings.wan_ip_address || '192.168.1.1');
            $('#wan-subnet-display').text(settings.wan_netmask || '255.255.255.0');
            $('#wan-gateway-display').text(settings.wan_gateway || '192.168.1.1');
            $(".wan-static-ip-display_div").removeClass('hidden');
        }else if (settings.wan_connection_type == 'PPPOE' || settings.wan_connection_type == 'pppoe') {
            $('#wan-connection-type').val(settings.wan_connection_type.toUpperCase());
            $('#wan-type-display').text(settings.wan_connection_type.toUpperCase());
            $("#wan-pppoe-username").text(settings.wan_pppoe_username || '');
            $("#wan-pppoe-password").text(settings.wan_pppoe_password || '');
            $("#wan-pppoe-service-name").text(settings.wan_pppoe_service_name || '');
            $(".wan-pppoe-display_div").removeClass('hidden');
            $(".wan-static-ip-display_div").addClass('hidden');
        } else if (settings.wan_connection_type == 'DHCP' || settings.wan_connection_type == 'dhcp') {
            $('#wan-connection-type').val(settings.wan_connection_type.toUpperCase());
            $('#wan-type-display').text(settings.wan_connection_type.toUpperCase());
            $(".wan-pppoe-display_div").addClass('hidden');
            $(".wan-static-ip-display_div").addClass('hidden');
        }        

        // Populate captive portal settings
        $('#captive-portal-ssid').val(settings.captive_portal_ssid || 'Guest WiFi');
        $('#captive-portal-visible').val(settings.captive_portal_visible ? 1 : 0);
        $('#captive-portal-vlan').val(settings.captive_portal_vlan || '');
        $('#captive-portal-redirect').val(settings.captive_portal_redirect || '');
        $('#captive-portal-vlan-tagging').val(settings.captive_portal_vlan_tagging || 'disabled');
        
        // Global VLAN settings
        $('#vlan-enabled').prop('checked', settings.vlan_enabled || false);
        
        // Enable/disable VLAN settings based on initial value
        toggleVlanFields(settings.vlan_enabled || false);
        
        // Determine authentication method based on settings
        if (settings.captive_portal_enabled) {
            $('#captive-auth-method').val(settings.captive_auth_method || 'click-through');
        } else {
            $('#captive-auth-method').val('click-through');
        }
    
        // Session settings
        $('#captive-session-timeout').val(settings.session_timeout || 60);
        $('#captive-idle-timeout').val(settings.idle_timeout || 15);
        
        // Bandwidth limits - handle null/undefined values properly
        if (settings.download_limit !== undefined && settings.download_limit !== null) {
            $('#captive-download-limit').val(settings.download_limit);
        } else {
            $('#captive-download-limit').val(''); // Default to empty for no limit
        }
        
        if (settings.upload_limit !== undefined && settings.upload_limit !== null) {
            $('#captive-upload-limit').val(settings.upload_limit);
        } else {
            $('#captive-upload-limit').val('0'); // Default to "0" option for no limit
        }
        console.log("captive_portal_designs", captive_portal_designs);
        // Populate captive portal design
        for (var i = 0; i < captive_portal_designs.length; i++) {
            console.log("captive_portal_designs[i]", captive_portal_designs[i]);
            if (settings.captive_portal_design == captive_portal_designs[i].id) {
                $('#captive-portal-design').append('<option value="' + captive_portal_designs[i].id + '" selected>' + captive_portal_designs[i].name + '</option>');
            }else{
                $('#captive-portal-design').append('<option value="' + captive_portal_designs[i].id + '">' + captive_portal_designs[i].name + '</option>');
            }
        }

        // Network settings for captive portal
        $('#captive-ip-display').text(settings.captive_portal_ip || '192.168.2.1');
        $('#captive-netmask-display').text(settings.captive_portal_netmask || '255.255.255.0');
        $('#captive-gateway-display').text(settings.captive_portal_gateway || '192.168.2.1');
        
        $("#captive-portal-ip").val(settings.captive_portal_ip || '192.168.2.1');
        $("#captive-portal-netmask").val(settings.captive_portal_netmask || '255.255.255.0');
        $("#captive-portal-gateway").val(settings.captive_portal_gateway || '192.168.2.1');
        $("#captive_portal_password").val(settings.captive_portal_password || '');
        $("#captive-social-auth-method").val(settings.captive_social_auth_method || 'facebook');

        // Show or hide password auth options and social auth options based on auth method
        if (settings.captive_auth_method == 'password') {
            $("#password-auth-options").removeClass('hidden');
            $("#social-auth-options").addClass('hidden');
        }else if (settings.captive_auth_method == 'social') {
            $("#password-auth-options").addClass('hidden');
            $("#social-auth-options").removeClass('hidden');

            // Select the social media login option
            $("#captive-social-auth-method").val(settings.captive_social_auth_method || 'facebook');
        }
        
        // Populate secured wifi settings
        $('#wifi-ssid').val(settings.wifi_name || 'Home WiFi');
        $('#wifi-password').val(settings.wifi_password || '');
        $('#wifi-security').val(settings.wifi_security_type || 'wpa2-psk');
        $("#password-wifi-ip-type-display").text(settings.password_wifi_ip_type.toUpperCase());
        if (settings.password_wifi_ip_type == 'static') {
        // Secured wifi network settings
            console.log("settings.password_wifi_ip", settings.password_wifi_ip);
            console.log("settings.password_wifi_netmask", settings.password_wifi_netmask);
            console.log("settings.password_wifi_gateway", settings.password_wifi_gateway);
            console.log("settings.password_wifi_dhcp_enabled", settings.password_wifi_dhcp_enabled);
            $('#password-ip-display').text(settings.password_wifi_ip);
            $('#password-netmask-display').text(settings.password_wifi_netmask);
            $('#password-gateway-display').text(settings.password_wifi_gateway);
            $('#password-dhcp-status-display').text(settings.password_wifi_dhcp_enabled ? 'Enabled' : 'Disabled');
            
            $("#password-ip").val(settings.password_wifi_ip || '192.168.1.1');
            $("#password-netmask").val(settings.password_wifi_netmask || '255.255.255.0');
            $("#password-gateway").val(settings.password_wifi_gateway);

            $(".password-ip-assignment-display_div").removeClass('hidden');
        }else{
            $("#password-ip-display").text(settings.password_wifi_ip);
            $("#password-netmask-display").text(settings.password_wifi_netmask);
            $("#password-gateway-display").text(settings.password_wifi_gateway);
            $("#password-dhcp-status-display").text(settings.password_wifi_dhcp_enabled ? 'Enabled' : 'Disabled');
            $("#password-ip-assignment-display_div").addClass('hidden');
        }

        $("#password_wifi_cipher_suites").val(settings.password_wifi_cipher_suites);
        $("#password-wifi-security").val(settings.password_wifi_security);
        $("#password-wifi-ssid").val(settings.password_wifi_ssid);
        $("#password-wifi-password").val(settings.password_wifi_password);
        $("#password-wifi-vlan").val(settings.password_wifi_vlan || '');
        $("#password-wifi-vlan-tagging").val(settings.password_wifi_vlan_tagging || 'disabled');
        
        // Web filtering
        $('#secured-web-filter, #guest-secured-web-filter').prop('checked', settings.web_filter_enabled);
        
        // Add this for password network IP mode
        if (settings.password_wifi_ip_mode) {
            $('#password-ip-assignment').val(settings.password_wifi_ip_mode);
        } else {
            // Default to static if not set
            $('#password-ip-assignment').val('static');
        }

        // Initialize the toggle behavior
        initPasswordNetworkToggle();
        // Populate WAN settings
        $('#wan-connection-type').val(settings.wan_connection_type.toUpperCase());
        $('#wan-type-display').text(settings.wan_connection_type.toUpperCase());
        $('#wan-ip-display').text(settings.wan_ip_address || '192.168.1.1');
        $('#wan-subnet-display').text(settings.wan_netmask || '255.255.255.0');
        $('#wan-gateway-display').text(settings.wan_gateway || '192.168.1.1');
        $('#wan-dns1-display').text(settings.wan_primary_dns || '8.8.8.8');
        $('#wan-dns2-display').text(settings.wan_secondary_dns || '8.8.4.4');

        /// --->>>
        // if (settings.wan_connection_type == 'static') {
        //     // Set text for wan-interface-details like wan-type-display, wan-ip-display, wan-subnet-display, wan-gateway-display, wan-dns1-display, wan-dns2-display
        //     $("#wan-type-display").text('Static');
        //     $("#wan-ip-display").text(settings.wan_ip_address);
        //     $("#wan-subnet-display").text(settings.wan_netmask);
        //     $("#wan-gateway-display").text(settings.wan_gateway);
        //     $("#wan-dns1-display").text(settings.wan_primary_dns);
        //     $("#wan-dns2-display").text(settings.wan_secondary_dns);
        //     // Hide pppoe_display and static_ip_display and ppoe_display
        //     $(".wan-pppoe-display_div").addClass('hidden');
        //     $(".wan-static-ip-display_div").removeClass('hidden');
        // }else if (settings.wan_connection_type == 'pppoe') {
        //     // Set text for wan-interface-details like wan-type-display, wan-ip-display, wan-subnet-display, wan-gateway-display, wan-dns1-display, wan-dns2-display
        //     $("#wan-type-display").text('PPOE');
        //     $("#wan-pppoe-username").text(settings.wan_pppoe_username || '');
        //     $("#wan-pppoe-password").text(settings.wan_pppoe_password || '');
        //     $("#wan-pppoe-service-name").text(settings.wan_pppoe_service_name || '');
        //     $(".wan-pppoe-display_div").removeClass('hidden');
        //     $(".wan-static-ip-display_div").addClass('hidden');
        // }else if (settings.wan_connection_type == 'DHCP') {
        //     $("#wan-type-display").text('DHCP');
        //     $("#wan-ip-display").text(settings.wan_ip_address);
        //     $("#wan-subnet-display").text(settings.wan_netmask);
        //     $("#wan-gateway-display").text(settings.wan_gateway);
        //     $("#wan-dns1-display").text(settings.wan_primary_dns);
        //     $("#wan-dns2-display").text(settings.wan_secondary_dns);
        //     $(".wan-pppoe-display_div").addClass('hidden');
        //     $(".wan-static-ip-display_div").removeClass('hidden');
        // }

        console.log("wan_connection_type", settings.wan_connection_type);
        console.log("wan_ip_address", settings.wan_ip_address);
        console.log("wan_netmask", settings.wan_netmask);
        console.log("wan_gateway", settings.wan_gateway);
        console.log("wan_primary_dns", settings.wan_primary_dns);
        console.log("wan_secondary_dns", settings.wan_secondary_dns);
        // Handle WAN connection type visibility
        handleWanConnectionTypeVisibility(settings.wan_connection_type || 'dhcp');
    }
    
    // Update location map if coordinates are available
    if (location.latitude && location.longitude) {
        updateLocationMap(location);
    }
    
    // Ensure all feather icons are properly rendered
    if (typeof feather !== 'undefined') {
        feather.replace({
            width: 14,
            height: 14
        });
    }
}

// Helper function to update location map
function updateLocationMap(location) {
    if (!location.latitude || !location.longitude) return;
    
    const locationMap = window.locationMap;
    if (!locationMap) return;
    
    locationMap.setView([location.latitude, location.longitude], 13);
    
    // Clear existing markers
    locationMap.eachLayer(function(layer) {
        if (layer instanceof L.Marker) {
            locationMap.removeLayer(layer);
        }
    });
    
    // Add new marker
    L.marker([location.latitude, location.longitude]).addTo(locationMap)
        .bindPopup(`<strong>${location.name}</strong><br>${location.address || ''}`)
        .openPopup();
}

// Initialize the Password WiFi IP assignment toggle behavior
function initPasswordNetworkToggle() {
    // Get the current IP assignment mode from settings
    const ipMode = $('#password-ip-assignment').val() || 'static';
    
    // Initial visibility based on selection
    if (ipMode === 'dhcp' || ipMode === 'DHCP') {
        $('#password-static-fields').addClass('hidden');
        $('#password-dhcp-message').removeClass('hidden');
        $('#password-dhcp-server-fields').addClass('hidden');
    } else {
        $('#password-static-fields').removeClass('hidden');
        $('#password-dhcp-message').addClass('hidden');
        $('#password-dhcp-server-fields').removeClass('hidden');
    }
}

$('#password-ip-assignment').on('change', function() {
    const selectedMode = $(this).val();
    
    if (selectedMode === 'dhcp' || selectedMode === 'DHCP') {
        // Hide static IP fields when DHCP Client is selected
        $('#password-static-fields').slideUp(200);
        $('#password-dhcp-message').slideDown(200);
        $('#password-dhcp-server-fields').addClass('hidden');
        $("#password-static-fields").addClass('hidden');
        // $('#password-dhcp-server-toggle').prop('checked', true);
    } else if (selectedMode === 'static' || selectedMode === 'STATIC') {
        // Show static IP fields when Static IP is selected
        $('#password-static-fields').slideDown(200);
        $('#password-dhcp-message').slideUp(200);
        $('#password-dhcp-server-fields').removeClass('hidden');
        $("#password-static-fields").removeClass('hidden');
    }
});

// Move these event handlers to the document ready section
$(document).ready(function() {

    $("#save-location-info").on('click', function(e) {
        e.preventDefault();
        var location_info = {
            name: $("#location-name").val(),
            address: $("#location-address").val(),
            city: $("#location-city").val(),
            state: $("#location-state").val(),
            country: $("#location-country").val(),
            manager_name: $("#location-manager").val(),
            contact_email: $("#location-contact-email").val(),
            contact_phone: $("#location-contact-phone").val(),
            status: $("#location-status").val(),
            description: $("#location-description").val()
        }

        $.ajax({
            url: '/api/locations/' + location_id,
            type: 'PUT',
            data: {
                settings_type: 'location_info',
                settings: location_info
            },
            headers: {
                'Authorization': 'Bearer ' + UserManager.getToken(),
            },
            success: function(response) {
                console.log("response", response);
                showNotification('success', 'Informations du lieu enregistrées avec succès');
            },
            error: function(xhr, status, error) {
                console.error("Error saving location info:", error);
                showNotification('error', 'Échec de l\'enregistrement des informations du lieu');
            }
        });
    });

    $('#password-network-modal').on('shown.bs.modal', function() {
        initPasswordNetworkToggle();
    });
    
    // Save password network settings
    $(".save-password-network").on('click', function(e) {
        e.preventDefault();
        
        // get the values from the form
        const password_wifi_ip = $("#password-ip").val();
        const password_wifi_netmask = $("#password-netmask").val();
        const password_wifi_gateway = $("#password-gateway").val();
        const password_wifi_broadcast = $("#password-broadcast").val();
        const password_wifi_primary_dns = $("#password-primary-dns").val();
        const password_wifi_secondary_dns = $("#password-secondary-dns").val();
        const password_wifi_dhcp_start = $("#password-dhcp-start").val();
        const password_wifi_dhcp_end = $("#password-dhcp-end").val();
        const password_wifi_lease_time = $("#password-lease-time").val();
        const password_wifi_dhcp_enabled = $("#password-dhcp-server-toggle").is(':checked');
        const password_wifi_ip_mode = $("#password-ip-assignment").val();
        const password_wifi_cipher_suites = $("#password_wifi_cipher_suites").val();
        const password_wifi_security = $("#password-wifi-security").val();
        const password_wifi_ssid = $("#password-wifi-ssid").val();
        const password_wifi_password = $("#password-wifi-password").val();
        const password_wifi_vlan = $("#password-wifi-vlan").val();
        const password_wifi_vlan_tagging = $("#password-wifi-vlan-tagging").val();
        const password_wifi_visible = $("#password-wifi-visible").val();
        // if password_wifi_visible is not set, set it to 1
        if (!password_wifi_visible) {
            password_wifi_visible = 1;
        }
        var settings = {
            password_wifi_ip: password_wifi_ip,
            password_wifi_netmask: password_wifi_netmask,
            password_wifi_gateway: password_wifi_gateway,
            password_wifi_broadcast: password_wifi_broadcast,
            password_wifi_primary_dns: password_wifi_primary_dns,
            password_wifi_secondary_dns: password_wifi_secondary_dns,
            password_wifi_ip_mode: password_wifi_ip_mode,
            password_wifi_cipher_suites: password_wifi_cipher_suites,
            password_wifi_security: password_wifi_security,
            password_wifi_ssid: password_wifi_ssid,
            password_wifi_password: password_wifi_password,
            password_wifi_dhcp_start: password_wifi_dhcp_start,
            password_wifi_dhcp_end: password_wifi_dhcp_end,
            password_wifi_lease_time: password_wifi_lease_time,
            password_wifi_dhcp_enabled: password_wifi_dhcp_enabled,
            password_wifi_vlan: password_wifi_vlan,
            password_wifi_vlan_tagging: password_wifi_vlan_tagging,
            password_wifi_visible: password_wifi_visible
        };
        console.log("settings", settings);
        // make ajax request to save the values 
        $.ajax({
            url: '/api/locations/' + location_id,
            type: 'PUT',
            data: {
                settings_type: 'password_network',
                settings: settings
            },
            headers: {
                'Authorization': 'Bearer ' + UserManager.getToken(),
            },
            success: function(response) {
                console.log("response", response);
                showNotification('success', 'Paramètres du réseau sécurisé enregistrés avec succès');
            },
            error: function(xhr, status, error) {
                console.error("Error saving password network:", error);
                showNotification('error', 'Échec de l\'enregistrement des paramètres du réseau sécurisé');
            }
        });
    });

    // Save captive portal settings
    $(".save-captive-portal").on('click', function(e) {
        e.preventDefault();
        
        // Store button reference but don't change appearance
        const $button = $(this);
        
        // get the values from the form
        const captive_portal_ssid = $("#captive-portal-ssid").val();
        const captive_portal_visible = $("#captive-portal-visible").val();
        const captive_portal_ip = $("#captive-portal-ip").val();
        const captive_portal_netmask = $("#captive-portal-netmask").val();
        const captive_portal_gateway = $("#captive-portal-gateway").val();
        const captive_portal_enabled = $("#captive-portal-enabled").val();
        const session_timeout = $("#captive-session-timeout").val();
        const idle_timeout = $("#captive-idle-timeout").val();
        const download_limit = $("#captive-download-limit").val();
        const upload_limit = $("#captive-upload-limit").val();
        const captive_auth_method = $("#captive-auth-method").val();
        const captive_portal_dns1 = $("#captive-portal-dns1").val();
        const captive_portal_dns2 = $("#captive-portal-dns2").val();
        const captive_download_limit = $("#captive-download-limit").val();
        const captive_upload_limit = $("#captive-upload-limit").val();
        const captive_portal_design = $("#captive-portal-design").val();
        const captive_portal_password = $("#captive_portal_password").val();
        const captive_social_auth_method = $("#captive-social-auth-method").val();
        const captive_portal_vlan = $("#captive-portal-vlan").val();
        const captive_portal_redirect = $("#captive-portal-redirect").val();
        const captive_portal_vlan_tagging = $("#captive-portal-vlan-tagging").val();
    
        settings = {
            captive_portal_ssid: captive_portal_ssid,
            captive_portal_visible: captive_portal_visible,
            captive_portal_ip: captive_portal_ip,
            captive_portal_netmask: captive_portal_netmask,
            captive_portal_gateway: captive_portal_gateway,
            captive_portal_enabled: captive_portal_enabled,
            session_timeout: session_timeout,
            idle_timeout: idle_timeout,
            download_limit: download_limit,
            upload_limit: upload_limit,
            captive_auth_method: captive_auth_method,
            captive_portal_dns1: captive_portal_dns1,
            captive_portal_dns2: captive_portal_dns2,
            captive_download_limit: captive_download_limit,
            captive_upload_limit: captive_upload_limit,
            captive_portal_design: captive_portal_design,
            captive_portal_password: captive_portal_password,
            captive_social_auth_method: captive_social_auth_method,
            captive_portal_vlan: captive_portal_vlan,
            captive_portal_redirect: captive_portal_redirect,
            captive_portal_vlan_tagging: captive_portal_vlan_tagging
        }

        console.log("settings", settings);

        $.ajax({
            url: '/api/locations/' + location_id,
            type: 'PUT',
            data: {
                settings_type: 'captive_portal',
                settings: settings
            },
            headers: {
                'Authorization': 'Bearer ' + UserManager.getToken(),
            },
            success: function(response) {
                console.log("response", response);
                showNotification('success', 'Paramètres du portail captif enregistrés avec succès');
            },
            error: function(xhr, status, error) {
                console.error("Error saving captive portal:", error);
                showNotification('error', 'Échec de l\'enregistrement des paramètres du portail captif');
            }
        });
    });

    // Add event handler for WAN settings save
    $(".save-wan-settings").on('click', function(e) {
        e.preventDefault();
        
        const wan_connection_type = $('#wan-connection-type').val();
        let settings = {
            wan_connection_type: wan_connection_type,
            wan_enabled: true,
            wan_nat_enabled: true,
            wan_ip_address: $('#wan-ip-address').val(),
            wan_netmask: $('#wan-netmask').val(),
            wan_gateway: $('#wan-gateway').val(),
            wan_primary_dns: $('#wan-primary-dns').val(),
            wan_secondary_dns: $('#wan-secondary-dns').val(),
            wan_pppoe_username: $('#wan-pppoe-username').val(),
            wan_pppoe_password: $('#wan-pppoe-password').val(),
            wan_pppoe_service_name: $('#wan-pppoe-service').val()
        };

        // Add fields based on connection type
        if (wan_connection_type === 'static') {
            settings = {
                ...settings,
                wan_ip_address: $('#wan-ip-address').val(),
                wan_netmask: $('#wan-netmask').val(),
                wan_gateway: $('#wan-gateway').val(),
                wan_primary_dns: $('#wan-primary-dns').val(),
                wan_secondary_dns: $('#wan-secondary-dns').val()
            };
        } else if (wan_connection_type === 'pppoe') {
            settings = {
                ...settings,
                wan_pppoe_username: $('#wan-pppoe-username').val(),
                wan_pppoe_password: $('#wan-pppoe-password').val(),
                wan_pppoe_service_name: $('#wan-pppoe-service').val()
            };
        }

        console.log("wan settings", settings);

        $.ajax({
            url: '/api/locations/' + location_id,
            type: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + UserManager.getToken(),
            },
            data: {
                settings_type: 'wan',
                settings: settings
            },
            success: function(response) {
                showNotification('success', 'Paramètres WAN enregistrés avec succès');
                loadLocationDetails(location_id); // Reload location details
            },
            error: function(xhr, status, error) {
                console.error("Error saving WAN settings:", error);
                showNotification('error', 'Échec de l\'enregistrement des paramètres WAN');
            }
        });
    });

    $("#captive-auth-method").on('change', function() {
        if ($(this).val() == 'social') {
            $("#social-auth-options").removeClass('hidden');
            $("#password-auth-options").addClass('hidden');
        }else if ($(this).val() == 'password') {
            $("#social-auth-options").addClass('hidden');
            $("#password-auth-options").removeClass('hidden');
        }
    });

    $("#captive-social-auth-method").on('change', function() {
        console.log("captive social auth method", $(this).val());
    });

    // Handle WAN connection type change
    $('#wan-connection-type').on('change', function() {
        handleWanConnectionTypeVisibility($(this).val());
    });

    $("#toggle-password").on('click', function() {
        if ($("#password-wifi-password").attr('type') === 'password') {
            $("#password-wifi-password").attr('type', 'text');
            $("#toggle-password").find('i').removeClass('feather-eye').addClass('feather-eye-off');
        } else {
            $("#password-wifi-password").attr('type', 'password');
            $("#toggle-password").find('i').removeClass('feather-eye-off').addClass('feather-eye');
        }
    });

    // Save VLAN global settings and toggle VLAN field enabled state
    $("#vlan-enabled").on('change', function() {
        const vlan_enabled = $(this).is(':checked');
        
        // Enable/disable VLAN settings based on toggle
        toggleVlanFields(vlan_enabled);
        
        const settings = {
            vlan_enabled: vlan_enabled
        };
        
        console.log("VLAN enabled setting:", settings);
        
        // Save immediately when the switch is toggled
        $.ajax({
            url: '/api/locations/' + location_id + '/settings',
            type: 'PUT',
            headers: {
                'Authorization': 'Bearer ' + UserManager.getToken(),
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(settings),
            success: function(response) {
                console.log("VLAN global setting saved:", response);
                showNotification('success', 'Support VLAN ' + (vlan_enabled ? 'activé' : 'désactivé') + ' avec succès');
            },
            error: function(xhr, status, error) {
                console.error("Error saving VLAN global setting:", error);
                showNotification('error', 'Échec de l\'enregistrement du paramètre VLAN global');
                // Revert the toggle on error
                $("#vlan-enabled").prop('checked', !vlan_enabled);
                // Revert field states as well
                toggleVlanFields(!vlan_enabled);
            }
        });
    });
    
    // Function to toggle VLAN field enabled/disabled state
    function toggleVlanFields(enabled) {
        const vlanInputs = $('#captive-portal-vlan, #password-wifi-vlan');
        const vlanSelects = $('#captive-portal-vlan-tagging, #password-wifi-vlan-tagging');
        
        if (enabled) {
            // Enable VLAN fields
            vlanInputs.prop('disabled', false);
            vlanSelects.prop('disabled', false);
            
            // Update help text to show normal usage
            $('#captive-portal-vlan').siblings('.text-muted').text('Spécifiez l\'ID VLAN pour la segmentation du réseau du portail captif (1-4094). Laisser vide par défaut.');
            $('#captive-portal-vlan-tagging').siblings('.text-muted').text('Configurez le mode de marquage VLAN pour le réseau du portail captif.');
            $('#password-wifi-vlan').siblings('.text-muted').text('Spécifiez l\'ID VLAN pour la segmentation du réseau (1-4094). Laisser vide par défaut.');
            $('#password-wifi-vlan-tagging').siblings('.text-muted').text('Configurez le mode de marquage VLAN pour le réseau WiFi sécurisé.');
        } else {
            // Disable VLAN fields
            vlanInputs.prop('disabled', true);
            vlanSelects.prop('disabled', true);
            
            // Update help text to indicate VLAN support needs to be enabled
            $('#captive-portal-vlan').siblings('.text-muted').text('Spécifiez l\'ID VLAN pour la segmentation du réseau du portail captif (1-4094). Activez le support VLAN dans les Paramètres du routeur pour utiliser cette fonctionnalité.');
            $('#captive-portal-vlan-tagging').siblings('.text-muted').text('Configurez le mode de marquage VLAN pour le réseau du portail captif. Activez le support VLAN dans les Paramètres du routeur pour utiliser cette fonctionnalité.');
            $('#password-wifi-vlan').siblings('.text-muted').text('Spécifiez l\'ID VLAN pour la segmentation du réseau (1-4094). Activez le support VLAN dans les Paramètres du routeur pour utiliser cette fonctionnalité.');
            $('#password-wifi-vlan-tagging').siblings('.text-muted').text('Configurez le mode de marquage VLAN pour le réseau WiFi sécurisé. Activez le support VLAN dans les Paramètres du routeur pour utiliser cette fonctionnalité.');
        }
    }

    $('#toggle-captive-password').on('click', function() {
        const passwordInput = $('#captive_portal_password');
        const icon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('feather-eye').addClass('feather-eye-off');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('feather-eye-off').addClass('feather-eye');
        }
        
        // Re-render feather icons
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    });

    // Device restart functionality
    $(document).on('click', '#device-restart-btn', function() {
        // Show the restart confirmation modal instead of browser confirm
        $('#restart-confirmation-modal').modal('show');
    });

    // Handle the actual restart when confirmed
    $(document).on('click', '#confirm-restart-btn', function() {
        const button = $(this);
        const originalText = button.html();
        
        // Show loading state
        button.html('<i data-feather="loader" class="mr-1 rotating"></i> Restarting...');
        button.prop('disabled', true);
        
        // Get device ID from current location
        const currentLocationId = location_id;
        
        // Get device ID from current location
        $.ajax({
            url: `/api/locations/${currentLocationId}`,
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${UserManager.getToken()}`,
                'Content-Type': 'application/json'
            },
            success: function(response) {
                if (response.data && response.data.device_id) {
                    // Call device reboot API
                    $.ajax({
                        url: `/api/devices/${response.data.device_id}/reboot`,
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${UserManager.getToken()}`,
                            'Content-Type': 'application/json'
                        },
                        success: function(rebootResponse) {
                            showNotification('success', 'Redémarrage de l\'appareil initié avec succès.');
                            $('#restart-confirmation-modal').modal('hide');
                            
                            // Reload location details after a short delay
                            setTimeout(() => {
                                loadLocationDetails(currentLocationId);
                            }, 2000);
                        },
                        error: function(xhr) {
                            let errorMessage = 'Échec du redémarrage de l\'appareil';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            showNotification('error', errorMessage);
                        },
                        complete: function() {
                            // Restore button state
                            button.html(originalText);
                            button.prop('disabled', false);
                            
                            // Re-initialize feather icons
                            if (typeof feather !== 'undefined') {
                                feather.replace();
                            }
                        }
                    });
                } else {
                    showNotification('error', 'Informations de l\'appareil introuvables');
                    button.html(originalText);
                    button.prop('disabled', false);
                }
            },
            error: function() {
                showNotification('error', 'Échec de la récupération des informations de l\'appareil');
                button.html(originalText);
                button.prop('disabled', false);
            }
        });
    });
});

// Remove these standalone event handlers since they've been moved to $(document).ready

// Helper function to display notifications
function showNotification(type, message) {
    if (typeof toastr !== 'undefined') {
        toastr[type](message, '', {
            closeButton: true,
            tapToDismiss: false,
            progressBar: true,
            timeOut: 5000
        });
    } else {
        console.log(`${type}: ${message}`);
    }
}

// Add this new function to handle WAN fields visibility
function handleWanConnectionTypeVisibility(connectionType) {
    // Hide all fields first
    // $('#wan-static-fields').addClass('hidden');
    // $('#wan-pppoe-fields').addClass('hidden');

    // Show relevant fields based on connection type
    if (connectionType === 'STATIC' || connectionType === 'static') {
        $('#wan-static-fields').removeClass('hidden');
        $('#wan-pppoe-fields').addClass('hidden');
    } else if (connectionType === 'PPPOE' || connectionType === 'pppoe') {
        $('#wan-pppoe-fields').removeClass('hidden');
        $('#wan-static-fields').addClass('hidden');
    } else if (connectionType === 'DHCP' || connectionType === 'dhcp') {
        $('#wan-static-fields').addClass('hidden');
        $('#wan-pppoe-fields').addClass('hidden');
    }
}