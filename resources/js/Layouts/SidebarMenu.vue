<template>
    <aside id="sidebar" :class="['sidebar', { 'collapsed': collapsed, 'toggled': toggled }]">
        <div class="sidebar-layout">
            <!-- Logo/Header -->
            <div class="sidebar-header">
                <h2>PCA</h2>
                <!-- Logo/Header
                <div v-show="!collapsed" class="logo-full">
                    <img src="/imagens/logotipo.png" class="w-60" alt="Logo">
                </div>
                <div v-show="collapsed" class="logo-icon">
                    <img src="/imagens/ico.png" class="w-12" alt="Logo">
                </div>
                -->
            </div>

            <!-- Menu Content -->
            <div class="sidebar-content">
                <nav class="menu">
                    <ul>
                        <!-- Item simples -->
                        <li class="menu-item" :class="{ 'active': isActive('dashboard') }">
                            <a href="#" class="menu-link">
                <span class="menu-icon">
                  <i class="fa fa-home"></i>
                </span>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>

                        <!-- Item simples -->
                        <li class="menu-item" :class="{ 'active': isActive('usuarios') }">
                            <a href="#" class="menu-link">
                                <span class="menu-icon">
                                    <i class="fa fa-user"></i>
                                </span>
                                <span class="menu-title">Organograma</span>
                            </a>
                        </li>

                        <!-- Item com submenu -->
                        <li class="menu-item sub-menu" :class="{ 'open': openSubmenus.includes('config') }">
                            <a href="#" class="menu-link" @click.prevent="toggleSubmenu('config')">
                <span class="menu-icon">
                  <i class="fa fa-gear"></i>
                </span>
                                <span class="menu-title">Configurações</span>
                                <span class="menu-arrow">
                  <i :class="['fa', openSubmenus.includes('config') ? 'fa-chevron-down' : 'fa-chevron-right']"></i>
                </span>
                            </a>
                            <div class="sub-menu-list" :style="getSubmenuStyle('config')">
                                <ul>
                                    <li class="menu-item" :class="{ 'active': isActive('config-geral') }">
                                        <a href="#" class="menu-link">
                                            <span class="menu-title">Geral</span>
                                        </a>
                                    </li>
                                    <li class="menu-item" :class="{ 'active': isActive('config-perfil') }">
                                        <a href="#" class="menu-link">
                                            <span class="menu-title">Perfil</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- Item com submenu -->
                        <li class="menu-item sub-menu" :class="{ 'open': openSubmenus.includes('relatorios') }">
                            <a href="#" class="menu-link" @click.prevent="toggleSubmenu('relatorios')">
                <span class="menu-icon">
                  <i class="fa fa-chart-bar"></i>
                </span>
                                <span class="menu-title">Relatórios</span>
                                <span class="menu-arrow">
                  <i :class="['fa', openSubmenus.includes('relatorios') ? 'fa-chevron-down' : 'fa-chevron-right']"></i>
                </span>
                            </a>
                            <div class="sub-menu-list" :style="getSubmenuStyle('relatorios')">
                                <ul>
                                    <li class="menu-item" :class="{ 'active': isActive('relatorio-diario') }">
                                        <a href="#" class="menu-link">
                                            <span class="menu-title">Diário</span>
                                        </a>
                                    </li>
                                    <li class="menu-item" :class="{ 'active': isActive('relatorio-mensal') }">
                                        <a href="#" class="menu-link">
                                            <span class="menu-title">Mensal</span>
                                        </a>
                                    </li>
                                    <li class="menu-item" :class="{ 'active': isActive('relatorio-anual') }">
                                        <a href="#" class="menu-link">
                                            <span class="menu-title">Anual</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </nav>
            </div>

            <!-- Footer exactamente como solicitado -->
            <div class="sidebar-footer">
                <div class="footer-content bg-gray-500 p-2 rounded">
                    <img src="/images/logo_TCE.png" class="w-16" alt="Logo Footer">
                </div>
            </div>
        </div>
    </aside>
</template>

<script>
export default {
    name: 'SidebarMenu',
    data() {
        return {
            collapsed: false,
            toggled: false,
            activeItem: 'dashboard',
            openSubmenus: [],
            submenuHeights: {}
        }
    },
    methods: {
        toggleCollapse() {
            this.collapsed = !this.collapsed;
            this.$emit('collapse-changed', this.collapsed);
        },

        toggleSidebar() {
            this.toggled = !this.toggled;
            this.$emit('toggle-changed', this.toggled);
        },

        isActive(itemName) {
            return this.activeItem === itemName;
        },

        toggleSubmenu(submenuName) {
            if (this.collapsed) {
                // Quando o menu está recolhido, apenas emite o evento para o componente pai
                this.$emit('submenu-clicked', submenuName);
                return;
            }

            const index = this.openSubmenus.indexOf(submenuName);
            if (index > -1) {
                this.openSubmenus.splice(index, 1);
            } else {
                // Fecha outros submenus quando estiver em modo "accordion"
                // Remova a linha abaixo se quiser permitir múltiplos submenus abertos
                this.openSubmenus = [];

                // Adiciona o novo submenu
                this.openSubmenus.push(submenuName);

                // Calcula a altura do submenu se ainda não foi calculada
                if (!this.submenuHeights[submenuName]) {
                    this.$nextTick(() => {
                        this.calculateSubmenuHeight(submenuName);
                    });
                }
            }
        },

        calculateSubmenuHeight(submenuName) {
            const submenuEl = this.$el.querySelector(`[data-submenu="${submenuName}"] .sub-menu-list ul`);
            if (submenuEl) {
                this.submenuHeights[submenuName] = `${submenuEl.scrollHeight}px`;
            }
        },

        getSubmenuStyle(submenuName) {
            if (this.openSubmenus.includes(submenuName)) {
                return {
                    height: this.submenuHeights[submenuName] || 'auto',
                    visibility: 'visible',
                    opacity: '1'
                };
            } else {
                return {
                    height: '0',
                    visibility: 'hidden',
                    opacity: '0'
                };
            }
        },

        // Para uso externo, se necessário
        openSubmenu(submenuName) {
            if (!this.openSubmenus.includes(submenuName)) {
                this.openSubmenus.push(submenuName);
            }
        },

        closeSubmenu(submenuName) {
            const index = this.openSubmenus.indexOf(submenuName);
            if (index > -1) {
                this.openSubmenus.splice(index, 1);
            }
        },

        setActive(itemName) {
            this.activeItem = itemName;
        }
    },
    mounted() {
        // Calcula todas as alturas dos submenus inicialmente
        this.$nextTick(() => {
            const submenus = ['config', 'relatorios'];
            submenus.forEach(menu => {
                this.calculateSubmenuHeight(menu);
            });
        });
    }
}
</script>

<style scoped>
/* Estilos base do sidebar */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    z-index: 40;
    width: 260px;
}

.sidebar.collapsed {
    width: 80px;
}

.sidebar.toggled {
    transform: translateX(0);
}

.sidebar-layout {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.sidebar-header {
    padding: 1rem;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 70px;
    border-bottom: 1px solid #f0f0f0;
}

.sidebar-content {
    flex-grow: 1;
    overflow-y: auto;
}

.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid #f0f0f0;
    margin-top: auto;
}

.footer-content {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Estilos do menu */
.menu {
    width: 100%;
}

.menu-item {
    width: 100%;
    position: relative;
}

.menu-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: #4b5563;
    transition: all 0.2s;
    width: 100%;
}

.menu-link:hover {
    background-color: #f3f4f6;
}

.menu-icon {
    margin-right: 0.75rem;
    width: 1.5rem;
    text-align: center;
}

.menu-title {
    font-size: 0.875rem;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.menu-arrow {
    margin-left: auto;
    font-size: 0.75rem;
}

.menu-item.active > .menu-link {
    background-color: #e0f2fe;
    color: #0284c7;
}

/* Estilos do submenu */
.sub-menu-list {
    transition: all 0.3s ease;
    overflow: hidden;
}

.sub-menu-list ul {
    padding: 0;
    margin: 0;
    list-style: none;
}

.sub-menu-list .menu-link {
    padding-left: 3rem;
}

/* Estilos para menu responsivo */
@media (max-width: 992px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar.toggled {
        transform: translateX(0);
    }
}

/* Estilos para o menu quando estiver recolhido */
.sidebar.collapsed .menu-title,
.sidebar.collapsed .menu-arrow {
    display: none;
}

.sidebar.collapsed .sub-menu-list {
    display: none;
}

.sidebar.collapsed .menu-icon {
    margin-right: 0;
    width: 100%;
}

/* Garantir que o footer fique no final do menu */
.sidebar-layout {
    min-height: 100%;
}
</style>
