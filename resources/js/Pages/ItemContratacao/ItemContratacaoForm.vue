<template>
    <div class="m-2" v-if="ready">
        <form @submit.prevent="submit">
            <div class="mb-4">
                <InputLabel for="descricao" value="Descrição" class="required"/>
                <textarea
                    id="descricao"
                    class="w-full rounded-md shadow-sm border-gray-500 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    v-model="form.descricao"
                    rows="4"
                    :disabled="readOnly"
                ></textarea>
                <InputError :message="form.errors.descricao"/>
            </div>

            <div class="mb-4">
                <InputLabel for="unidade_medida" value="Unidade de Medida" class="required"/>
                <TextInput id="unidade_medida" class="w-full" v-model="form.unidade_medida" :disabled="readOnly"/>
                <InputError :message="form.errors.unidade_medida"/>
            </div>

            <div class="mb-4">
                <InputLabel for="quantidade" value="Quantidade" class="required"/>
                <TextInput id="quantidade" type="number" class="w-full" v-model="form.quantidade" :disabled="readOnly"/>
                <InputError :message="form.errors.quantidade"/>
            </div>

            <div class="mb-4">
                <InputLabel for="valor_unitario_estimado" value="Valor Unitário Estimado" class="required"/>
                <TextInput id="valor_unitario_estimado" type="number" step="0.01" class="w-full" v-model="form.valor_unitario_estimado" :disabled="readOnly"/>
                <InputError :message="form.errors.valor_unitario_estimado"/>
            </div>

            <div class="mb-4">
                <InputLabel for="valor_total" value="Valor Total" class="required"/>
                <input
                    id="valor_total"
                    type="text"
                    class="w-full rounded-md shadow-sm border-gray-300 bg-gray-100 text-gray-700 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    v-model="form.valor_total"
                    readonly
                    style="cursor: not-allowed;"
                    tabindex="-1"
                />
                <InputError :message="form.errors.valor_total"/>
            </div>

            <div class="mb-4">
                <InputLabel for="data_desejada" value="Data Desejada" class="required"/>
                <TextInput id="data_desejada" type="date" class="w-full" v-model="form.data_desejada" :disabled="readOnly"/>
                <InputError :message="form.errors.data_desejada"/>
            </div>

            <div class="mb-4">
                <InputLabel for="classificacao" value="Classificação" class="required"/>
                <select
                    id="classificacao"
                    class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    v-model="form.classificacao"
                    :disabled="readOnly"
                >
                    <option value="">Por favor, selecione...</option>
                    <option value="A4">ND 33.90.30 - Material de Consumo</option>
                    <option value="A5">ND 44.90.52 - Material Permanente</option>
                    <option value="A1">ND 33.90.36 - Serviços Pessoa Física</option>
                    <option value="A2">ND 33.90.37 - Serviços de TI</option>
                    <option value="A3">ND 33.90.39 - Serviços Pessoa Jurídica</option>
                </select>
                <InputError :message="form.errors.classificacao"/>
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
import {inject, onMounted, ref, watch} from 'vue';
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
    descricao: '',
    unidade_medida: '',
    quantidade: '',
    valor_unitario_estimado: '',
    valor_total: '',
    data_desejada: '',
    classificacao: '',
    status: '',
});
const ready = ref(false);
const readOnly = ref(false);

// Calcular valor total quando quantidade ou valor unitário mudar
watch([() => form.quantidade, () => form.valor_unitario_estimado], ([novaQuantidade, novoValorUnitario]) => {
    if (novaQuantidade && novoValorUnitario) {
        form.valor_total = (parseFloat(novaQuantidade) * parseFloat(novoValorUnitario)).toFixed(2);
    } else {
        form.valor_total = '';
    }
});

function submit() {
    form.post('/item-contratacao/', {
        onSuccess: () => handleSuccess('Item Contratação criado com sucesso!'),
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
        message: 'Ocorreu um erro ao salvar o Item Contratação.'
    });
    events.emit('form-submitted', false);
}

const loadData = async () => {
    try {
        const response = await axios.get(`/item-contratacao/${props.data.id}`);
        // Set form data
        form.descricao = response.data.descricao || '';
        form.unidade_medida = response.data.unidade_medida || '';
        form.quantidade = response.data.quantidade || '';
        form.valor_unitario_estimado = response.data.valor_unitario_estimado || '';
        form.valor_total = response.data.valor_total || '';
        form.data_desejada = response.data.data_desejada || '';
        form.classificacao = response.data.classificacao || '';
        form.status = response.data.status || '';

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
    console.log(props.data)
    events.off("form-submitted");
    events.on("form-submitted", (sucesso) => {
        if (sucesso) {
            events.emit('table-reload', true);
        }
    });


    if (props.data?.id) {
        await loadData();
    } else {
        form.id_plano_contratacao = props.data.id_plano;
        ready.value = true;
    }
});
</script>
