<template>
    <AppLayout>
    <div class="bg-white p-6 rounded">
        <datatable id="plano_contratacao" :columns="columns" @delete="confirmRemove" :source="source"></datatable>
    </div>
    </AppLayout>
</template>

<script setup>
import {ref, inject} from 'vue';
import Datatable from "@/Components/datatable/Datatable.vue";
import AppLayout from "@/Layouts/LayoutPrincipal.vue";
const events = inject('events');
const source = '/plano-contratacao/list';
const columns = ref([
    {name: 'codigo_setor', title: 'Codigo Setor', width: '20%', sort: 'codigo_setor', nowrap: true},
    {name: 'numero_matricula_gestor', title: 'Numero Matricula Gestor', width: '20%', sort: 'numero_matricula_gestor', nowrap: true},
    {name: 'exercicio', title: 'Exercicio', width: '20%', sort: 'exercicio', nowrap: true},
    {name: 'email', title: 'Email', width: '20%', sort: 'email', nowrap: true},
    {name: 'telefone', title: 'Telefone', width: '20%', sort: 'telefone', nowrap: true},
    {name: 'status', title: 'Status', width: '20%', sort: 'status', nowrap: true},
    {name: 'valor_total', title: 'Valor Total', width: '20%', sort: 'valor_total', nowrap: true},
    {
        name: 'id',
        title: 'Ação',
        width: '9%',
        nowrap: true,
        contentClass: 'text-center',
        formatter: (value, row) => {
            let output = "";
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Editar" data-action="popup" data-size="xl" data-component="plano-contratacao-form" data-title="Editar  Plano Contratacao" class=" mx-1 action text-align-center tooltip tooltip--top"><i class="fa fa-pencil"></i></a>`;
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Remover" data-action="delete" class="action mx-0 action-delete tooltip tooltip--top"><i class="fa fa-trash mx-1"></i></a>`;
            return output;
        }
    }
]);

const confirmRemove = async (data) => {
    events.emit('loading', true);
    try {
        await axios.delete('/plano-contratacao/' + data.id);
        events.emit('table-reload');
        events.emit('notification', {
            type: 'success',
            message: ' Plano Contratacao excluído com Sucesso.'
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
