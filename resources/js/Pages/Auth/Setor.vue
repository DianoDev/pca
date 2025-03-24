<template>

        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-6 text-gray-800">Selecione o Setor para acessar o Plano de Contratação Anual</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Setores que o usuário administra -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4 text-blue-600 border-b pb-2">
                        <div class="flex items-center">
                            <i class="fa fa-user-shield mr-2"></i>
                            Setores que você administra
                        </div>
                    </h2>

                    <div v-if="adm_setor.length === 0" class="text-center py-4 text-gray-500">
                        <p>Você não administra nenhum setor.</p>
                    </div>

                    <ul v-else class="space-y-2">
                        <li v-for="setor in adm_setor" :key="setor.codigo_setor"
                            class="p-3 rounded-md cursor-pointer transition-colors duration-200"
                            :class="selectedSetor && selectedSetor.codigo_setor === setor.codigo_setor
                                ? 'bg-blue-100 border border-blue-300'
                                : 'hover:bg-gray-100 border border-transparent'"
                            @click="selectSetor(setor, 'admin')">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="font-medium text-gray-800">{{ setor.nome_setor_formatado }}</h3>
                                    <p class="text-sm text-gray-600">{{ setor.nome_sigla || 'Sem sigla' }}</p>
                                </div>
                                <i v-if="selectedSetor && selectedSetor.codigo_setor === setor.codigo_setor"
                                   class="fa fa-check-circle text-blue-600 text-lg"></i>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Setores onde o usuário é funcionário -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold mb-4 text-green-600 border-b pb-2">
                        <div class="flex items-center">
                            <i class="fa fa-users mr-2"></i>
                            Setores onde você é funcionário
                        </div>
                    </h2>

                    <div v-if="func_setor.length === 0" class="text-center py-4 text-gray-500">
                        <p>Você não está alocado em nenhum setor como funcionário.</p>
                    </div>

                    <ul v-else class="space-y-2">
                        <li v-for="setor in func_setor" :key="setor.codigo_setor"
                            class="p-3 rounded-md cursor-pointer transition-colors duration-200"
                            :class="selectedSetor && selectedSetor.codigo_setor === setor.codigo_setor
                                ? 'bg-green-100 border border-green-300'
                                : 'hover:bg-gray-100 border border-transparent'"
                            @click="selectSetor(setor, 'funcionario')">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="font-medium text-gray-800">{{ setor.nome_setor }}</h3>
                                    <p class="text-sm text-gray-600">{{ setor.nome_sigla || 'Sem sigla' }}</p>
                                </div>
                                <i v-if="selectedSetor && selectedSetor.codigo_setor === setor.codigo_setor"
                                   class="fa fa-check-circle text-green-600 text-lg"></i>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Botão de confirmação -->
            <div class="mt-8 flex justify-center">
                <button
                    @click="confirmarSelecao"
                    :disabled="!selectedSetor"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                    <i v-if="isLoading" class="fa fa-spinner fa-spin mr-2"></i>
                    <i v-else class="fa fa-check mr-2"></i>
                    Confirmar Seleção
                </button>
            </div>
        </div>

</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/LayoutPrincipal.vue';

// Props recebidas do Inertia
const props = defineProps({
    adm_setor: {
        type: Object,
        default: () => []
    },
    func_setor: {
        type: Object,
        default: () => []
    }
});

// Estado
const selectedSetor = ref(null);
const selectedType = ref(null);
const isLoading = ref(false);

// Selecionar um setor
const selectSetor = (setor, type) => {
    selectedSetor.value = setor;
    selectedType.value = type;
};

// Confirmar a seleção e redirecionar
const confirmarSelecao = () => {
    if (!selectedSetor.value) return;

    isLoading.value = true;

    // Usar o Inertia para fazer a requisição e redirecionamento
    router.post('/setor/selecionar-setor', {
        codigo_setor: selectedSetor.value.codigo_setor,
    }, {
        onFinish: () => {
            isLoading.value = false;
        }
    });
};
</script>

<style scoped>
/* Estilos adicionais se necessário */
</style>
