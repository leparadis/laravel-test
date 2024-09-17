<template>
    <div class="affiliate-list">
        <h1>Filtered Affiliate List</h1>
        <div v-if="loading" class="loading">Loading...</div>
        <div v-else-if="error" class="error">{{ error }}</div>
        <div v-else>
            <p v-if="affiliates.length === 0" class="no-data">No affiliates found.</p>
            <table v-else>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Affiliate ID</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="affiliate in affiliates" :key="affiliate.affiliateId">
                    <td>{{ affiliate.name }}</td>
                    <td>{{ affiliate.affiliateId }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            affiliates: [],
            loading: true,
            error: null,
            debugMessage: 'Component mounted'
        };
    },
    mounted() {
        this.fetchAffiliates();
    },
    methods: {
        async fetchAffiliates() {
            this.debugMessage = 'Fetching filtered affiliates...';
            try {
                const response = await axios.get('/api/affiliates/filtered');
                this.affiliates = response.data;
                this.loading = false;
                this.debugMessage = `Fetched ${this.affiliates.length} filtered affiliates`;
            } catch (e) {
                this.error = 'An error occurred while fetching data';
                this.loading = false;
                this.debugMessage = `Error: ${e.message}`;
            }
        }
    }
};
</script>

<style scoped>
/* The same styles as AffiliateList.vue */
.affiliate-list {
    font-family: Arial, sans-serif;
    background-color: #1e1e1e;
    color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #bb86fc;
    margin-bottom: 20px;
}

.debug {
    color: #03dac6;
    font-style: italic;
    margin-bottom: 15px;
}

.loading, .error, .no-data {
    background-color: #2e2e2e;
    padding: 15px;
    border-radius: 4px;
    text-align: center;
}

.error {
    color: #cf6679;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-top: 20px;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #333333;
}

th {
    background-color: #2e2e2e;
    color: #bb86fc;
    font-weight: bold;
    text-transform: uppercase;
}

tr:nth-child(even) {
    background-color: #252525;
}

tr:hover {
    background-color: #333333;
}
</style>
