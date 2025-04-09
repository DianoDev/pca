<template>
    <div class="historico-aprovacoes">
        <!-- Cabeçalho -->
        <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-800">Histórico de Aprovações</h3>
            <p class="text-sm text-gray-600">
                {{ aprovacoes.length > 0 ?
                `Histórico de aprovações para o plano ${planoInfo}` :
                'Nenhum registro de aprovação encontrado' }}
            </p>
        </div>

        <!-- Loading indicator -->
        <div v-if="loading" class="py-4 flex justify-center">
            <i class="fa fa-spinner fa-spin mr-2"></i>
            <span>Carregando histórico...</span>
        </div>

        <!-- Mensagem de erro -->
        <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
            <i class="fa fa-exclamation-circle mr-2"></i>
            {{ error }}
        </div>

        <!-- Timeline de aprovações -->
        <div v-else-if="aprovacoes.length > 0" class="border-l-2 border-blue-500 ml-3">
            <div v-for="(aprovacao, index) in aprovacoes" :key="aprovacao.id"
                 class=" mb-6 ml-6">
                <!-- Indicador na timeline -->
                <div :class="[
                    'absolute -left-8 mt-2 rounded-full w-6 h-6 flex items-center justify-center',
                    getStatusIconBackgroundColor(aprovacao.status)
                ]">
                    <i :class="[
                        'fa text-xs',
                        getStatusIcon(aprovacao.status),
                        getStatusIconTextColor(aprovacao.status)
                    ]"></i>
                </div>

                <!-- Card da aprovação -->
                <div class="bg-white p-4 rounded-lg shadow border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <div class="font-medium text-gray-800">
                            {{ aprovacao.usuario.nome_funcionario }}
                        </div>
                        <span :class="[
                            'inline-flex px-2 py-1 text-xs font-medium rounded-full text-white',
                            getStatusBadgeColor(aprovacao.status)
                        ]">
                            {{ formatStatus(aprovacao.status) }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-600 mb-2">
                        {{aprovacao.nome_setor.nome_setor_formatado }}
                    </div>
                    <div class="text-sm text-gray-600 mb-2">
                        <i class="fa fa-clock mr-1"></i>
                        {{ formatDate(aprovacao.created_at) }}
                    </div>

                    <div v-if="aprovacao.observacao" class="mt-2 p-2 bg-gray-50 rounded text-sm text-gray-700">
                        <p class="italic">{{ aprovacao.observacao }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensagem quando não há aprovações -->
        <div v-else class="bg-gray-50 border border-gray-200 text-gray-700 px-4 py-3 rounded text-center">
            <i class="fa fa-info-circle mr-2"></i>
            Não há registros de aprovação para este plano.
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { inject } from 'vue';

// Props e eventos injetados
const events = inject('events');

// Props
const props = defineProps({
    idPlano: {
        type: [Number, String],
        required: true
    }
});

// Estado reativo
const loading = ref(false);
const error = ref(null);
const aprovacoes = ref([]);

// Computed properties
const planoInfo = computed(() => {
    if (aprovacoes.value.length > 0 && aprovacoes.value[0].plano) {
        const plano = aprovacoes.value[0].plano;
        return `${plano.exercicio}`;
    }
    return '';
});

// Função para buscar dados
const fetchAprovacoes = async () => {
    if (!props.idPlano) return;

    loading.value = true;
    error.value = null;

    try {
        const response = await axios.get(`/plano-contratacao-setor/aprovacao-contratacao/${props.idPlano}`);

        // Ordenar por data (mais recente primeiro)
        aprovacoes.value = response.data.sort((b, a) => {
            return new Date(b.created_at) - new Date(a.created_at);
        });
    } catch (err) {
        console.error('Erro ao carregar histórico de aprovações:', err);
        error.value = 'Não foi possível carregar o histórico de aprovações. Tente novamente mais tarde.';
        aprovacoes.value = [];
    } finally {
        loading.value = false;
    }
};

// Funções de formatação
const formatDate = (dateStr) => {
    if (!dateStr) return '';

    const date = new Date(dateStr);

    // Verificar se a data é válida
    if (isNaN(date.getTime())) return dateStr;

    // Formatação para pt-BR
    return date.toLocaleString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatStatus = (status) => {
    switch (status) {
        case 'P':
            return 'Pendente Envio';
        case 'E':
            return 'Enviado';
        case 'A':
            return 'Aprovado';
        case 'R':
            return 'Reprovado';
        case 'I':
            return 'Iniciado';
        case 'F':
            return 'Finalizado';
        case 'C':
            return 'Cancelado';
        default:
            return status || 'Não definido';
    }
};

// Funções para estilos baseados no status
const getStatusBadgeColor = (status) => {
    switch (status) {
        case 'P':
            return 'bg-yellow-500';
        case 'E':
            return 'bg-blue-500';
        case 'A':
            return 'bg-green-500';
        case 'R':
            return 'bg-red-500';
        case 'I':
            return 'bg-indigo-500';
        case 'F':
            return 'bg-gray-500';
        case 'C':
            return 'bg-gray-600';
        default:
            return 'bg-gray-500';
    }
};

const getStatusTextColor = (status) => {
    switch (status) {
        case 'P':
            return 'text-yellow-600';
        case 'E':
            return 'text-blue-600';
        case 'A':
            return 'text-green-600';
        case 'R':
            return 'text-red-600';
        case 'I':
            return 'text-indigo-600';
        case 'F':
            return 'text-gray-600';
        case 'C':
            return 'text-gray-700';
        default:
            return 'text-gray-600';
    }
};

const getStatusIconBackgroundColor = (status) => {
    switch (status) {
        case 'P':
            return 'bg-yellow-100';
        case 'E':
            return 'bg-blue-100';
        case 'A':
            return 'bg-green-100';
        case 'R':
            return 'bg-red-100';
        case 'I':
            return 'bg-indigo-100';
        case 'F':
            return 'bg-gray-100';
        case 'C':
            return 'bg-gray-200';
        default:
            return 'bg-gray-300';
    }
};

const getStatusIconTextColor = (status) => {
    switch (status) {
        case 'P':
            return 'text-yellow-600';
        case 'E':
            return 'text-blue-600';
        case 'A':
            return 'text-green-600';
        case 'R':
            return 'text-red-600';
        case 'I':
            return 'text-indigo-600';
        case 'F':
            return 'text-gray-600';
        case 'C':
            return 'text-gray-700';
        default:
            return 'text-gray-600';
    }
};

const getStatusIcon = (status) => {
    switch (status) {
        case 'P':
            return 'fa-clock';
        case 'E':
            return 'fa-clipboard-list';
        case 'A':
            return 'fa-check-circle';
        case 'R':
            return 'fa-times-circle';
        case 'I':
            return 'fa-play-circle';
        case 'F':
            return 'fa-flag-checkered';
        case 'C':
            return 'fa-ban';
        default:
            return 'fa-info-circle';
    }
};

// Lifecycle hooks
onMounted(() => {
    fetchAprovacoes();
});
</script>
