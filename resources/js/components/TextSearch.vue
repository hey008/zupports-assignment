<template>
    <div class="flex-fill">
        <div class="input-group">
            <input 
                type="text"
                placeholder="Type here..."
                v-model="sourceText"
                @focus="sourceFocus = true"
                @blur="closeSearch()"
                class="form-control position-relative"
            />
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
    </div>
</template>

<script>
import { ref, computed } from 'vue'

export default {
    setup() {
        let sourceText = ref('');
        let sourceFocus = ref(false);
        let sourceRecentSelected = ref('');
        let sourceRecents = ['Bang Sue'];

        const searchRecent = computed(() => {
            console.log(sourceFocus);
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
        }

        const closeSearch = () => {
            setTimeout(() => { sourceFocus.value = false; }, 500);
        }

        const submitSearch = () => {

        }

        return { 
            sourceText,
            searchRecent,
            sourceRecents,
            sourceFocus,
            sourceRecentSelected,
            selectRecent,
            submitSearch,
            closeSearch
        }
    }
}
</script>