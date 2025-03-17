<template>
    <div class="setor-tree-node">
        <div class="org-tree-node">
            <div class="org-tree-node-info">
                <h3 class="font-semibold">{{ setor.nome }}</h3>
                <p v-if="setor.nome_funcionario" class="text-sm text-gray-600">
                    Responsável: {{ setor.nome_funcionario }}
                </p>
            </div>
            <div class="org-tree-node-actions">
                <button
                    @click="$emit('adicionar-filho', setor)"
                    class="org-tree-btn org-tree-btn-add"
                    title="Adicionar subsetor">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                </button>
                <button
                    @click="$emit('remover-setor', setor)"
                    class="org-tree-btn org-tree-btn-remove"
                    title="Remover setor">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Renderiza recursivamente os subsetores, se houver -->
        <ul v-if="temFilhos" class="children-container">
            <li v-for="filho in filhos" :key="filho.codigo_setor" class="children-item">
                <!-- Renderização recursiva dos nós filhos -->
                <SetorNode
                    :setor="filho"
                    :todos-setores="todosSetores"
                    :nivel="nivel + 1"
                    @adicionar-filho="$emit('adicionar-filho', $event)"
                    @remover-setor="$emit('remover-setor', $event)"
                />
            </li>
        </ul>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    // Setor atual sendo renderizado
    setor: {
        type: Object,
        required: true
    },
    // Todos os setores disponíveis para encontrar os filhos
    todosSetores: {
        type: Array,
        default: () => []
    },
    // Nível na hierarquia para controlar estilos visuais
    nivel: {
        type: Number,
        default: 0
    }
});

defineEmits(['adicionar-filho', 'remover-setor']);

// Encontrar todos os filhos deste setor
const filhos = computed(() => {
    return props.todosSetores.filter(s => s.codigo_setor_pai === props.setor.codigo_setor);
});

// Verificar se o setor tem filhos
const temFilhos = computed(() => {
    return filhos.value.length > 0;
});
</script>

<style scoped>
.setor-tree-node {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.org-tree-node {
    display: inline-flex;
    flex-direction: column;
    min-width: 220px;
    background-color: white;
    border-radius: 8px;
    padding: 1rem;
    position: relative;
    z-index: 1;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    margin-top: 20px;
    transition: all 0.2s ease;
}

/* Bordas coloridas baseadas no nível da hierarquia */
.org-tree-node {
    border: 2px solid #3B82F6; /* Nível 0: Azul (padrão) */
}

/* Aplicar cores diferentes para cada nível */
.setor-tree-node:deep(.setor-tree-node:nth-child(1) .org-tree-node) {
    border-color: #8B5CF6; /* Nível 1: Roxo */
}

.setor-tree-node:deep(.setor-tree-node:nth-child(1) .setor-tree-node:nth-child(1) .org-tree-node) {
    border-color: #EC4899; /* Nível 2: Rosa */
}

.setor-tree-node:deep(.setor-tree-node:nth-child(1) .setor-tree-node:nth-child(1) .setor-tree-node:nth-child(1) .org-tree-node) {
    border-color: #F59E0B; /* Nível 3: Amarelo */
}

.setor-tree-node:deep(.setor-tree-node:nth-child(1) .setor-tree-node:nth-child(1) .setor-tree-node:nth-child(1) .setor-tree-node:nth-child(1) .org-tree-node) {
    border-color: #10B981; /* Nível 4: Verde */
}

.org-tree-node:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    transform: translateY(-2px);
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

/* Estilos para o container de filhos */
.children-container {
    position: relative;
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 30px;
    width: 100%;
}

/* Linhas de conexão */
.children-container::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 50%;
    height: 20px;
    width: 0;
    border-left: 2px solid #CBD5E0;
}

/* Estilo para os itens filhos */
.children-item {
    position: relative;
    padding: 0 1rem;
    margin: 0 0.5rem 1.5rem;
}

/* Linha que conecta pai aos filhos */
.children-item::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 50%;
    height: 20px;
    border-left: 2px solid #CBD5E0;
    width: 0;
}

/* Linha horizontal que conecta irmãos */
.children-item::after {
    content: '';
    position: absolute;
    top: -20px;
    width: 100%;
    left: 0;
    border-top: 2px solid #CBD5E0;
}

/* Ajustes para o primeiro e último filho */
.children-item:first-child::after {
    left: 50%;
    width: 50%;
}

.children-item:last-child::after {
    width: 50%;
    right: 50%;
    left: auto;
}

/* Se é filho único, não precisa de linha horizontal */
.children-item:only-child::after {
    display: none;
}

@media (max-width: 768px) {
    .children-container {
        flex-direction: column;
    }

    .children-item {
        margin: 1.5rem 0;
    }

    .children-item::after {
        display: none;
    }
}
</style>
