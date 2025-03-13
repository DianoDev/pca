<template>
    <div class="m-2" v-if="ready">
        <form id="frm" name="frm" data-method="post" :action="acao">
            <form-error></form-error>
<div class="col-lg-12 col-md-12 mb-3">
    <label for="codigo_setor" class="form-label required">Codigo Setor</label>
    <input v-model="info.codigo_setor" required type="text" name="codigo_setor" id="codigo_setor" class="form-control"
           :disabled="readOnly"/>
</div>
<div class="col-lg-12 col-md-12 mb-3">
    <label for="numero_matricula" class="form-label required">Numero Matricula</label>
    <input v-model="info.numero_matricula" required type="text" name="numero_matricula" id="numero_matricula" class="form-control"
           :disabled="readOnly"/>
</div>
<div class="col-lg-12 col-md-12 mb-3">
    <label for="numero_matricula_gestor" class="form-label required">Numero Matricula Gestor</label>
    <input v-model="info.numero_matricula_gestor" required type="text" name="numero_matricula_gestor" id="numero_matricula_gestor" class="form-control"
           :disabled="readOnly"/>
</div>
            <div class="row border-top pt-4">
                <div class="col-12 d-flex justify-content-center align-items-center" v-if="readOnly">
                    <button type="button" class="btn btn-danger text-white" @click="close" aria-label="Close">
                        <i class="fa fa-close"></i> Sair
                    </button>
                </div>
                <div class="col-12 text-center" v-if="!readOnly">
                    <submit-rest label="Salvar"></submit-rest>
                    &nbsp;
                    <button type="button" class="btn btn-danger text-white" @click="close" aria-label="Close">
                        <i class="fa fa-close"></i> Cancelar
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import {inject, onMounted, ref, computed} from 'vue';

export default {
    setup(props, {emit}) {
        const events = inject('events');
        const info = ref({});
        const ready = ref(false);
        const acao = ref('/usuario-setor/');
        const readOnly = ref(false);

        const loadData = async () => {
            try {
                acao.value = '/usuario-setor/';
               const response = await axios.get(acao.value + props.data.id);
                acao.value += props.data.id;
                info.value = response.data;
                readOnly.value = Boolean(props.data.readOnly);
            } catch (err) {
                emit('notification', {
                    type: 'error',
                    message: 'Não foi possível recuperar os dados do  Usuario Setor.',
                });
            }
            ready.value = true;
        }

        const close = () => {
            events.emit('popup-close', true);
        }

        onMounted(async () => {
            events.off("form-submitted");
            events.on("form-submitted", (sucesso) => {
                if (sucesso) {
                    events.emit('table-reload', true);
                    events.emit('notification', {
                        type: 'success',
                        message: ' Usuario Setor salvo com Sucesso!'
                    });
                    emit('close', true);
                }
            });
            if (props.data) {
                await loadData();
            } else {
                ready.value = true;
            }
        });

        return {
            info,
            ready,
            acao,
            readOnly,
            close,
        }

    },

    props: {
        data: {default: null, required: true},
    }

}
</script>