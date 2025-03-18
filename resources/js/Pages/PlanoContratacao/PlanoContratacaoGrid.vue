<template>
    <LayoutPrincipal>
        <div class="bg-white p-6 rounded">
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

            <!-- Create Plan Button (shown only if no plan exists for the selected year) -->
            <div v-if="!hasPlanForSelectedYear" class="mb-4 flex justify-center">
                <popup-button
                    id="novo-plano"
                    title="Novo Plano de Contratação"
                    size="xl"
                    component="PlanoContratacaoForm"
                    :data="{year:selectedYear}"
                    variant="primary"
                >
                    <i class="fa fa-plus mr-2"></i>
                    Criar Plano de Contratação para {{ selectedYear }}
                </popup-button>
            </div>

            <div v-if="hasPlanForSelectedYear " class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="space-y-1">
                        <h3 class="text-lg font-semibold text-gray-700">Setor</h3>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Nome:</span>
                            {{ hasPlanForSelectedYear.gestor?.nome_setor_formatado || 'Não informado' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <h3 class="text-lg font-semibold text-gray-700">Gestor do PCA</h3>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Nome:</span>
                            {{ hasPlanForSelectedYear.gestor?.nome_funcionario || 'Não informado' }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Matrícula:</span>
                            {{ hasPlanForSelectedYear.numero_matricula_gestor || 'Não informado' }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <h3 class="text-lg font-semibold text-gray-700">Informações do Plano</h3>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Status:</span>
                            <span :class="[
                                hasPlanForSelectedYear.status === 'A' ? 'text-green-600' :
                                hasPlanForSelectedYear.status === 'I' ? 'text-yellow-600' :
                                'text-gray-600'
                            ]">
                                {{
                                    hasPlanForSelectedYear.status === 'A' ? 'Aprovado' :
                                        hasPlanForSelectedYear.status === 'I' ? 'Iniciado' :
                                            hasPlanForSelectedYear.status || 'Não definido'
                                }}
                            </span>
                        </p>
                        <p v-if="hasPlanForSelectedYear.valor_total" class="text-sm text-gray-600">
                            <span class="font-medium">Valor Total:</span> R$ {{
                                parseFloat(hasPlanForSelectedYear.valor_total).toLocaleString('pt-BR', {minimumFractionDigits: 2})
                            }}
                        </p>
                        <p v-else class="text-sm text-gray-600">
                            <span class="font-medium">Valor Total:</span> Não informado
                        </p>
                    </div>
                </div>
            </div>
            <!-- Datatable Component -->
            <datatable
                v-if="hasPlanForSelectedYear"
                id="plano_contratacao"
                :columns="columns"
                @delete="confirmRemove"
                :source="source"
                :params="tableParams"
                ref="datatable"
            ></datatable>
        </div>

    </LayoutPrincipal>
</template>

<script setup>
import {ref, inject, computed, onMounted, watch} from 'vue';
import Datatable from "@/Components/datatable/Datatable.vue";
import PopupButton from "@/Components/PopupButton.vue";
import LayoutPrincipal from "@/Layouts/LayoutPrincipal.vue";

const events = inject('events');
const datatable = ref(null);

// Source URL for the datatable
const source = '/plano-contratacao/list';

// Current year and years list
const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);
const yearsWithPlans = ref([]);
const hasPlanForSelectedYear = ref(false);

// Table parameters for filtering
const tableParams = computed(() => ({
    exercicio: selectedYear.value
}));

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
// Set selected year and force table reload
const setSelectedYear = (year) => {
    selectedYear.value = year;
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
        console.log(hasPlanForSelectedYear.value)
    } catch (error) {
        console.error('Erro ao verificar existência do plano:', error);
        hasPlanForSelectedYear.value = false;
    } finally {
        events.emit('loading', false);
    }
};

// Datatable columns
const columns = ref([
    {name: 'codigo_setor', title: 'Codigo Setor', width: '12%', sort: 'codigo_setor', nowrap: true},
    {
        name: 'numero_matricula_gestor',
        title: 'Matricula Gestor',
        width: '12%',
        sort: 'numero_matricula_gestor',
        nowrap: true
    },
    {name: 'exercicio', title: 'Exercício', width: '8%', sort: 'exercicio', nowrap: true},
    {name: 'email', title: 'Email', width: '15%', sort: 'email', nowrap: true},
    {name: 'telefone', title: 'Telefone', width: '10%', sort: 'telefone', nowrap: true},
    {name: 'status', title: 'Status', width: '10%', sort: 'status', nowrap: true},
    {
        name: 'valor_total', title: 'Valor Total', width: '10%', sort: 'valor_total', nowrap: true,
        formatter: (value) => {
            return value ? `R$ ${parseFloat(value).toLocaleString('pt-BR', {minimumFractionDigits: 2})}` : '';
        }
    },
    {
        name: 'id',
        title: 'Ação',
        width: '9%',
        nowrap: true,
        contentClass: 'text-center',
        formatter: (value, row) => {
            let output = "";
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Editar" data-action="popup" data-size="xl" data-component="plano-contratacao-form" data-title="Editar Plano Contratação" class="mx-1 action text-align-center tooltip tooltip--top"><i class="fa fa-pencil"></i></a>`;
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Remover" data-action="delete" class="action mx-0 action-delete tooltip tooltip--top"><i class="fa fa-trash mx-1"></i></a>`;
            return output;
        }
    }
]);

// Delete confirmation
const confirmRemove = async (data) => {
    events.emit('loading', true);
    try {
        await axios.delete('/plano-contratacao/' + data.id);
        events.emit('table-reload');
        await checkPlanExistence(); // Re-check after deletion
        await fetchYearsWithPlans(); // Update years list
        events.emit('notification', {
            type: 'success',
            message: 'Plano de Contratação excluído com sucesso.'
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

// Watch for changes to selectedYear
watch(selectedYear, () => {
    checkPlanExistence();
});

// Event listeners
onMounted(() => {
    fetchYearsWithPlans();

    // Listen for form submission to refresh the data
    events.on('form-submitted', (success) => {
        if (success) {
            checkPlanExistence();
            fetchYearsWithPlans();
        }
    });
});
</script>
