<template>
    <div v-if="ready">
        <datatable id="item_prorrogacao" :columns="columns" @delete="confirmRemove" :source="source"></datatable>
    </div>
    <div v-else class="flex justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
    </div>
</template>

<script setup>
import { ref, inject, onMounted } from 'vue';
import Datatable from "@/Components/datatable/Datatable.vue";

const currentYear = new Date().getFullYear();
const selectedYear = ref(currentYear);
const events = inject('events');
const source = ref('');
const hasPlanForSelectedYear = ref(null);
const ready = ref(false);
const props = defineProps({
    plano_status: {
        default: null,
    }
});
// Datatable columns para Itens de Prorrogação
const columns = ref([
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
        name: 'classificacao',
        title: 'Classificação',
        width: '15%',
        sort: 'classificacao',
        nowrap: true,
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
                output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Editar" data-action="popup" data-size="xl" data-component="ItemContratacaoForm" data-title="Editar Item de Contratacao" class="mx-1 action text-align-center tooltip tooltip--top"><i class="fa fa-pencil text-blue-600"></i></a>`;
                output += `<a href="javascript:;" data-json='{"id": "${value}","tipo": "contratacao"}' data-tooltip="Remover" data-action="delete" class="action mx-0 action-delete tooltip tooltip--top"><i class="fa fa-trash mx-1 text-blue-600"></i></a>`;
            }
            return output;
        }
    }
]);

// Funções de formatação
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

const formatStatus = (value) => {
    switch (value) {
        case 'E':
            return 'Pendente Aprovação';
        case 'A':
            return 'Aprovado';
        case 'R':
            return 'Reprovado';
        case 'I':
            return 'Iniciado';
        default:
            return value || 'Não definido';
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

const confirmRemove = async (data) => {
    events.emit('loading', true);
    if (data.tipo === 'contratacao') {
        try {
            await axios.delete('/item-contratacao-setor/' + data.id);
            events.emit('table-reload');
            events.emit('notification', {
                type: 'success',
                message: 'Item Prorrogação excluído com sucesso.'
            });
            events.emit('reload-plano', true);
        } catch (err) {
            events.emit('notification', {
                type: 'error',
                message: err.response?.data?.message || 'Não foi possível excluir o registro.'
            });
        } finally {
            events.emit('loading', false);
        }
    }
};

const loadData = async () => {
    if (props.plano_status !== 'P') {
        columns.value = ([
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
                name: 'classificacao',
                title: 'Classificação',
                width: '15%',
                sort: 'classificacao',
                nowrap: true,
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
        ]);
    }
    try {
        events.emit('loading', true);
        const response = await axios.get(`/plano-contratacao-setor/exists?exercicio=${selectedYear.value}`);
        hasPlanForSelectedYear.value = response.data.exists;

        if (hasPlanForSelectedYear.value) {
            // Corrigindo o endpoint para lista de prorrogações
            source.value = `/item-contratacao-setor/${hasPlanForSelectedYear.value.id}/list`;
        }
    } catch (error) {
        console.error('Erro ao carregar dados:', error);
        events.emit('notification', {
            type: 'error',
            message: 'Não foi possível carregar os dados de prorrogação.'
        });
    } finally {
        // Definindo ready como true após carregar os dados, com um pequeno delay
        setTimeout(() => {
            ready.value = true;
            events.emit('loading', false);
        }, 300);
    }
};

onMounted(async () => {
    await loadData();
    events.on('reload-item-grid', async (ano) => {
        selectedYear.value = ano;
        ready.value = false; // Desativa o componente durante o recarregamento
        await loadData();
    });
});
</script>
