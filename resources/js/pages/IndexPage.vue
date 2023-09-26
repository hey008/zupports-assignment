<template>
    <div class="container-fluid mb-3">
        <h3 v-show="textSearch.length" class="mt-3">Search result of "{{ textSearch }}"</h3>
        <div v-show="loading">LOADING....</div>
        <div v-if="results.length" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-6 g-4 my-2">
            <div 
                v-for="result in results" 
                :key="result.name"
                class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ result.name }}</h5>
                        <p class="card-text">{{ result.formatted_address }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, watch } from 'vue';
import { useStore, mapGetters } from 'vuex';

export default {
    computed: {
        ...mapGetters(["textSearch"])
    },
    mounted() {
        this.construct();
    },
    watch: { 
        textSearch(newval, oldval) {
            if (newval == "") {
                this.render([]);
                return ;
            }

            this.fetchData(newval);
        }
    },
    setup () {
        let loading = ref(false);
        let results = ref([]);
        const store = useStore();

        function construct () {
            fetchData(store.state.textSearch);
        }

        async function fetchData(txt) {
            openLoading()

            var formdata = new FormData();
            formdata.append("keyword", txt);

            var requestOptions = {
                method: 'POST',
                body: formdata,
            };

            await fetch(import.meta.env.VITE_API_URL + "/api/restaurant/search", requestOptions)
                .then(response => response.text())
                .then(result => render(result))
                .catch(error => console.log('error', error));
            
            closeLoading();
        }

        function openLoading() { results.value = []; loading.value = true; }
        function closeLoading() { loading.value = false; }
        function render(response) {
            if (response.length === 0) {
                results.value = [];
                return;
            }

            let jsonResponse = JSON.parse(response);
            console.log(jsonResponse.data.results);
            results.value = jsonResponse.data.results;
        }

        return {
            construct, fetchData,
            loading, openLoading, closeLoading,
            render, results
        }
    }
}
</script>
