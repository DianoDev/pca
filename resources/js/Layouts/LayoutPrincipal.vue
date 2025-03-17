<template>
    <div class="app-layout" :class="{ 'sidebar-collapsed': sidebarCollapsed }">
        <!-- Menu Lateral -->
        <SidebarMenu
            ref="sidebar"
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

                    <!-- Título da página -->
                    <h1 class="page-title">Organograma de Setores</h1>

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

<script>
import SidebarMenu from './SidebarMenu.vue';

export default {
    name: 'AppLayout',
    components: {
        SidebarMenu
    },
    data() {
        return {
            sidebarCollapsed: false,
            sidebarVisible: false
        }
    },
    methods: {
        toggleCollapse() {
            this.$refs.sidebar.toggleCollapse();
        },

        toggleSidebar() {
            this.$refs.sidebar.toggleSidebar();
        },

        closeSidebar() {
            if (this.sidebarVisible) {
                this.$refs.sidebar.toggleSidebar();
            }
        },

        onSidebarCollapse(collapsed) {
            this.sidebarCollapsed = collapsed;
        },

        onSidebarToggle(toggled) {
            this.sidebarVisible = toggled;
        },

        handleSubmenuClick(submenuName) {
            console.log('Submenu clicked:', submenuName);
            // Adicione aqui a lógica específica para quando um submenu é clicado no modo collapsed
        }
    }
}
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
