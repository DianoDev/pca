<template>
    <div class="overflow-x-auto">
        <datatable
            id="itens_contratacao"
            :columns="columnsContratacao"
            :source="`/item-contratacao-setor/${planoId}/list`">
        </datatable>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import Datatable from "@/Components/datatable/Datatable.vue";

// Propriedades recebidas
const props = defineProps({
    planoId: {
        type: [Number, String],
        required: true
    }
});

// Colunas para o datatable de Itens de Contratação
const columnsContratacao = ref([
    {
        name: 'descricao',
        title: 'Descrição',
        width: '30%',
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
        width: '20%',
        sort: 'valor_total',
        nowrap: true,
        formatter: (value) => {
            return value ? `R$ ${parseFloat(value).toLocaleString('pt-BR', {minimumFractionDigits: 2})}` : '';
        }
    },
    {
        name: 'classificacao',
        title: 'Classificação',
        width: '25%',
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
            return `<span class="inline-flex px-2 py-1 text-xs font-medium rounded-md text-white ${getStatusBadgeColor(value)}">${formatStatus(value)}</span>`;
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
                output += `<a href="javascript:;" data-json='{"id": "${value}", "readOnly": "true"}' data-tooltip="Validar" data-action="popup" data-size="xl" data-component="ItemContratacaoForm" data-title="Editar Item de Contratacao" class="mx-1 action text-align-center tooltip tooltip--top"><i class="fa fa-file-circle-check text-blue-600"></i></a>`;
            return output;
        }
    }
]);

// Métodos para formatação
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
</script>
