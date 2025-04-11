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

const variantClasses = {
    primary: 'bg-[#235a99] hover:bg-[#1d4b80] text-white focus:ring-[#2d6ab8]',
    secondary: 'bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500',
    success: 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500',
    danger: 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500',
    warning: 'bg-yellow-500 hover:bg-yellow-600 text-black focus:ring-yellow-400',
    info: 'bg-cyan-600 hover:bg-cyan-700 text-white focus:ring-cyan-500',
};
</script>

<template>
    <button
        @click="open"
        :class="[
            'inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2',
            variantClasses[variant]
        ]"
    >
        <slot></slot>
    </button>
</template>
