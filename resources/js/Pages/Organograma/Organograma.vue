<template>
    <AppLayout>
        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold mb-6">Organograma de Setores</h1>

            <div class="bg-white rounded-lg shadow p-6">
                <!-- Árvore de Setores -->
                <div class="organograma">
                    <div v-if="arvoreSetores.length === 0" class="text-center py-4">
                        <p>Nenhum setor encontrado. Adicione o primeiro setor.</p>
                        <button
                            @click="mostrarBuscaSetorRaiz = true"
                            class="mt-2 bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">
                            Adicionar Setor Raiz
                        </button>
                    </div>

                    <div v-else>
                        <div v-for="setor in arvoreSetores" :key="setor.id" class="mb-4">
                            <ItemOrganograma
                                :setor="setor"
                                @adicionar-filho="abrirModalAdicionarFilho"
                                @remover-setor="confirmarRemoverSetor"/>
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

                    <div v-if="setoresEncontrados.length > 0" class="max-h-96 overflow-y-auto mb-4">
                        <div
                            v-for="setor in setoresEncontrados"
                            :key="setor.codigo_setor"
                            @click="selecionarSetor(setor)"
                            class="p-2 hover:bg-gray-100 cursor-pointer border-b"
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
                            class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                            Cancelar
                        </button>
                        <button
                            @click="adicionarFilho"
                            :disabled="!setorSelecionado"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
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

                    <div v-if="setoresEncontrados.length > 0" class="max-h-96 overflow-y-auto mb-4">
                        <div
                            v-for="setor in setoresEncontrados"
                            :key="setor.codigo_setor"
                            @click="selecionarSetorRaiz(setor)"
                            class="p-2 hover:bg-gray-100 cursor-pointer border-b"
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
                            class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                            Cancelar
                        </button>
                        <button
                            @click="adicionarSetorRaiz"
                            :disabled="!setorSelecionado"
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed">
                            Adicionar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Confirmação de Remoção -->
            <div v-if="modalConfirmacaoAberto"
                 class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <h2 class="text-lg font-bold mb-4">Confirmar Remoção</h2>
                    <p>Tem certeza que deseja remover o setor "{{ setorRemoverSelecionado?.nome }}"?</p>

                    <div v-if="setorRemoverSelecionado?.tem_filho === 'S'"
                         class="mt-2 p-2 bg-yellow-100 rounded text-sm">
                        <strong>Atenção:</strong> Este setor possui subsetores que também serão desativados.
                    </div>

                    <div class="flex justify-end mt-4 space-x-2">
                        <button
                            @click="modalConfirmacaoAberto = false"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                            Cancelar
                        </button>
                        <button
                            @click="removerSetor"
                            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                            Remover
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import ItemOrganograma from './Componentes/ItemOrganograma.vue';
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
    console.log(setorPaiSelecionado.value,'setorPaiSelecionado.value')
    try {
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
    console.log(setorSelecionado.value,'setorPaiSelecionado.value')
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

const removerSetor = async () => {
    if (!setorRemoverSelecionado.value) return;

    try {
        isLoading.value = true;
        await axios.delete('/organograma/remover-setor', {
            data: {
                codigo_setor: setorRemoverSelecionado.value.id
            }
        });

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
.organograma {
    padding: 1rem;
}
</style>
