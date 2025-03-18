<template>
    <LayoutPrincipal>
        <div class="p-6 bg-white rounded">
            <div class="mb-6">
                <popup-button id="novo-publicacao" title="Nova Publicação" size="xl"
                              component="UsuarioSetorForm">
                    <i class="fa fa-plus"></i>
                    Nova Publicação
                </popup-button>
            </div>
            <datatable id="usuario_setor" :columns="columns" @delete="confirmRemove" :source="source"></datatable>
        </div>
    </LayoutPrincipal>

</template>

<script setup>
import LayoutPrincipal from '@/Layouts/LayoutPrincipal.vue';
import {ref, inject} from 'vue';
import Datatable from "@/Components/datatable/Datatable.vue";
import PopupButton from "@/Components/PopupButton.vue";


const events = inject('events');
const source = '/usuario-setor/list';
const columns = ref([
    {name: 'codigo_setor', title: 'Codigo Setor', width: '20%', sort: 'codigo_setor', nowrap: true},
    {name: 'nome_setor_formatado', title: 'Nome Setor', width: '20%', sort: 'nome_setor_formatado', nowrap: true},
    {name: 'nome_funcionario', title: 'Nome Funcionario', width: '20%', sort: 'nome_funcionario', nowrap: true},
    {
        name: 'id',
        title: 'Ação',
        width: '9%',
        nowrap: true,
        formatter: (value, row) => {
            let output = "";
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Editar" data-action="popup" data-size="xl" data-component="UsuarioSetorForm" data-title="Editar  Usuario Setor" class=" mx-1 action text-blue-600 text-align-center tooltip tooltip--top"><i class="fa fa-pencil"></i></a>`;
            output += `<a href="javascript:;" data-json='{"id": "${value}"}' data-tooltip="Remover" data-action="delete" class="action mx-0 action-delete text-blue-600 tooltip tooltip--top"><i class="fa fa-trash mx-1"></i></a>`;
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
