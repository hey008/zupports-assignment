import { createStore } from 'vuex'

export default createStore({
    state () {
        return {
            textSearch: ""
        }
    },
    mutations: {
        updateTextSearch (state, txt) {
            state.textSearch = txt;
        }
    },
    getters: {
        textSearch: state => {
            return state.textSearch;
        }
    }
})