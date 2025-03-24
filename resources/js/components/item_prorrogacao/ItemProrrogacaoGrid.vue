<template>
    <div>
        <datatable id="item_prorrogacao" :columns="columns" @delete="confirmRemove" :source="source"></datatable>
    </div>
</template>

<script setup>
import {ref, inject} from 'vue';

const events = inject('events');
const source = '/item-prorrogacao/list';
const columns = ref([
    {name: 'objeto', title: 'Objeto', width: '20%', sort: 'objeto', nowrap: true},
    {name: 'numero', title: 'Numero', width: '20%', sort: 'numero', nowrap: true},
    {name: 'empresa', title: 'Empresa', width: '20%', sort: 'empresa', nowrap: true},
    {name: 'cnpj', title: 'Cnpj', width: '20%', sort: 'cnpj', nowrap: true},
    {name: 'valor_global', title: 'Valor Global', width: '20%', sort: 'valor_global', nowrap: true},
    {
        name: 'id',
        title: 'Ação',
        width: '9%',
        nowrap: true,
        contentClass: 'text-center',
        formatter: (value, row) => {
            let output = "";
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Editar" data-action="popup" data-size="xl" data-component="item-prorrogacao-form" data-title="Editar  Item Prorrogacao" class=" mx-1 action text-align-center tooltip tooltip--top"><i class="fa fa-pencil"></i></a>`;
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Remover" data-action="delete" class="action mx-0 action-delete tooltip tooltip--top"><i class="fa fa-trash mx-1"></i></a>`;
            return output;
        }
    }
]);

const confirmRemove = async (data) => {
    events.emit('loading', true);
    try {
        await axios.delete('/item-prorrogacao/' + data.id);
        events.emit('table-reload');
        events.emit('notification', {
            type: 'success',
            message: ' Item Prorrogacao excluído com Sucesso.'
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