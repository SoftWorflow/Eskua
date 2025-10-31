class AuthManager {
    constructor() {
        this.checkInterval = null;
    }

    // Saves the atuhentication data
    saveAuth(authData) {
        localStorage.setItem('access_token', authData.access_token);
        localStorage.setItem('access_expires_at', authData.access_expires_at);
        localStorage.setItem('user', JSON.stringify(authData.user));
    }

    // Gets the access token
    getAccessToken() {
        return localStorage.getItem('access_token');
    }

    // Get the user data
    getUser() {
        const userData = localStorage.getItem('user');
        return userData ? JSON.parse(userData) : null;
    }

    // Checks if the token is expired or close to expiring
    isTokenExpired() {
        const expiresAt = localStorage.getItem('access_expires_at');
        if (!expiresAt) return true;

        const expirationTime = new Date(expiresAt).getTime();
        const currentTime = new Date().getTime();
        
        // If expires in 2 minutes counts as expired 
        const bufferTime = 2 * 60 * 1000; // 2 minutes in milliseconds
        
        return (expirationTime - currentTime) < bufferTime;
    }

    // Trys to refresh the token using the refresh token that is on the cookie
    async refreshAccessToken() {
        try {
            const response = await fetch('/api/user/refresh.php', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.ok) {
                this.saveAuth(data);
                return true;
            } else {
                this.clearAuth();
                return false;
            }
        } catch (error) {
            console.error('Error refrezcando el token:', error);
            this.clearAuth();
            return false;
        }
    }

    // Verifys and renews the token if necessary
    async ensureValidToken() {
        const token = this.getAccessToken();

        // If there is no token trys to get one with the refresh token
        if (!token || this.isTokenExpired()) {
            return await this.refreshAccessToken();
        }

        return true;
    }

    // Cleans all the authentication
    clearAuth() {
        localStorage.removeItem('access_token');
        localStorage.removeItem('access_expires_at');
        localStorage.removeItem('user');
    }

    // Close the session
    async logout() {
        try {
            const res = await fetch('/api/user/logout.php', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'include'
            });

            if (!res.ok) {
                throw new Error(`Error HTTP ${res.status}`);
            }

            const data = await res.json();
            console.log(data);
        } catch (err) {
            console.error('Error en logout:', err);
        }

        this.clearAuth();
        window.location.href = '/home/index.php';
    }


    // Starts the automatic token monitoring
    startTokenMonitoring() {
        // Verifys if the token needs to be renewd every 4 minutes
        this.checkInterval = setInterval(async () => {
            if (this.isTokenExpired()) {
                const renewed = await this.refreshAccessToken();
                if (!renewed) {
                    // If coudn't renew, redirects to the login
                    this.logout();
                }
            }
        }, 4 * 60 * 1000); // 4 minutes in milliseconds
    }

    // Stops the moitoring
    stopTokenMonitoring() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
            this.checkInterval = null;
        }
    }
}

// Global instance
const authManager = new AuthManager();

// Middleware for protected pages
async function requireAuth() {
    const isValid = await authManager.ensureValidToken();
    
    if (!isValid) {
        // Redirects to login if couldnt authenticate
        window.location.href = '/login';
        return false;
    }

    // Start automatic monitoring
    authManager.startTokenMonitoring();
    return true;
}


// Helper to do fetch with authentication
async function authenticatedFetch(url, options = {}) {
    // Ensures that the token is valid before making the petition
    await authManager.ensureValidToken();
    
    const token = authManager.getAccessToken();
    
    const defaultOptions = {
        headers: {
            'Authorization': `Bearer ${token}`,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'include'
    };

    if (!(options.body instanceof FormData)) {
        defaultOptions.headers['Content-Type'] = 'application/json';
    }

    const mergedOptions = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...(options.headers || {})
        }
    };

    return fetch(url, mergedOptions);
}

// Check if user has a specific role
function hasRole(role) {
    const user = authManager.getUser();
    if (!user) return false;
    
    const token = authManager.getAccessToken();
    if (!token) return false;

    try {
        // Decode JWT to get role (simple base64 decode of payload)
        const payload = JSON.parse(atob(token.split('.')[1]));
        return payload.role === role;
    } catch (e) {
        return false;
    }
}

// Check if user has any of the allowed roles
function hasAnyRole(roles) {
    const token = authManager.getAccessToken();
    if (!token) return false;

    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        return roles.includes(payload.role);
    } catch (e) {
        return false;
    }
}

// Get current user role
function getUserRole() {
    const token = authManager.getAccessToken();
    if (!token) return null;

    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        return payload.role;
    } catch (e) {
        return null;
    }
}

// Middleware for role-protected pages
async function requireRole(allowedRoles) {
    const isValid = await authManager.ensureValidToken();
    
    if (!isValid) {
        window.location.href = '/login';
        return false;
    }

    const userRole = getUserRole();
    
    if (!allowedRoles.includes(userRole)) {
        // Redirects the user
        window.location.href = '/error-pages/403.html';
        return false;
    }

    authManager.startTokenMonitoring();
    return true;
}