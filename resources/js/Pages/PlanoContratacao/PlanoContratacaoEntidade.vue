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
                <!-- Cabeçalho com informações da entidade -->
                <div class="mb-5">
                    <div class="bg-white rounded-md shadow-md border border-gray-200 overflow-hidden mb-6">
                        <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 rounded-full p-2 mr-3 bg-blue-100">
                                    <i class="fa fa-building text-blue-600"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">TCE - {{ gestorInfo.nome_setor_formatado || 'Entidade' }}</h3>
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Informações do Gestor -->
                                <div>
                                    <div class="flex items-start mb-4">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-user text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Gestor Responsável</h4>
                                            <p class="text-base font-semibold text-gray-800">
                                                {{ gestorInfo.nome_funcionario || 'Não informado' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data de Encerramento -->
                                <div class="border-t md:border-t-0 md:border-l border-gray-200 md:pl-6 pt-4 md:pt-0">
                                    <div class="flex items-start mb-4">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-calendar text-gray-500"></i>
                                        </div>
                                        <div class="">
                                            <h4 class="text-sm font-medium text-gray-500">Data Encerramento</h4>
                                            <p class="text-base font-semibold text-gray-800">30 de Abril de {{ selectedYear }}</p>
                                            <p class="text-sm" :class="getDiasRestantesClass(diasRestantes)">
                                                {{ diasRestantes > 0 ? `Faltam ${diasRestantes} dias` : 'Prazo encerrado' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Valor Total Agregado -->
                                <div class="border-t md:border-t-0 md:border-l border-gray-200 md:pl-6 pt-4 md:pt-0">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-md bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-money-bill-wave text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Valor Total de Contratações</h4>
                                            <p class="text-base font-semibold text-blue-600">
                                                {{ valorTotalConsolidado ? `R$ ${parseFloat(valorTotalConsolidado).toLocaleString('pt-BR', {minimumFractionDigits: 2})}` : 'Não informado' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grid de Planos -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Planos de Contratação dos Setores </h2>
                        <div class="overflow-x-auto">
                            <datatable
                                id="planos_consolidados"
                                :columns="columns"
                                :source="source"
                                @view-items="viewItems"
                            ></datatable>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="bg-white p-6 rounded content-center">
                <i class="fa fa-spinner fa-spin mr-1"></i> Carregando...
            </div>
        </div>
    </LayoutPrincipal>
</template>

<script setup>
import { ref, inject, computed, onMounted, watch } from 'vue';
import LayoutPrincipal from "@/Layouts/LayoutPrincipal.vue";
import Datatable from "@/Components/datatable/Datatable.vue";

const events = inject('events');

// State variables
const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);
const yearsWithPlans = ref([]);
const ready = ref(false);
const gestorInfo = ref({});
const valorTotalConsolidado = ref(0);
const planosConsolidados = ref([]);

// URL para os dados da tabela com o ano selecionado
const source = computed(() => {
    return `/plano-contratacao-tce/list?exercicio=${selectedYear.value}`;
});

// Dias restantes até o prazo final
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

// Computed para os anos disponíveis (anos com planos + ano atual + próximo ano)
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

// Colunas para o datatable
const columns = ref([
    {
        name: 'nome_setor_formatado',
        title: 'Setor',
        width: '25%',
        sort: 'nome_setor_formatado',
        nowrap: true,
    },
    {
        name: 'nome_funcionario',
        title: 'Gestor Responsável',
        width: '20%',
        sort: 'nome_funcionario',
        nowrap: true,
    },
    {
        name: 'valor_total',
        title: 'Valor Total',
        width: '15%',
        sort: 'valor_total',
        nowrap: true,
        formatter: (value) => {
            return value ? `R$ ${parseFloat(value).toLocaleString('pt-BR', {minimumFractionDigits: 2})}` : 'R$ 0,00';
        }
    },
    {
        name: 'status',
        title: 'Status',
        width: '10%',
        sort: 'status',
        nowrap: true,
        formatter: (value) => {
            return `<span class="inline-flex px-2 py-1 text-xs font-medium  rounded-md text-white ${getStatusBadgeColor(value)}">${formatStatus(value)}</span>`;
        }
    },
    {
        name: 'id',
        title: 'Ações',
        width: '8%',
        nowrap: true,
        formatter: (value, row) => {
            // Botão para ver os itens de contratação e prorrogação
            return `<a href="/plano-contratacao-tce/validacao/${row.id}" data-json='{"id": "${value}", "setor": "${row.codigo_setor}"}' data-action="view-items" class="mx-1 tooltip tooltip--top" data-tooltip="Ver Itens"><i class="fa fa-file-circle-check text-blue-600"></i></a>`;
        }
    }
]);

// Métodos para formatação e estilos
const formatStatus = (status) => {
    switch (status) {
        case 'E':
            return 'Enviado';
        case 'A':
            return 'Aprovado';
        case 'R':
            return 'Reprovado';
        case 'P':
            return 'Pendente Envio';
        default:
            return status || 'Não definido';
    }
};

const getStatusBadgeColor = (status) => {
    switch (status) {
        case 'P':
            return 'bg-yellow-500';
        case 'A':
            return 'bg-green-500';
        case 'R':
            return 'bg-red-500';
        case 'E':
            return 'bg-blue-500';
        default:
            return 'bg-gray-500';
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

// Método para buscar os anos com planos
const fetchYearsWithPlans = async () => {
    try {
        events.emit('loading', true);
        const response = await axios.get('/plano-contratacao-tce/years');
        yearsWithPlans.value = response.data.years || [];
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

// Método para buscar informações do gestor
const fetchGestorInfo = async () => {
    try {
        events.emit('loading', true);
        const response = await axios.get('/plano-contratacao-tce/gestorInfo');
        gestorInfo.value = response.data || {};
    } catch (error) {
        console.error('Erro ao buscar informações do gestor:', error);
        events.emit('notification', {
            type: 'error',
            message: 'Não foi possível carregar as informações do gestor'
        });
    } finally {
        events.emit('loading', false);
    }
};

// Método para buscar planos e calcular o valor total consolidado
const fetchPlanosECalcularTotal = async () => {
    try {
        events.emit('loading', true);
        const response = await axios.get(`/plano-contratacao-tce/list?exercicio=${selectedYear.value}`);
        planosConsolidados.value = response.data.data || [];

        // Calcular a soma de todos os valores totais
        valorTotalConsolidado.value = planosConsolidados.value.reduce((total, plano) => {
            return total + (parseFloat(plano.valor_total) || 0);
        }, 0);
    } catch (error) {
        console.error('Erro ao buscar planos e calcular valor total:', error);
        events.emit('notification', {
            type: 'error',
            message: 'Erro ao carregar planos consolidados'
        });
    } finally {
        events.emit('loading', false);
    }
};

const viewItems = (data) => {
    // Implementar a lógica para visualizar os itens de contratação e prorrogação
    // Pode abrir um modal com as informações detalhadas
    console.log('Ver itens:', data);
    events.emit('notification', {
        type: 'info',
        message: `Visualizando itens do setor ${data.setor}`
    });
};

const setSelectedYear = async (year) => {
    selectedYear.value = year;
    await fetchPlanosECalcularTotal();
    events.emit('table-reload');
};

// Lifecycle hooks
onMounted(async () => {
    ready.value = false;
    try {
        await fetchYearsWithPlans();
        await fetchGestorInfo();
        await fetchPlanosECalcularTotal();
    } catch (error) {
        console.error('Erro durante a inicialização:', error);
    } finally {
        ready.value = true;
    }

    // Configurar listeners de eventos
    events.on('reload-plano', async () => {
        await fetchYearsWithPlans();
        await fetchPlanosECalcularTotal();
        events.emit('table-reload');
    });
});
</script>
