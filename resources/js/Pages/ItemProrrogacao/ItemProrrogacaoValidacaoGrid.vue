<template>
    <div class="overflow-x-auto">
        <datatable
            id="itens_prorrogacao"
            :columns="columnsProrrogacao"
            :source="`/item-prorrogacao-setor/${planoId}/list`">
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

// Colunas para o datatable de Itens de Prorrogação
const columnsProrrogacao = ref([
    {
        name: 'objeto',
        title: 'Objeto',
        width: '30%',
        sort: 'objeto',
        nowrap: true,
        formatter: (value) => {
            return value && value.length > 40 ?
                `<span title="${value.replace(/"/g, '&quot;')}">${value.substring(0, 40)}...</span>` :
                value;
        }
    },
    {
        name: 'numero',
        title: 'Número',
        width: '15%',
        sort: 'numero',
        nowrap: true
    },
    {
        name: 'valor_global',
        title: 'Valor Global',
        width: '20%',
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
            return `<span class="inline-flex px-2 py-1 text-xs font-medium  rounded-md text-white ${getStatusBadgeColor(value)}">${formatStatusContrato(value)}</span>`;
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
            output += `<a href="javascript:;" data-json='{"id": "${value}", "readOnly": "true"}' data-tooltip="Validar" data-action="popup" data-size="xl" data-component="ItemProrrogacaoForm" data-title="Editar Item de Contratacao" class="mx-1 action text-align-center tooltip tooltip--top"><i class="fa fa-file-circle-check text-blue-600"></i></a>`;
            return output;
        }
    }
]);

// Métodos para formatação
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
