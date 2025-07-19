/**
 * MrWiFi Configuration and Utility Functions
 * This file contains global configuration variables and utility functions
 * for API communication, authentication, and user management.
 */

// Core application configuration
const APP_CONFIG = {
    // API Configuration
    API: {
        BASE_URL: window.location.hostname === 'localhost' 
            ? 'http://localhost:8000/api'
            : 'https://portal.monsieur-wifi.com/api',
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
     * Logs the user out
     * @param {boolean} redirect - Whether to redirect to root page
     */
    logout: function(redirect = true) {
        // Clear authentication data
        localStorage.removeItem(APP_CONFIG.AUTH.TOKEN_KEY);
        localStorage.removeItem(APP_CONFIG.AUTH.USER_KEY);
        
        // Make API call to invalidate token on server
        fetch(`${APP_CONFIG.API.BASE_URL}/${APP_CONFIG.API.VERSION}/auth/logout`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.getToken()}`,
                'Content-Type': 'application/json'
            }
        }).finally(() => {
            // Redirect to root page if specified
            if (redirect) {
                window.location.href = '/';
            }
        });
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
            // Redirect to root if no token exists
            window.location.href = '/';
            throw new Error('No authentication token found');
        }
        
        headers['Authorization'] = `Bearer ${token}`;
        
        // Setup request options
        const requestOptions = {
            ...options,
            headers,
            timeout: APP_CONFIG.API.TIMEOUT
        };
        
        try {
            const response = await fetch(url, requestOptions);
            
            // Handle unauthorized responses (expired token)
            if (response.status === 401) {
                UserManager.logout(true); // This will now redirect to '/'
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
});

// Export the objects for use in other scripts
window.APP_CONFIG = APP_CONFIG;
window.UserManager = UserManager;
window.ApiService = ApiService;
