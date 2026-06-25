<template>
    <div class="chat-container"> 
        <div class="chat-body"> 
            <!-- Sidebar Overlay -->
            <div class="sidebar-overlay" :class="{ show: showOverlay }" @click="closeAllSidebars"></div>
            <!-- Left Sidebar -->
            <GroupComponent 
                :class="{ show: showLeftSidebar }"
                @close-sidebar="closeLeftSidebar"
            />
            <!-- Main Content -->
            <router-view @toggle-left-sidebar="toggleLeftSidebar"></router-view>
        </div>
        <nprogress-container></nprogress-container>
    </div>
</template>

<script>
import GroupComponent from "./Components/GroupComponent.vue";

export default {
    name: "App",
    components: { GroupComponent },
    data() {
        return {
            userName: "",
            showLeftSidebar: false,
            showOverlay: false,
        };
    },
    watch: {
        showLeftSidebar(val) {
            this.$nextTick(() => this._applySidebar(val));
        },
        // Auto-close sidebar when navigating to a chat on mobile
        '$route'(to) {
            if (this._isMobile() && to.name === 'chat_room') {
                this.closeLeftSidebar();
            }
        }
    },
    methods: {
        toggleLeftSidebar() {
            this.showLeftSidebar = !this.showLeftSidebar;
            this.showOverlay = this.showLeftSidebar;
        },
        closeLeftSidebar() {
            this.showLeftSidebar = false;
            this.showOverlay = false;
        },
        closeAllSidebars() {
            this.showLeftSidebar = false;
            this.showOverlay = false;
        },

        /* ─── JS-driven mobile drawer ─────────────────────────────────
         * Sets inline styles with 'important' priority.
         * This beats ANY external CSS, including !important in stylesheets,
         * because inline style + 'important' has absolute highest priority.
         * ────────────────────────────────────────────────────────────── */
        _isMobile() {
            return window.innerWidth <= 992;
        },

        _initSidebar() {
            const el = document.getElementById('leftSidebar');
            if (!el) return;

            if (this._isMobile()) {
                // Force base styles for off-canvas drawer
                const styles = {
                    'position': 'fixed',
                    'top': '0',
                    'left': '0',
                    'bottom': '0',
                    'height': '100%',
                    'width': '85vw',
                    'max-width': '360px',
                    'z-index': '999999',
                    'transform': 'translateX(-110%)',
                    'transition': 'transform 0.3s cubic-bezier(0.4,0,0.2,1)',
                    'background-color': '#fff',
                    'overflow-y': 'auto',
                    'overflow-x': 'hidden',
                    'visibility': 'visible',
                    'opacity': '1',
                    'will-change': 'transform',
                };
                for (const [k, v] of Object.entries(styles)) {
                    el.style.setProperty(k, v, 'important');
                }
            } else {
                // Reset to desktop flow
                el.style.cssText = '';
            }
        },

        _applySidebar(open) {
            if (!this._isMobile()) return;
            const el = document.getElementById('leftSidebar');
            if (!el) return;

            if (open) {
                el.style.setProperty('transform', 'translateX(0)', 'important');
                el.style.setProperty('box-shadow', '4px 0 24px rgba(0,0,0,0.3)', 'important');
            } else {
                el.style.setProperty('transform', 'translateX(-110%)', 'important');
                el.style.setProperty('box-shadow', 'none', 'important');
            }
        },

        _onResize() {
            this._initSidebar();
            if (!this._isMobile()) {
                this.showLeftSidebar = false;
                this.showOverlay = false;
            }
        },

        themeConfiguration() {
            if ($(".main-wrapper").length > 0) {
                document.getElementsByClassName("main-wrapper")[0].style.visibility = "visible";
            }
        },

        async getUserInfo() {
            try {
                const response = await this.$axios.get("/components/system");
                this.userName = response.data.name || "User";
            } catch (error) {
                console.error("Error fetching user info:", error);
            }
        },
    },

    mounted() {
        this.themeConfiguration();
        this.getUserInfo();
        // Init sidebar after Vue renders GroupComponent
        this.$nextTick(() => {
            this._initSidebar();
        });
        window.addEventListener('resize', this._onResize);
    },

    beforeDestroy() {
        window.removeEventListener('resize', this._onResize);
    },
};
</script>

<style>
/*
 * CRM Shell — minimal CSS, backed by JS for mobile sidebar.
 * The JS-driven approach (_initSidebar, _applySidebar) overrides all CSS.
 */

.chat-container {
    display: flex;
    flex-direction: column;
    height: 100dvh;
    position: relative;
}

.chat-body {
    display: flex;
    flex: 1;
    position: relative;
    min-height: 0;
}

/* Desktop: sidebar in-flow */
@media (min-width: 993px) {
    .sidebar-left {
        width: 340px;
        min-width: 280px;
        flex-shrink: 0;
        overflow-y: auto;
        background: #fff;
        border-right: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .sidebar-overlay { display: none !important; }
}

/* Overlay — CSS fallback (JS might not set this) */
.sidebar-overlay {
    display: none;
    position: fixed !important;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 999998 !important;
    cursor: pointer;
}
.sidebar-overlay.show {
    display: block !important;
}

/* Mobile: sidebar initial state (JS will override on mount) */
@media (max-width: 992px) {
    /* Fix: subtract header height so container doesn't overflow viewport */
    .chat-container {
        height: calc(100dvh - 3.75rem);
    }

    .chat-body > div:not(.sidebar-left):not(.sidebar-overlay) {
        flex: 1;
        min-width: 0;
        width: 100%;
    }

    .main-chat {
        /* Removed: height: 100dvh — caused input to go off-screen on mobile */
        flex: 1;
        min-height: 0;
        display: flex;
        flex-direction: column;
    }

    .chat-messages {
        flex: 1;
        min-height: 0;  /* Critical: allows flex shrinking so input stays visible */
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    .chat-input-area {
        flex-shrink: 0;
        padding-bottom: env(safe-area-inset-bottom, 0px);
    }
}
</style>