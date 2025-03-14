<template>
    <div class="m-2" v-if="ready">
        <form @submit.prevent="submit">
            <div class="mb-4">
                <InputLabel for="codigo_setor" value="Codigo Setor" class="required" />
                <TextInput id="codigo_setor" v-model="form.codigo_setor" class="w-full" :disabled="readOnly"/>
                <InputError :message="form.errors.codigo_setor" />
            </div>

            <div class="mb-4">
                <InputLabel for="numero_matricula" value="Numero Matricula" class="required" />
                <TextInput id="numero_matricula" class="w-full" v-model="form.numero_matricula" :disabled="readOnly"/>
                <InputError :message="form.errors.numero_matricula" />
            </div>

            <div class="mb-4">
                <InputLabel for="numero_matricula_gestor" value="Numero Matricula Gestor" class="required" />
                <TextInput id="numero_matricula_gestor" class="w-full" v-model="form.numero_matricula_gestor" :disabled="readOnly"/>
                <InputError :message="form.errors.numero_matricula_gestor" />
            </div>

            <div class="w-full border-t border-gray-200 pt-4 mt-4">
                <div class="flex justify-center" v-if="readOnly">
                    <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" @click="close" aria-label="Close">
                        <i class="fa fa-close mr-1"></i> Sair
                    </button>
                </div>
                <div class="flex justify-center space-x-2" v-if="!readOnly">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        :disabled="form.processing"
                    >
                        <i v-if="!form.processing" class="fa fa-check mr-1"></i>
                        <i v-else class="fa fa-spinner fa-spin mr-1"></i>
                        {{ form.processing ? 'Salvando...' : 'Salvar' }}
                    </button>
                    <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" @click="close" aria-label="Close">
                        <i class="fa fa-close mr-1"></i> Cancelar
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import {inject, onMounted, ref} from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    data: {
        type: Object,
        default: null,
        required: false
    }
});

const emit = defineEmits(['close', 'notification']);
const events = inject('events');
const form = useForm({
    codigo_setor: '',
    numero_matricula: '',
    numero_matricula_gestor: ''
});
const ready = ref(false);
const readOnly = ref(false);

function submit() {
    if (props.data?.id) {
        form.put(`/usuario-setor/${props.data.id}`, {
            onSuccess: () => handleSuccess('Usuário Setor atualizado com sucesso!'),
            onError: () => handleError()
        });
    } else {
        form.post('/usuario-setor', {
            onSuccess: () => handleSuccess('Usuário Setor criado com sucesso!'),
            onError: () => handleError()
        });
    }
}

function handleSuccess(message) {
    events.emit('table-reload', true);
    events.emit('notification', {
        type: 'success',
        message: message
    });
    events.emit('form-submitted', true);
    events.emit('popup-close', true);
}

function handleError() {
    events.emit('notification', {
        type: 'error',
        message: 'Ocorreu um erro ao salvar o Usuário Setor.'
    });
    events.emit('form-submitted', false);
}

const loadData = async () => {
    try {
        const response = await axios.get(`/usuario-setor/${props.data.id}`);
        // Set form data
        form.codigo_setor = response.data.codigo_setor || '';
        form.numero_matricula = response.data.numero_matricula || '';
        form.numero_matricula_gestor = response.data.numero_matricula_gestor || '';

        readOnly.value = Boolean(props.data.readOnly);
    } catch (err) {
        console.error('Error loading data:', err);
        events.emit('notification', {
            type: 'error',
            message: 'Não foi possível recuperar os dados do Usuário Setor.'
        });
    } finally {
        ready.value = true;
    }
}

const close = () => {
    events.emit('popup-close', true);
}

onMounted(async () => {
    events.off("form-submitted");
    events.on("form-submitted", (sucesso) => {
        if (sucesso) {
            events.emit('table-reload', true);
        }
    });

    if (props.data?.id) {
        await loadData();
    } else {
        ready.value = true;
    }
});
</script>
