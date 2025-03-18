<template>
  <div class="m-2" v-if="ready">
    <form @submit.prevent="submit">
      <div class="mb-4">
        <InputLabel for="nome_setor_formatado" value="Setor" class="required"/>
        <input
            id="nome_setor_formatado"
            type="text"
            class="w-full rounded-md shadow-sm border-gray-300 bg-gray-100 text-gray-700 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            v-model="form.nome_setor_formatado"
            readonly
            style="cursor: not-allowed;"
            tabindex="-1"
        />
        <InputError :message="form.errors.nome_setor_formatado"/>
      </div>

      <div class="mb-4">
        <InputLabel for="nome_funcionario" value="Gestor" class="required"/>
        <input
            id="nome_funcionario"
            type="text"
            class="w-full rounded-md shadow-sm border-gray-300 bg-gray-100 text-gray-700 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            v-model="form.nome_funcionario"
            readonly
            style="cursor: not-allowed;"
            tabindex="-1"
        />
        <InputError :message="form.errors.nome_funcionario"/>
      </div>

      <div class="mb-4">
        <InputLabel for="exercicio" value="Exercicio" class="required"/>
        <input
            id="exercicio"
            type="text"
            class="w-full rounded-md shadow-sm border-gray-300 bg-gray-100 text-gray-700 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            v-model="form.exercicio"
            readonly
            style="cursor: not-allowed;"
            tabindex="-1"
        />
        <InputError :message="form.errors.exercicio"/>
      </div>

      <div class="mb-4">
        <InputLabel for="email" value="Email" class="required"/>
        <TextInput id="email" class="w-full" v-model="form.email" :disabled="readOnly"/>
        <InputError :message="form.errors.email"/>
      </div>

      <div class="mb-4">
        <InputLabel for="telefone" value="Telefone" class="required"/>
        <TextInput id="telefone" class="w-full" v-model="form.telefone" :disabled="readOnly"/>
        <InputError :message="form.errors.telefone"/>
      </div>

      <div class="w-full border-t border-gray-200 pt-4 mt-4">
        <div class="flex justify-center" v-if="readOnly">
          <button type="button"
                  class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                  @click="close" aria-label="Close">
            <i class="fa fa-close mr-1"></i> Sair
          </button>
        </div>
        <div class="flex justify-center space-x-2" v-if="!readOnly">
          <button
              type="submit"
              class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              :disabled="form.processing"
          >
            <i v-if="!form.processing" class="fa fa-check mr-1"></i>
            <i v-else class="fa fa-spinner fa-spin mr-1"></i>
            {{ form.processing ? 'Salvando...' : 'Salvar' }}
          </button>
          <button type="button"
                  class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                  @click="close" aria-label="Close">
            <i class="fa fa-close mr-1"></i> Cancelar
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import {inject, onMounted, ref} from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import {useForm} from '@inertiajs/vue3';

const props = defineProps({
  data: {
    type: Object,
    default: null,
    required: false
  }
});

const emit = defineEmits(['close', 'notification']);
const events = inject('events');
const form = useForm({
  codigo_setor: '',
  numero_matricula_gestor: '',
  nome_funcionario: '',
  nome_setor_formatado: '',
  exercicio: '',
  email: '',
  telefone: '',
});
const ready = ref(false);
const readOnly = ref(false);

function submit() {
  form.post('/plano-contratacao/', {
    onSuccess: () => handleSuccess('Plano Contratação criado com sucesso!'),
    onError: () => handleError()
  });
}

function handleSuccess(message) {
  events.emit('table-reload', true);
  events.emit('notification', {
    type: 'success',
    message: message
  });
  events.emit('form-submitted', true);
  events.emit('popup-close', true);
}

function handleError() {
  events.emit('notification', {
    type: 'error',
    message: 'Ocorreu um erro ao salvar o Plano Contratação.'
  });
  events.emit('form-submitted', false);
}

const loadData = async () => {
  try {
    const response = await axios.get(`/plano-contratacao/gestorInfo`);
    console.log(response, 're')
    // Set form data
    form.codigo_setor = response.data.codigo_setor || '';
    form.numero_matricula_gestor = response.data.responsavel || '';
    form.nome_funcionario = response.data.nome_funcionario || '';
    form.nome_setor_formatado = response.data.nome_setor_formatado || '';
    form.exercicio = props.data.year
    form.email = response.data.email || '';
    form.telefone = response.data.telefone || '';
    form.status = response.data.status || '';
    form.valor_total = response.data.valor_total || '';

    readOnly.value = Boolean(props.data.readOnly);
  } catch (err) {
    console.error('Error loading data:', err);
    events.emit('notification', {
      type: 'error',
      message: 'Não foi possível recuperar os dados do Plano Contratação.'
    });
  } finally {
    ready.value = true;
  }
}

const close = () => {
  events.emit('popup-close', true);
}

onMounted(async () => {
  console.log(props)
  events.off("form-submitted");
  events.on("form-submitted", (sucesso) => {
    if (sucesso) {
      events.emit('table-reload', true);
    }
  });

  if (props.data?.year) {
    await loadData();
  } else {
    ready.value = true;
  }
});
</script>
