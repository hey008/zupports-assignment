import { createStore } from 'vuex'

export default createStore({
    state () {
        return {
            textSearch: "Bang sue"
        }
    },
    mutations: {
        updateTextSearch (state, txt) {
            state.textSearch = txt;
        }
    }
})