import { createStore } from "vuex";
import axiosClient from "../axiosClient";

const store = createStore({
    state: {
        user: {
            data: {},
            role: sessionStorage.getItem('role'),
            token: sessionStorage.getItem('token')
        }
    },
    getters: {},
    actions: {
        register({ commit }, user) {
            return axiosClient.post('/register', user)
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
        }
    },
    mutations: {
        setUser: (state, data) => {
            state.user.data = data.user;
            state.user.token = data.token;
            sessionStorage.setItem('role', data.role)
            sessionStorage.setItem("token", data.token);
        }
    },
    modules: {}
});

export default store;
