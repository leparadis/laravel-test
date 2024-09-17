import './bootstrap';
import { createApp } from 'vue'
import AffiliateList from './components/AffiliateList.vue'
import FilteredAffiliateList from "./components/FilteredAffiliateList.vue";

const app = createApp({})
app.component('affiliate-list', AffiliateList)
app.component('filtered-affiliate-list', FilteredAffiliateList)

app.mount('#app')
