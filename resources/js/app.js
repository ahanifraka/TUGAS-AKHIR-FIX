import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { router } from '@inertiajs/vue3';

// Override the global route function to handle missing routes gracefully
if (typeof window !== 'undefined') {
    // Wait for Ziggy to be fully initialized
    const initializeRouteOverride = () => {
        const originalRoute = window.route;
        if (!originalRoute) {
            // Ziggy not loaded yet, try again
            setTimeout(initializeRouteOverride, 100);
            return;
        }
        
        window.route = function(...args) {
            try {
                return originalRoute(...args);
            } catch (error) {
                if (error.message && error.message.includes('is not in the route list')) {
                    console.warn('Ziggy route error:', error.message);
                    // Return a fallback URL to the home page
                    return '/';
                }
                throw error;
            }
        };
    };
    
    initializeRouteOverride();
}

// PrimeVue setup
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import Button from 'primevue/button';
import SplitButton from 'primevue/splitbutton';
import ConfirmationService from 'primevue/confirmationservice';

// Loading component
import LoadingSpinner from './Components/LoadingSpinner.vue';
import vAnimateOnScroll from './Directives/vAnimateOnScroll';

const appName = import.meta.env.VITE_APP_NAME || 'Badan Pembinaan BUMD';

// Create a global loading state
let isLoading = false;
let loadingElement = null;

// Function to show loading
function showLoading() {
    if (!loadingElement) {
        loadingElement = document.createElement('div');
        loadingElement.id = 'global-loading';
        document.body.appendChild(loadingElement);
        
        const app = createApp(LoadingSpinner, { show: true });
        app.mount(loadingElement);
    }
    isLoading = true;
}

// Function to hide loading
function hideLoading() {
    if (loadingElement && isLoading) {
        loadingElement.remove();
        loadingElement = null;
        isLoading = false;
    }
}

// Show loading only for GET requests (page navigation), not for form submissions or deletes
router.on('start', (event) => {
    try {
        const visit = event?.detail?.visit;
        const method = visit?.method?.toLowerCase() || 'get';
        const visitUrl = visit?.url || '';
        
        // Only show loading for GET requests (page navigation)
        // Skip for POST, PUT, PATCH, DELETE (form submissions and deletions)
        if (method === 'get') {
            const nextPathname = visitUrl ? new URL(visitUrl, window.location.origin).pathname : null;
            if (nextPathname && nextPathname !== window.location.pathname) {
                showLoading();
            }
        }
    } catch (_) {
        // Don't show loading on error
    }
});

// Hide loading on navigation finish
router.on('finish', () => {
    hideLoading();
});

// Hide loading on navigation error
router.on('error', () => {
    hideLoading();
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: { preset: Aura, options: { darkModeSelector: 'none' } },
            });

        // Register commonly used PrimeVue components globally
        app.component('Button', Button);
        app.component('SplitButton', SplitButton);
        app.use(ConfirmationService);

        // Register global directives
        app.directive('animate-on-scroll', vAnimateOnScroll);

        return app.mount(el);
    },
    progress: {
        color: '#1C5EBD',
        includeCSS: true,
    },
});