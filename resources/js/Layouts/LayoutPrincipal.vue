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

// Mapeamento correto de URLs para títulos de páginas
// Corresponde exatamente às URLs do menu
const urlTitles = {
    '/dashboard': 'Painel Principal',
    '/organograma': 'Estrutura Organizacional',
    '/ciclo-contratacao': 'Ciclo de Contratações',
    '/plano-contratacao-setor': 'Planos do Setor',
    '/plano-contratacao-tce': 'Aprovação de Planos',
    '/relatorios/diario': 'Resumo Diário',
    '/relatorios/mensal': 'Análise Mensal',
    '/relatorios/anual': 'Consolidado Anual'
};

// Computed property para o título da página baseado em diferentes fontes
const pageTitle = computed(() => {
    // 1. Prioridade: Props vindos do componente pai
    if (props.pageTitle && props.pageTitle !== 'Página Inicial') {
        return props.pageTitle;
    }

    // 2. Usando URL da página atual
    const currentPath = window.location.pathname;

    // Verificação exata pela URL completa
    if (urlTitles[currentPath]) {
        return urlTitles[currentPath];
    }

    // 3. Verificação parcial (caso seja uma rota com parâmetros)
    for (const [urlPath, title] of Object.entries(urlTitles)) {
        if (currentPath.includes(urlPath)) {
            return title;
        }
    }

    // 4. Se estiver usando Inertia.js, tenta pelo componente
    if (page && page.value && page.value.component) {
        const componentName = page.value.component;

        // Mapeamento de componentes para títulos
        const componentTitles = {
            'Dashboard': 'Painel Principal',
            'Organograma': 'Estrutura Organizacional',
            'PlanoCotratacaoSetor': 'Planos do Setor',
            'CicloContratacao': 'Ciclo de Contratações'
        };

        if (componentTitles[componentName]) {
            return componentTitles[componentName];
        }
    }

    // 5. Fallback: extrai a última parte do caminho da URL
    const pathSegments = currentPath.split('/').filter(Boolean);
    if (pathSegments.length > 0) {
        const lastSegment = pathSegments[pathSegments.length - 1];

        // Tratamento de casos especiais
        if (lastSegment === 'contratos') {
            return 'Gestão de Contratos';
        }

        return lastSegment.charAt(0).toUpperCase() + lastSegment.slice(1);
    }

    // 6. Valor padrão
    return 'Painel Principal';
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
    background-color: #fafbff;
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
    background: #235a99;
    box-shadow: 0 4px 12px rgba(43, 108, 176, 0.1);
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
    color: #ffffff;
    margin-right: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.mobile-toggle-btn:hover {
    transform: rotate(5deg) scale(1.1);
}

.collapse-btn {
    background: none;
    border: none;
    font-size: 1.25rem;
    cursor: pointer;
    color: #ffffff;
    margin-right: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.collapse-btn:hover {
    transform: rotate(5deg) scale(1.1);
}

.page-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #ffffff;
    text-shadow: 0px 1px 2px rgba(0, 0, 0, 0.1);
}

.user-area {
    margin-left: auto;
    display: flex;
    align-items: center;
    cursor: pointer;
    background-color: rgba(255, 255, 255, 0.12);
    padding: 0.5rem 1rem;
    border-radius: 30px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.user-area:hover {
    background-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
}

.user-name {
    font-size: 0.875rem;
    font-weight: 500;
    margin-right: 0.75rem;
    color: #ffffff;
}

.user-avatar {
    width: 2.2rem;
    height: 2.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transition: all 0.3s;
}

.user-area:hover .user-avatar {
    transform: scale(1.05);
    background-color: rgba(255, 255, 255, 0.18);
}

.page-content {
    flex: 1;
    padding: 1.5rem;
}

.app-footer {
    padding: 1rem;
    text-align: center;
    color: #718096;
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
