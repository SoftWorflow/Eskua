class ContentLoader {
    constructor() {
        this.contentMap = {
            'admin': '/home/homes/admin.html',
            'teacher': '/home/homes/teacher.html',
            'student': '/home/homes/student.html',
            'guest': '/home/homes/guest.html',
            'not_logged': '/home/homes/public.html'
        };
        this.defaultContent = '/home/homes/public.html';
    }

    // Get the content file based on user role
    getContentPath(role) {
        return this.contentMap[role] || this.defaultContent;
    }

    // Load content HTML
    async loadContent(role) {
        const contentPath = this.getContentPath(role);

        try {
            const response = await fetch(contentPath);

            if (!response.ok) {
                throw new Error(`Failed to load content: ${response.status}`);
            }

            if (role === 'admin') {
                const backofficeScript = document.createElement('script');
                backofficeScript.src = '../backoffice/backofficeMainScript.js';
                document.head.appendChild(backofficeScript);
            }

            if (role !== 'not_logged') {
                requireAuth();
            }

            const contentHTML = await response.text();
            return contentHTML;
        } catch (error) {
            console.error('Error loading content:', error);
            // Load default content as fallback
            try {
                const fallbackResponse = await fetch(this.defaultContent);
                return await fallbackResponse.text();
            } catch (fallbackError) {
                console.error('Error loading fallback content:', fallbackError);
                return '<div class="p-8 text-center"><h1 class="text-2xl font-bold text-red-600">Error loading content</h1></div>';
            }
        }
    }

    // Inject content into the page
    async injectContent(targetSelector = '#content-container') {
        const userRole = getUserRole() || 'not_logged';

        const contentHTML = await this.loadContent(userRole);
        const container = document.querySelector(targetSelector);

        if (container) {
            container.innerHTML = contentHTML;
            // Dispatch event for other scripts
            document.dispatchEvent(new CustomEvent('contentLoaded', {
                detail: { role: userRole }
            }));
        } else {
            console.error(`Content container "${targetSelector}" not found`);
        }
    }

    // Initialize content
    async initialize(targetSelector = '#content-container') {
        await this.injectContent(targetSelector);
    }
}

// Global instance
const contentLoader = new ContentLoader();

// Auto-initialize function
async function initializeContent(targetSelector = '#content-container') {
    await contentLoader.initialize(targetSelector);
}