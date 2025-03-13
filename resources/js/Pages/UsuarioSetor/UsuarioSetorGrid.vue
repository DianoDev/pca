<template>
    <div class="p-6">
        <datatable id="usuario_setor" :columns="columns" @delete="confirmRemove" :source="source"></datatable>
    </div>
</template>

<script setup>
import {ref, inject} from 'vue';
import Datatable from "@/Components/datatable/Datatable.vue";

const events = inject('events');
const source = '/usuario-setor/list';
const columns = ref([
    {name: 'codigo_setor', title: 'Codigo Setor', width: '20%', sort: 'codigo_setor', nowrap: true},
    {name: 'nome_setor_formatado', title: 'Nome Setor', width: '20%', sort: 'numero_matricula', nowrap: true},
    {name: 'nome_funcionario', title: 'Nome Funcionario', width: '20%', sort: 'numero_matricula_gestor', nowrap: true},
    {
        name: 'id',
        title: 'Ação',
        width: '9%',
        nowrap: true,
        contentClass: 'text-center',
        formatter: (value, row) => {
            let output = "";
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Editar" data-action="popup" data-size="xl" data-component="usuario-setor-form" data-title="Editar  Usuario Setor" class=" mx-1 action text-align-center tooltip tooltip--top"><i class="fa fa-pencil"></i></a>`;
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Remover" data-action="delete" class="action mx-0 action-delete tooltip tooltip--top"><i class="fa fa-trash mx-1"></i></a>`;
            return output;
        }
    }
]);

const confirmRemove = async (data) => {
    events.emit('loading', true);
    try {
        await axios.delete('/usuario-setor/' + data.id);
        events.emit('table-reload');
        events.emit('notification', {
            type: 'success',
            message: ' Usuario Setor excluído com Sucesso.'
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
