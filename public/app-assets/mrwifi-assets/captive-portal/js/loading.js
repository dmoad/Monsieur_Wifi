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

    const parsed = parseNasid(nasid);

    if (!parsed.networkId) {
        showError('Invalid network identifier: ' + nasid);
        return;
    }

    fetchNetworkInfo(parsed, macAddress, challenge, nasIp);
});

/**
 * Parse the nasid parameter into its three components.
 *
 * Supported formats:
 *   - "3-4-7"  → { zoneId: 3, locationId: 4, networkId: 7 }  (new format)
 *   - "n7"     → { zoneId: 0, locationId: 0, networkId: 7 }  (legacy n{network_id})
 *   - "l4"     → { zoneId: 0, locationId: 4, networkId: 4 }  (legacy l{location_id})
 *   - "7"      → { zoneId: 0, locationId: 0, networkId: 7 }  (plain numeric fallback)
 */
function parseNasid(nasid) {
    // New format: three dash-separated integers  zone_id-location_id-network_id
    const tripleMatch = nasid.match(/^(\d+)-(\d+)-(\d+)$/);
    if (tripleMatch) {
        return {
            zoneId:     parseInt(tripleMatch[1], 10),
            locationId: parseInt(tripleMatch[2], 10),
            networkId:  tripleMatch[3],
        };
    }

    // Plain integer
    if (/^\d+$/.test(nasid)) {
        return { zoneId: 0, locationId: 0, networkId: nasid };
    }

    // Legacy prefixed: n{network_id} or l{location_id}
    const prefixMatch = nasid.match(/^([nl])(\d+)$/);
    if (prefixMatch) {
        const isLocation = prefixMatch[1] === 'l';
        const id         = prefixMatch[2];
        return {
            zoneId:     0,
            locationId: isLocation ? parseInt(id, 10) : 0,
            networkId:  id,
        };
    }

    return { zoneId: 0, locationId: 0, networkId: null };
}

/**
 * Fetch network/captive-portal info from the API.
 */
function fetchNetworkInfo(parsed, macAddress, challenge, nasIp) {
    fetch(`/api/captive-portal/${parsed.networkId}/info`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                processNetworkData(data.location, parsed, macAddress, challenge, nasIp);
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
 * Store network data and redirect to the correct login page based on auth method(s).
 */
function processNetworkData(location, parsed, macAddress, challenge, nasIp) {
    if (!location || !location.settings) {
        showError('Invalid network data');
        return;
    }

    const settings = location.settings;
    const design   = location.design || {};

    console.log('Network data:', location);
    console.log('Design data:', design);
    console.log('Parsed nasid:', parsed);

    localStorage.setItem('location_data', JSON.stringify(location));
    localStorage.setItem('design_data', JSON.stringify(design));
    localStorage.setItem('nas_ip', nasIp);
    localStorage.setItem('challenge', challenge);
    localStorage.setItem('zone_id', parsed.zoneId);
    localStorage.setItem('location_id', parsed.locationId);

    const redirectUrl = settings.captive_portal_redirect || 'https://citypassenger.com';
    localStorage.setItem('captive_portal_redirect', redirectUrl);

    if (!settings.captive_portal_enabled) {
        showError('Captive portal is not enabled for this network');
        return;
    }

    // location.id is the network_id returned by the info() endpoint
    const networkId = location.id;
    const zoneId    = parsed.zoneId;

    // Resolve the ordered list of methods. captive_auth_methods (array) is the new field;
    // fall back to the legacy single captive_auth_method string for older data.
    const methods = (settings.captive_auth_methods && settings.captive_auth_methods.length)
        ? settings.captive_auth_methods
        : [settings.captive_auth_method || 'click-through'];

    if (methods.length > 1) {
        // More than one method — show the selection page and let the user choose.
        window.location.href = `/login-select/${networkId}/${zoneId}/${macAddress}`;
    } else {
        redirectToMethod(methods[0], settings, networkId, zoneId, macAddress);
    }
}

/**
 * Redirect to the appropriate login page for a single auth method.
 * Shared by loading.js (single-method path) and login-select.blade.php (after user picks).
 */
function redirectToMethod(method, settings, networkId, zoneId, macAddress) {
    switch (method) {
        case 'email':
            window.location.href = `/email-login/${networkId}/${zoneId}/${macAddress}`;
            break;
        case 'sms':
            window.location.href = `/sms-login/${networkId}/${zoneId}/${macAddress}`;
            break;
        case 'social':
            if ((settings.captive_social_auth_method || '').includes('facebook')) {
                window.location.href = `/social-login/facebook/${networkId}/${zoneId}/${macAddress}`;
            } else if ((settings.captive_social_auth_method || '').includes('google')) {
                window.location.href = `/social-login/google/${networkId}/${zoneId}/${macAddress}`;
            }
            break;
        case 'password':
            window.location.href = `/password-login/${networkId}/${zoneId}/${macAddress}`;
            break;
        case 'click-through':
        default:
            window.location.href = `/click-login/${networkId}/${zoneId}/${macAddress}`;
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
