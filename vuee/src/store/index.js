import { createStore } from "vuex";
import axiosClient from "../axiosClient";

const store = createStore({
    state: {
        user: {
            name: sessionStorage.getItem('name'),
            role: sessionStorage.getItem('role'),
            token: sessionStorage.getItem('token')
        }
    },
    getters: {},
    actions: {

        register({ commit }, user) {
            return axiosClient.post('/users', user)
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    return null;
                });
        },

        login({ commit }, user) {
            return axiosClient.post('/login', user)
                .then((response) => {
                    commit("setUser", response.data);
                    return response.data;
                }).catch((error) => {
                    return null;
                });
        },

        getUsers({ commit }) {
            return axiosClient.get('/users')
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    return error;
                });
        },

        updateUser({ commit }, user) {
            return axiosClient.put(`/dropUsers/${user.id}`)
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    return error;
                });
        },

        dropUser({ commit }, user) {
            return axiosClient.delete(`/users/${user.id}`)
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    return error;
                });
        },

        // Gein Departments
        getDepartments({ commit }) {
            return axiosClient.get('/departments')
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    return error;
                });
        },
        createDepartment({ commit }, department) {
            return axiosClient.post('/departments', department)
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    return error;
                });
        },

        updateDepartment({ commit }, department) {
            return axiosClient.put(`/departments/${department.id}`, department)
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    return error;
                });
        },

        deleteDepartment({ commit }, department) {
            return axiosClient.delete(`/departments/${department.id}`)
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    return error;
                });
        },
    },
    mutations: {
        setUser: (state, data) => {
            state.user.name = data.user.name;
            state.user.token = data.token;
            sessionStorage.setItem('name', data.user.name)
            sessionStorage.setItem('role', data.role)
            sessionStorage.setItem("token", data.token);
        }
    },
    modules: {}
});

export default store;
