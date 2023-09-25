<script setup>
    import TextSeatch from './components/TextSearch.vue';
</script>
<template>
    <nav class="navbar navbar-dark bg-dark sticky-top">
        <div class="container-fluid d-flex flex-nowrap">
            <a class="navbar-brand d-block d-md-none">Z</a>
            <a class="navbar-brand d-none d-md-block">Zupports Assignment</a>
            <TextSeatch />
        </div>
    </nav>

    <div class="main">
        <router-view />
    </div>

    <div class="bg-dark sticky-bottom p-2">
        <div class="small text-white ms-auto">Code by Jirawit Inkhao</div>
    </div>

</template>