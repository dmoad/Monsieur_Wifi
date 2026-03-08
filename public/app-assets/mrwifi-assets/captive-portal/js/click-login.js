$(document).ready(function() {
    // URL format: /click-login/{network_id}/{mac_address}
    let networkId, macAddress;
    let locationData = JSON.parse(localStorage.getItem('location_data') || '{}');
    let designData   = JSON.parse(localStorage.getItem('design_data'));

    console.log('locationData', locationData);
    console.log('designData', designData);

    const pathSegments = window.location.pathname.split('/').filter(s => s.length > 0);
    if (pathSegments.length >= 3 && pathSegments[0] === 'click-login') {
        networkId  = pathSegments[1];
        macAddress = pathSegments[2];
    } else {
        const urlParams = new URLSearchParams(window.location.search);
        networkId  = urlParams.get('network_id');
        macAddress = urlParams.get('mac_address');
    }

    function handleLogin(e) {
        if (e) e.preventDefault();

        const $loginButton       = $('.login-button');
        const originalButtonText = $loginButton.text();
        $loginButton.text('Connecting...').prop('disabled', true);

        const challenge      = localStorage.getItem('challenge');
        const locationDataObj = JSON.parse(localStorage.getItem('location_data') || '{}');
        const ipAddress      = locationDataObj.ip_address;

        const loginData = {
            network_id:   networkId,
            mac_address:  macAddress,
            login_method: 'click-through',
            challenge:    challenge,
            ip_address:   ipAddress,
        };

        console.log('Login data:', loginData);

        $.ajax({
            url:  '/api/guest/login',
            type: 'POST',
            data: loginData,
            success: function(data) {
                console.log('Login response:', data);

                if (data.success) {
                    $loginButton.text('Connected!').removeClass('btn-primary').addClass('btn-success');
                    showAlert('Successfully connected to WiFi', 'success');
                    setTimeout(function() { window.location.href = data.login_url; }, 2000);
                } else {
                    $loginButton.text('Login Failed').removeClass('btn-primary').addClass('btn-danger');
                    showAlert(data.message || 'Failed to connect to WiFi', 'danger');
                    setTimeout(function() {
                        $loginButton.text(originalButtonText).removeClass('btn-danger').addClass('btn-primary').prop('disabled', false);
                    }, 3000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error during login:', error);
                $('.login-button').text('Connection Error').removeClass('btn-primary').addClass('btn-danger');
                const errorMessage = xhr.responseJSON?.message || 'Error connecting to WiFi';
                showAlert(errorMessage, 'danger');
                setTimeout(function() {
                    $('.login-button').text(originalButtonText).removeClass('btn-danger').addClass('btn-primary').prop('disabled', false);
                }, 3000);
            }
        });
    }

    function showAlert(message, type) {
        if ($('#alert-container').length === 0) {
            $('<div id="alert-container" style="margin-bottom: 20px;"></div>').insertBefore('#login-form');
        }
        $('#alert-container').html(`<div class="alert alert-${type}" role="alert">${message}</div>`).show();
        if (type === 'success') {
            setTimeout(function() { $('#alert-container').fadeOut(); }, 5000);
        }
    }

    // Refresh challenge/ip from the network info endpoint
    $.ajax({
        url:     `/api/captive-portal/${networkId}/info`,
        type:    'GET',
        data:    { mac_address: macAddress },
        headers: { 'Accept': 'application/json' },
        success: function(info) {
            console.log('Network info:', info);
            if (info.success && info.location) {
                localStorage.setItem('location_data', JSON.stringify(info.location));
                localStorage.setItem('challenge', info.location.challenge);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching network info:', error);
            showAlert('Error loading WiFi information. Please refresh the page.', 'danger');
        }
    });

    $('form').on('submit', handleLogin);
    $('.login-button').on('click', handleLogin);

    if (!networkId || !macAddress) {
        $('.portal-container').html(`
            <div class="text-center">
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading">Error</h4>
                    <p>Required information is missing. Please check your connection or contact support.</p>
                </div>
            </div>`);
    }
});
