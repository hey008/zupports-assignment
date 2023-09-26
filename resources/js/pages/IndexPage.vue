<template>
    <div class="container-fluid">
        {{ textSearch }}
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        computed: {
            ...mapGetters(["textSearch"])
        },
        mounted() {
            console.log('Index mounted.')
        }
    }
</script>
