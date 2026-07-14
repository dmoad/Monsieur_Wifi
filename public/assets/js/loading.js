document.addEventListener('DOMContentLoaded', function() {
    // Re-render the working-hours block in the newly selected language when the
    // guest uses the language switcher. This listener runs after the inline
    // switchLanguage handler (registered earlier in the page), which has already
    // persisted the new language, so buildWorkingHoursMessage() reads the right one.
    document.querySelectorAll('.language-btn').forEach(function (btn) {
        btn.addEventListener('click', renderBlockedMessage);
    });

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
 * Store network data and redirect to the correct login page based on auth method.
 */
function processNetworkData(location, parsed, macAddress, challenge, nasIp) {
    if (!location || !location.settings) {
        showError('Invalid network data');
        return;
    }

    const settings = location.settings;
    const design   = location.design || {};

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

    // Working-hours enforcement: block before redirecting to any login page.
    const workingHours = location.working_hours;
    if (workingHours && workingHours.enabled === false) {
        blockedWorkingHours = workingHours;
        renderBlockedMessage();
        return;
    }

    // location.id is the network_id returned by the info() endpoint
    const networkId = location.id;
    const zoneId    = parsed.zoneId;
    const methods = (settings.captive_auth_methods && settings.captive_auth_methods.length)
        ? settings.captive_auth_methods
        : [settings.captive_auth_method || 'click-through'];

    if (methods.length > 1) {
        window.location.href = `/login-select/${networkId}/${zoneId}/${macAddress}`;
        return;
    }

    const authMethod = methods[0];
    switch (authMethod) {
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

/**
 * Bilingual (EN/FR) strings for the working-hours "disabled" message.
 * {start} / {end} are substituted with today's configured window.
 */
const workingHoursTranslations = {
    en: {
        disabled:    'WiFi login is currently unavailable due to working hours.',
        availToday:  'Available today {start}–{end}.',
        overnight:   'Available from {start} until {end}.',
    },
    fr: {
        disabled:    'La connexion WiFi est actuellement indisponible en raison des heures d’ouverture.',
        availToday:  'Disponible aujourd’hui de {start} à {end}.',
        overnight:   'Disponible de {start} jusqu’à {end}.',
    },
};

/**
 * Resolve the portal language — mirrors the logic in guest-login.blade.php:
 * localStorage preference first, then browser language, fallback English.
 */
function getPortalLanguage() {
    const stored = localStorage.getItem('wifiPortalLanguage');
    if (stored === 'en' || stored === 'fr') {
        return stored;
    }
    const browserLang = (navigator.language || navigator.userLanguage || 'en').toLowerCase().split('-')[0];
    return browserLang === 'fr' ? 'fr' : 'en';
}

/**
 * Compose the "login disabled" message, appending today's available window
 * when both start and end times are configured.
 */
function buildWorkingHoursMessage(workingHours) {
    const lang = getPortalLanguage();
    const t = workingHoursTranslations[lang] || workingHoursTranslations.en;

    let message = t.disabled;

    const start = workingHours.start_time;
    const end   = workingHours.end_time;
    if (start && end) {
        // Overnight window when the end time is earlier than the start time.
        const template = end < start ? t.overnight : t.availToday;
        message += ' ' + template.replace('{start}', start).replace('{end}', end);
    }

    return message;
}

/**
 * The working-hours payload currently being displayed as a block message, or
 * null when we're not blocking. Kept so the message can be re-rendered in the
 * newly selected language when the guest uses the language switcher.
 */
let blockedWorkingHours = null;

/**
 * Render (or re-render) the working-hours block message in the current language.
 * No-op unless we're actually blocking.
 */
function renderBlockedMessage() {
    if (!blockedWorkingHours) return;
    showBlocked(buildWorkingHoursMessage(blockedWorkingHours));
}

/**
 * Display a neutral "blocked" message (no "Error:" prefix, no spinner).
 */
function showBlocked(message) {
    const loadingSpinner = document.querySelector('.loading-spinner');
    const loadingText    = document.querySelector('.loading-text');

    if (loadingSpinner) loadingSpinner.style.display = 'none';

    if (loadingText) {
        // Detach from the i18n system so the language switcher's applyTranslations
        // (which only touches [data-i18n] nodes) can't overwrite this message back
        // to the "loading" string.
        loadingText.removeAttribute('data-i18n');
        loadingText.className   = 'loading-text';
        loadingText.textContent = message;
        loadingText.style.color = '#666';
    }
}

function showError(message) {
    const loadingSpinner = document.querySelector('.loading-spinner');
    const loadingText    = document.querySelector('.loading-text');

    if (loadingSpinner) loadingSpinner.style.display = 'none';

    if (loadingText) {
        // Detach from i18n so a language switch can't overwrite the error text.
        loadingText.removeAttribute('data-i18n');
        loadingText.className   = 'loading-text error';
        loadingText.textContent = `Error: ${message}`;
        loadingText.style.color = '#e53e3e';
    }
}
