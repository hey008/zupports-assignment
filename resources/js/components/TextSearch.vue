<template>
    <div class="input-group sourceTextGroup">
        <input 
            ref="textInput"
            type="text"
            placeholder="Type here..."
            v-model="sourceText"
            @focus="sourceFocus = true"
            @blur="closeSearch()"
            @keyup.enter="submitSearch" 
            class="form-control position-relative"
        />
        <div v-if="sourceText.length" @click="clearSearch" class="input-group-text small text-primary cursor-pointer">clear</div>
        <ul v-if="searchRecent.length" class="text-suggest list-group">
            <li class="list-group-item active">Recent Searches</li>
            <li 
                v-for="recent in searchRecent" 
                :key="recent"
                @click="selectRecent(recent)"
                class="list-group-item item">
                {{ recent }}
            </li>
        </ul>
        <button class="btn btn-primary" @click="submitSearch()">Search</button>
    </div>
</template>

<script>
import { ref, computed, reactive, watch } from 'vue';
import store from '../store';

export default {
    mounted () {
        // Run this script for default value
        this.construct();
    },
    setup() {
        let textInput = ref(null);
        let sourceText = ref('');
        let sourceFocus = ref(false);

        // Use LocalStorage for recent search display feature.
        const sourceRecents = reactive( 
            JSON.parse(localStorage.getItem('RecentSearch')) || [] 
        );

        // Assign default value
        function construct() {
            sourceText.value = "Bang Sue";
            submitSearch();
        }

        // Update LocalStorage when variable updated
        watch(sourceRecents, (newvalue) => {
            localStorage.setItem('RecentSearch', JSON.stringify(newvalue));
        })

        // Return suggest items by filter recent search list from input
        const searchRecent = computed(() => {
            if (sourceText.value === '' || sourceFocus.value === false) {
                return [];
            }

            let matches = 0;
            return sourceRecents.filter(recent => {
                if (
                    recent.toLowerCase().includes(sourceText.value.toLowerCase())
                    && matches < 10
                ) {
                    matches++;
                    return recent;
                }
            });
        });

        // Update & Submit when clicked on suggest item
        const selectRecent = (recent) => {
            sourceText.value = recent;
            sourceFocus.value = false;
            submitSearch();
        }

        // Close suggest box when click outside textbox
        const closeSearch = () => {
            setTimeout(() => { sourceFocus.value = false; }, 500);
        }

        // Empty text in textbox
        const clearSearch = () => {
            sourceText.value = "";
            textInput.value.focus();
            sourceFocus.value = true;
        }

        // Submit text to search 
        const submitSearch = () => {
            sourceFocus.value = false;
            store.commit('updateTextSearch', sourceText.value);
            addRecentSearch(sourceText.value);
        }

        // Add new item in recen search
        function addRecentSearch(txt) {
            if(!sourceRecents.includes(txt)){
                sourceRecents.push(txt);
            }
        }

        return { 
            construct,
            textInput,
            sourceText,
            searchRecent,
            sourceRecents,
            sourceFocus,
            addRecentSearch,
            selectRecent,
            submitSearch,
            closeSearch,
            clearSearch
        }
    }
}
</script>