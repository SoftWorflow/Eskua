class GroupNavbarLoader {
    async loadNavbar() {
        const navbarPath = '/groups/group-navbar.html';

        try {
            const response = await fetch(navbarPath);

            if (!response.ok) {
                throw new Error(`Failed to load navbar: ${response.status}`);
            }

            const navbarHTML = await response.text();

            return navbarHTML;
        } catch (error) {
            console.error('Error loading group navbar:', error);
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

    async injectNavbar(page, targetSelector = '#group-navbar-container') {

        const navbarHTML = await this.loadNavbar();
        const container = document.querySelector(targetSelector);

        const urlParams = new URLSearchParams(window.location.search);
        const groupId = urlParams.get('groupId');

        if (container) {
            container.innerHTML = navbarHTML;
            container.className = 'flex flex-col h-full w-[13vw] border-r-2 border-[#DFDFDF]';
            
            // CAMBIAR ESTO
            const home = document.getElementById('home');
            home.href = `/groups/teacher/?groupId=${groupId}`;

            const tasks = document.getElementById('tasks');
            tasks.href = `/groups/teacher/assignments/?groupId=${groupId}`;

            switch (page) {
                case 'home':
                    home.className = 'font no-underline interactive text-[#1B3B50] text-xl hover:text-[#E1A05B] transition duration-100 border-2 border-[#E1A05B] pl-2 pr-6 py-2 rounded-lg shadow-md/20';
                break;
                case 'tasks':
                    tasks.className = 'font no-underline interactive text-[#1B3B50] text-xl hover:text-[#E1A05B] transition duration-100 border-2 border-[#E1A05B] pl-2 pr-6 py-2 rounded-lg shadow-md/20';
                break;
            }

            // Dispatch event for other scripts that might need to know navbar is loaded
            document.dispatchEvent(new CustomEvent('groupNavbarLoaded', {
                detail: { page: page }
            }));
        } else {
            console.error(`Navbar container "${targetSelector}" not found`);
        }
    }

    async initialize(page, targetSelector = '#group-navbar-container') {
        await this.injectNavbar(page, targetSelector);
    }
}

// Global instance
const groupNavbar = new GroupNavbarLoader();

// Auto-initialize function to be called after auth check
async function initializeGroupNavbar(page, targetSelector = '#group-navbar-container') {
    await groupNavbar.initialize(page, targetSelector);
}