class NavbarLoader {
    constructor() {
        this.navbarMap = {
            'admin': 'navbars/navbarAdmin.html',
            'teacher': 'navbars/navbarTeacher.html',
            'student': 'navbars/navbarStudent.html',
            'guest': 'navbars/navbarNormalUser.html'
        };

        this.defaultNavbar = 'navbars/navbarNotLoged.html';
    }

    // Get the navbar file based on user role
    getNavbarPath(role) {
        return this.navbarMap[role] || this.defaultNavbar;
    }

    // Load navbar HTML content
    async loadNavbar(role) {
        const navbarPath = this.getNavbarPath(role);

        try {
            const response = await fetch(navbarPath);

            if (!response.ok) {
                throw new Error(`Failed to load navbar: ${response.status}`);
            }

            const navbarHTML = await response.text();
            return navbarHTML;
        } catch (error) {
            console.error('Error loading navbar:', error);
            // Load default navbar as fallback
            try {
                const fallbackResponse = await fetch(this.defaultNavbar);
                return await fallbackResponse.text();
            } catch (fallbackError) {
                console.error('Error loading fallback navbar:', fallbackError);
                return '<nav>Error loading navigation</nav>';
            }
        }
    }

    // Inject navbar into the page
    async injectNavbar(targetSelector = '#navbar-container') {
        const userRole = getUserRole();

        if (!userRole) {
            console.warn('No user role found, loading default navbar');
        }

        const navbarHTML = await this.loadNavbar(userRole);
        const container = document.querySelector(targetSelector);

        if (container) {
            container.innerHTML = navbarHTML;
            // Dispatch event for other scripts that might need to know navbar is loaded
            document.dispatchEvent(new CustomEvent('navbarLoaded', {
                detail: { role: userRole }
            }));
        } else {
            console.error(`Navbar container "${targetSelector}" not found`);
        }
    }

    // Initialize navbar
    async initialize(targetSelector = '#navbar-container') {
        await this.injectNavbar(targetSelector);
    }
}

// Global instance
const navbarLoader = new NavbarLoader();

// Auto-initialize function to be called after auth check
async function initializeNavbar(targetSelector = '#navbar-container') {
    await navbarLoader.initialize(targetSelector);
}