import { createStore } from "vuex";
import axiosClient from "../axiosClient";

const store = createStore({
    state: {
        user: {
            id: sessionStorage.getItem('id'),
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

        getUser({ commit }, user) {

            return axiosClient.get(`/users/${user.id}`)
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    console.log("Shida ni", error);
                    return error;
                });
        },

        updateUser({ commit }, user) {
            return axiosClient.put(`/users/${user.id}`, user)
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

        // reports thingies
        getDepartmentReports({ commit }, deptId) {
            return axiosClient.get(`/spreports/${deptId}/reports`)
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    return error;
                });
        },

        createReport({ commit }, report) {
            return axiosClient.post(`/reports`, report)
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    console.log("Error: ", error);
                    return error;
                });
        },

        editReport({ commit }, report) {
            return axiosClient.put(`/reports/${report.id}`, report)
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    console.log("Error: ", error);
                    return error;
                });
        },

        dropReport({ commit }, report) {
            return axiosClient.delete(`/reports/${report.id}`)
                .then((response) => {
                    return response.data;
                }).catch((error) => {
                    console.log("Error: ", error);
                    return error;
                });
        },

    },
    mutations: {
        setUser: (state, data) => {
            state.user.id = data.user.id;
            state.user.name = data.user.name;
            state.user.role = data.role;
            state.user.token = data.token;
            sessionStorage.setItem('id', data.user.id)
            sessionStorage.setItem('name', data.user.name)
            sessionStorage.setItem('role', data.role)
            sessionStorage.setItem("token", data.token);
        }
    },
    modules: {}
});

export default store;
