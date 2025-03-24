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
                           <ItemContratacaoGrid></ItemContratacaoGrid>
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
                            <ItemProrrogacaoGrid></ItemProrrogacaoGrid>
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
import LayoutPrincipal from "@/Layouts/LayoutPrincipal.vue";
import ItemContratacaoGrid from "@/Pages/ItemContratacao/ItemContratacaoGrid.vue";
import ItemProrrogacaoGrid from "@/Pages/ItemProrrogacao/ItemProrrogacaoGrid.vue";

const events = inject('events');

// Current year and years list
const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);
const yearsWithPlans = ref([]);
const ready = ref(false);
const hasPlanForSelectedYear = ref(false);
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
    events.emit('reload-item-grid', year);
    // Primeiro verifica se existe um plano para o ano selecionado
    await checkPlanExistence();
    // Após verificar a existência do plano, os datatables serão atualizados automaticamente
    events.emit('loading', false);
};

// Fetch years with plans from the server
const fetchYearsWithPlans = async () => {
    try {
        events.emit('loading', true);
        const response = await axios.get('/plano-contratacao-setor/years');
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
        const response = await axios.get(`/plano-contratacao-setor/exists?exercicio=${selectedYear.value}`);
        hasPlanForSelectedYear.value = response.data.exists;
    } catch (error) {
        console.error('Erro ao verificar existência do plano:', error);
        hasPlanForSelectedYear.value = false;
    } finally {
        events.emit('loading', false);
    }
};

// Event listeners
onMounted(async () => {
    await fetchYearsWithPlans();
    ready.value = true;
    events.on('reload-plano', async () => {
        await checkPlanExistence();
        await fetchYearsWithPlans();
    });
});
</script>
