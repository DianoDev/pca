<template>
    <LayoutPrincipal>
        <div class="">
            <div v-if="ready">
                <div v-if="planoSelecionado" class="mb-5">
                    <!-- Card cabeçalho com ações de validação -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden mb-6">
                        <div :class="[
                            'px-6 py-4 border-b border-gray-200',
                            getStatusBackgroundColor(planoSelecionado.status)
                        ]">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div :class="[
                                        'flex-shrink-0 rounded-full p-2 mr-3',
                                        getStatusIconBackgroundColor(planoSelecionado.status)
                                    ]">
                                        <i :class="[
                                            'fa',
                                            getStatusIcon(planoSelecionado.status),
                                            getStatusIconTextColor(planoSelecionado.status)
                                        ]"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800">
                                        Validação do Plano de Contratação {{ planoSelecionado.exercicio }}
                                    </h3>
                                </div>
                                <!-- Botões de Validação -->
                                <div class="flex items-center space-x-3" v-if="planoSelecionado.status !== 'A' && planoSelecionado.status !== 'R'">
                                    <button
                                        @click="updateStatus('A')"
                                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md flex items-center"
                                    >
                                        <i class="fa fa-check mr-2"></i> Aprovar
                                    </button>
                                    <button
                                        @click="updateStatus('R')"
                                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md flex items-center"
                                    >
                                        <i class="fa fa-times mr-2"></i> Reprovar
                                    </button>
                                </div>
                                <!-- Mensagem de Já Validado -->
                                <div v-else class="text-gray-600 italic">
                                    Plano {{ planoSelecionado.status === 'A' ? 'Aprovado' : 'Reprovado' }}
                                </div>
                            </div>
                        </div>

                        <!-- Corpo do card com as informações do plano -->
                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Primeira coluna - Setor e Gestor -->
                                <div>
                                    <div class="flex items-start mb-4">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-building text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Setor Responsável</h4>
                                            <p class="text-base font-semibold text-gray-800">
                                                {{ planoSelecionado.setor_nome || 'Não informado' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-user text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Gestor Responsável</h4>
                                            <p class="text-base font-semibold text-gray-800">
                                                {{ planoSelecionado.gestor_nome || 'Não informado' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Segunda coluna - Exercício e Contato -->
                                <div class="border-t md:border-t-0 md:border-l border-gray-200 md:pl-6 pt-4 md:pt-0">
                                    <div class="flex items-start mb-4">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-calendar text-gray-500"></i>
                                        </div>
                                        <div class="">
                                            <h4 class="text-sm font-medium text-gray-500">Data Encerramento</h4>
                                            <p class="text-base font-semibold text-gray-800">30 de Abril de
                                                {{ planoSelecionado.exercicio }}</p>
                                            <p class="text-sm" :class="getDiasRestantesClass(diasRestantes)">
                                                {{ diasRestantes > 0 ? `Faltam ${diasRestantes} dias` : 'Prazo encerrado' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-envelope text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Contato</h4>
                                            <p class="text-base font-semibold text-gray-800">
                                                {{ planoSelecionado.email || 'E-mail não informado' }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ planoSelecionado.telefone || 'Telefone não informado' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terceira coluna - Status e Valor Total -->
                                <div class="border-t md:border-t-0 md:border-l border-gray-200 md:pl-6 pt-4 md:pt-0">
                                    <div class="flex items-start mb-4">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-check-circle text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                                            <p class="mt-1">
                                                <span :class="[
                                                    'inline-flex px-3 py-1 text-sm font-medium rounded-full text-white',
                                                    getStatusBadgeColor(planoSelecionado.status)
                                                ]">
                                                    {{ formatStatus(planoSelecionado.status) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-money-bill-wave text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Valor Total</h4>
                                            <p class="text-base font-semibold text-blue-600">
                                                {{
                                                    planoSelecionado.valor_total ? `R$ ${parseFloat(planoSelecionado.valor_total).toLocaleString('pt-BR', {minimumFractionDigits: 2})}` : 'Não informado'
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row space-y-8 md:space-y-0 md:space-x-6">
                        <!-- Seção de Itens de Contratação -->
                        <div class="w-full md:w-1/2 bg-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center mb-4">
                                <h2 class="text-xl font-bold text-gray-800 mr-2">Itens de Contratação</h2>
                            </div>

                            <!-- Componente Grid para Itens de Contratação -->
                            <ItemContratacaoValidacaoGrid :plano-id="planoSelecionado.id" />
                        </div>

                        <!-- Seção de Itens de Prorrogação -->
                        <div class="w-full md:w-1/2 bg-white p-6 rounded-lg shadow-md">
                            <div class="flex items-center mb-4">
                                <h2 class="text-xl font-bold text-gray-800 mr-2">Itens de Prorrogação</h2>
                            </div>

                            <!-- Componente Grid para Itens de Prorrogação -->
                            <ItemProrrogacaoValidacaoGrid :plano-id="planoSelecionado.id" />
                        </div>
                    </div>
                </div>
                <div v-else-if="erro" class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="mb-4">
                        <i class="fa fa-exclamation-triangle text-yellow-500 text-3xl mb-2"></i>
                        <p class="text-gray-700">{{ erro }}</p>
                    </div>
                    <a href="/plano-contratacao-tce" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md inline-block">
                        <i class="fa fa-arrow-left mr-2"></i> Voltar
                    </a>
                </div>
                <div v-else class="bg-white p-6 rounded-lg shadow-md text-center">
                    <div class="mb-4">
                        <i class="fa fa-exclamation-triangle text-yellow-500 text-3xl mb-2"></i>
                        <p class="text-gray-700">Nenhum plano encontrado para validação.</p>
                    </div>
                    <a href="/plano-contratacao-tce" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md inline-block">
                        <i class="fa fa-arrow-left mr-2"></i> Voltar
                    </a>
                </div>
            </div>
            <div v-else class="bg-white p-6 rounded content-center">
                <i class="fa fa-spinner fa-spin mr-1"></i> Carregando...
            </div>
        </div>
    </LayoutPrincipal>
</template>

<script setup>
import { ref, inject, computed, onMounted } from 'vue';
import LayoutPrincipal from "@/Layouts/LayoutPrincipal.vue";
import ItemContratacaoValidacaoGrid from "@/Pages/ItemContratacao/ItemContratacaoValidacaoGrid.vue";
import ItemProrrogacaoValidacaoGrid from "@/Pages/ItemProrrogacao/ItemProrrogacaoValidacaoGrid.vue";

const events = inject('events');

// Props recebidas do controlador
const props = defineProps({
    plano: {
        type: Object,
        default: null
    },
    erro: {
        type: String,
        default: null
    }
});

// State variables
const ready = ref(false);
const planoSelecionado = ref(props.plano);

// Dias restantes até o prazo final
const diasRestantes = computed(() => {
    if (!planoSelecionado.value) return 0;

    const hoje = new Date();
    const dataEncerramento = new Date(planoSelecionado.value.exercicio, 3, 30); // Month is 0-indexed, so 3 = April

    // Set both dates to midnight to avoid time differences
    hoje.setHours(0, 0, 0, 0);
    dataEncerramento.setHours(0, 0, 0, 0);

    const diferenca = dataEncerramento - hoje;
    const dias = Math.ceil(diferenca / (1000 * 60 * 60 * 24));

    return dias;
});

// Métodos para formatação e estilos
const formatStatus = (status) => {
    switch (status) {
        case 'E':
            return 'Pendente Aprovação';
        case 'A':
            return 'Aprovado';
        case 'R':
            return 'Reprovado';
        case 'I':
            return 'Iniciado';
        default:
            return status || 'Não definido';
    }
};

const getStatusBadgeColor = (status) => {
    switch (status) {
        case 'E':
            return 'bg-yellow-500';
        case 'A':
            return 'bg-green-500';
        case 'R':
            return 'bg-red-500';
        case 'I':
            return 'bg-blue-500';
        default:
            return 'bg-gray-500';
    }
};

const getStatusBackgroundColor = (status) => {
    switch (status) {
        case 'E':
            return 'bg-yellow-50';
        case 'A':
            return 'bg-green-50';
        case 'R':
            return 'bg-red-50';
        case 'I':
            return 'bg-blue-50';
        default:
            return 'bg-gray-100';
    }
};

const getStatusIconBackgroundColor = (status) => {
    switch (status) {
        case 'E':
            return 'bg-yellow-100';
        case 'A':
            return 'bg-green-100';
        case 'R':
            return 'bg-red-100';
        case 'I':
            return 'bg-blue-100';
        default:
            return 'bg-gray-300';
    }
};

const getStatusIconTextColor = (status) => {
    switch (status) {
        case 'E':
            return 'text-yellow-600';
        case 'A':
            return 'text-green-600';
        case 'R':
            return 'text-red-600';
        case 'I':
            return 'text-blue-600';
        default:
            return 'text-gray-600';
    }
};

const getStatusIcon = (status) => {
    switch (status) {
        case 'E':
            return 'fa-clock';
        case 'A':
            return 'fa-check-circle';
        case 'R':
            return 'fa-times-circle';
        case 'I':
            return 'fa-clipboard-list';
        default:
            return 'fa-clipboard-list';
    }
};

const getDiasRestantesClass = (dias) => {
    if (dias <= 0) {
        return 'text-red-600 font-medium';
    } else if (dias <= 30) {
        return 'text-yellow-600 font-medium';
    } else {
        return 'text-green-600';
    }
};

// Método para atualizar o status do plano
const updateStatus = async (status) => {
    if (!planoSelecionado.value) return;

    try {
        events.emit('loading', true);

        await axios.post(`/plano-contratacao-tce/updatestatus/${planoSelecionado.value.id}`, {
            status: status
        });

        // Atualiza o status na interface
        planoSelecionado.value.status = status;

        events.emit('notification', {
            type: 'success',
            message: `Plano ${status === 'A' ? 'aprovado' : 'reprovado'} com sucesso!`
        });

        // Recarregar datatables para refletir possíveis mudanças nos itens
        events.emit('table-reload');
    } catch (error) {
        console.error('Erro ao atualizar status:', error);
        events.emit('notification', {
            type: 'error',
            message: 'Não foi possível atualizar o status do plano'
        });
    } finally {
        events.emit('loading', false);
    }
};

// Lifecycle hooks
onMounted(() => {
    // Já temos os dados do plano via props, só precisamos marcar como pronto
    ready.value = true;
});
</script>
