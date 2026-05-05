<template>
    <!-- Floating Action Menu - Bisa dihilangkan jika tidak diperlukan -->
    <div class="floating-nav" v-if="showNav">
        <div class="nav-menu">
            <button 
                class="nav-item" 
                :class="{ active: $store.getters.get_active_tab === 'chat' }"
                @click="changeTab('chat')"
                title="Chat"
            >
                <i class="ti ti-message-2-heart"></i>
            </button>
            <button 
                class="nav-item" 
                :class="{ active: $store.getters.get_active_tab === 'assignment' }"
                @click="changeTab('assignment')"
                title="Assignment"
            >
                <i class="ti ti-check"></i>
            </button>
            <button 
                class="nav-item" 
                @click="toHome"
                title="Kembali ke Dashboard"
            >
                <i class="ti ti-logout-2"></i>
            </button>
        </div>
    </div>
</template>

<script>
import { mapActions } from "vuex";

export default {
    name: "SidebarComponent",
    data() {
        return {
            showNav: false, // Set false jika tidak ingin menampilkan navigasi ini
            attribute: {
                name: "",
                icon: "",
            },
        };
    },
    methods: {
        ...mapActions(["set_tab"]),

        changeTab(tab) {
            this["set_tab"](tab);
        },

        toHome() {
            window.location = "/app";
        },

        async getLogo() {
            try {
                const response = await this.$axios.get(`/components/system`);
                this.attribute = response.data;
            } catch (error) {
                console.error(error);
            }
        },
    },
    mounted() {
        this.getLogo();
    },
};
</script>

<style scoped>
/* Floating Navigation - Optional */
.floating-nav {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}

.nav-menu {
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #fff;
    padding: 10px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.nav-item {
    width: 48px;
    height: 48px;
    border: none;
    background: #f3f4f6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 24px;
    color: #374151;
}

.nav-item:hover {
    background: #e5e7eb;
}

.nav-item.active {
    background: #3b82f6;
    color: white;
}

@media (max-width: 768px) {
    .floating-nav {
        bottom: 10px;
        right: 10px;
    }
}
</style>