<template>
    <div class="sidebar-menu">
        <div class="logo">
            <a href="javascript:void(0);" class="logo-normal"
                ><img :src="attribute.icon" :alt="attribute.name"
            /></a>
        </div>
        <div class="menu-wrap">
            <div class="main-menu">
                <ul class="nav">
                    <li>
                        <a
                            href="javascript:void(0);"
                            @click="changeTab('chat')"
                            :class="
                                $store.getters.get_active_tab == 'chat'
                                    ? 'active'
                                    : ''
                            "
                        >
                            <i class="ti ti-message-2-heart"></i>
                        </a>
                    </li>

                    <li>
                        <a
                            href="javascript:void(0);"
                            @click="changeTab('group')"
                            :class="
                                $store.getters.get_active_tab == 'group'
                                    ? 'active'
                                    : ''
                            "
                        >
                            <i class="ti ti-users-group"></i>
                        </a>
                    </li>
                    <li>
                        <a
                            href="javascript:void(0);"
                            @click="changeTab('contact')"
                            :class="
                                $store.getters.get_active_tab == 'contact'
                                    ? 'active'
                                    : ''
                            "
                        >
                            <i class="ti ti-list"></i>
                        </a>
                    </li>

                    <li>
                        <a href="javascript:void(0);" @click="toHome">
                            <i class="ti ti-logout-2"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="profile-menu">
                <ul>
                    <li>
                        <a
                            href="#"
                            id="dark-mode-toggle"
                            @click="enableDarkMode()"
                            class="dark-mode-toggle active"
                        >
                            <i class="ti ti-moon"></i>
                        </a>
                        <a
                            href="#"
                            @click="disableDarkMode()"
                            id="light-mode-toggle"
                            class="dark-mode-toggle"
                        >
                            <i class="ti ti-sun"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
export default {
    components: {},
    data() {
        return {
            attribute: {
                name: "",
                icon: "",
            },
        };
    },
    computed: {},
    methods: {
        ...mapActions(["set_tab", "set_url"]),

        changeTab(tab) {
            this["set_tab"](tab);
        },

        toHome() {
            return (window.location = "/app/device");
        },

        enableDarkMode() {
            var darkModeToggle = document.getElementById("dark-mode-toggle");
            var lightModeToggle = document.getElementById("light-mode-toggle");

            document.body.classList.add("darkmode");
            darkModeToggle.classList.remove("active");
            lightModeToggle.classList.add("active");
            localStorage.setItem("darkMode", "enabled");
        },

        disableDarkMode() {
            var darkModeToggle = document.getElementById("dark-mode-toggle");
            var lightModeToggle = document.getElementById("light-mode-toggle");
            document.body.classList.remove("darkmode");
            darkModeToggle.classList.add("active");
            lightModeToggle.classList.remove("active");
            localStorage.setItem("darkMode", "disabled");
        },

        setTheme() {
            if (localStorage.getItem("darkMode") === "enabled") {
                this.enableDarkMode();
            } else {
                this.disableDarkMode();
            }
        },

        async getLogo() {
            try {
                const response = await this.$axios.get(`/components/system`);
                var data = response.data;
                this.attribute = data;
                this["set_url"](data.server_url);
            } catch (error) {
                this.$handleErrorResponse(error);
            }
        },
    },

    mounted() {
        this.setTheme();
        this.getLogo();
    },

    watch: {
        $route(to, from) {
            if (
                this.$store.getters.get_active_tab == "" ||
                this.$store.getters.get_active_tab == null
            ) {
                this.changeTab("chat");
            }
        },
    },
};
</script>
