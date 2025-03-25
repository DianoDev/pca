<template>
    <div class="m-2" v-if="ready">
        <form @submit.prevent="submit">
            <div class="mb-4">
                <InputLabel
                    for="objeto"
                    value="Objeto"
                    class="required"
                    :class="{'text-gray-400': readOnly}"
                />
                <textarea
                    id="objeto"
                    class="w-full rounded-md shadow-sm border-gray-500 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    v-model="form.objeto"
                    rows="4"
                    :disabled="readOnly"
                    :class="{'bg-gray-100 cursor-not-allowed': readOnly}"
                ></textarea>
                <InputError :message="errors.objeto"/>
            </div>

            <div class="mb-4">
                <InputLabel
                    for="numero"
                    value="Número do Contrato"
                    :class="{'text-gray-400': readOnly}"
                />
                <TextInput
                    id="numero"
                    class="w-full"
                    v-model="form.numero"
                    :disabled="readOnly"
                    :class="{'bg-gray-100 cursor-not-allowed': readOnly}"
                />
                <InputError :message="errors.numero"/>
            </div>

            <div class="mb-4">
                <InputLabel
                    for="empresa"
                    value="Empresa"
                    :class="{'text-gray-400': readOnly}"
                />
                <TextInput
                    id="empresa"
                    class="w-full"
                    v-model="form.empresa"
                    :disabled="readOnly"
                    :class="{'bg-gray-100 cursor-not-allowed': readOnly}"
                />
                <InputError :message="errors.empresa"/>
            </div>

            <div class="mb-4">
                <InputLabel
                    for="cnpj"
                    value="CNPJ"
                    :class="{'text-gray-400': readOnly}"
                />
                <TextInput
                    id="cnpj"
                    class="w-full"
                    v-model="form.cnpj"
                    :disabled="readOnly"
                    :class="{'bg-gray-100 cursor-not-allowed': readOnly}"
                />
                <InputError :message="errors.cnpj"/>
            </div>

            <div class="mb-4">
                <InputLabel
                    for="valor_global"
                    value="Valor Global"
                    class="required"
                    :class="{'text-gray-400': readOnly}"
                />
                <TextInput
                    id="valor_global"
                    type="number"
                    step="0.01"
                    class="w-full"
                    v-model.string="form.valor_global"
                    :disabled="readOnly"
                    :class="{'bg-gray-100 cursor-not-allowed': readOnly}"
                />
                <InputError :message="errors.valor_global"/>
            </div>

            <div class="mb-4">
                <InputLabel
                    for="termino_vigencia"
                    value="Término da Vigência"
                    class="required"
                    :class="{'text-gray-400': readOnly}"
                />
                <TextInput
                    id="termino_vigencia"
                    type="date"
                    class="w-full"
                    v-model="form.termino_vigencia"
                    :disabled="readOnly"
                    :class="{'bg-gray-100 cursor-not-allowed': readOnly}"
                />
                <InputError :message="errors.termino_vigencia"/>
            </div>

            <div class="w-full border-t border-gray-200 pt-4 mt-4">
                <!-- Botões para modo somente leitura -->
                <div class="flex justify-center space-x-4" v-if="readOnly">
                    <!-- Botão de Aprovação -->
                    <button type="button"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                            @click="updateStatus('A')"
                            v-if="form.status !== 'A' && form.status !== 'R'">
                        <i class="fa fa-check-circle mr-1"></i> Aprovar
                    </button>

                    <!-- Botão de Revogação -->
                    <button type="button"
                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            @click="updateStatus('R')"
                            v-if="form.status !== 'A' && form.status !== 'R'">
                        <i class="fa fa-times-circle mr-1"></i> Revogar
                    </button>

                    <!-- Mensagem de status quando já foi aprovado ou revogado -->
                    <div class="text-center text-gray-600 italic" v-if="form.status === 'A' || form.status === 'R'">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                              :class="form.status === 'A' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                            <i :class="form.status === 'A' ? 'fa fa-check-circle mr-1' : 'fa fa-times-circle mr-1'"></i>
                            {{ form.status === 'A' ? 'Aprovado' : 'Revogado' }}
                        </span>
                    </div>

                    <!-- Botão de Sair -->
                    <button type="button"
                            class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                            @click="close" aria-label="Close">
                        <i class="fa fa-arrow-left mr-1"></i> Voltar
                    </button>
                </div>

                <!-- Botões para modo de edição -->
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
const form = ref({
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
const errors = ref({});
const processing = ref(false);
const ready = ref(false);
const readOnly = ref(false);

function submit() {
    processing.value = true;

    // Garantir que os valores numéricos estejam no formato correto para o servidor
    const formData = {
        ...form.value,
        valor_global: form.value.valor_global ? String(form.value.valor_global) : ''
    };

    if (props.data?.id) {
        // Rota de update
        axios.post(`/plano-contratacao-tce/item-prorrogacao/${props.data.id}`, form.value)
            .then(response => {
                handleSuccess('Item Prorrogação atualizado com sucesso!');
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
        axios.post('/plano-contratacao-tce/item-prorrogacao', form.value)
            .then(response => {
                handleSuccess('Item Prorrogação criado com sucesso!');
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

// Função para atualizar o status (aprovação/revogação)
function updateStatus(status) {
    processing.value = true;

    axios.post(`/item-prorrogacao-setor/updatestatus/${props.data.id}`, { status: status })
        .then(response => {
            // Atualiza o status no formulário
            form.value.status = status;

            // Mostra notificação de sucesso
            events.emit('notification', {
                type: 'success',
                message: status === 'A' ? 'Item aprovado com sucesso!' : 'Item revogado com sucesso!'
            });

            // Atualiza a tabela e o plano
            events.emit('table-reload', true);
            events.emit('reload-plano', true);

            processing.value = false;
        })
        .catch(error => {
            events.emit('notification', {
                type: 'error',
                message: 'Erro ao atualizar o status do item.'
            });
            processing.value = false;
        });
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
    events.emit('notification', {
        type: 'error',
        message: 'Ocorreu um erro ao salvar o Item Prorrogação.'
    });
    events.emit('reload-plano', false);
}

function close() {
    emit('close');
}

const loadData = async () => {
    try {
        if (props.data?.id) {
            const response = await axios.get(`/item-prorrogacao-setor/${props.data.id}`);
            // Set form data
            const data = response.data;
            form.value = {
                id_plano_contratacao: data.id_plano_contratacao ? String(data.id_plano_contratacao) : '',
                id_contrato: data.id_contrato || '',
                objeto: data.objeto || '',
                numero: data.numero || '',
                empresa: data.empresa || '',
                cnpj: data.cnpj || '',
                valor_global: data.valor_global ? String(data.valor_global) : '',
                termino_vigencia: data.termino_vigencia || '',
                status: data.status || ''
            };

            readOnly.value = Boolean(props.data.readOnly);
        } else if (props.data?.id_plano) {
            // Caso de criação de novo item com id do plano já definido
            form.value.id_plano_contratacao = props.data.id_plano;
            form.value.status = 'E'; // Status inicial "Em andamento"
        }

        ready.value = true;
    } catch (err) {
        console.error('Error loading data:', err);
        events.emit('notification', {
            type: 'error',
            message: 'Erro ao carregar os dados do item.'
        });
    }
};

onMounted(async () => {
    await loadData();
});
</script>
