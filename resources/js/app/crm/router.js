// router.js
import { createRouter, createWebHistory } from "vue-router";
import NProgress from "nprogress";

const routes = [
    {
        path: "/app/crm", 
        redirect: "/app/crm/blank",
        component: () => import("./pages/index.vue"),
        children: [
            {
                name: "blank_chat",
                path: "blank",
                meta: { title: "Replai Automation - Select Chat" },
                component: () => import("./pages/blank.vue"),
            }, 
            {
                name: "new_chat",
                path: "new-chat",
                meta: { title: "Creating Chat..." },
                component: () => import("./pages/NewChat.vue"),
            },
            
            {
                name: "chat_room",
                path: "chat/:chatid",
                meta: { title: "Chat" },
                component: () => import("./pages/chat.vue"),
            },
            {
                name: "group_room",
                path: "group/:chatid",
                meta: { title: "Group Chat" },
                component: () => import("./pages/group.vue"),
            },
        ],
    },
];

const router = createRouter({
    mode: "history",
    history: createWebHistory(),
    routes: routes,
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        }
        return { left: 0, top: 0, behavior: 'smooth' };
    },
});

router.beforeEach((to, from, next) => {
    if (to.path) { 
        NProgress.start();
        NProgress.set(0.1);
    }
    next();
});

router.afterEach((to) => {
    setTimeout(() => {
        NProgress.done();
        document.title = to.meta.title || 'Replai Automation';
    }, 200);
});

export default router;