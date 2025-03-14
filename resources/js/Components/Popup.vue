<template>
    <Modal
        :show="isOpen"
        :max-width="modalSize"
        :closeable="true"
        @close="close"
    >
        <!-- Header with improved spacing and cleaner design -->
        <div class="border-b border-white px-6 py-4 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100" id="modal-title">
                    {{ title }}
                </h3>
                <button
                    type="button"
                    class="rounded-md p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:hover:bg-gray-700"
                    @click="close"
                    aria-label="Close"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Body with improved spacing and handling of component loading -->
        <div class="min-h-[250px] bg-white p-6 overflow-y-auto">
            <!-- Loading indicator -->
            <div v-if="isLoading" class="flex h-40 items-center justify-center">
                <div class="h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-blue-600"></div>
            </div>

            <!-- Error message if component fails to load -->
            <div v-else-if="componentError" class="rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Erro ao carregar componente</h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                            {{ componentError }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Component render -->
            <component
                v-else-if="componentToRender"
                :is="componentToRender"
                v-bind="data"
                :id="id"
                :data="data"
                @close="close"
            />
        </div>
    </Modal>
</template>

<script setup>
import { ref, computed, onMounted, inject, onBeforeUnmount, defineAsyncComponent, markRaw } from 'vue';
import Modal from './Modal.vue';

// Inject event emitter
const events = inject('events');

// State
const isOpen = ref(false);
const componentToRender = ref(null);
const id = ref(null);
const data = ref(null);
const title = ref(null);
const size = ref('md');
const isLoading = ref(false);
const componentError = ref(null);

// Component cache to prevent reloading components
const componentCache = new Map();

// Computed prop to convert size to Tailwind Modal format
const modalSize = computed(() => {
    switch (size.value) {
        case 'sm': return 'sm';
        case 'md': return 'md';
        case 'lg': return 'lg';
        case 'xl': return 'xl';
        case '2xl': return '2xl';
        default: return 'md';
    }
});

/**
 * Resolves a component by name, with proper path resolution and error handling
 */
const resolveComponent = async (componentName) => {
    // Return from cache if already loaded
    if (componentCache.has(componentName)) {
        return componentCache.get(componentName);
    }

    try {
        // Extract the module/folder name from the component name
        const folderName = componentName.replace(/Form$|Grid$|List$|Detail$/, '');

        // Define possible paths to try
        const possiblePaths = [
            `../../js/Pages/${folderName}/${componentName}.vue`,
            `../Pages/${folderName}/${componentName}.vue`,
            `./Pages/${folderName}/${componentName}.vue`
        ];

        // Create async component with proper loading states
        const asyncComponent = markRaw(defineAsyncComponent({
            loader: async () => {
                for (const path of possiblePaths) {
                    try {
                        console.log(`Attempting to load component from: ${path}`);
                        const module = await import(path);
                        console.log(`Successfully loaded ${componentName}`);
                        return markRaw(module.default);
                    } catch (error) {
                        console.log(`Failed to load from ${path}, trying next path...`);
                        // Continue to next path
                    }
                }

                // If all paths fail
                throw new Error(`Component ${componentName} could not be found in any expected location`);
            },
            loadingComponent: null, // We handle loading state ourselves
            errorComponent: null,   // We handle errors ourselves
            onError(error) {
                console.error(`Failed to load component: ${componentName}`, error);
                componentError.value = `Não foi possível carregar o componente "${componentName}". Verifique o console para mais detalhes.`;
            }
        }));

        // Store in cache
        componentCache.set(componentName, asyncComponent);
        return asyncComponent;
    } catch (error) {
        console.error(`Failed to register component: ${componentName}`, error);
        componentError.value = `Erro ao registrar o componente "${componentName}": ${error.message}`;
        return null;
    }
};

/**
 * Opens the popup with the specified component
 */
const open = async (evt) => {
    // Reset state
    isLoading.value = true;
    componentError.value = null;
    componentToRender.value = null;

    // Set popup properties
    id.value = evt.id || 'component-popup';
    title.value = evt.title || 'Sem título';
    size.value = evt.size || 'lg';
    data.value = evt.data ? markRaw(evt.data) : null;
    isOpen.value = true;

    // Log attempt
    console.log(`Opening popup with component: ${evt.component}`);

    try {
        // Load component
        componentToRender.value = await resolveComponent(evt.component);
    } catch (error) {
        componentError.value = `Erro ao carregar o componente: ${error.message}`;
        console.error(error);
    } finally {
        isLoading.value = false;
    }
};

/**
 * Closes the popup and resets state
 */
const close = () => {
    isOpen.value = false;

    // Reset state after animation completes
    setTimeout(() => {
        componentToRender.value = null;
        id.value = null;
        data.value = null;
        title.value = null;
        componentError.value = null;
    }, 300);
};

// Event listeners
onMounted(() => {
    events.on('popup', open);
    events.on('popup-close', close);
});

onBeforeUnmount(() => {
    events.off('popup', open);
    events.off('popup-close', close);
});
</script>
