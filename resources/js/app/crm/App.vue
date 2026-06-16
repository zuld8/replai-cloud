<template>
    <div class="chat-container"> 
        <div class="chat-body"> 
            
            <!-- Sidebar Overlay untuk Mobile -->
            <div class="sidebar-overlay" :class="{ show: showOverlay }" @click="closeAllSidebars"></div>

            <!-- Left Sidebar - Group Component -->
            <GroupComponent 
                :class="{ show: showLeftSidebar }"
                @close-sidebar="closeLeftSidebar"
            />

            <!-- Main Content Area -->
          <router-view @toggle-left-sidebar="toggleLeftSidebar"></router-view>
        </div>

        <nprogress-container></nprogress-container>
    </div>
</template>

<script>
import GroupComponent from "./Components/GroupComponent.vue";

export default {
    name: "App",
    components: {
        GroupComponent,
    },
    data() {
        return {
            userName: "",
            showLeftSidebar: false,
            showOverlay: false,
        };
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
    },
};
</script>

<style>
/*
 * CRM Shell Layout — single source of truth (not in gitignored chatui.css)
 * Non-scoped so rules apply globally to .sidebar-left etc rendered by child components.
 */

/* ── Desktop: two-column flex (sidebar + content) ── */
.chat-container {
    display: flex;
    flex-direction: column;
    height: 100dvh;
    overflow: hidden;
}

.chat-body {
    display: flex;
    flex: 1;
    overflow: hidden;
    position: relative;
}

/* sidebar-left is GroupComponent root */
.sidebar-left {
    width: 340px;
    min-width: 280px;
    max-width: 380px;
    flex-shrink: 0;
    overflow-y: auto;
    background: #fff;
    border-right: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    position: relative;
    z-index: 1;
    transition: left 0.3s ease;
}

/* sidebar-overlay hidden on desktop */
.sidebar-overlay {
    display: none;
}

/* ── Mobile/Tablet (max 992px): off-canvas ── */
@media (max-width: 992px) {
    .chat-container {
        height: 100dvh;
    }

    .sidebar-left {
        position: fixed;
        top: 0;
        bottom: 0;
        left: -100%;
        width: 85%;
        max-width: 360px;
        min-width: 0;
        z-index: 1100;
        transition: left 0.3s ease;
        box-shadow: none;
        overflow-y: auto;
        background: #fff;
    }

    .sidebar-left.show {
        left: 0;
        box-shadow: 2px 0 20px rgba(0, 0, 0, 0.18);
    }

    .sidebar-overlay {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
        z-index: 1090;
    }

    .sidebar-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .chat-body > .chat-wrapper,
    .chat-body > div:not(.sidebar-left):not(.sidebar-overlay) {
        width: 100%;
        flex: 1;
        min-width: 0;
    }
}

/* ── Narrow phones (<576px) ── */
@media (max-width: 576px) {
    .sidebar-left {
        width: 92%;
        max-width: 340px;
    }
}

/* ── Close button inside sidebar ── */
.sidebar-close-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    border: none;
    background: rgba(0,0,0,0.07);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    color: #374151;
    z-index: 10;
    transition: background 0.2s;
    min-height: 44px;
    min-width: 44px;
}

.sidebar-close-btn:hover {
    background: rgba(0,0,0,0.13);
}
</style>