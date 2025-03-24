<template>
    <div class="m-2" v-if="ready">
        <form @submit.prevent="submit">
            <!-- Novos campos adicionados -->
            <div class="mb-4">
                <InputLabel for="objeto" value="Objeto" class="required"/>
                <textarea
                    id="objeto"
                    class="w-full rounded-md shadow-sm border-gray-500 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    v-model="form.objeto"
                    rows="4"
                    :disabled="readOnly"
                ></textarea>
                <InputError :message="errors.objeto"/>
            </div>

            <div class="mb-4">
                <InputLabel for="numero" value="Numero" class="required"/>
                <TextInput id="numero" class="w-full" v-model="form.numero" required :disabled="readOnly"/>
                <InputError :message="errors.numero"/>
            </div>

            <div class="mb-4">
                <InputLabel for="empresa" value="Empresa" class="required"/>
                <TextInput id="empresa" class="w-full" v-model="form.empresa" required :disabled="readOnly"/>
                <InputError :message="errors.empresa"/>
            </div>

            <div class="mb-4">
                <InputLabel for="cnpj" value="CNPJ" class="required"/>
                <TextInput id="cnpj" class="w-full" v-model="form.cnpj" required :disabled="readOnly"/>
                <InputError :message="errors.cnpj"/>
            </div>

            <div class="mb-4">
                <InputLabel for="valor_global" value="Valor Global" class="required"/>
                <TextInput id="valor_global" class="w-full" v-model="form.valor_global" required :disabled="readOnly"/>
                <InputError :message="errors.valor_global"/>
            </div>

            <div class="mb-4">
                <InputLabel for="termino_vigencia" value="Termino Vigência" class="required"/>
                <TextInput id="termino_vigencia" type="date" class="w-full" v-model="form.termino_vigencia" :disabled="readOnly"/>
                <InputError :message="errors.termino_vigencia"/>
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
                        :disabled="processing"
                    >
                        <i v-if="!processing" class="fa fa-check mr-1"></i>
                        <i v-else class="fa fa-spinner fa-spin mr-1"></i>
                        {{ processing ? 'Salvando...' : 'Salvar' }}
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
import { inject, onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    data: {
        type: Object,
        default: null,
        required: false
    }
});

const emit = defineEmits(['close', 'notification']);
const events = inject('events');
const errors = ref({});
const processing = ref(false);
const form = ref({
    id_plano_contratacao: '',
    objeto: '',
    numero: '',
    empresa: '',
    cnpj: '',
    valor_global: '',
    termino_vigencia: ''
});
const ready = ref(false);
const readOnly = ref(false);

function submit() {
    processing.value = true;

    if (props.data?.id) {
        // Rota de update
        axios.post(`/item-prorrogacao-setor/${props.data.id}`, form.value)
            .then(response => {
                handleSuccess('Plano Contratação atualizado com sucesso!');
                processing.value = false;
            })
            .catch(error => {
                if (error.response && error.response.data.errors) {
                    errors.value = error.response.data.errors;
                }
                handleError();
                processing.value = false;
            });
    } else {
        // Rota de criação
        axios.post('/item-prorrogacao-setor', form.value)
            .then(response => {
                handleSuccess('Plano Contratação criado com sucesso!');
                processing.value = false;
            })
            .catch(error => {
                if (error.response && error.response.data.errors) {
                    errors.value = error.response.data.errors;
                }
                handleError();
                processing.value = false;
            });
    }
}
function handleSuccess(message) {
    events.emit('reload-plano', true);
    events.emit('notification', {
        type: 'success',
        message: message
    });
    events.emit('table-reload', true);
    close();
}

function handleError() {
    console.log('foi');
    events.emit('notification', {
        type: 'error',
        message: 'Ocorreu um erro ao salvar o Plano Contratação.'
    });
    events.emit('reload-plano', false);
}

const loadData = async () => {
    try {
        console.log('oi')
        const response = await axios.get(`/item-prorrogacao-setor/${props.data.id}`);
        const data = response.data;
        form.value = {
            id_plano_contratacao: data.id_plano_contratacao ? String(data.id_plano_contratacao) : '',
            objeto: data.objeto || '',
            numero: data.numero || '',
            empresa: data.empresa ? String(data.empresa) : '',
            cnpj: data.cnpj ? String(data.cnpj) : '',
            valor_global: data.valor_global ? String(data.valor_global) : '',
            termino_vigencia: data.termino_vigencia || '',
        };

        readOnly.value = Boolean(props.data.readOnly);
    } catch (err) {
        console.error('Error loading data:', err);
        events.emit('notification', {
            type: 'error',
            message: 'Não foi possível recuperar os dados do item.'
        });
    } finally {
        ready.value = true;
    }
}
const close = () => {
    events.emit('popup-close', true);
}

onMounted(async () => {
    console.log(props);
    events.off("form-submitted");
    events.on("form-submitted", (sucesso) => {
        if (sucesso) {
            events.emit('table-reload', true);
        }
    });

    if (props.data?.id) {
        await loadData();
    } else {
        form.value.id_plano_contratacao = props.data.id_plano ? String(props.data.id_plano) : '';
        ready.value = true;
    }
});
</script>
