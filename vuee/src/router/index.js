import Login from "../views/Login.vue";
import AuthLayout from "../components/AuthLayout.vue";
import Departments from "../views/Departments.vue";
import Reports from "../views/Reports.vue"
import store from "../store";
import ManageDepartments from "../views/ManageDepartments.vue";
import ManageUsers from "../views/ManageUsers.vue";
import ForgotPassword from "../views/ForgotPassword.vue";
import ResetPassword from "../views/ResetPassword.vue";


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
            },
            {
                path: "/forgot-password",
                name: "ForgotPassword",
                component: ForgotPassword
            },
            {
                path: "/reset-password",
                name: "ResetPassword",
                component: ResetPassword
            }
        ]
    },


    {
        path: "/",
        name: "Departments",
        meta: {
            requiresAuth: true
        },
        component: Departments
    },

    {
        path: "/reports/:name/:id",
        name: "Reports",
        meta: {
            requiresAuth: true
        },
        props: true,
        component: Reports,
    },

    {
        path: "/manage-departments",
        name: "Manage Departments",
        meta: {
            requiresAuth: true
        },
        // props: true,
        component: ManageDepartments,
    },

    {
        path: "/manage-users",
        name: "Manage Users",
        meta: {
            requiresAuth: true
        },
        // props: true,
        component: ManageUsers,
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !store.state.user.token) {
        next({ name: "Login" });
    } else if (store.state.user.token && to.meta.isGuest) {
        next({ name: "Departments" });
    } else {
        next();
    }
})

export default router;