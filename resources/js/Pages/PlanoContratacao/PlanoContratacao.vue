<template>
    <LayoutPrincipal>
        <div class="">
            <!-- Year Tabs -->
            <div class="border-b border-gray-200 mb-4">
                <nav class="flex -mb-px space-x-4" aria-label="Exercícios">
                    <button
                        v-for="year in availableYears"
                        :key="year"
                        class="py-4 px-6 text-center border-b-2 font-medium text-sm"
                        :class="[
                            selectedYear === year
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                        ]"
                        @click="setSelectedYear(year)"
                    >
                        {{ year }}
                    </button>
                </nav>
            </div>

            <div v-if="ready">
                <!-- Create Plan Button (shown only if no plan exists for the selected year) -->
                <div v-if="!hasPlanForSelectedYear" class="mb-4 flex justify-center">
                    <popup-button
                        id="novo-plano"
                        title="Novo Plano de Contratação"
                        size="xl"
                        component="PlanoContratacaoForm"
                        :data="{year: selectedYear}"
                        variant="primary"
                    >
                        <i class="fa fa-plus mr-2"></i>
                        Criar Plano de Contratação para {{ selectedYear }}
                    </popup-button>
                </div>

                <div v-if="hasPlanForSelectedYear" class="mb-5">
                    <!-- Card único com informações organizadas em seções -->
                    <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                        <!-- Cabeçalho do card com cor baseada no status -->
                        <div :class="[
                            'px-6 py-4 border-b border-gray-200',
                            getStatusBackgroundColor(hasPlanForSelectedYear.status)
                        ]">
                            <div class="flex items-center">
                                <div :class="[
                                    'flex-shrink-0 rounded-full p-2 mr-3',
                                    getStatusIconBackgroundColor(hasPlanForSelectedYear.status)
                                ]">
                                    <i :class="[
                                        'fa',
                                        getStatusIcon(hasPlanForSelectedYear.status),
                                        getStatusIconTextColor(hasPlanForSelectedYear.status)
                                    ]"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Plano de Contratação {{ selectedYear }}</h3>
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
                                                {{
                                                    hasPlanForSelectedYear.gestor?.nome_setor_formatado || 'Não informado'
                                                }}
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
                                                {{ hasPlanForSelectedYear.gestor?.nome_funcionario || 'Não informado' }}
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
                                                {{ selectedYear }}</p>
                                            <p class="text-sm text-yellow-600">
                                                {{
                                                    diasRestantes > 0 ? `Faltam ${diasRestantes} dias` : 'Prazo encerrado'
                                                }}
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
                                                {{ hasPlanForSelectedYear.email || 'E-mail não informado' }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ hasPlanForSelectedYear.telefone || 'Telefone não informado' }}
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
                                                    getStatusBadgeColor(hasPlanForSelectedYear.status)
                                                ]">
                                                    {{ formatStatus(hasPlanForSelectedYear.status) }}
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
                                                    hasPlanForSelectedYear.valor_total ? `R$ ${parseFloat(hasPlanForSelectedYear.valor_total).toLocaleString('pt-BR', {minimumFractionDigits: 2})}` : 'Não informado'
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="hasPlanForSelectedYear"
                     class="flex flex-col md:flex-row space-y-8 md:space-y-0 md:space-x-6">
                    <!-- Seção de Itens de Contratação -->
                    <div class="w-full md:w-1/2 bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-gray-800 mr-2">Contratação</h2>
                            <popup-button
                                id="novo-item-contratacao"
                                title="Novo Item do PCA"
                                size="xl"
                                component="ItemContratacaoForm"
                                :data="{id_plano: hasPlanForSelectedYear.id}"
                                variant="primary"
                            >
                                <i class="fa fa-plus mr-2"></i>
                                Nova Contratação
                            </popup-button>
                        </div>

                        <!-- Datatable para Itens de Contratação -->
                        <div class="overflow-x-auto">
                            <datatable
                                id="itens-contratacao"
                                :columns="columnsContratacao"
                                @delete="confirmRemoveItem('contratacao', $event.id)"
                                :source="sourceContratacao"
                                ref="datatableContratacao"
                            ></datatable>
                        </div>
                    </div>

                    <!-- Seção de Itens de Prorrogação -->
                    <div class="w-full md:w-1/2 bg-white p-6 rounded-lg shadow-md">
                        <div class="flex items-center mb-4 justify-between">
                            <h2 class="text-xl font-bold text-gray-800 mr-2">Prorrogação</h2>
                            <popup-button
                                id="novo-item-prorrogacao"
                                title="Novo Item de Prorrogação"
                                size="xl"
                                component="ItemProrrogacaoForm"
                                :data="{id_plano: hasPlanForSelectedYear.id}"
                                variant="primary"
                            >
                                <i class="fa fa-plus mr-2"></i>
                                Nova Prorrogação
                            </popup-button>
                        </div>

                        <!-- Datatable para Itens de Prorrogação -->
                        <div class="overflow-x-auto">
                            <datatable
                                id="itens-prorrogacao"
                                :columns="columnsProrrogacao"
                                @delete="confirmRemoveItem('prorrogacao', $event.id)"
                                :source="sourceProrrogacao"
                                ref="datatableProrrogacao"
                            ></datatable>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="bg-white p-6 rounded content-center">
                <i class="fa fa-spinner fa-spin mr-1"></i>
            </div>
        </div>
    </LayoutPrincipal>
</template>

<script setup>
import {ref, inject, computed, onMounted, watch} from 'vue';
import PopupButton from "@/Components/PopupButton.vue";
import PopupIcon from "@/Components/PopupIcon.vue";
import Datatable from "@/Components/datatable/Datatable.vue";
import LayoutPrincipal from "@/Layouts/LayoutPrincipal.vue";

const events = inject('events');

// Current year and years list
const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);
const yearsWithPlans = ref([]);
const ready = ref(false);
const hasPlanForSelectedYear = ref(false);

// Referências para os datatables
const datatableContratacao = ref(null);
const datatableProrrogacao = ref(null);

// Source URLs para os datatables
const sourceContratacao = ref('');
const sourceProrrogacao = ref('');

// Available years for tabs (current year, next year, and years with plans)
const availableYears = computed(() => {
    // Start with an empty array
    let years = [];

    // Add years with plans (if any) - convert to integers
    if (yearsWithPlans.value && yearsWithPlans.value.length > 0) {
        years = yearsWithPlans.value.map(year => parseInt(year));
    }

    // Add current year if not already included
    if (!years.includes(currentYear)) {
        years.push(currentYear);
    }

    // Add next year if not already included
    if (!years.includes(currentYear + 1)) {
        years.push(currentYear + 1);
    }

    // Sort in descending order (newest years first)
    return years.sort((a, b) => b - a);
});

const diasRestantes = computed(() => {
    const hoje = new Date();
    const dataEncerramento = new Date(selectedYear.value, 3, 30); // Month is 0-indexed, so 3 = April

    // Set both dates to midnight to avoid time differences
    hoje.setHours(0, 0, 0, 0);
    dataEncerramento.setHours(0, 0, 0, 0);

    const diferenca = dataEncerramento - hoje;
    const dias = Math.ceil(diferenca / (1000 * 60 * 60 * 24));

    return dias;
});

// Add this method to style based on remaining days
const getDiasRestantesClass = (dias) => {
    if (dias <= 0) {
        return 'text-red-600 font-medium';
    } else if (dias <= 30) {
        return 'text-yellow-600 font-medium';
    } else {
        return 'text-green-600';
    }
};
// Datatable columns para Itens de Contratação
const columnsContratacao = ref([
    {
        name: 'descricao',
        title: 'Descrição',
        width: '25%',
        sort: 'descricao',
        nowrap: true,
        formatter: (value) => {
            return value && value.length > 20 ?
                `<span title="${value.replace(/"/g, '&quot;')}">${value.substring(0, 20)}...</span>` :
                value;
        }
    },
    {
        name: 'valor_total',
        title: 'Valor Total',
        width: '15%',
        sort: 'valor_total',
        nowrap: true,
        formatter: (value) => {
            return value ? `R$ ${parseFloat(value).toLocaleString('pt-BR', {minimumFractionDigits: 2})}` : '';
        }
    },
    {
        name: 'classificacao', title: 'Classificação', width: '15%', sort: 'classificacao', nowrap: true,
        formatter: (value) => {
            return formatClassificacao(value);
        }
    },
    {
        name: 'status',
        title: 'Status',
        width: '15%',
        sort: 'status',
        nowrap: true,
        formatter: (value) => {
            return `<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full text-white ${getStatusBadgeColor(value)}">${formatStatus(value)}</span>`;
        }
    },
    {
        name: 'id',
        title: 'Ação',
        width: '10%',
        nowrap: true,
        contentClass: 'text-center',
        formatter: (value, row) => {
            let output = "";
            if (row.status !== 'A') {
                output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Editar" data-action="popup" data-size="xl" data-component="ItemContratacaoForm" data-title="Editar Item de Contratação" class="mx-1 action text-align-center tooltip tooltip--top"><i class="fa fa-pencil text-blue-600"></i></a>`;
                output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Remover" data-action="delete" class="action mx-0 action-delete tooltip tooltip--top"><i class="fa fa-trash mx-1 text-blue-600"></i></a>`;
            }
            return output;
        }
    }
]);

// Datatable columns para Itens de Prorrogação
const columnsProrrogacao = ref([
    {
        name: 'objeto',
        title: 'Objeto',
        width: '25%',
        sort: 'objeto',
        nowrap: true,
        formatter: (value) => {
            return value && value.length > 40 ?
                `<span title="${value.replace(/"/g, '&quot;')}">${value.substring(0, 40)}...</span>` :
                value;
        }
    },
    {name: 'numero', title: 'Número', width: '15%', sort: 'numero', nowrap: true},
    {
        name: 'valor_global',
        title: 'Valor Global',
        width: '15%',
        sort: 'valor_global',
        nowrap: true,
        formatter: (value) => {
            return value ? `R$ ${parseFloat(value).toLocaleString('pt-BR', {minimumFractionDigits: 2})}` : '';
        }
    },
    {
        name: 'status',
        title: 'Status',
        width: '15%',
        sort: 'status',
        nowrap: true,
        formatter: (value) => {
            return `<span class="inline-flex px-2 py-1 text-xs font-medium rounded-full text-white ${getStatusBadgeColor(value)}">${formatStatusContrato(value)}</span>`;
        }
    },
    {
        name: 'id',
        title: 'Ação',
        width: '10%',
        nowrap: true,
        formatter: (value, row) => {
            let output = "";
            if (row.status !== 'A') {
                output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Editar" data-action="popup" data-size="xl" data-component="ItemProrrogacaoForm" data-title="Editar Item de Prorrogação" class="mx-1 action text-align-center tooltip tooltip--top"><i class="fa fa-pencil text-blue-600"></i></a>`;
                output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Remover" data-action="delete" class="action mx-0 action-delete tooltip tooltip--top"><i class="fa fa-trash mx-1 text-blue-600"></i></a>`;
            }
            return output;
        }
    }
]);

// Função para formatar o status
const formatStatus = (status) => {
    switch (status) {
        case 'E':
            return 'Em andamento';
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

const formatStatusContrato = (status) => {
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

// Funções para definir cores baseadas no status do plano
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

// Nova função para definir cores do badge de status (background com texto branco)
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

// Set selected year and force table reload
const setSelectedYear = async (year) => {
    selectedYear.value = year;
    events.emit('loading', true);
    // Primeiro verifica se existe um plano para o ano selecionado
    await checkPlanExistence();
    // Após verificar a existência do plano, os datatables serão atualizados automaticamente
    events.emit('loading', false);
};

// Fetch years with plans from the server
const fetchYearsWithPlans = async () => {
    try {
        events.emit('loading', true);
        const response = await axios.get('/plano-contratacao/years');
        yearsWithPlans.value = response.data.years || [];
        await checkPlanExistence();
    } catch (error) {
        console.error('Erro ao buscar anos com planos:', error);
        events.emit('notification', {
            type: 'error',
            message: 'Não foi possível carregar os anos disponíveis'
        });
    } finally {
        events.emit('loading', false);
    }
};

// Check if the selected year has a plan
const checkPlanExistence = async () => {
    try {
        events.emit('loading', true);
        const response = await axios.get(`/plano-contratacao/exists?exercicio=${selectedYear.value}`);
        hasPlanForSelectedYear.value = response.data.exists;

        if (hasPlanForSelectedYear.value) {
            // Atualiza as URLs de origem para os datatables
            sourceContratacao.value = `/item-contratacao/${hasPlanForSelectedYear.value.id}/list`;
            sourceProrrogacao.value = `/item-prorrogacao/${hasPlanForSelectedYear.value.id}/list`;

            // Recarrega os datatables se já estiverem inicializados
            if (datatableContratacao.value) {
                events.emit('table-reload', 'itens-contratacao');
            }
            if (datatableProrrogacao.value) {
                events.emit('table-reload', 'itens-prorrogacao');
            }
        }
    } catch (error) {
        console.error('Erro ao verificar existência do plano:', error);
        hasPlanForSelectedYear.value = false;
    } finally {
        events.emit('loading', false);
    }
};

// Formatação de valores
const formatDate = (dateString) => {
    if (!dateString) return 'Não informado';
    const options = {day: '2-digit', month: '2-digit', year: 'numeric'};
    return new Date(dateString).toLocaleDateString('pt-BR', options);
};

const formatClassificacao = (value) => {
    switch (value) {
        case 'A4':
            return 'Material de Consumo';
        case 'A5':
            return 'Material Permanente';
        case 'A1':
            return 'Serviços Pessoa Física';
        case 'A2':
            return 'Serviços de TI';
        case 'A3':
            return 'Serviços Pessoa Jurídica';
        default:
            return value || 'Não classificado';
    }
};

// Delete confirmation
const confirmRemoveItem = async (type, id) => {
    if (!confirm(`Tem certeza que deseja excluir este item de ${type === 'contratacao' ? 'contratação' : 'prorrogação'}?`)) {
        return;
    }

    events.emit('loading', true);

    try {
        if (type === 'contratacao') {
            await axios.delete(`/item-contratacao/${id}`);
            events.emit('table-reload', 'itens-contratacao');
        } else {
            await axios.delete(`/item-prorrogacao/${id}`);
            events.emit('table-reload', 'itens-prorrogacao');
        }

        events.emit('notification', {
            type: 'success',
            message: `Item de ${type === 'contratacao' ? 'contratação' : 'prorrogação'} excluído com sucesso.`
        });
    } catch (err) {
        events.emit('notification', {
            type: 'error',
            message: err.response?.data?.message || 'Não foi possível excluir o registro.'
        });
    } finally {
        events.emit('loading', false);
    }
};

// Event listeners
onMounted(async () => {
    await fetchYearsWithPlans();
    ready.value = true;

    // Listen for form submission to refresh the data
    events.on('reload-plano', async () => {
        await checkPlanExistence();
        await fetchYearsWithPlans();
    });
});
</script>
