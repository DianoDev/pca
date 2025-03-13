import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';
import mitt from 'mitt';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';

// Create Pinia instance
const pinia = createPinia();

// Create Mitt event emitter
const emitter = mitt();

// Define emitter type (for better TypeScript support)
type Emitter = ReturnType<typeof mitt>;

// Define custom properties to extend the Vue application
declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        $events: Emitter;
    }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Use plugins
        app.use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .use(Toast, {});

        // Add event emitter to globalProperties and provide it to the app
        app.config.globalProperties.$events = emitter;
        app.provide('events', emitter);

        // Register global components (if needed)
        // This pattern is different from your previous approach because Inertia
        // handles component resolution differently
        // If you need global components, you can register them here

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
