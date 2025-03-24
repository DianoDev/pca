<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" :id="id">
        <div class="min-h-screen px-4 text-center flex items-center justify-center w-full">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="cancel"></div>

            <!-- Modal panel -->
            <div class="relative inline-block transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                <!-- Header -->
                <div class="bg-gray-800 px-4 py-3 text-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium">{{ confirmation.title }}</h3>
                        <button type="button" class="text-gray-400 hover:text-white focus:outline-none" @click="cancel">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="bg-white px-4 py-5">
                    <p v-html="confirmation.message"></p>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button
                        type="button"
                        class="inline-flex w-full items-center justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:ml-3 sm:w-auto"
                        @click="confirm"
                    >
                        <i class="fa fa-check mr-2"></i>
                        Confirmar
                    </button>
                    <button
                        type="button"
                        class="mt-3 inline-flex w-full items-center justify-center rounded-md bg-gray-500 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 sm:mt-0 sm:w-auto"
                        @click="cancel"
                        id="confirmation-cancel-button"
                    >
                        <i class="fa fa-times mr-2"></i>
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, inject, onMounted } from 'vue';

const props = defineProps({
    id: {
        type: String,
        default: 'global-confirmation-popup'
    },
    data: {
        type: [Object, null],
        default: null
    },
    message: {
        type: String,
        default: 'Você tem certeza?'
    },
    title: {
        type: String,
        default: 'Confirmação'
    },
    event: {
        type: [Function, String, null],
        default: null
    },
    top: {
        type: Boolean,
        default: false
    },
});

const emit = defineEmits(['close']);
const show = ref(false);
const events = inject('events');
const confirmation = ref({
    title: props.title,
    message: props.message,
    data: props.data,
    event: props.event,
});

const confirm = () => {
    if (typeof confirmation.value.event === 'function') {
        confirmation.value.event();
    } else if (confirmation.value.event) {
        events.emit(confirmation.value.event, confirmation.value.data);
    }
    closeConfirmation();
    emit('close');
};

const closeConfirmation = () => {
    confirmation.value = {
        title: props.title,
        message: props.message,
        data: props.data,
        event: props.event,
    };
    show.value = false;
};

const cancel = () => {
    closeConfirmation();
    emit('close');
};

onMounted(() => {
    events.on('confirmation', (data) => {
        confirmation.value = data;
        show.value = true;
    });
});
</script>
