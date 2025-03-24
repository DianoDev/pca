<template>
    <div class="app-layout" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
        <!-- Menu Lateral -->
        <SidebarMenu
            ref="sidebarRef"
            @collapse-changed="onSidebarCollapse"
            @toggle-changed="onSidebarToggle"
            @submenu-clicked="handleSubmenuClick"
        />

        <!-- Container principal -->
        <div class="main-content">
            <!-- Header -->
            <header class="app-header">
                <div class="header-container">
                    <!-- Botão para toggle em mobile -->
                    <button
                        @click="toggleSidebar"
                        class="mobile-toggle-btn">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Botão para recolher/expandir sidebar -->
                    <button
                        @click="toggleCollapse"
                        class="collapse-btn">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Título da página dinâmico -->
                    <h1 class="page-title">{{ pageTitle }}</h1>

                    <!-- Área do usuário -->
                    <div class="user-area">
                        <span class="user-name">Nome do Usuário</span>
                        <div class="user-avatar">
                            <i class="fa fa-user-circle"></i>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Conteúdo da página -->
            <main class="page-content">
                <confirmation-popup></confirmation-popup>
              <popup></popup>
                <slot></slot>
            </main>

            <!-- Footer -->
            <footer class="app-footer">
                <div class="footer-content">
                    © 2024, Tribunal de Contas
                </div>
            </footer>
        </div>

        <!-- Overlay para fechar o menu em mobile -->
        <div
            @click="closeSidebar"
            :class="['overlay', { 'active': sidebarVisible }]">
        </div>
    </div>
</template>

<script setup>
import { ref, computed, inject, onMounted } from 'vue';
import SidebarMenu from './SidebarMenu.vue';
import Popup from "@/Components/Popup.vue";
import ConfirmationPopup from "@/components/laravel-vue-crud/ConfirmationPopup.vue";
// Refs para o estado do componente
const sidebarCollapsed = ref(false);
const sidebarVisible = ref(false);
const sidebarRef = ref(null);

// Props para receber o título da página
const props = defineProps({
    pageTitle: {
        type: String,
        default: 'Página Inicial'
    }
});

// Alternate approach: Using Inertia.js (if available)
const page = inject('page', null);

// Computed property para o título da página baseado em diferentes fontes
const pageTitle = computed(() => {
    // Mapeamento de rotas para títulos (URLs parciais)
    const urlTitles = {
        '/dashboard': 'Dashboard',
        '/organograma': 'Organograma de Setores',
        '/plano-contratacao-setor': 'Plano de Contratação do Setor',
        '/configuracoes/geral': 'Configurações Gerais',
        '/configuracoes/perfil': 'Configurações de Perfil',
        '/relatorios/diario': 'Relatório Diário',
        '/relatorios/mensal': 'Relatório Mensal',
        '/relatorios/anual': 'Relatório Anual'
    };

    // 1. Prioridade: Props vindos do componente pai
    if (props.pageTitle && props.pageTitle !== 'Página Inicial') {
        return props.pageTitle;
    }

    // 2. Se estiver usando Inertia.js
    if (page && page.value && page.value.component) {
        // Nome do componente no Inertia
        const componentName = page.value.component;
        if (componentName === 'Dashboard') return 'Dashboard';
        if (componentName === 'Organograma') return 'Organograma de Setores';
        if (componentName === 'Contratos') return 'Gestão de Contratos';
    }

    // 3. Usando URL da página atual
    const currentPath = window.location.pathname;

    // Verifica se a URL atual contém alguma das chaves no mapeamento
    for (const [urlPath, title] of Object.entries(urlTitles)) {
        if (currentPath.includes(urlPath)) {
            return title;
        }
    }

    // 4. Fallback: extrai a última parte do caminho da URL
    const pathSegments = currentPath.split('/').filter(Boolean);
    if (pathSegments.length > 0) {
        const lastSegment = pathSegments[pathSegments.length - 1];

        // Tratamento de casos especiais
        if (lastSegment === 'contratos') {
            return 'Gestão de Contratos';
        }

        return lastSegment.charAt(0).toUpperCase() + lastSegment.slice(1);
    }

    // 5. Valor padrão
    return 'Página Inicial';
});

// Métodos
const toggleCollapse = () => {
    if (sidebarRef.value) {
        sidebarRef.value.toggleCollapse();
    }
};

const toggleSidebar = () => {
    if (sidebarRef.value) {
        sidebarRef.value.toggleSidebar();
    }
};

const closeSidebar = () => {
    if (sidebarVisible.value && sidebarRef.value) {
        sidebarRef.value.toggleSidebar();
    }
};

const onSidebarCollapse = (collapsed) => {
    sidebarCollapsed.value = collapsed;
};

const onSidebarToggle = (toggled) => {
    sidebarVisible.value = toggled;
};

const handleSubmenuClick = (submenuName) => {
    console.log('Submenu clicked:', submenuName);
    // Adicione aqui a lógica específica para quando um submenu é clicado no modo collapsed
};
</script>

<style scoped>
.app-layout {
    display: flex;
    min-height: 100vh;
    background-color: #f5f5f7;
}

.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    margin-left: 260px; /* Largura do sidebar */
    transition: margin-left 0.3s ease;
}

.app-layout.sidebar-collapsed .main-content {
    margin-left: 80px; /* Largura do sidebar quando recolhido */
}

.app-header {
    background-color: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 0 1.5rem;
    height: 64px;
    display: flex;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 20;
}

.header-container {
    width: 100%;
    display: flex;
    align-items: center;
}

.mobile-toggle-btn {
    display: none;
    background: none;
    border: none;
    font-size: 1.25rem;
    cursor: pointer;
    color: #6b7280;
    margin-right: 1rem;
}

.collapse-btn {
    background: none;
    border: none;
    font-size: 1.25rem;
    cursor: pointer;
    color: #6b7280;
    margin-right: 1rem;
}

.page-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #111827;
}

.user-area {
    margin-left: auto;
    display: flex;
    align-items: center;
    cursor: pointer;
}

.user-name {
    font-size: 0.875rem;
    font-weight: 500;
    margin-right: 0.75rem;
}

.user-avatar {
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #6b7280;
}

.page-content {
    flex: 1;
    padding: 1.5rem;
}

.app-footer {
    padding: 1rem;
    text-align: center;
    color: #6b7280;
}

.overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 30;
    display: none;
}

.overlay.active {
    display: block;
}

/* Ajustes responsivos */
@media (max-width: 992px) {
    .main-content {
        margin-left: 0;
    }

    .app-layout.sidebar-collapsed .main-content {
        margin-left: 0;
    }

    .mobile-toggle-btn {
        display: block;
    }

    .collapse-btn {
        display: none;
    }
}
</style>
