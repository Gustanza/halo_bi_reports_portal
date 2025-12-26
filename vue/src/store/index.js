import { createStore } from "vuex";

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
    mutations: {},
    modules: {}
});

export default store;
