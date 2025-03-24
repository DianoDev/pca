<script setup lang="ts">
import { inject } from 'vue';
const events = inject('events');
const props = withDefaults(
    defineProps<{
        title?: string;
        component?: string;
        data?: any;
        size?: 'sm' | 'md' | 'lg' | 'xl' | '2xl';
        id?: string;
        variant?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info';
    }>(),
    {
        title: 'Título',
        component: null,
        data: null,
        size: 'lg',
        id: null,
        variant: 'primary',
    },
);

const open = () => {
    console.log(props.component)
    const parsedData = typeof props.data === 'string' ? JSON.parse(props.data) : props.data;
    events.emit('popup', {
        title: props.title,
        component: props.component,
        data: parsedData,
        size: props.size,
        id: props.id
    });
};

// Computed styles based on variant - sem background e sem shadow
const variantClasses = {
    primary: 'text-blue-600 hover:text-blue-800',
    secondary: 'text-gray-600 hover:text-gray-800',
    success: 'text-green-600 hover:text-green-800',
    danger: 'text-red-600 hover:text-red-800',
    warning: 'text-yellow-500 hover:text-yellow-700',
    info: 'text-cyan-600 hover:text-cyan-800',
};
</script>

<template>
    <button
        @click="open"
        :class="[
            'inline-flex items-center text-sm focus:outline-none',
            variantClasses[variant]
        ]"
    >
        <slot></slot>
    </button>
</template>
