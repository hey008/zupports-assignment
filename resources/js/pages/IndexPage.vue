<template>
    <div class="container-fluid mb-3">
        <h3 v-if="textSearch.length" class="mt-3">Search result of "{{ textSearch }}"</h3>
        <div v-if="loading" class="d-flex mt-3">
            <div class="spinner-border me-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h2>LOADING....</h2>
        </div>
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
                    <div class="card-footer d-grid gap-2">
                        <button @click="openMap(result)" class="btn btn-primary btn-block">Map</button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="errorDisplay" class="alert alert-danger" role="alert">Sorry! Something went wrong</div>
    </div>

    <div class="modal fade" :class="{ show:isModalOpen }">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalToggleLabel">{{ modalTitle }}</h1>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeMap()"></button>
                </div>
                <div class="modal-body p-0">
                    <GoogleMap :api-key="GoogleMapKey" style="width: 100%; height: 100%" :center="mapPoint" :zoom="19">
                        <Marker :options="{ position: mapPoint }" />
                    </GoogleMap>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import { ref } from 'vue';
import { useStore, mapGetters } from 'vuex';
import { GoogleMap, Marker } from "vue3-google-map";

export default {
    components: {
        GoogleMap, Marker
    },
    computed: {
        ...mapGetters(["textSearch"])
    },
    mounted() {
        // Run this script for default render
        this.construct();
    },
    watch: { 
        // If Keyword update fetch data and rerender result.
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
        let errorDisplay = ref(false);
        let results = ref([]);
        let isModalOpen = ref(false);
        let modalTitle = ref("");

        const store = useStore();

        const GoogleMapKey = import.meta.env.VITE_GOOGLE_MAP_API_KEY;
        let mapPoint = ref({ lat: 40.689247, lng: -74.044502 });

        // Start fetchdata when component is ready
        function construct () {
            fetchData(store.state.textSearch);
        }

        // Fetch and Render data
        async function fetchData(txt) {
            errorDisplay.value = false;

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
                .catch(error => { errorDisplay.value = true; } );
            
            closeLoading();
        }

        // Display Loading Message 
        function openLoading() { results.value = []; loading.value = true; }

        // Close Loading Message
        function closeLoading() { loading.value = false; }

        // Render Card from API fetch results
        function render(response) {
            if (response.length === 0) {
                results.value = [];
                return;
            }

            let jsonResponse = JSON.parse(response);
            results.value = jsonResponse.data.results;
        }

        // Open Map modal when click "Open Map" button 
        function openMap(mapData) {
            modalTitle.value = mapData.name;
            mapPoint.value = { lat: mapData.geometry.location.lat, lng: mapData.geometry.location.lng };
            isModalOpen.value = true;
        }

        // Close Map modal
        function closeMap() {
            isModalOpen.value = false;
        }

        return {
            construct, fetchData,
            loading, openLoading, closeLoading, errorDisplay,
            render, results,
            isModalOpen, openMap, closeMap, mapPoint, modalTitle, GoogleMapKey
        }
    }
}
</script>
