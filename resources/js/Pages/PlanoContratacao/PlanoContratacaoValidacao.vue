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
                                <!-- Primeira coluna - Setor, Gestor e Status -->
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

                                    <div class="flex items-start mb-4">
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

                                    <div class="flex items-start">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-check-circle text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Status do Plano</h4>
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
                                </div>



                                <!-- Terceira coluna - Valores -->
                                <div class="border-t md:border-t-0 md:border-l border-gray-200 md:pl-6 pt-4 md:pt-0">
                                    <div class="flex items-start mb-4">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-money-bill-wave text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Valor Total</h4>
                                            <p class="text-base font-semibold ">
                                                {{
                                                    planoSelecionado.valor_total ? `R$ ${parseFloat(planoSelecionado.valor_total).toLocaleString('pt-BR', {minimumFractionDigits: 2})}` : 'Não informado'
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start mb-4">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-money-bill-wave text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Valor Contratações</h4>
                                            <p class="text-base font-semibold">
                                                {{
                                                    valor_contratacao ? `R$ ${parseFloat(valor_contratacao).toLocaleString('pt-BR', {minimumFractionDigits: 2})}` : 'R$ 0,00'
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-money-bill-wave text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Valor Prorrogações</h4>
                                            <p class="text-base font-semibold">
                                                {{
                                                    valor_prorrogacao ? `R$ ${parseFloat(valor_prorrogacao).toLocaleString('pt-BR', {minimumFractionDigits: 2})}` : 'R$ 0,00'
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Segunda coluna - Data e Contato -->
                                <div class="border-t md:border-t-0 md:border-l border-gray-200 md:pl-6 pt-4 md:pt-0">
                                    <div class="flex items-start mb-4">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-calendar-alt text-gray-500"></i>
                                        </div>
                                        <div class="">
                                            <h4 class="text-sm font-medium text-gray-500">Plano de Contratação do
                                                TCE</h4>
                                            <p class="text-base font-semibold text-gray-800">
                                                {{
                                                    planoSelecionado.ciclo ?
                                                        formatDateMonthYear(planoSelecionado.ciclo.data_inicio) + ' - ' +
                                                        formatDateMonthYear(planoSelecionado.ciclo.data_fim) :
                                                        'Não informado'
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start mb-4">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-clock text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Prazo do Setor para Envio do
                                                PCA</h4>
                                            <div class="flex items-center space-x-2">
                                                <p class="text-base font-semibold text-gray-800">
                                                    {{
                                                        planoSelecionado.ciclo_hierarquia ?
                                                            formatDataLimite(planoSelecionado.ciclo_hierarquia.dia_limite_cadastro,
                                                                planoSelecionado.ciclo_hierarquia.mes_limite_cadastro) :
                                                            '30 de Abril'
                                                    }}
                                                </p>
                                                <span class="text-gray-400">•</span>
                                                <p :class="[
                                                    'text-sm',
                                                    getDiasRestantesClass(diasRestantesCadastro)
                                                ]">
                                                    {{
                                                        diasRestantesCadastro > 0 ? `Faltam ${diasRestantesCadastro} dias` : 'Prazo encerrado'
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-3">
                                            <i class="fa fa-history text-gray-500"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500">Aprovação do PCA</h4>
                                            <div v-if="planoSelecionado.status === 'P'" class="flex items-center">
                                                <button
                                                    @click="updateStatus('E')"
                                                    class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded-md"
                                                >
                                                    <i class="fa fa-check mr-2"></i> Enviar
                                                </button>
                                            </div>
                                            <div v-if="planoSelecionado.status === 'E' || planoSelecionado.status === 'A'" class="flex items-center">
                                                <popup-icon
                                                    id="novo-plano"
                                                    title="Historico"
                                                    size="xl"
                                                    component="AprovacaoContratacao"
                                                    :data="{idPlano: planoSelecionado.id}"
                                                    variant="primary"
                                                >
                                                    Histórico
                                                </popup-icon>
                                            </div>
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
import PopupIcon from "@/Components/PopupIcon.vue";

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
    },
    hierarquia_setor: {
        type: String,
        default: null
    },
    valor_prorrogacao: {
        type: Number,
        default: null
    },
    valor_contratacao: {
        type: Number,
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

// Calcular dias restantes com base no ciclo_hierarquia
const diasRestantesCadastro = computed(() => {
    if (!planoSelecionado.value || !planoSelecionado.value.ciclo_hierarquia) {
        // Data padrão 30 de Abril se não tiver configuração específica
        const hoje = new Date();
        const dataEncerramento = new Date(planoSelecionado.value?.exercicio, 3, 30); // Month is 0-indexed, so 3 = April

        // Set both dates to midnight to avoid time differences
        hoje.setHours(0, 0, 0, 0);
        dataEncerramento.setHours(0, 0, 0, 0);

        const diferenca = dataEncerramento - hoje;
        return Math.ceil(diferenca / (1000 * 60 * 60 * 24));
    }

    // Usar a configuração do ciclo_hierarquia
    const hoje = new Date();
    const hierarquia = planoSelecionado.value.ciclo_hierarquia;
    const mes = parseInt(hierarquia.mes_limite_cadastro) - 1; // Ajustar para 0-indexed
    const dia = parseInt(hierarquia.dia_limite_cadastro);

    const dataEncerramento = new Date(planoSelecionado.value.exercicio, mes, dia);

    // Set both dates to midnight to avoid time differences
    hoje.setHours(0, 0, 0, 0);
    dataEncerramento.setHours(0, 0, 0, 0);

    const diferenca = dataEncerramento - hoje;
    return Math.ceil(diferenca / (1000 * 60 * 60 * 24));
});

// Formatar datas
const formatDate = (dateStr) => {
    if (!dateStr) return '';

    // Remove a parte do horário se existir
    dateStr = dateStr.split(' ')[0];

    // Se o formato for YYYY-MM-DD, converte para DD/MM/YYYY
    if (dateStr.includes('-')) {
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
    }

    return dateStr;
};

// Formatar data para mostrar apenas mês e ano
const formatDateMonthYear = (dateStr) => {
    if (!dateStr) return '';

    // Remove a parte do horário se existir
    dateStr = dateStr.split(' ')[0];

    // Se o formato for YYYY-MM-DD, extrai mês e ano
    if (dateStr.includes('-')) {
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            const mes = getNomeMes(parseInt(parts[1]));
            return `${mes}/${parts[0]}`;
        }
    }

    return dateStr;
};

// Função para formatar a data limite de cadastro
const formatDataLimite = (dia, mes) => {
    if (!dia || !mes) return '30 de Abril';

    const nomeMes = getNomeMes(parseInt(mes));
    return `${dia} de ${nomeMes}`;
};

// Obter nome do mês em português
const getNomeMes = (numMes) => {
    const meses = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril',
        'Maio', 'Junho', 'Julho', 'Agosto',
        'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];

    return meses[numMes - 1] || '';
};

// Métodos para formatação e estilos
const formatStatus = (status) => {
    switch (status) {
        case 'P':
            return 'Pendente Envio';
        case 'A':
            return 'Aprovado';
        case 'R':
            return 'Reprovado';
        case 'E':
            return 'Em Análise';
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

const getStatusBackgroundColor = (status) => {
    switch (status) {
        case 'P':
            return 'bg-yellow-50';
        case 'A':
            return 'bg-green-50';
        case 'R':
            return 'bg-red-50';
        case 'E':
            return 'bg-blue-50';
        default:
            return 'bg-gray-100';
    }
};

const getStatusIconBackgroundColor = (status) => {
    switch (status) {
        case 'P':
            return 'bg-yellow-100';
        case 'A':
            return 'bg-green-100';
        case 'R':
            return 'bg-red-100';
        case 'E':
            return 'bg-blue-100';
        default:
            return 'bg-gray-300';
    }
};

const getStatusIconTextColor = (status) => {
    switch (status) {
        case 'P':
            return 'text-yellow-600';
        case 'A':
            return 'text-green-600';
        case 'R':
            return 'text-red-600';
        case 'E':
            return 'text-blue-600';
        default:
            return 'text-gray-600';
    }
};

const getStatusIcon = (status) => {
    switch (status) {
        case 'P':
            return 'fa-clock';
        case 'A':
            return 'fa-check-circle';
        case 'R':
            return 'fa-times-circle';
        case 'E':
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

        await axios.post(`/plano-contratacao-setor/updatestatus/${planoSelecionado.value.id}`, {
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
    console.log(props.hierarquia_setor, 'propsss');
    // Já temos os dados do plano via props, só precisamos marcar como pronto
    ready.value = true;
});
</script>
