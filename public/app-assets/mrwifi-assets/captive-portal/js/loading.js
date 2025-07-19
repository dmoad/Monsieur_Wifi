document.addEventListener('DOMContentLoaded', function() {
    // Extract location ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const nasid = urlParams.get('nasid');
    const mac_address = urlParams.get('mac');
    const challenge = urlParams.get('challenge');
    const nas_ip = urlParams.get('uamip');
    if (!nasid) {
        showError('Missing location identifier');
        return;
    }

    // Extract location ID from nasid
    const locationId = extractLocationId(nasid);
    
    if (!locationId) {
        showError('Invalid location identifier: ' + nasid+" locationId :: "+locationId);
        return;
    }
    // Fetch location details
    fetchLocationInfo(locationId, mac_address, challenge, nas_ip);
});

/**
 * Extract location ID from nasid parameter
 * @param {string} nasid - The nasid parameter from URL
 * @returns {string|null} - The extracted location ID or null if invalid
 */
function extractLocationId(nasid) {
    // Assuming nasid format contains location ID
    // This may need to be adjusted based on your actual nasid format
    // If nasid is just a number, return it directly
    if (/^\d+$/.test(nasid)) {
        return nasid;
    }
    // Otherwise try to extract from l{number} format
    const match = nasid.match(/l(\d+)/);
    return match ? match[1] : null;
}

/**
 * Fetch location info from API
 * @param {string} locationId - Location ID
 */
function fetchLocationInfo(locationId, mac_address, challenge, nas_ip) {
    fetch(`/api/captive-portal/${locationId}/info`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                processLocationData(data.location, mac_address, challenge, nas_ip);
            } else {
                showError(data.message || 'Could not fetch location data');
            }
        })
        .catch(error => {
            console.error('Error fetching location data:', error);
            showError('Failed to load location data');
        });
}

/**
 * Process location data and redirect based on auth method
 * @param {Object} location - Location data from API
 * @param {string} mac_address - MAC address
 * @param {string} challenge - Challenge
 */
function processLocationData(location, mac_address, challenge, nas_ip) {
    if (!location || !location.settings) {
        showError('Invalid location data');
        return;
    }
    
    const settings = location.settings;
    const design = location.design || {};
    
    console.log('Location data:', location);
    console.log('Design data:', design);
    
    // Save complete location data including design in localStorage
    localStorage.setItem('location_data', JSON.stringify(location));
    localStorage.setItem('design_data', JSON.stringify(design));
    localStorage.setItem('nas_ip', nas_ip);
    localStorage.setItem('challenge', challenge);
    // if redirect_url is set, then redirect to redirect_url else set it to citypassenger.com
    const redirect_url = settings.captive_portal_redirect || 'https://citypassenger.com';
    localStorage.setItem('captive_portal_redirect', redirect_url);
    // Check if captive portal is enabled
    if (!settings.captive_portal_enabled) {
        showError('Captive portal is not enabled for this location');
        return;
    }

    // Determine login method from captive_auth_method
    const authMethod = settings.captive_auth_method;
    // alert(authMethod);
    // Redirect based on auth method
    switch (authMethod) {
        case 'email':
            window.location.href = `/email-login/${location.id}/${mac_address}`;
            break;
        case 'sms':
            console.log('SMS login');
            console.log('location', location);
            window.location.href = `/sms-login/${location.id}/${mac_address}`;
            break;
        case 'social':
            var social_logins = location.settings.captive_social_auth_method
            // alert('social_logins' + social_logins);
            if (social_logins.includes('facebook')) {
                window.location.href = `/social-login/facebook/${location.id}/${mac_address}`;
            } else if (social_logins.includes('google')) {
                window.location.href = `/social-login/google/${location.id}/${mac_address}`;
            }
            break;
        case 'click-through':
            window.location.href = `/click-login/${location.id}/${mac_address}`;
            break;
        case 'password':
            window.location.href = `/password-login/${location.id}/${mac_address}`;
            break;
        default:
            // Default to click-through if auth method is not recognized
            window.location.href = `/click-login/${location.id}/${mac_address}`;
    }
}

/**
 * Show error message in the UI
 * @param {string} message - Error message to display
 */
function showError(message) {
    const loadingSpinner = document.querySelector('.loading-spinner');
    const loadingText = document.querySelector('.loading-text');
    
    if (loadingSpinner) {
        loadingSpinner.style.display = 'none';
    }
    
    if (loadingText) {
        loadingText.className = 'loading-text error';
        loadingText.textContent = `Error: ${message}`;
        loadingText.style.color = '#e53e3e';
    }
}
