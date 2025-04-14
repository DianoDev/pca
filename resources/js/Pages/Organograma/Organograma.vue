<template>
    <AppLayout>
        <div class=" container contpage">
            <div class=" ">
                <!-- Árvore de Setores -->
                <div class="">
                    <div v-if="arvoreSetores.length === 0" class="text-center py-8">
                        <div class="text-gray-500 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <p class="text-lg mb-4">Nenhum setor encontrado. Adicione o primeiro setor.</p>
                        <button
                            @click="mostrarBuscaSetorRaiz = true"
                            class="mt-2 bg-blue-500 hover:bg-blue-700 text-white py-2 px-6 rounded-lg flex items-center justify-center mx-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Adicionar Setor Raiz
                        </button>
                    </div>

                    <div v-else class="overflow-auto">
                        <div class="org-tree">
                            <div class="org-tree-container">
                                <ul>
                                    <li v-for="setor in arvoreSetores" :key="setor.id">
                                        <div class="org-tree-node-container">
                                            <div class="org-tree-node">
                                                <div class="org-tree-node-info">
                                                    <h3 class="font-semibold">{{ setor.nome }}</h3>
                                                    <p v-if="setor.responsavel" class="text-sm text-gray-600">Responsável: {{ setor.responsavel }}</p>
                                                </div>
                                                <div class="org-tree-node-actions">
                                                    <button
                                                        v-if="setor.filhos.length === 0"
                                                        @click="abrirModalAdicionarFilho(setor)"
                                                        class="org-tree-btn org-tree-btn-add p-2"
                                                        title="Adicionar subsetor">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Subsetores -->
                                            <ul v-if="setor.filhos && setor.filhos.length > 0">
                                                <li v-for="filho in setor.filhos" :key="filho.id" class="org-tree-child">
                                                    <div class="org-tree-node-container">
                                                        <div class="org-tree-node min-h-36">
                                                            <div class="org-tree-node-info">
                                                                <h3 class="font-semibold">{{ filho.nome }}</h3>
                                                                <p v-if="filho.responsavel" class="text-sm text-gray-600">Responsável: {{ filho.responsavel }}</p>
                                                            </div>
                                                            <div class="org-tree-node-actions">
                                                                <button
                                                                    v-if="filho.filhos.length === 0"
                                                                    @click="abrirModalAdicionarFilho(filho)"
                                                                    class="org-tree-btn org-tree-btn-add p-2"
                                                                    title="Adicionar subsetor">
                                                                    <i class="fa fa-plus"></i>
                                                                </button>
                                                                <button
                                                                    @click="confirmarRemoverSetor(filho)"
                                                                    class="org-tree-btn org-tree-btn-remove p-2"
                                                                    title="Remover setor">
                                                                    <i class="fa fa-remove"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <!-- Terceiro nível (se necessário) -->
                                                        <ul v-if="filho.filhos && filho.filhos.length > 0">
                                                            <li v-for="subfilho in filho.filhos" :key="subfilho.id" class="org-tree-child">
                                                                <div class="org-tree-node-container">
                                                                    <div class="org-tree-node min-h-36">
                                                                        <div class="org-tree-node-info">
                                                                            <h3 class="font-semibold">{{ subfilho.nome }}</h3>
                                                                            <p v-if="subfilho.responsavel" class="text-sm text-gray-600">Responsável: {{ subfilho.responsavel }}</p>
                                                                        </div>
                                                                        <div class="org-tree-node-actions">
                                                                            <button
                                                                                @click="abrirModalAdicionarFilho(subfilho)"
                                                                                class="org-tree-btn org-tree-btn-add p-2"
                                                                                title="Adicionar subsetor">
                                                                                <i class="fa fa-plus"></i>
                                                                            </button>
                                                                            <button
                                                                                @click="confirmarRemoverSetor(subfilho)"
                                                                                class="org-tree-btn org-tree-btn-remove p-2"
                                                                                title="Remover setor">
                                                                                <i class="fa fa-remove"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>

                                                                    <!-- ADICIONAR AQUI: Quarto nível (subsubfilho) -->
                                                                    <ul v-if="subfilho.filhos && subfilho.filhos.length > 0">
                                                                        <li v-for="subsubfilho in subfilho.filhos" :key="subsubfilho.id" class="org-tree-child ">
                                                                            <div class="org-tree-node min-h-36">
                                                                                <div class="org-tree-node-info">
                                                                                    <h3 class="font-semibold">{{ subsubfilho.nome }}</h3>
                                                                                    <p v-if="subsubfilho.responsavel" class="text-sm text-gray-600">Responsável: {{ subsubfilho.responsavel }}</p>
                                                                                </div>
                                                                                <div class="org-tree-node-actions">
                                                                                    <button
                                                                                        @click="confirmarRemoverSetor(subsubfilho)"
                                                                                        class="org-tree-btn org-tree-btn-remove p-2"
                                                                                        title="Remover setor">
                                                                                        <i class="fa fa-remove"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    </ul>
                                                                    <!-- FIM DO QUARTO NÍVEL -->
                                                                </div>
                                                            </li>
                                                        </ul>

                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Busca para Adicionar Filho -->
            <div v-if="modalAdicionarFilhoAberto"
                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-3xl">
                    <h2 class="text-lg font-bold mb-4">Adicionar Subsetor em: {{ setorPaiSelecionado?.nome }}</h2>

                    <div class="mb-4">
                        <input
                            v-model="termoBusca"
                            @input="buscarSetores"
                            type="text"
                            placeholder="Digite para buscar um setor..."
                            class="w-full px-3 py-2 border rounded-lg"
                        />
                    </div>

                    <div v-if="isLoading" class="flex justify-center py-4">
                        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                    </div>

                    <div v-else-if="setoresEncontrados.length > 0" class="max-h-96 overflow-y-auto mb-4">
                        <div
                            v-for="setor in setoresEncontrados"
                            :key="setor.codigo_setor"
                            @click="selecionarSetor(setor)"
                            class="p-3 hover:bg-blue-50 cursor-pointer border-b transition-colors duration-150 rounded-md mb-1"
                            :class="{'bg-blue-50 border border-blue-200': setorSelecionado && setorSelecionado.codigo_setor === setor.codigo_setor}"
                        >
                            <div class="font-medium">{{ setor.nome_setor_formatado }}</div>
                            <div v-if="setor.nome_funcionario" class="text-sm text-gray-600">
                                Responsável: {{ setor.nome_funcionario }}
                            </div>
                        </div>
                    </div>
                    <div v-else-if="termoBusca.length > 0" class="py-2 text-gray-600">
                        Nenhum setor encontrado com esse termo.
                    </div>

                    <div class="flex justify-end mt-4 space-x-2">
                        <button
                            @click="fecharModalAdicionarFilho"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition-colors duration-150">
                            Cancelar
                        </button>
                        <button
                            @click="adicionarFilho"
                            :disabled="!setorSelecionado || isLoading"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150 flex items-center">
                            <span v-if="isLoading" class="mr-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            Adicionar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal para adicionar setor raiz -->
            <div v-if="mostrarBuscaSetorRaiz"
                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-3xl">
                    <h2 class="text-lg font-bold mb-4">Adicionar Setor Raiz</h2>

                    <div class="mb-4">
                        <input
                            v-model="termoBusca"
                            @input="buscarSetores"
                            type="text"
                            placeholder="Digite para buscar um setor..."
                            class="w-full px-3 py-2 border rounded-lg"
                        />
                    </div>

                    <div v-if="isLoading" class="flex justify-center py-4">
                        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                    </div>

                    <div v-else-if="setoresEncontrados.length > 0" class="max-h-96 overflow-y-auto mb-4">
                        <div
                            v-for="setor in setoresEncontrados"
                            :key="setor.codigo_setor"
                            @click="selecionarSetorRaiz(setor)"
                            class="p-3 hover:bg-blue-50 cursor-pointer border-b transition-colors duration-150 rounded-md mb-1"
                            :class="{'bg-blue-50 border border-blue-200': setorSelecionado && setorSelecionado.codigo_setor === setor.codigo_setor}"
                        >
                            <div class="font-normal">{{ setor.nome_setor_formatado }}</div>
                            <div v-if="setor.nome_funcionario" class="text-sm text-gray-600">
                                Responsável: {{ setor.nome_funcionario }}
                            </div>
                        </div>
                    </div>
                    <div v-else-if="termoBusca.length > 0" class="py-2 text-gray-600">
                        Nenhum setor encontrado com esse termo.
                    </div>

                    <div class="flex justify-end mt-4 space-x-2">
                        <button
                            @click="mostrarBuscaSetorRaiz = false"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-100 transition-colors duration-150">
                            Cancelar
                        </button>
                        <button
                            @click="adicionarSetorRaiz"
                            :disabled="!setorSelecionado || isLoading"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150 flex items-center">
                            <span v-if="isLoading" class="mr-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            Adicionar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Confirmação de Remoção -->
            <div v-if="modalConfirmacaoAberto"
                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <div class="mb-4 text-red-500 flex justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold mb-4 text-center">Confirmar Remoção</h2>
                    <p class="text-center mb-4">Tem certeza que deseja remover o setor "{{ setorRemoverSelecionado?.nome }}"?</p>

                    <div v-if="setorRemoverSelecionado?.tem_filho === 'S'"
                         class="mt-2 p-3 bg-yellow-100 rounded-lg text-sm mb-4 border border-yellow-300">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 mt-0.5 text-yellow-700" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <strong>Atenção:</strong> Este setor possui subsetores que também serão desativados.
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center space-x-4 mt-4">
                        <button
                            @click="modalConfirmacaoAberto = false"
                            class="px-6 py-2 border rounded-lg hover:bg-gray-100 transition-colors duration-150">
                            Cancelar
                        </button>
                        <button
                            @click="removerSetor"
                            :disabled="isLoading"
                            class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-150 flex items-center">
                            <span v-if="isLoading" class="mr-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            Remover
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import {onMounted, ref, watch} from 'vue';
import axios from 'axios';
import AppLayout from "@/Layouts/LayoutPrincipal.vue";

// Props
const props = defineProps({
    arvoreSetores: {
        type: Array,
        required: true
    }
});

// Estado reativo
const modalAdicionarFilhoAberto = ref(false);
const modalConfirmacaoAberto = ref(false);
const mostrarBuscaSetorRaiz = ref(false);
const setorPaiSelecionado = ref(null);
const setorSelecionado = ref(null);
const setorRemoverSelecionado = ref(null);
const termoBusca = ref('');
const setoresEncontrados = ref([]);
const timeoutBusca = ref(null);
const isLoading = ref(false);

// Métodos
const abrirModalAdicionarFilho = (setor) => {
    setorPaiSelecionado.value = setor;
    setorSelecionado.value = null;
    termoBusca.value = '';
    setoresEncontrados.value = [];
    modalAdicionarFilhoAberto.value = true;
};

const fecharModalAdicionarFilho = () => {
    modalAdicionarFilhoAberto.value = false;
    setorPaiSelecionado.value = null;
    setorSelecionado.value = null;
};

const buscarSetores = () => {
    // Debounce para evitar muitas requisições
    clearTimeout(timeoutBusca.value);
    timeoutBusca.value = setTimeout(async () => {
        if (termoBusca.value.length < 2) {
            setoresEncontrados.value = [];
            return;
        }

        try {
            isLoading.value = true;
            const response = await axios.get('/organograma/buscar-setores', {
                params: { termo: termoBusca.value }
            });
            setoresEncontrados.value = response.data;
        } catch (error) {
            console.error('Erro ao buscar setores:', error);
            setoresEncontrados.value = [];
        } finally {
            isLoading.value = false;
        }
    }, 300);
};

const selecionarSetor = (setor) => {
    setorSelecionado.value = setor;
};

const selecionarSetorRaiz = (setor) => {
    setorSelecionado.value = setor;
};

const adicionarFilho = async () => {
    if (!setorSelecionado.value || !setorPaiSelecionado.value) return;

    try {
        console.log('oi')
        isLoading.value = true;
        await axios.post('/organograma/adicionar-filho', {
            codigo_setor_pai: setorPaiSelecionado.value.id,
            codigo_setor: setorSelecionado.value.codigo_setor
        });

        // Recarregar a página ou atualizar os dados
        window.location.reload();
        fecharModalAdicionarFilho();
    } catch (error) {
        console.error('Erro ao adicionar filho:', error);
        // Aqui você pode adicionar um sistema de notificação para o usuário
    } finally {
        isLoading.value = false;
    }
};

const adicionarSetorRaiz = async () => {
    if (!setorSelecionado.value) return;

    try {
        isLoading.value = true;
        await axios.post('/organograma/adicionar-filho', {
            codigo_setor_pai: null,
            codigo_setor: setorSelecionado.value.codigo_setor
        });

        // Recarregar a página ou atualizar os dados
        window.location.reload();
        mostrarBuscaSetorRaiz.value = false;
        setorSelecionado.value = null;
    } catch (error) {
        console.error('Erro ao adicionar setor raiz:', error);
        // Aqui você pode adicionar um sistema de notificação para o usuário
    } finally {
        isLoading.value = false;
    }
};

const confirmarRemoverSetor = (setor) => {
    setorRemoverSelecionado.value = setor;
    modalConfirmacaoAberto.value = true;
};

onMounted(async () => {
console.log(props.arvoreSetores)
});
const removerSetor = async () => {
    if (!setorRemoverSelecionado.value) return;
    console.log(setorRemoverSelecionado.value.id)
    try {
        isLoading.value = true;
        await axios.delete(`/organograma/remover-setor/${setorRemoverSelecionado.value.id}`);

        // Recarregar a página ou atualizar os dados
        window.location.reload();
        modalConfirmacaoAberto.value = false;
        setorRemoverSelecionado.value = null;
    } catch (error) {
        console.error('Erro ao remover setor:', error);
        // Aqui você pode adicionar um sistema de notificação para o usuário
    } finally {
        isLoading.value = false;
    }
};

// Limpar a busca quando os modais são fechados
watch(modalAdicionarFilhoAberto, (newValue) => {
    if (!newValue) {
        termoBusca.value = '';
        setoresEncontrados.value = [];
    }
});

watch(mostrarBuscaSetorRaiz, (newValue) => {
    if (!newValue) {
        termoBusca.value = '';
        setoresEncontrados.value = [];
    }
});
</script>

<style scoped>
.contpage{
    max-width:4000px !important;
}

.organograma {
    padding: 1rem;
    overflow-x: auto;
}

.org-tree {
    width: 100%;
    overflow-x: auto;
}

.org-tree-container {
    min-width: max-content;
}

.org-tree ul {
    list-style-type: none;
    padding: 0;
    position: relative;
}

.org-tree ul::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    border-left: 2px solid #CBD5E0;
    height: 20px;
    width: 0;
}

.org-tree ul ul::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 50%;
    border-left: 2px solid #CBD5E0;
    height: 20px;
    width: 0;
}

.org-tree li {
    position: relative;
    padding: 1rem 0.5rem;
    text-align: center;
}

.org-tree li::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    border-left: 2px solid #CBD5E0;
    height: 20px;
    width: 0;
}

.org-tree li::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    border-top: 2px solid #CBD5E0;
    width: 100%;
    height: 0;
}

.org-tree li:first-child::after {
    width: 50%;
    left: 50%;
}

.org-tree li:last-child::after {
    width: 50%;
    right: 50%;
}

.org-tree li:only-child::after {
    display: none;
}

.org-tree ul ul li {
    padding-top: 2rem;
}

.org-tree-node {
    display: inline-flex;
    flex-direction: column;
    max-width: 350px;
    background-color: white;
    border: 2px solid #3B82F6;
    border-radius: 8px;
    padding: 1rem;
    position: relative;
    z-index: 1;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.2s ease;
}

.org-tree-node:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transform: translateY(-2px);
}

.org-tree-node-container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.org-tree-node-info {
    text-align: center;
    margin-bottom: 0.5rem;
}

.org-tree-node-actions {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.org-tree-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s, transform 0.1s;
}

.org-tree-btn:hover {
    transform: scale(1.1);
}

.org-tree-btn-add {
    background-color: #10B981;
    color: white;
}

.org-tree-btn-add:hover {
    background-color: #059669;
}

.org-tree-btn-remove {
    background-color: #EF4444;
    color: white;
}

.org-tree-btn-remove:hover {
    background-color: #DC2626;
}

/* Estilos para níveis recursivos do organograma */
.org-tree ul ul {
    margin-top: 2rem;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}

.org-tree ul ul li {
    margin: 0 1rem;
}

/* Estilos recursivos para suportar múltiplos níveis */
.org-tree > ul > li > .org-tree-node-container > ul > li > .org-tree-node {
    border-color: #8B5CF6;
}

.org-tree > ul > li > .org-tree-node-container > ul > li > .org-tree-node-container > ul > li > .org-tree-node {
    border-color: #EC4899;
}

/* Adaptação para organogramas mais complexos */
@media (min-width: 1024px) {

    .org-tree-node {
        min-width: 250px;
    }
}

@media (max-width: 768px) {
    .org-tree ul ul {
        flex-direction: column;
    }

    .org-tree li:first-child::after,
    .org-tree li:last-child::after {
        width: 0;
    }
}



/* Título do nó (nome do setor) */
.org-tree-node-info h3 {
    font-size: 0.875rem; /* 14px */
    line-height: 1.25rem;
}

/* Texto de responsável */
.org-tree-node-info p {
    font-size: 0.75rem; /* 12px */
    line-height: 1rem;
}

/* Ajustar padding do nó para compensar a fonte menor */
.org-tree-node {
    padding: 0.75rem;
}

/* Reduzir tamanho dos ícones de ação */
.org-tree-btn {
    width: 20px;
    height: 20px;
}

.org-tree-btn svg {
    width: 0.875rem;
    height: 0.875rem;
}

/* Espaçamento entre os nós */
.org-tree ul ul li {
    padding-top: 1.5rem;
    margin: 0 0.75rem;
}

/* Reduzir espaçamento vertical geral */
.org-tree-node-container {
    margin-top: 0.5rem;
}

/* Ajustar medida mínima do nó */
.org-tree-node {
    min-width: 200px;
    max-width: 280px;
}

</style>
