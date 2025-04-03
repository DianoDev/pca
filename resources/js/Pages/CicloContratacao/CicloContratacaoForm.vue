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
        <InputError :message="errors.nome_setor_formatado"/>
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
        <InputError :message="errors.nome_funcionario"/>
      </div>

      <div class="mb-4">
        <InputLabel
            for="ano"
            value="Exercicio"
            class="required"
            :class="{'text-gray-400': readOnly}"
        />
        <TextInput
            id="ano"
            type="number"
            class="w-full"
            v-model="form.ano"
            :disabled="readOnly"
            :class="{'bg-gray-100 cursor-not-allowed': readOnly}"
        />
        <InputError :message="errors.ano"/>
      </div>

      <div class="mb-4">
        <InputLabel
            for="data_inicio"
            value="Data Início"
            class="required"
            :class="{'text-gray-400': readOnly}"
        />
        <div class="flex space-x-2">
          <div class="w-1/2">
            <select
                id="data_inicio_mes"
                class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                v-model="dataInicio.mes"
                :disabled="readOnly"
                :class="{'bg-gray-100 cursor-not-allowed': readOnly}"
            >
              <option value="">Mês</option>
              <option value="01">Janeiro</option>
              <option value="02">Fevereiro</option>
              <option value="03">Março</option>
              <option value="04">Abril</option>
              <option value="05">Maio</option>
              <option value="06">Junho</option>
              <option value="07">Julho</option>
              <option value="08">Agosto</option>
              <option value="09">Setembro</option>
              <option value="10">Outubro</option>
              <option value="11">Novembro</option>
              <option value="12">Dezembro</option>
            </select>
          </div>
          <div class="w-1/2">
            <select
                id="data_inicio_dia"
                class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                v-model="dataInicio.dia"
                :disabled="readOnly"
                :class="{'bg-gray-100 cursor-not-allowed': readOnly}"
            >
              <option value="">Dia</option>
              <option v-for="dia in getDiasPorMes(dataInicio.mes, form.ano)" :key="`inicio-dia-${dia}`" :value="String(dia).padStart(2, '0')">
                {{ dia }}
              </option>
            </select>
          </div>
        </div>
        <InputError :message="errors.data_inicio"/>
      </div>

      <div class="mb-4">
        <InputLabel
            for="data_fim"
            value="Data Fim"
            class="required"
            :class="{'text-gray-400': readOnly}"
        />
        <div class="flex space-x-2">
          <div class="w-1/2">
            <select
                id="data_fim_mes"
                class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                v-model="dataFim.mes"
                :disabled="readOnly"
                :class="{'bg-gray-100 cursor-not-allowed': readOnly}"
            >
              <option value="">Mês</option>
              <option value="01">Janeiro</option>
              <option value="02">Fevereiro</option>
              <option value="03">Março</option>
              <option value="04">Abril</option>
              <option value="05">Maio</option>
              <option value="06">Junho</option>
              <option value="07">Julho</option>
              <option value="08">Agosto</option>
              <option value="09">Setembro</option>
              <option value="10">Outubro</option>
              <option value="11">Novembro</option>
              <option value="12">Dezembro</option>
            </select>
          </div>
          <div class="w-1/2">
            <select
                id="data_fim_dia"
                class="w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                v-model="dataFim.dia"
                :disabled="readOnly"
                :class="{'bg-gray-100 cursor-not-allowed': readOnly}"
            >
              <option value="">Dia</option>
              <option v-for="dia in getDiasPorMes(dataFim.mes, form.ano)" :key="`fim-dia-${dia}`" :value="String(dia).padStart(2, '0')">
                {{ dia }}
              </option>
            </select>
          </div>
        </div>
        <InputError :message="errors.data_fim"/>
      </div>


      <div class="w-full border-t border-gray-200 pt-4 mt-4">
        <!-- Botões para modo somente leitura -->
        <div class="flex justify-center space-x-4" v-if="readOnly">
          <button type="button"
                  class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2"
                  @click="close" aria-label="Close">
            <i class="fa fa-arrow-left mr-1"></i> Voltar
          </button>
        </div>

        <!-- Botões para modo de edição -->
        <div class="flex justify-center space-x-2" v-if="!readOnly">
          <button
              type="submit"
              class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              :disabled="processing"
          >
            <i v-if="!processing" class="fa fa-check mr-1"></i>
            <i v-else class="fa fa-spinner fa-spin mr-1"></i>
            {{ processing ? 'Salvando...' : 'Salvar' }}
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
import { inject, onMounted, ref, watch, computed } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
  data: {
    type: Object,
    default: null,
    required: false
  }
});

const emit = defineEmits(['close', 'notification']);
const events = inject('events');

// Form principal
const form = ref({
  ano: '',
  descricao: '',
  data_inicio: '', // Será construído a partir de dia/mês + ano
  data_fim: '',    // Será construído a partir de dia/mês + ano
  status: 'A'      // Default para Ativo
});

// Objetos para gerenciar separadamente dia/mês das datas
const dataInicio = ref({
  dia: '01',
  mes: '01'
});

const dataFim = ref({
  dia: '30',
  mes: '06'
});

// Estados do formulário
const errors = ref({});
const processing = ref(false);
const ready = ref(false);
const readOnly = ref(false);


function extrairDiaMes(dataISO) {
  if (!dataISO) return { dia: '', mes: '' };

  const partes = dataISO.split('-');
  if (partes.length === 3) {
    return {
      dia: partes[2],  // DD
      mes: partes[1]   // MM
    };
  }

  return { dia: '', mes: '' };
}

// Função para mostrar datas no formato DD/MM/YYYY
function formatDateForDisplay(dateStr) {
  if (!dateStr || dateStr.trim() === '') return '';

  try {
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;

    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}/${month}/${year}`;
  } catch (e) {
    return dateStr;
  }
}

// Funções para os nomes dos meses em português
const mesesPtBR = [
  'Janeiro', 'Fevereiro', 'Março', 'Abril',
  'Maio', 'Junho', 'Julho', 'Agosto',
  'Setembro', 'Outubro', 'Novembro', 'Dezembro'
];

// Função para obter o nome do mês com base no valor numérico
function getNomeMes(mesNumero) {
  const mes = parseInt(mesNumero, 10);
  return mes >= 1 && mes <= 12 ? mesesPtBR[mes - 1] : '';
}

// Função para determinar se o ano é bissexto
function isAnoBissexto(ano) {
  if (!ano) return false;
  const year = parseInt(ano, 10);
  return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
}

// Função para obter o número de dias em um mês
function getDiasPorMes(mes, ano) {
  if (!mes) return 31; // Default para mostrar todos os dias

  const mesInt = parseInt(mes, 10);

  // Meses com 31 dias: Janeiro (1), Março (3), Maio (5), Julho (7), Agosto (8), Outubro (10), Dezembro (12)
  if ([1, 3, 5, 7, 8, 10, 12].includes(mesInt)) {
    return 31;
  }

  // Meses com 30 dias: Abril (4), Junho (6), Setembro (9), Novembro (11)
  if ([4, 6, 9, 11].includes(mesInt)) {
    return 30;
  }

  // Fevereiro (2): 29 dias em anos bissextos, 28 dias nos demais
  if (mesInt === 2) {
    return isAnoBissexto(ano) ? 29 : 28;
  }

  return 31; // Valor padrão
}

function submit() {
  processing.value = true;

  // Validação das datas
  if (!dataInicio.value.dia || !dataInicio.value.mes) {
    errors.value.data_inicio = 'A data de início é obrigatória.';
    processing.value = false;
    return;
  }

  if (!dataFim.value.dia || !dataFim.value.mes) {
    errors.value.data_fim = 'A data de fim é obrigatória.';
    processing.value = false;
    return;
  }

  // Construir datas completas com o ano selecionado
  form.value.data_inicio = `${form.value.ano}-${dataInicio.value.mes}-${dataInicio.value.dia}`;
  form.value.data_fim = `${form.value.ano}-${dataFim.value.mes}-${dataFim.value.dia}`;

  // Preparar dados para envio (incluindo informações de gestor e setor)
  const formData = {
    ...form.value,
    // Garantir que os campos de gestor e setor serão enviados
    codigo_setor: form.value.codigo_setor,
    numero_matricula_gestor: form.value.numero_matricula_gestor,
    nome_funcionario: form.value.nome_funcionario,
    nome_setor_formatado: form.value.nome_setor_formatado
  };

  const url = props.data?.id
      ? `/ciclo-contratacao/${props.data.id}` // Rota de update
      : '/ciclo-contratacao'; // Rota de criação

  axios.post(url, formData)
      .then(response => {
        handleSuccess('Ciclo de Contratação salvo com sucesso!');
        processing.value = false;
      })
      .catch(error => {
        if (error.response && error.response.data.errors) {
          errors.value = error.response.data.errors;
        }
        handleError();
        processing.value = false;
      });
}

function handleSuccess(message) {
  events.emit('table-reload', true);
  events.emit('notification', {
    type: 'success',
    message: message
  });
  close();
}

function handleError() {
  events.emit('notification', {
    type: 'error',
    message: 'Ocorreu um erro ao salvar o Ciclo de Contratação.'
  });
}

function close() {
  emit('close');
}

const loadData = async () => {
  try {
    if (props.data?.id) {
      const response = await axios.get(`/ciclo-contratacao/${props.data.id}`);
      const data = response.data;

      // Preencher o formulário
      form.value = {
        ano: data.ano || '',
        descricao: data.descricao || '',
        data_inicio: data.data_inicio || '',
        data_fim: data.data_fim || '',
        status: data.status || 'A'
      };

      // Extrair dia e mês das datas
      if (data.data_inicio) {
        const inicioParts = extrairDiaMes(data.data_inicio);
        dataInicio.value.dia = inicioParts.dia;
        dataInicio.value.mes = inicioParts.mes;
      }

      if (data.data_fim) {
        const fimParts = extrairDiaMes(data.data_fim);
        dataFim.value.dia = fimParts.dia;
        dataFim.value.mes = fimParts.mes;
      }

      readOnly.value = Boolean(props.data.readOnly);
    }

    ready.value = true;
  } catch (err) {
    console.error('Error loading data:', err);
    events.emit('notification', {
      type: 'error',
      message: 'Erro ao carregar os dados do ciclo de contratação.'
    });
  }
};

onMounted(async () => {
  const response = await axios.get(`/plano-contratacao-setor/gestorInfo`);
  const data = response.data;

  form.value.codigo_setor = response.data.codigo_setor || '';
  form.value.numero_matricula_gestor = response.data.responsavel || '';
  form.value.nome_funcionario = response.data.nome_funcionario || '';
  form.value.nome_setor_formatado = response.data.nome_setor_formatado || '';
  await loadData();
});
</script>
