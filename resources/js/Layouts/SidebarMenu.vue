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
                        <!-- Dashboard -->
                        <li class="menu-item" :class="{ 'active': isActive('dashboard') }">
                            <a href="/dashboard" class="menu-link">
                                <span class="menu-icon">
                                  <i class="fa fa-home"></i>
                                </span>
                                <span class="menu-title">Dashboard</span>
                            </a>
                        </li>

                        <!-- Organograma -->
                        <li v-if="setorInfo.hierarquia === '1'" class="menu-item" :class="{ 'active': isActive('organograma') }">
                            <a href="/organograma" class="menu-link">
                                <span class="menu-icon">
                                    <i class="fa fa-sitemap"></i>
                                </span>
                                <span class="menu-title">Organograma</span>
                            </a>
                        </li>

                        <li v-if="setorInfo.hierarquia === '1'" class="menu-item" :class="{ 'active': isActive('ciclo-contratacao') }">
                            <a href="/ciclo-contratacao" class="menu-link">
                                <span class="menu-icon">
                                    <i class="fa fa-calendar-check"></i>
                                </span>
                                <span class="menu-title">Ciclo Contratação</span>
                            </a>
                        </li>
                        <!-- Contratos -->
                        <li class="menu-item" :class="{ 'active': isActive('plano-contratacao-setor') }">
                            <a href="/plano-contratacao-setor" class="menu-link">
                                <span class="menu-icon">
                                    <i class="fa fa-file-contract"></i>
                                </span>
                                <span class="menu-title">Plano Contratação Setor</span>
                            </a>
                        </li>
                        <li v-if="setorInfo.hierarquia !== '4' " class="menu-item" :class="{ 'active': isActive('contratos') }">
                            <a href="/plano-contratacao-tce" class="menu-link">
                                <span class="menu-icon">
                                    <i class="fa fa-book-bookmark"></i>
                                </span>
                                <span class="menu-title">Plano Contratação TCE</span>
                            </a>
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
                                        <a href="/relatorios/diario" class="menu-link">
                                            <span class="menu-title">Diário</span>
                                        </a>
                                    </li>
                                    <li class="menu-item" :class="{ 'active': isActive('relatorio-mensal') }">
                                        <a href="/relatorios/mensal" class="menu-link">
                                            <span class="menu-title">Mensal</span>
                                        </a>
                                    </li>
                                    <li class="menu-item" :class="{ 'active': isActive('relatorio-anual') }">
                                        <a href="/relatorios/anual" class="menu-link">
                                            <span class="menu-title">Anual</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- Logout (Novo item adicionado) -->
                        <li class="menu-item mt-auto border-t border-gray-200 pt-2">
                            <a href="#" @click.prevent="logout" class="menu-link text-red-600 hover:bg-red-50">
                                <span class="menu-icon">
                                    <i class="fa fa-sign-out-alt"></i>
                                </span>
                                <span class="menu-title">Sair</span>
                            </a>
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

<script setup>
import { ref, nextTick, onMounted, defineEmits, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3'; // Adicionado "router" para fazer o logout
const emit = defineEmits(['collapse-changed', 'toggle-changed', 'submenu-clicked']);
const collapsed = ref(false);
const toggled = ref(false);
const activeItem = ref('dashboard');
const openSubmenus = ref([]);
const submenuHeights = ref({});
const setorInfo = ref({});

// Função de logout
const logout = () => {
    router.post('/logout', {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Opcional: redirecionar para uma página específica
            window.location.href = '/login';
        }
    });
};

// Determina o item ativo com base na URL atual
onMounted(() => {
    const page = usePage();
    setorInfo.value = computed(() => page.props.setor_info);
    setorInfo.value = setorInfo.value.value;
    const path = window.location.pathname;

    if (path.includes('dashboard')) {
        activeItem.value = 'dashboard';
    } else if (path.includes('organograma')) {
        activeItem.value = 'organograma';
    } else if (path.includes('plano-contratacao-setor')) {
        activeItem.value = 'plano-contratacao-setor';
    }else if (path.includes('ciclo-contratacao')) {
        activeItem.value = 'ciclo-contratacao';
    } else if (path.includes('configuracoes/geral')) {
        activeItem.value = 'config-geral';
        openSubmenus.value.push('config');
    } else if (path.includes('configuracoes/perfil')) {
        activeItem.value = 'config-perfil';
        openSubmenus.value.push('config');
    } else if (path.includes('relatorios/diario')) {
        activeItem.value = 'relatorio-diario';
        openSubmenus.value.push('relatorios');
    } else if (path.includes('relatorios/mensal')) {
        activeItem.value = 'relatorio-mensal';
        openSubmenus.value.push('relatorios');
    } else if (path.includes('relatorios/anual')) {
        activeItem.value = 'relatorio-anual';
        openSubmenus.value.push('relatorios');
    }

    // Calcula todas as alturas dos submenus inicialmente
    nextTick(() => {
        const submenus = ['config', 'relatorios'];
        submenus.forEach(menu => {
            calculateSubmenuHeight(menu);
        });
    });
});

// Métodos
const toggleCollapse = () => {
    collapsed.value = !collapsed.value;
    emit('collapse-changed', collapsed.value);
};

const toggleSidebar = () => {
    toggled.value = !toggled.value;
    emit('toggle-changed', toggled.value);
};

const isActive = (itemName) => {
    return activeItem.value === itemName;
};

const toggleSubmenu = (submenuName) => {
    if (collapsed.value) {
        // Quando o menu está recolhido, apenas emite o evento para o componente pai
        emit('submenu-clicked', submenuName);
        return;
    }

    const index = openSubmenus.value.indexOf(submenuName);
    if (index > -1) {
        openSubmenus.value.splice(index, 1);
    } else {
        // Fecha outros submenus quando estiver em modo "accordion"
        // Remova a linha abaixo se quiser permitir múltiplos submenus abertos
        openSubmenus.value = [];

        // Adiciona o novo submenu
        openSubmenus.value.push(submenuName);

        // Calcula a altura do submenu se ainda não foi calculada
        if (!submenuHeights.value[submenuName]) {
            nextTick(() => {
                calculateSubmenuHeight(submenuName);
            });
        }
    }
};

const calculateSubmenuHeight = (submenuName) => {
    const submenuEl = document.querySelector(`[data-submenu="${submenuName}"] .sub-menu-list ul`);
    if (submenuEl) {
        submenuHeights.value[submenuName] = `${submenuEl.scrollHeight}px`;
    }
};

const getSubmenuStyle = (submenuName) => {
    if (openSubmenus.value.includes(submenuName)) {
        return {
            height: submenuHeights.value[submenuName] || 'auto',
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
};

// Métodos expostos
const openSubmenu = (submenuName) => {
    if (!openSubmenus.value.includes(submenuName)) {
        openSubmenus.value.push(submenuName);
    }
};

const closeSubmenu = (submenuName) => {
    const index = openSubmenus.value.indexOf(submenuName);
    if (index > -1) {
        openSubmenus.value.splice(index, 1);
    }
};

const setActive = (itemName) => {
    activeItem.value = itemName;
};

// Expor métodos para uso externo
defineExpose({
    toggleCollapse,
    toggleSidebar,
    openSubmenu,
    closeSubmenu,
    setActive
});
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
    display: flex;
    flex-direction: column;
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
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.menu ul {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
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
