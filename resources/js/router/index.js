import { createRouter, createWebHistory } from "vue-router";
import { authController } from "../controllers/authController";

import Home from "../components/Home.vue";
import Login from "../components/Login.vue";
import Logout from "../components/Logout.vue";

const routes = [
    {
        path: "/",
        redirect: "home",
    },
    {
        path: "/home",
        name: "home",
        component: Home,
        meta: { requiresAuth: true },
    },
    {
        path: "/login",
        name: "login",
        component: Login,
    },
    {
        path: "/logout",
        name: "logout",
        component: Logout,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

const defineToken = () => {
    const isLoggedIn = authController.isLoggedIn();
    if (isLoggedIn) {
        axios.defaults.headers.common = {
            Authorization: `Bearer ${authController.getToken()}`,
        };
    }
};

router.beforeEach((to, from, next) => {
    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (!authController.isLoggedIn()) {
            next({
                path: "/login",
                query: {
                    redirect: to.fullPath,
                },
            });
        }
        defineToken();
        next();
    } else {
        next();
    }
});

export default router;
