<template>
    <div>
        <datatable id="ciclo_contratacao" :columns="columns" @delete="confirmRemove" :source="source"></datatable>
    </div>
</template>

<script setup>
import {ref, inject} from 'vue';

const events = inject('events');
const source = '/ciclo-contratacao/list';
const columns = ref([
    {name: 'ano', title: 'Ano', width: '20%', sort: 'ano', nowrap: true},
    {name: 'descricao', title: 'Descricao', width: '20%', sort: 'descricao', nowrap: true},
    {name: 'data_inicio', title: 'Data Inicio', width: '20%', sort: 'data_inicio', nowrap: true},
    {name: 'data_fim', title: 'Data Fim', width: '20%', sort: 'data_fim', nowrap: true},
    {
        name: 'id',
        title: 'Ação',
        width: '9%',
        nowrap: true,
        contentClass: 'text-center',
        formatter: (value, row) => {
            let output = "";
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Editar" data-action="popup" data-size="xl" data-component="ciclo-contratacao-form" data-title="Editar  Ciclo Contratacao" class=" mx-1 action text-align-center tooltip tooltip--top"><i class="fa fa-pencil"></i></a>`;
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Remover" data-action="delete" class="action mx-0 action-delete tooltip tooltip--top"><i class="fa fa-trash mx-1"></i></a>`;
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
            message: ' Ciclo Contratacao excluído com Sucesso.'
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