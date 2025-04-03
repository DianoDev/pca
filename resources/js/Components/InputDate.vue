<template>
    <div class="date-picker-container">
        <Datepicker
            v-model="selectedDate"
            :placeholder="placeholder"
            :format="customFormat"
            :preview-format="'dd/MM/yyyy'"
            :disabled="disabled"
            :clearable="clearable"
            :auto-apply="autoApply"
            :teleport="teleport"
            :enable-time-picker="false"
            :min-date="minDate"
            :max-date="maxDate"
            locale="ptBR"
            :text-input="true"
            :class="{ 'error-input': error }"
            @update:model-value="onDateChange"
            :inline="false"
        />
    </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits, onMounted, watch } from 'vue';
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { pt } from 'date-fns/locale';

// Definição de props
const props = defineProps({
    modelValue: {
        type: Date,
        default: null
    },
    placeholder: {
        type: String,
        default: 'Selecione uma data'
    },
    disabled: {
        type: Boolean,
        default: false
    },
    clearable: {
        type: Boolean,
        default: true
    },
    autoApply: {
        type: Boolean,
        default: true
    },
    teleport: {
        type: Boolean,
        default: true
    },
    minDate: {
        type: Date,
        default: null
    },
    maxDate: {
        type: Date,
        default: null
    },
    error: {
        type: Boolean,
        default: false
    }
});

// Definição de emits
const emit = defineEmits(['update:modelValue', 'date-selected']);

// Referência à data selecionada
const selectedDate = ref(props.modelValue);

// Configuração para português brasileiro
const ptBR = pt;

// Formato dd/mm/yyyy
const customFormat = (date) => {
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
};

// Função para lidar com a mudança de data
const onDateChange = (date) => {
    emit('update:modelValue', date);
    emit('date-selected', date);
};

// Monitora mudanças no modelValue externo
watch(() => props.modelValue, (newValue) => {
    if (newValue !== selectedDate.value) {
        selectedDate.value = newValue;
    }
});

// Monitora mudanças no minDate e maxDate
watch(() => props.minDate, () => {
    // Verificar se a data selecionada está dentro do novo intervalo permitido
    if (selectedDate.value && props.minDate && selectedDate.value < props.minDate) {
        selectedDate.value = props.minDate;
        emit('update:modelValue', selectedDate.value);
        emit('date-selected', selectedDate.value);
    }
});

watch(() => props.maxDate, () => {
    // Verificar se a data selecionada está dentro do novo intervalo permitido
    if (selectedDate.value && props.maxDate && selectedDate.value > props.maxDate) {
        selectedDate.value = props.maxDate;
        emit('update:modelValue', selectedDate.value);
        emit('date-selected', selectedDate.value);
    }
});

onMounted(() => {
    if (props.modelValue !== selectedDate.value) {
        selectedDate.value = props.modelValue;
    }
});
</script>

<style scoped>
.date-picker-container {
    width: 100%;
}

:deep(.dp__input) {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    border: 1px solid #d1d5db;
    font-size: 0.875rem;
    line-height: 1.25rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

:deep(.dp__input:focus) {
    outline: 2px solid transparent;
    outline-offset: 2px;
    border-color: #818cf8;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
}

:deep(.dp__input:disabled) {
    background-color: #f3f4f6;
    cursor: not-allowed;
    opacity: 1;
}

:deep(.dp__menu) {
    font-family: inherit;
}

:deep(.dp__today) {
    background-color: rgba(99, 102, 241, 0.1);
}

:deep(.dp__active_date) {
    background-color: #6366f1;
    color: white;
}

.error-input :deep(.dp__input) {
    border-color: #ef4444;
}

/* Estilos adicionais para se integrar com o Tailwind */
:deep(.dp__action_buttons) {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem;
}
</style>
