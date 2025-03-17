<template>
    <div class="border rounded-lg p-4 bg-white">
        <!-- Cabeçalho do Item -->
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-bold text-lg">{{ setor.nome }}</h3>
                <div v-if="setor.responsavel" class="text-sm text-gray-600 mt-1">
                    Responsável: {{ setor.responsavel }}
                    <span v-if="setor.logon" class="text-gray-500">({{ setor.logon }})</span>
                </div>
            </div>

            <!-- Botões de ação -->
            <div class="flex space-x-2">
                <button
                    @click="$emit('adicionar-filho', setor)"
                    class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Adicionar
                </button>

                <button
                    @click="$emit('remover-setor', setor)"
                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Remover
                </button>
            </div>
        </div>

        <!-- Filhos do Item (recursivo) -->
        <div v-if="setor.filhos && setor.filhos.length > 0" class="mt-4 pl-6 border-l-2 border-gray-300">
            <div v-for="filho in setor.filhos" :key="filho.id" class="mt-4">
                <ItemOrganograma
                    :setor="filho"
                    @adicionar-filho="$emit('adicionar-filho', $event)"
                    @remover-setor="$emit('remover-setor', $event)" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ItemOrganograma',
    props: {
        setor: {
            type: Object,
            required: true
        }
    },
    emits: ['adicionar-filho', 'remover-setor']
}
</script>
