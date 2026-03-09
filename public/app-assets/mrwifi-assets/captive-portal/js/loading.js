document.addEventListener('DOMContentLoaded', function() {
    const urlParams  = new URLSearchParams(window.location.search);
    const nasid      = urlParams.get('nasid');
    const macAddress = urlParams.get('mac');
    const challenge  = urlParams.get('challenge');
    const nasIp      = urlParams.get('uamip');

    if (!nasid) {
        showError('Missing network identifier');
        return;
    }

    const networkId = extractNetworkId(nasid);

    if (!networkId) {
        showError('Invalid network identifier: ' + nasid);
        return;
    }

    fetchNetworkInfo(networkId, macAddress, challenge, nasIp);
});

/**
 * Extract the network ID from the nasid parameter.
 * Supported formats:
 *   - "n7"   → 7   (new format: n{network_id})
 *   - "7"    → 7   (plain numeric fallback)
 *   - "l4"   → 4   (legacy location format — still accepted during transition)
 */
function extractNetworkId(nasid) {
    if (/^\d+$/.test(nasid)) {
        return nasid;
    }
    const match = nasid.match(/^[nl](\d+)$/);
    return match ? match[1] : null;
}

/**
 * Fetch network/captive-portal info from the API.
 */
function fetchNetworkInfo(networkId, macAddress, challenge, nasIp) {
    fetch(`/api/captive-portal/${networkId}/info`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                processNetworkData(data.location, macAddress, challenge, nasIp);
            } else {
                showError(data.message || 'Could not fetch network data');
            }
        })
        .catch(error => {
            console.error('Error fetching network data:', error);
            showError('Failed to load network data');
        });
}

/**
 * Store network data and redirect to the correct login page based on auth method.
 */
function processNetworkData(location, macAddress, challenge, nasIp) {
    if (!location || !location.settings) {
        showError('Invalid network data');
        return;
    }

    const settings = location.settings;
    const design   = location.design || {};

    console.log('Network data:', location);
    console.log('Design data:', design);

    localStorage.setItem('location_data', JSON.stringify(location));
    localStorage.setItem('design_data', JSON.stringify(design));
    localStorage.setItem('nas_ip', nasIp);
    localStorage.setItem('challenge', challenge);

    const redirectUrl = settings.captive_portal_redirect || 'https://citypassenger.com';
    localStorage.setItem('captive_portal_redirect', redirectUrl);

    if (!settings.captive_portal_enabled) {
        showError('Captive portal is not enabled for this network');
        return;
    }

    // location.id is now the network_id returned by the info() endpoint
    const networkId   = location.id;
    const authMethod  = settings.captive_auth_method;

    switch (authMethod) {
        case 'email':
            window.location.href = `/email-login/${networkId}/${macAddress}`;
            break;
        case 'sms':
            window.location.href = `/sms-login/${networkId}/${macAddress}`;
            break;
        case 'social':
            if ((settings.captive_social_auth_method || '').includes('facebook')) {
                window.location.href = `/social-login/facebook/${networkId}/${macAddress}`;
            } else if ((settings.captive_social_auth_method || '').includes('google')) {
                window.location.href = `/social-login/google/${networkId}/${macAddress}`;
            }
            break;
        case 'password':
            window.location.href = `/password-login/${networkId}/${macAddress}`;
            break;
        case 'click-through':
        default:
            window.location.href = `/click-login/${networkId}/${macAddress}`;
            break;
    }
}

function showError(message) {
    const loadingSpinner = document.querySelector('.loading-spinner');
    const loadingText    = document.querySelector('.loading-text');

    if (loadingSpinner) loadingSpinner.style.display = 'none';

    if (loadingText) {
        loadingText.className   = 'loading-text error';
        loadingText.textContent = `Error: ${message}`;
        loadingText.style.color = '#e53e3e';
    }
}
