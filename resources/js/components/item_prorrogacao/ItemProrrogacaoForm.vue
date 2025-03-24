<template>
    <div class="m-2" v-if="ready">
        <form id="frm" name="frm" data-method="post" :action="acao">
            <form-error></form-error>
<div class="col-lg-12 col-md-12 mb-3">
    <label for="objeto" class="form-label required">Objeto</label>
    <input v-model="info.objeto" required type="text" name="objeto" id="objeto" class="form-control"
           :disabled="readOnly"/>
</div>
<div class="col-lg-12 col-md-12 mb-3">
    <label for="numero" class="form-label required">Numero</label>
    <input v-model="info.numero" required type="text" name="numero" id="numero" class="form-control"
           :disabled="readOnly"/>
</div>
<div class="col-lg-12 col-md-12 mb-3">
    <label for="empresa" class="form-label required">Empresa</label>
    <input v-model="info.empresa" required type="text" name="empresa" id="empresa" class="form-control"
           :disabled="readOnly"/>
</div>
<div class="col-lg-12 col-md-12 mb-3">
    <label for="cnpj" class="form-label required">Cnpj</label>
    <input v-model="info.cnpj" required type="text" name="cnpj" id="cnpj" class="form-control"
           :disabled="readOnly"/>
</div>
<div class="col-lg-12 col-md-12 mb-3">
    <label for="valor_global" class="form-label required">Valor Global</label>
    <input v-model="info.valor_global" required type="text" name="valor_global" id="valor_global" class="form-control"
           :disabled="readOnly"/>
</div>
<div class="col-lg-12 col-md-12 mb-3">
    <label for="termino_vigencia" class="form-label required">Termino Vigencia</label>
    <input v-model="info.termino_vigencia" required type="text" name="termino_vigencia" id="termino_vigencia" class="form-control"
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
        const acao = ref('/item-prorrogacao/');
        const readOnly = ref(false);

        const loadData = async () => {
            try {
                acao.value = '/item-prorrogacao/';
               const response = await axios.get(acao.value + props.data.id);
                acao.value += props.data.id;
                info.value = response.data;
                readOnly.value = Boolean(props.data.readOnly);
            } catch (err) {
                emit('notification', {
                    type: 'error',
                    message: 'Não foi possível recuperar os dados do  Item Prorrogacao.',
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
                        message: ' Item Prorrogacao salvo com Sucesso!'
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