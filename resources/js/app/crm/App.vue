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