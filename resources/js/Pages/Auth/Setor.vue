<template>
    <div class="app-container">
        <!-- Cabeçalho PCA -->
        <header class="app-header">
            <div class="header-content">
                <h1 class="logo-text">PCA</h1>
            </div>
        </header>

        <div class="main-content">
            <div class="content-wrapper">
                <div class="container mx-auto px-4">
                    <!-- Título da página -->
                    <div class="mb-8 px-1">
                        <h1 class="text-2xl font-semibold text-gray-800 flex header-content">
                            Selecione o Setor para acessar o Plano de Contratação Anual
                        </h1>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Setores que o usuário administra -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100">
                            <div class="bg-[#235a99] p-4">
                                <h2 class="text-lg font-semibold text-white">
                                    <div class="flex items-center">
                                        <i class="fa fa-user-shield mr-2"></i>
                                        Setores que você administra
                                    </div>
                                </h2>
                            </div>

                            <div class="p-5">
                                <div v-if="adm_setor.length === 0" class="text-center py-6 text-gray-500">
                                    <i class="fa fa-info-circle text-gray-400 text-3xl mb-2"></i>
                                    <p>Você não administra nenhum setor.</p>
                                </div>

                                <ul v-else class="space-y-2">
                                    <li v-for="setor in adm_setor" :key="setor.codigo_setor"
                                        class="p-3 rounded-md cursor-pointer transition-all duration-200 border-l-4"
                                        :class="selectedSetor && selectedSetor.codigo_setor === setor.codigo_setor
                                            ? 'bg-blue-50 border-l-[#235a99] shadow-sm'
                                            : 'hover:bg-gray-50 border-l-transparent hover:border-l-[#235a99] hover:translate-x-1'"
                                        @click="selectSetor(setor, 'admin')">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <h3 class="font-medium text-gray-800">{{ setor.nome_setor_formatado }}</h3>
                                                <p class="text-sm text-gray-600">{{ setor.nome_sigla || 'Sem sigla' }}</p>
                                            </div>
                                            <div class="flex items-center">
                                                <span v-if="selectedSetor && selectedSetor.codigo_setor === setor.codigo_setor"
                                                      class="bg-[#235a99] text-white rounded-full w-6 h-6 flex items-center justify-center mr-1">
                                                    <i class="fa fa-check text-sm"></i>
                                                </span>
                                                <i class="fa fa-chevron-right text-gray-400"></i>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Setores onde o usuário é funcionário -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100">
                            <div class="bg-[#235a99] p-4">
                                <h2 class="text-lg font-semibold text-white">
                                    <div class="flex items-center">
                                        <i class="fa fa-users mr-2"></i>
                                        Setores onde você é funcionário
                                    </div>
                                </h2>
                            </div>

                            <div class="p-5">
                                <div v-if="func_setor.length === 0" class="text-center py-6 text-gray-500">
                                    <i class="fa fa-info-circle text-gray-400 text-3xl mb-2"></i>
                                    <p>Você não está alocado em nenhum setor como funcionário.</p>
                                </div>

                                <ul v-else class="space-y-2">
                                    <li v-for="setor in func_setor" :key="setor.codigo_setor"
                                        class="p-3 rounded-md cursor-pointer transition-all duration-200 border-l-4"
                                        :class="selectedSetor && selectedSetor.codigo_setor === setor.codigo_setor
                                            ? 'bg-blue-50 border-l-[#235a99] shadow-sm'
                                            : 'hover:bg-gray-50 border-l-transparent hover:border-l-[#235a99] hover:translate-x-1'"
                                        @click="selectSetor(setor, 'funcionario')">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <h3 class="font-medium text-gray-800">{{ setor.nome_setor }}</h3>
                                                <p class="text-sm text-gray-600">{{ setor.nome_sigla || 'Sem sigla' }}</p>
                                            </div>
                                            <div class="flex items-center">
                                                <span v-if="selectedSetor && selectedSetor.codigo_setor === setor.codigo_setor"
                                                      class="bg-[#235a99] text-white rounded-full w-6 h-6 flex items-center justify-center mr-1">
                                                    <i class="fa fa-check text-sm"></i>
                                                </span>
                                                <i class="fa fa-chevron-right text-gray-400"></i>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Botão de confirmação -->
                    <div class="mt-8 flex justify-center">
                        <button
                            @click="confirmarSelecao"
                            :disabled="!selectedSetor"
                            :class="[
                                'px-6 py-3 rounded-lg shadow-md focus:outline-none transition-all duration-200 flex items-center',
                                selectedSetor
                                    ? 'bg-[#235a99] text-white hover:bg-[#1d4b80] hover:shadow-lg'
                                    : 'bg-gray-200 text-gray-500 cursor-not-allowed'
                            ]">
                            <i v-if="isLoading" class="fa fa-spinner fa-spin mr-2"></i>
                            <i v-else class="fa fa-check-circle mr-2"></i>
                            Confirmar Seleção
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer igual ao cabeçalho -->
        <footer class="app-footer">
            <div class="footer-content">
                <img src="/images/logo_TCE.png" class="w-16" alt="Logo Footer">
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

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
.app-container {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    background-color: #f5f5f5;
}

.app-header {
    background: #235a99;
    padding: 1rem 1.5rem;
    color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: center;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
}

.logo-text {
    font-weight: 600;
    font-size: 1.5rem;
    letter-spacing: 1px;
}

.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center; /* Centraliza no eixo Y */
}

.content-wrapper {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.app-footer {
    background: #235a99;
    padding: 1rem;
    color: white;
    margin-top: auto;
    display: flex;
    justify-content: center;
    align-items: center;
}

.footer-content {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Animações sutis para os itens da lista */
li {
    transition: all 0.2s ease-in-out;
}

/* Efeito suave para o botão desabilitado */
button:disabled {
    opacity: 0.7;
}
</style>
