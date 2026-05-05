import { createRouter, createWebHistory } from "vue-router";
import NProgress from "nprogress";

// Router Module

const routes = [
    {
        path: "/app/device/chat-app", 
        component: () => import("../pages/index.vue"),
        children: [
            {
                name: "blank_chat",
                path: "blank/:id",
                meta: {
                    title: "Chat App",
                },
                component: () => import("../pages/blank.vue"),
            },
            {
                name: "chat_room",
                path: "chat/:id/:chatid",
                meta: {
                    title: "Chat App",
                },
                component: () => import("../pages/chat.vue"),
            },
            {
                name: "group_room",
                path: "group/:id/:chatid",
                meta: {
                    title: "Chat App",
                },
                component: () => import("../pages/group.vue"),
            },
        ],
    },
];

const router = createRouter({
    mode: "history",
    history: createWebHistory(),
    routes: routes,
    scrollBehavior(to, from, savedPosition) {
        return { left: 0, top: 0 };
    },
});

router.beforeEach((to, from, next) => {
    if (to.path) { 
        NProgress.start();
        NProgress.set(0.1);
    }
    next();
});

router.afterEach((to, from) => {
    setTimeout(() => NProgress.done(), 200);
});

export default router;
