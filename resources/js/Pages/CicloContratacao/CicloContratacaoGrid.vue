<template>
    <LayoutPrincipal>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-4 justify-between">
                <div class=""></div>
                <popup-button
                    id="novo-item-prorrogacao"
                    title="Novo Ciclo de Contratacao"
                    size="xl"
                    component="CicloContratacaoForm"
                    variant="primary"
                >
                    <i class="fa fa-plus mr-2"></i>
                    Novo Ciclo
                </popup-button>
            </div>
            <datatable id="ciclo_contratacao" :columns="columns" @delete="confirmRemove" :source="source"></datatable>
        </div>
    </LayoutPrincipal>
</template>

<script setup>
import {ref, inject} from 'vue';
import LayoutPrincipal from "@/Layouts/LayoutPrincipal.vue";
import Datatable from "@/Components/datatable/Datatable.vue";
import PopupButton from "@/Components/PopupButton.vue";

const events = inject('events');
const source = '/ciclo-contratacao/list';

// Função para formatar data para exibir apenas dia e mês
const formatarDataDiaMes = (dataStr) => {
    if (!dataStr) return '';

    let dia, mes;

    // Remover qualquer componente de horário que possa estar presente
    dataStr = dataStr.split(' ')[0]; // Remove a parte do horário se existir

    // Verificar formato da data (pode ser DD/MM/YYYY ou YYYY-MM-DD)
    if (dataStr.includes('/')) {
        // Formato DD/MM/YYYY
        const partes = dataStr.split('/');
        if (partes.length >= 2) {
            dia = partes[0];
            mes = partes[1];
        }
    } else if (dataStr.includes('-')) {
        // Formato YYYY-MM-DD
        const partes = dataStr.split('-');
        if (partes.length >= 3) {
            dia = partes[2];
            mes = partes[1];
        }
    } else {
        return dataStr; // Retorna como está se não reconhecer o formato
    }

    // Array com nomes dos meses em português
    const mesesPtBR = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril',
        'Maio', 'Junho', 'Julho', 'Agosto',
        'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];

    // Converter número do mês para nome do mês
    const mesNumero = parseInt(mes, 10);
    const mesNome = mesNumero >= 1 && mesNumero <= 12 ? mesesPtBR[mesNumero - 1] : mes;

    // Retornar no formato dia/nome do mês
    return `${dia}/${mesNome}`;
};

const columns = ref([
    {name: 'nome_funcionario', title: 'Responsavel', width: '20%', sort: 'responsavel', nowrap: true},
    {name: 'ano', title: 'Ano', width: '20%', sort: 'ano', nowrap: true},
    {
        name: 'data_inicio',
        title: 'Data Início',
        width: '20%',
        nowrap: true,
        formatter: (value) => formatarDataDiaMes(value)
    },
    {
        name: 'data_fim',
        title: 'Data Fim',
        width: '20%',
        nowrap: true,
        formatter: (value) => formatarDataDiaMes(value)
    },
    {
        name: 'status',
        title: 'Status',
        width: '12%',
        sort: 'status',
        nowrap: true,
        formatter: (value) => {
            const status = {
                'I': 'Iniciado',
                'E': 'Encerrado',
                'F': 'Finalizado',
                'C': 'Cadastrado'
            };
            return status[value] || value;
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
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Editar" data-action="popup" data-size="xl" data-component="CicloContratacaoForm" data-title="Editar Ciclo Contratação" style="color:#235a99" class="mx-1 action text-align-center tooltip tooltip--top"><i class="fa fa-pencil"></i></a>`;
            output += `<a href="javascript:;" data-json='{"id": "${value}"}'  style="color:#235a99" data-tooltip="Remover" data-action="delete" class="action mx-0 action-delete tooltip tooltip--top"><i class="fa fa-trash mx-1"></i></a>`;
            return output;
        }
    }
]);

const confirmRemove = async (data) => {
    events.emit('loading', true);
    try {
        await axios.delete('/ciclo-contratacao/' + data.id);
        events.emit('table-reload');
        events.emit('notification', {
            type: 'success',
            message: 'Ciclo Contratação excluído com Sucesso.'
        });
    } catch (err) {
        events.emit('notification', {
            type: 'error',
            message: err.response?.data?.message || 'Não foi possível excluir o registro.'
        });
    } finally {
        events.emit('loading', false);
    }
}
</script>
