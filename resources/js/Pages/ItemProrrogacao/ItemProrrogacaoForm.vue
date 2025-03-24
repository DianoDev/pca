<template>
    <div class="m-2" v-if="ready">
        <form @submit.prevent="submit">
            <div class="mb-4">
                <InputLabel for="objeto" value="Objeto" class="required"/>
                <textarea
                    id="objeto"
                    class="w-full rounded-md shadow-sm border-gray-500 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    v-model="form.objeto"
                    rows="4"
                    :disabled="readOnly"
                ></textarea>
                <InputError :message="form.errors.objeto"/>
            </div>

            <div class="mb-4">
                <InputLabel for="numero" value="Número" class="required"/>
                <TextInput id="numero" class="w-full" v-model="form.numero" :disabled="readOnly"/>
                <InputError :message="form.errors.numero"/>
            </div>

            <div class="mb-4">
                <InputLabel for="empresa" value="Empresa" class="required"/>
                <TextInput id="empresa" class="w-full" v-model="form.empresa" :disabled="readOnly"/>
                <InputError :message="form.errors.empresa"/>
            </div>

            <div class="mb-4">
                <InputLabel for="cnpj" value="CNPJ" class="required"/>
                <TextInput id="cnpj" class="w-full" v-model="form.cnpj" :disabled="readOnly"/>
                <InputError :message="form.errors.cnpj"/>
            </div>

            <div class="mb-4">
                <InputLabel for="valor_global" value="Valor Global" class="required"/>
                <TextInput id="valor_global" type="number" step="0.01" class="w-full" v-model="form.valor_global" :disabled="readOnly"/>
                <InputError :message="form.errors.valor_global"/>
            </div>

            <div class="mb-4">
                <InputLabel for="termino_vigencia" value="Término da Vigência" class="required"/>
                <TextInput id="termino_vigencia" type="date" class="w-full" v-model="form.termino_vigencia" :disabled="readOnly"/>
                <InputError :message="form.errors.termino_vigencia"/>
            </div>
            <div class="w-full border-t border-gray-200 pt-4 mt-4">
                <div class="flex justify-center" v-if="readOnly">
                    <button type="button"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            @click="close" aria-label="Close">
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
                    <button type="button"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            @click="close" aria-label="Close">
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
import {useForm} from '@inertiajs/vue3';

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
    id_plano_contratacao: '',
    id_contrato: '',
    objeto: '',
    numero: '',
    empresa: '',
    cnpj: '',
    valor_global: '',
    termino_vigencia: '',
    status: '',
});
const ready = ref(false);
const readOnly = ref(false);

function submit() {
    form.post('/item-prorrogacao/', {
        onSuccess: () => handleSuccess('Contrato criado com sucesso!'),
        onError: () => handleError()
    });
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
        message: 'Ocorreu um erro ao salvar o Contrato.'
    });
    events.emit('form-submitted', false);
}

const loadData = async () => {
    try {
        const response = await axios.get(`/item-prorrogacao/${props.data.id}`);
        console.log(response.data,'meucu')
        // Set form data
        form.id_plano_contratacao = response.data.id_plano_contratacao || '';
        form.id_contrato = response.data.id_contrato || '';
        form.objeto = response.data.objeto || '';
        form.numero = response.data.numero || '';
        form.empresa = response.data.empresa || '';
        form.cnpj = response.data.cnpj || '';
        form.valor_global = response.data.valor_global || '';
        form.termino_vigencia = response.data.termino_vigencia || '';
        form.status = response.data.status || '';

        readOnly.value = Boolean(props.data.readOnly);
    } catch (err) {
        console.error('Error loading data:', err);
        events.emit('notification', {
            type: 'error',
            message: 'Não foi possível recuperar os dados do contrato.'
        });
    } finally {
        ready.value = true;
    }
}

const close = () => {
    events.emit('popup-close', true);
}

onMounted(async () => {
    console.log(props.data);
    events.off("form-submitted");
    events.on("form-submitted", (sucesso) => {
        if (sucesso) {
            events.emit('table-reload', true);
        }
    });

    if (props.data?.id) {
        await loadData();
    } else {
        form.id_plano_contratacao = props.data?.id_plano || '';
        ready.value = true;
    }
});
</script>
