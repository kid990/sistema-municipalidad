document.addEventListener('alpine:init', () => {
    window.Alpine.data('clientTableFilter', () => ({
        search: '',

        matches(value) {
            if (!this.search.trim()) {
                return true;
            }

            return value.toLowerCase().includes(this.search.trim().toLowerCase());
        },

        resetSearch() {
            this.search = '';
        },
    }));
});
