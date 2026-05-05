<template>
    <div class="content main_content">
        <SidebarComponent />
        <GroupComponent />
        <router-view> </router-view>
        <nprogress-container></nprogress-container>
    </div>
</template>

<script>
import SidebarComponent from "./pages/Components/SidebarComponent.vue";
import GroupComponent from "./pages/Components/GroupComponent.vue";
export default {
    components: {
        SidebarComponent,
        GroupComponent,
    },
    data() {
        return {};
    },

    methods: {
        themeConfiguration() {
            if ($(".main-wrapper").length > 0) {
                document.getElementsByClassName(
                    "main-wrapper"
                )[0].style.visibility = "visible";
            }
        },
 
	  
    },

    mounted() {
        this.themeConfiguration();
    },

    watch: {
        $route(to, from) {},
    },
};
</script>
