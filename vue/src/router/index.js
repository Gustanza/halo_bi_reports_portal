import Login from "../views/Login.vue";
import AuthLayout from "../components/AuthLayout.vue";
import DashLayout from "../components/DashLayout.vue";
import Departments from "../views/Departments.vue";
import Reports from "../views/Reports.vue"
import store from "../store";
import { createRouter, createWebHistory } from 'vue-router';

const routes = [

    {
        path: '/auth',
        redirect: "/login",
        name: "AuthLayout",
        meta: {
            isGuest: true
        },
        component: AuthLayout,
        children: [
            {
                path: "/login",
                name: "Login",
                component: Login
            }
        ]
    },


    {
        path: "/",
        name: "Departments",
        component: Departments
    },

    // {
    //     path: "/",
    //     name: "DashLayout",
    //     redirect: "/",
    //     meta: {
    //         requiresAuth: true
    //     },
    //     component: DashLayout,
    //     children: [
    //         {
    //             path: "/reports/:title",
    //             name: "Reports",
    //             props: true,
    //             component: Reports
    //         }
    //     ]
    // }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !store.state.user.token) {
        next({ name: "Login" });
    } else if (store.state.user.token && to.meta.isGuest) {
        next({ name: "DashLayout" });
    } else {
        next();
    }
})

export default router;