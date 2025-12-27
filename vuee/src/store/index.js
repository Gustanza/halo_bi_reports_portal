import { createStore } from "vuex";
import axiosClient from "../axiosClient";

const store = createStore({
    state: {
        user: {
            data: {
                fullName: "Gustanza Sam"
            },
            clearanceLevel: 9,
            token: 1
        }
    },
    getters: {},
    actions: {
        register({ commit }, user) {
            try {
                user.passwordConfrimation = user.password;
                const respo = axiosClient.post('/register', user);
                console.log("Came is Good");
                return respo;
            } catch (error) {
                return console.log("Error is:", user);
            }
        }
    },
    mutations: {},
    modules: {}
});

export default store;
