/**
 * MrWiFi Configuration and Utility Functions
 * This file contains global configuration variables and utility functions
 * for API communication, authentication, and user management.
 */

// Core application configuration
const APP_CONFIG = {
    // API Configuration
    API: {
        BASE_URL: ['localhost', '127.0.0.1'].includes(window.location.hostname)
            ? `${window.location.protocol}//${window.location.host}/api`
            : 'https://predev.monsieur-wifi.com/api',
        VERSION: 'v1',
        TIMEOUT: 30000, // 30 seconds
    },
    
    
    // Authentication settings
    AUTH: {
        TOKEN_KEY: 'mrwifi_auth_token',
        USER_KEY: 'mrwifi_user',
        REFRESH_INTERVAL: 1800000, // 30 minutes
    },
    
    // Application settings
    APP: {
        NAME: 'MrWiFi',
        THEME: 'light',
        DATE_FORMAT: 'YYYY-MM-DD',
        TIME_FORMAT: 'HH:mm:ss',
    }
};

// User Management Functions
const UserManager = {
    /**
     * Sets the current user in local storage
     * @param {Object} user - User data from API
     */
    setUser: function(user) {
        if (!user) return;
        console.log("config user: ", user);
        
        localStorage.setItem(APP_CONFIG.AUTH.USER_KEY, JSON.stringify({
            id: user.id,
            name: user.name,
            email: user.email,
            profile_picture: user.profile_picture,
            role: user.role,
            platform_role: user.platform_role || 'user',
            features: user.features || [],
            entitlements: user.entitlements || null,
            last_active: new Date().toISOString()
        }));
        
        // Update UI elements with user info
        this.updateUserUI(user);
    },
    
    /**
     * Gets the current user from local storage
     * @returns {Object|null} The user object or null if not logged in
     */
    getUser: function() {
        const userData = localStorage.getItem(APP_CONFIG.AUTH.USER_KEY);
        console.log("config userData: ", userData);
        return userData ? JSON.parse(userData) : null;
    },
    
    /**
     * Updates UI elements with user information
     * @param {Object} user - User data 
     */
    updateUserUI: function(user) {
        // Update username displays
        const userNameElements = document.querySelectorAll('.user-name');
        userNameElements.forEach(el => {
            el.textContent = user.name;
        });
        
        // Update user role indicators
        const userRoleElements = document.querySelectorAll('.user-role');
        userRoleElements.forEach(el => {
            el.textContent = user.role;
        });
        
        // Update user profile images if available
        if (user.profile_image) {
            const userImageElements = document.querySelectorAll('.user-image');
            userImageElements.forEach(el => {
                el.src = user.profile_image;
            });
        }
    },
    
    /**
     * Checks if the user has a specific role
     * @param {string} role - The role to check
     * @returns {boolean} - True if user has the specified role
     */
    hasRole: function(role) {
        const user = this.getUser();
        return user && user.role === role;
    },
    
    /**
     * Checks if the user is a superadmin
     * @returns {boolean} - True if user is superadmin
     */
    isSuperAdmin: function() {
        const user = this.getUser();
        return user && user.platform_role === 'superadmin';
    },

    isAdmin: function() {
        const user = this.getUser();
        return user && ['admin', 'superadmin'].includes(user.platform_role);
    },

    isAdminOrAbove: function() {
        const user = this.getUser();
        return user && ['admin', 'superadmin'].includes(user.platform_role);
    },

    hasFeature: function(feature) {
        const user = this.getUser();
        return user && Array.isArray(user.features) && user.features.includes(feature);
    },

    getEntitlements: function() {
        const user = this.getUser();
        return user ? user.entitlements : null;
    },

    getPlan: function() {
        const e = this.getEntitlements();
        return e ? e.plan : 'free';
    },

    getLimit: function(key) {
        const e = this.getEntitlements();
        if (!e || !e.limits) return 0;
        return e.limits[key] ?? 0;
    },

    /**
     * Check if org is within a limit. -1 means unlimited.
     */
    withinLimit: function(key, currentCount) {
        const max = this.getLimit(key);
        return max === -1 || currentCount < max;
    },

    /**
     * Logs the user out
     * @param {boolean} redirect - Whether to redirect to root page
     */
    logout: function(redirect = true) {
        // Clear authentication data
        localStorage.removeItem(APP_CONFIG.AUTH.TOKEN_KEY);
        localStorage.removeItem(APP_CONFIG.AUTH.USER_KEY);

        if (redirect) {
            // Submit POST to Zitadel logout route (destroys session + redirects to Zitadel end_session)
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/auth/logout';
            const csrf = document.querySelector('meta[name="csrf-token"]');
            if (csrf) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_token';
                input.value = csrf.getAttribute('content');
                form.appendChild(input);
            }
            document.body.appendChild(form);
            form.submit();
        }
    },
    
    /**
     * Gets the current organization ID
     * @returns {number|null} The org ID or null
     */
    getOrgId: function() {
        const user = this.getUser();
        return user ? user.current_org_id : null;
    },

    /**
     * Gets the current organization name
     * @returns {string|null} The org name or null
     */
    getOrgName: function() {
        const user = this.getUser();
        return user ? user.current_org_name : null;
    },

    /**
     * Gets all organizations the user has access to
     * @returns {Array} List of org objects
     */
    getOrganizations: function() {
        const user = this.getUser();
        return user && user.organizations ? user.organizations : [];
    },

    /**
     * Switches to a different organization and reloads
     * @param {number} orgId - The organization ID to switch to
     */
    switchOrg: async function(orgId) {
        try {
            const token = this.getToken();
            const resp = await fetch('/api/organizations/switch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token,
                },
                body: JSON.stringify({ organization_id: orgId }),
            });
            if (!resp.ok) return false;
            const data = await resp.json();
            if (data.organization) {
                const user = this.getUser();
                user.current_org_id = data.organization.id;
                user.current_org_name = data.organization.name;
                user.role = data.organization.role;
                localStorage.setItem(APP_CONFIG.AUTH.USER_KEY, JSON.stringify(user));
                window.location.reload();
                return true;
            }
            return false;
        } catch (e) {
            return false;
        }
    },

    /**
     * Gets the auth token from local storage
     * @returns {string|null} The token or null if not available
     */
    getToken: function() {
        return localStorage.getItem(APP_CONFIG.AUTH.TOKEN_KEY);
    },
    
    /**
     * Sets the auth token in local storage
     * @param {string} token - The authentication token
     */
    setToken: function(token) {
        if (token) {
            localStorage.setItem(APP_CONFIG.AUTH.TOKEN_KEY, token);
        }
    },
    
    /**
     * Attempts to refresh the access token via the server-side refresh endpoint.
     * @returns {Promise<boolean>} True if refresh succeeded
     */
    refreshToken: async function() {
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]');
            const resp = await fetch('/auth/refresh', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf ? csrf.getAttribute('content') : '',
                },
            });
            if (!resp.ok) return false;
            const data = await resp.json();
            if (data.token) {
                this.setToken(data.token);
                return true;
            }
            return false;
        } catch (e) {
            return false;
        }
    },

    /**
     * Clears all authentication data without redirecting
     * Useful for registration and login pages
     * Preserves user preferences like language
     */
    clearAuth: function() {
        console.log('Clearing all authentication data');
        
        // Preserve language preference before clearing
        const savedLang = localStorage.getItem('preferred_language');
        
        // Clear all authentication related data
        localStorage.removeItem(APP_CONFIG.AUTH.TOKEN_KEY);
        localStorage.removeItem(APP_CONFIG.AUTH.USER_KEY);
        localStorage.removeItem('profile_picture');
        localStorage.removeItem('user');
        localStorage.removeItem('token');
        localStorage.removeItem('access_token');
        sessionStorage.clear();
        
        // Restore language preference
        if (savedLang) {
            localStorage.setItem('preferred_language', savedLang);
            console.log('Language preference preserved:', savedLang);
        }
        
        console.log('Authentication data cleared');
    }
};

// API Utility Functions
const ApiService = {
    /**
     * Makes an authenticated API request
     * @param {string} endpoint - API endpoint
     * @param {Object} options - Fetch options
     * @returns {Promise} - Fetch promise
     */
    request: async function(endpoint, options = {}) {
        const url = `${APP_CONFIG.API.BASE_URL}/${APP_CONFIG.API.VERSION}/${endpoint}`;
        
        // Set default headers
        const headers = options.headers || {};
        headers['Content-Type'] = headers['Content-Type'] || 'application/json';
        
        // Add auth token if available
        const token = UserManager.getToken();
        if (!token) {
            // Redirect to Zitadel login if no token exists
            window.location.href = '/auth/login';
            throw new Error('No authentication token found');
        }
        
        headers['Authorization'] = `Bearer ${token}`;

        // Add current org context
        const orgId = UserManager.getOrgId();
        if (orgId) {
            headers['X-Org-Id'] = orgId;
        }

        // Setup request options
        const requestOptions = {
            ...options,
            headers,
            timeout: APP_CONFIG.API.TIMEOUT
        };
        
        try {
            const response = await fetch(url, requestOptions);
            
            // Handle unauthorized responses — try to refresh token first
            if (response.status === 401) {
                const refreshed = await UserManager.refreshToken();
                if (refreshed) {
                    // Retry the original request with the new token
                    headers['Authorization'] = `Bearer ${UserManager.getToken()}`;
                    const retry = await fetch(url, { ...requestOptions, headers });
                    if (retry.ok) {
                        return await retry.json();
                    }
                }
                UserManager.logout(true);
                throw new Error('Session expired. Please log in again.');
            }
            
            // Parse JSON response
            const data = await response.json();
            
            // Handle API errors
            if (!response.ok) {
                throw new Error(data.message || 'API request failed');
            }
            
            return data;
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    },
    
    /**
     * GET request shorthand
     * @param {string} endpoint - API endpoint
     * @param {Object} params - URL parameters
     * @returns {Promise} - API response
     */
    get: function(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;
        
        return this.request(url, { method: 'GET' });
    },
    
    /**
     * POST request shorthand
     * @param {string} endpoint - API endpoint
     * @param {Object} data - Request body data
     * @returns {Promise} - API response
     */
    post: function(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },
    
    /**
     * PUT request shorthand
     * @param {string} endpoint - API endpoint
     * @param {Object} data - Request body data
     * @returns {Promise} - API response
     */
    put: function(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },
    
    /**
     * DELETE request shorthand
     * @param {string} endpoint - API endpoint
     * @returns {Promise} - API response
     */
    delete: function(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    }
};

// Initialize event listeners when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Set up logout button handlers
    const logoutButtons = document.querySelectorAll('.logout-button');
    logoutButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            UserManager.logout(true);
        });
    });
    
    // Update UI with current user info if logged in
    const currentUser = UserManager.getUser();
    if (currentUser) {
        UserManager.updateUserUI(currentUser);
    }

    if(currentUser) {
    // if user is admin, show admin menu items
        if (currentUser.role === 'admin') {
            $('.only_admin').removeClass('hidden');
        }

        // User menu role badge
        const userMenuRole = document.getElementById('user-menu-role');
        if (userMenuRole && currentUser.role) {
            const ROLE_LABELS = { owner: 'Owner', admin: 'Admin', operator: 'Operator', viewer: 'Viewer', partner: 'Partner' };
            const ROLE_BG = { owner: '#7367f0', admin: '#ff9f43', operator: '#28c76f', viewer: '#82868b', partner: '#00cfe8' };
            const role = currentUser.role;
            const label = ROLE_LABELS[role] || role;
            const bg = ROLE_BG[role] || '#82868b';
            userMenuRole.innerHTML = '<span style="display:inline-block;padding:2px 8px;border-radius:4px;background:' + bg + '14;color:' + bg + ';font-size:12px;font-weight:600;text-transform:capitalize;">' + label + '</span>';
        }

        // Initialize org switcher
        const orgs = UserManager.getOrganizations();
        const orgSwitcher = document.getElementById('org-switcher');
        const orgList = document.getElementById('org-list');
        const orgNameEl = orgSwitcher ? orgSwitcher.querySelector('.org-name') : null;
        const orgAvatarEl = document.getElementById('org-avatar');
        const orgSearch = document.getElementById('org-search');

        const ROLE_COLORS = {
            owner: '#7367f0', admin: '#ff9f43', operator: '#28c76f',
            viewer: '#82868b', partner: '#00cfe8', none: '#b0b0b0'
        };

        const ORG_PALETTE = ['#7367f0','#ff9f43','#28c76f','#00cfe8','#ea5455','#a855f7','#3b82f6','#f97316'];

        function orgInitials(name) {
            const words = (name || '?').trim().split(/\s+/);
            return words.length >= 2 ? (words[0][0] + words[1][0]).toUpperCase() : name.substring(0, 2).toUpperCase();
        }

        function orgColor(id) {
            return ORG_PALETTE[(parseInt(id) || 0) % ORG_PALETTE.length];
        }

        function renderOrgList(filter) {
            if (!orgList) return;
            orgList.innerHTML = '';
            const q = (filter || '').toLowerCase();
            orgs.forEach(function(org) {
                if (q && org.name.toLowerCase().indexOf(q) === -1) return;
                const isActive = org.id == currentUser.current_org_id;
                const role = org.role || 'none';
                const roleColor = ROLE_COLORS[role] || ROLE_COLORS.none;
                const bg = orgColor(org.id);
                const a = document.createElement('a');
                a.className = 'dropdown-item d-flex align-items-center' + (isActive ? ' active' : '');
                a.href = 'javascript:void(0);';
                a.style.cssText = 'padding:6px 8px;gap:10px;cursor:pointer;border-radius:6px;margin:1px 0;';
                if (!isActive) a.setAttribute('data-switch-org', org.id);
                a.innerHTML =
                    '<span style="width:28px;height:28px;border-radius:6px;background:' + (isActive ? bg : bg + '12') + ';color:' + (isActive ? '#fff' : bg) + ';display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;line-height:1;">' + orgInitials(org.name) + '</span>' +
                    '<span style="min-width:0;flex:1;line-height:1.35;">' +
                        '<span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:13px;font-weight:' + (isActive ? '600' : '400') + ';color:' + (isActive ? '#fff' : '#625f6e') + ';">' + org.name + '</span>' +
                        '<span style="display:block;font-size:11px;color:' + (isActive ? 'rgba(255,255,255,.75)' : roleColor) + ';font-weight:500;text-transform:capitalize;">' + role + '</span>' +
                    '</span>' +
                    (isActive ? '<i data-feather="check" style="width:14px;height:14px;flex-shrink:0;color:#fff;"></i>' : '');
                orgList.appendChild(a);
            });
            if (typeof feather !== 'undefined') feather.replace();
        }

        if (orgSwitcher && orgs.length > 0) {
            orgSwitcher.classList.remove('d-none');
            const currentOrgName = UserManager.getOrgName() || orgs[0].name;
            const currentOrgId = currentUser.current_org_id || orgs[0].id;
            if (orgNameEl) orgNameEl.textContent = currentOrgName;
            if (orgAvatarEl) {
                orgAvatarEl.textContent = orgInitials(currentOrgName);
                orgAvatarEl.style.background = orgColor(currentOrgId);
            }

            renderOrgList();

            // Search filter
            if (orgSearch) {
                orgSearch.addEventListener('input', function() {
                    renderOrgList(this.value);
                });
                // Prevent dropdown close when typing
                orgSearch.addEventListener('click', function(e) { e.stopPropagation(); });
                orgSearch.addEventListener('keydown', function(e) { e.stopPropagation(); });
            }

            // Event delegation for org switching
            if (orgList) {
                orgList.addEventListener('click', function(e) {
                    const item = e.target.closest('[data-switch-org]');
                    if (item) {
                        e.preventDefault();
                        e.stopPropagation();
                        UserManager.switchOrg(item.getAttribute('data-switch-org'));
                    }
                });
            }
        }
    }
});

// Export the objects for use in other scripts
window.APP_CONFIG = APP_CONFIG;
window.UserManager = UserManager;
window.ApiService = ApiService;
