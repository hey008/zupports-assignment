<template>
    <div class="input-group sourceTextGroup">
        <input 
            type="text"
            placeholder="Type here..."
            v-model="sourceText"
            @focus="sourceFocus = true"
            @blur="closeSearch()"
            @keyup.enter="submitSearch" 
            class="form-control position-relative"
        />
        <div v-if="sourceText.length" @click="sourceText = ''" class="input-group-text small text-primary cursor-pointer">clear</div>
        <div v-if="searchRecent.length" class="text-suggest">
            <div>Recent Searches</div>
            <div 
                v-for="recent in searchRecent" 
                :key="recent"
                @click="selectRecent(recent)"
                class="item">
                {{ recent }}
            </div>
        </div>
        <button class="btn btn-primary" @click="submitSearch()">Search</button>
    </div>
</template>

<script>
import { ref, computed, reactive, watch } from 'vue'
import store from '../store';

export default {
    setup() {
        let sourceText = ref('');
        let sourceFocus = ref(false);
        const sourceRecents = reactive( 
            JSON.parse(localStorage.getItem('RecentSearch')) || [] 
        );

        watch(sourceRecents, (newvalue) => {
            localStorage.setItem('RecentSearch', JSON.stringify(newvalue));
        })

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

        const selectRecent = (recent) => {
            sourceText.value = recent;
            sourceFocus.value = false;
            submitSearch();
        }

        const closeSearch = () => {
            setTimeout(() => { sourceFocus.value = false; }, 500);
        }

        const submitSearch = () => {
            sourceFocus.value = false;
            store.commit('updateTextSearch', sourceText.value);
            addRecentSearch(sourceText.value);
        }

        function addRecentSearch(txt) {
            if(!sourceRecents.includes(txt)){
                sourceRecents.push(txt);
            }
        }

        return { 
            sourceText,
            searchRecent,
            sourceRecents,
            sourceFocus,
            addRecentSearch,
            selectRecent,
            submitSearch,
            closeSearch
        }
    }
}
</script>