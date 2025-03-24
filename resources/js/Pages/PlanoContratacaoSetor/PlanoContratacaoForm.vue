<template>
    <div class="m-2" v-if="ready">
        <form @submit.prevent="submit">
            <div class="mb-4">
                <InputLabel for="nome_setor_formatado" value="Setor" class="required"/>
                <input
                    id="nome_setor_formatado"
                    type="text"
                    class="w-full rounded-md shadow-sm border-gray-300 bg-gray-100 text-gray-700 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    v-model="form.nome_setor_formatado"
                    readonly
                    style="cursor: not-allowed;"
                    tabindex="-1"
                />
                <InputError :message="errors.nome_setor_formatado"/>
            </div>

            <div class="mb-4">
                <InputLabel for="nome_funcionario" value="Gestor" class="required"/>
                <input
                    id="nome_funcionario"
                    type="text"
                    class="w-full rounded-md shadow-sm border-gray-300 bg-gray-100 text-gray-700 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    v-model="form.nome_funcionario"
                    readonly
                    style="cursor: not-allowed;"
                    tabindex="-1"
                />
                <InputError :message="errors.nome_funcionario"/>
            </div>

            <div class="mb-4">
                <InputLabel for="exercicio" value="Exercicio" class="required"/>
                <input
                    id="exercicio"
                    type="text"
                    class="w-full rounded-md shadow-sm border-gray-300 bg-gray-100 text-gray-700 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    v-model="form.exercicio"
                    readonly
                    style="cursor: not-allowed;"
                    tabindex="-1"
                />
                <InputError :message="errors.exercicio"/>
            </div>

            <div class="mb-4">
                <InputLabel for="email" value="Email" class="required"/>
                <TextInput id="email" class="w-full" v-model="form.email" :disabled="readOnly"/>
                <InputError :message="errors.email"/>
            </div>

            <div class="mb-4">
                <InputLabel for="telefone" value="Telefone" class="required"/>
                <TextInput id="telefone" class="w-full" v-model="form.telefone" :disabled="readOnly"/>
                <InputError :message="errors.telefone"/>
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
    codigo_setor: '',
    numero_matricula_gestor: '',
    nome_funcionario: '',
    nome_setor_formatado: '',
    exercicio: '',
    email: '',
    telefone: '',
    status: '',
    valor_total: ''
});
const ready = ref(false);
const readOnly = ref(false);

function submit() {
    processing.value = true;

    axios.post('/plano-contratacao-setor', form.value)
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
function handleSuccess() {
    close();
    events.emit('reload-plano', true);
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
        const response = await axios.get(`/plano-contratacao-setor/gestorInfo`);
        form.value.codigo_setor = response.data.codigo_setor || '';
        form.value.numero_matricula_gestor = response.data.responsavel || '';
        form.value.nome_funcionario = response.data.nome_funcionario || '';
        form.value.nome_setor_formatado = response.data.nome_setor_formatado || '';
        form.value.exercicio = props.data.year;
        form.value.email = response.data.email || '';
        form.value.telefone = response.data.telefone || '';
        form.value.status = response.data.status || '';
        form.value.valor_total = response.data.valor_total || '';

        readOnly.value = Boolean(props.data.readOnly);
    } catch (err) {
        console.error('Error loading data:', err);
        events.emit('notification', {
            type: 'error',
            message: 'Não foi possível recuperar os dados do Plano Contratação.'
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

    if (props.data?.year) {
        await loadData();
    } else {
        ready.value = true;
    }
});
</script>
