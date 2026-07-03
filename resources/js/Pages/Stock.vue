<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import MainLayout from '../Layouts/MainLayout.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    stocks: Array,
    sites: Array,
    familles: Array,
    sousfamilles: Array,
    fournisseurs: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const siteid = ref(props.filters.siteid || '');
const familleid = ref(props.filters.familleid || '');
const sousfamilleid = ref(props.filters.sousfamilleid || '');
const fournisseurid = ref(props.filters.fournisseurid || '');
const pmarque = ref(props.filters.marque || '');
const showFilters = ref(false);

const updateFilters = debounce(() => {
    router.get('/stock', {
        search: search.value,
        siteid: siteid.value,
        familleid: familleid.value,
        sousfamilleid: sousfamilleid.value,
        fournisseurid: fournisseurid.value,
        marque: pmarque.value,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300);

watch([search, siteid, familleid, sousfamilleid, fournisseurid, pmarque], () => {
    updateFilters();
});

const formatCurrency = (val) => {
    if (val == null) return '0.000';
    return Number(val).toFixed(3).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
};
</script>

<template>
    <MainLayout>
        <Head title="Gestion du Stock" />
        
        <div class="p-4 sm:p-6 md:p-8 lg:p-12 pb-8 safe-bottom">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 md:mb-12">
                <div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-brand-charcoal tracking-tight mb-1">État de Stock</h2>
                    <p class="text-sm md:text-base text-[#706f6c] font-medium">Consultez et filtrez les produits en stock</p>
                </div>
                <button 
                    @click="showFilters = !showFilters"
                    class="lg:hidden flex items-center justify-center gap-2 px-6 py-3 bg-white border border-[#e3e3e0] rounded-xl font-bold text-brand-charcoal shadow-sm hover:bg-[#f8f8f7] transition-all"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    <span>{{ showFilters ? 'Masquer Filtres' : 'Afficher Filtres' }}</span>
                </button>
            </header>

            <!-- Filters & Search -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar Filters -->
                <div :class="['lg:col-span-1 space-y-6', showFilters ? 'block' : 'hidden lg:block']">
                    <div class="bg-white p-6 rounded-[2rem] border border-[#e3e3e0] shadow-sm space-y-6">
                        <div class="space-y-4">
                            <h3 class="text-xs font-bold text-brand-charcoal uppercase tracking-widest pb-2 border-b border-[#f0f0f0]">Filtres</h3>
                            
                            <!-- Site Filter -->
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-[#706f6c] uppercase tracking-wider ml-1">Site / Dépôt</label>
                                <select v-model="siteid" class="w-full bg-[#f8f8f7] border-transparent rounded-xl py-2.5 px-4 text-sm focus:bg-white focus:border-brand-gold focus:ring-0 transition-all outline-none border focus:border-brand-gold">
                                    <option value="">Tous les sites</option>
                                    <option v-for="s in sites" :key="s.siteid" :value="s.siteid">{{ s.sitelibelle }}</option>
                                </select>
                            </div>

                            <!-- Family Filter -->
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-[#706f6c] uppercase tracking-wider ml-1">Famille</label>
                                <select v-model="familleid" class="w-full bg-[#f8f8f7] border-transparent rounded-xl py-2.5 px-4 text-sm focus:bg-white focus:border-brand-gold focus:ring-0 transition-all outline-none">
                                    <option value="">Toutes les familles</option>
                                    <option v-for="f in familles" :key="f.familleid" :value="f.familleid">{{ f.famillelibelle }}</option>
                                </select>
                            </div>

                            <!-- Sub-Family Filter -->
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-[#706f6c] uppercase tracking-wider ml-1">Sous-Famille</label>
                                <select v-model="sousfamilleid" class="w-full bg-[#f8f8f7] border-transparent rounded-xl py-2.5 px-4 text-sm focus:bg-white focus:border-brand-gold focus:ring-0 transition-all outline-none">
                                    <option value="">Toutes les sous-familles</option>
                                    <option v-for="sf in sousfamilles" :key="sf.sousfamilleid" :value="sf.sousfamilleid">{{ sf.sousfamillelibelle }}</option>
                                </select>
                            </div>

                            <!-- Supplier Filter -->
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-[#706f6c] uppercase tracking-wider ml-1">Fournisseur</label>
                                <select v-model="fournisseurid" class="w-full bg-[#f8f8f7] border-transparent rounded-xl py-2.5 px-4 text-sm focus:bg-white focus:border-brand-gold focus:ring-0 transition-all outline-none">
                                    <option value="">Tous les fournisseurs</option>
                                    <option v-for="f in fournisseurs" :key="f.fournisseurid" :value="f.fournisseurid">{{ f.nom }}</option>
                                </select>
                            </div>

                            <!-- Brand Filter -->
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-[#706f6c] uppercase tracking-wider ml-1">Marque</label>
                                <input v-model="pmarque" type="text" placeholder="Ex: Schmidt..." class="w-full bg-[#f8f8f7] border-transparent rounded-xl py-2.5 px-4 text-sm focus:bg-white focus:border-brand-gold focus:ring-0 transition-all outline-none border focus:border-brand-gold" />
                            </div>
                        </div>

                        <button @click="search = ''; siteid = ''; familleid = ''; sousfamilleid = ''; fournisseurid = ''; pmarque = ''" class="w-full py-3 rounded-xl text-[10px] font-bold uppercase tracking-widest text-[#706f6c] hover:bg-[#f8f8f7] transition-all border border-[#e3e3e0]">
                            Réinitialiser
                        </button>
                    </div>
                </div>

                <!-- Main Data Grid -->
                <div class="lg:col-span-3 space-y-6 min-w-0">
                    <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] border border-[#e3e3e0] shadow-sm overflow-hidden flex flex-col min-h-0">
                        <div class="p-6 border-b border-[#f0f0f0] flex flex-col sm:flex-row sm:items-center justify-between gap-4 flex-shrink-0">
                            <h3 class="font-bold text-brand-charcoal">Produits en Stock ({{ stocks.length }})</h3>
                            <div class="relative w-full sm:w-auto">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-[#706f6c]" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                <input v-model="search" type="text" placeholder="Rechercher un produit..." class="w-full bg-[#f8f8f7] border-transparent rounded-xl py-2 pl-10 pr-4 text-sm focus:bg-white focus:border-brand-gold focus:ring-0 transition-all outline-none border focus:border-brand-gold sm:w-64" />
                            </div>
                        </div>

                        <!-- Mobile: cartes | Desktop: tableau -->
                        <div class="block md:hidden overflow-y-auto max-h-[65vh] space-y-3 p-4">
                            <div v-for="s in stocks" :key="s.stockid" class="bg-[#fdfdfc] rounded-xl border border-[#e3e3e0] p-4 shadow-sm">
                                <div class="flex justify-between items-start gap-2 mb-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-brand-charcoal text-sm leading-tight truncate">{{ s.produit?.produitlibelle }}</p>
                                        <p class="text-[10px] text-[#706f6c] font-mono">{{ s.produit?.produitcode }}</p>
                                    </div>
                                    <span :class="[
                                        parseFloat(s.qte_affichee ?? s.qtestock) <= 0 ? 'text-red-600 bg-red-50' : 'text-brand-gold bg-brand-gold/10',
                                        'shrink-0 px-2 py-1 rounded-lg font-black text-xs tabular-nums'
                                    ]">{{ s.qte_affichee ?? s.qtestock ?? 0 }}</span>
                                </div>
                                <div class="flex flex-wrap gap-2 text-[10px] text-[#706f6c]">
                                    <span>{{ s.produit?.famille?.famillelibelle }}</span>
                                    <span>·</span>
                                    <span>{{ s.site?.sitelibelle || 'Dépôt' }}</span>
                                </div>
                                <div class="mt-3 pt-3 border-t border-[#f0f0f0] flex justify-between text-xs">
                                    <span class="text-[#706f6c]">P.U TTC</span>
                                    <span class="font-mono font-bold">{{ formatCurrency(s.prixunitairettc ?? 0) }}</span>
                                </div>
                                <div class="flex justify-between text-xs mt-1">
                                    <span class="text-[#706f6c]">Valeur TTC</span>
                                    <span class="font-mono font-bold text-brand-charcoal">{{ formatCurrency(s.valeurstockttc ?? 0) }}</span>
                                </div>
                            </div>
                            <div v-if="stocks.length === 0" class="py-12 text-center text-[#706f6c] text-sm">Aucun produit avec ces critères.</div>
                        </div>
                        <div class="hidden md:block overflow-y-auto max-h-[70vh] table-scroll">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest bg-[#fdfdfc] border-b border-[#f0f0f0]">
                                        <th class="px-3 py-4 min-w-[140px]">Produit</th>
                                        <th class="px-3 py-4 min-w-[100px]">Famille</th>
                                        <th class="px-3 py-4 min-w-[80px]">Site</th>
                                        <th class="px-3 py-4 min-w-[70px] text-right">Qte</th>
                                        <th class="px-3 py-4 min-w-[90px] text-right">P.U TTC</th>
                                        <th class="px-3 py-4 min-w-[100px] text-right">Valeur TTC</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#f0f0f0]">
                                    <tr v-for="s in stocks" :key="s.stockid" class="hover:bg-[#fcfcfb] group transition-colors">
                                        <td class="px-3 py-3 align-top">
                                            <div class="min-w-0 break-words">
                                                <span class="font-bold text-brand-charcoal group-hover:text-brand-gold transition-colors leading-tight text-sm">{{ s.produit?.produitlibelle }}</span>
                                                <span class="block text-[10px] text-[#706f6c] font-mono mt-0.5">{{ s.produit?.produitcode }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <div class="min-w-0 break-words">
                                                <span class="text-xs font-bold text-brand-charcoal">{{ s.produit?.famille?.famillelibelle }}</span>
                                                <span class="block text-[10px] text-[#706f6c]">{{ s.produit?.sousfamille?.sousfamillelibelle }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 align-top">
                                            <span class="inline-block px-2 py-0.5 bg-[#f8f8f7] rounded-lg text-[10px] font-bold text-brand-charcoal border border-[#e3e3e0] uppercase tracking-wider break-words">
                                                {{ s.site?.sitelibelle || 'Dépôt Central' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-right align-top">
                                            <span :class="[
                                                parseFloat(s.qte_affichee ?? s.qtestock) <= 0 ? 'text-red-500 bg-red-50' : 'text-brand-gold bg-brand-gold/5',
                                                'inline-block px-2 py-1 rounded-lg font-black text-sm tabular-nums'
                                            ]">
                                                {{ s.qte_affichee ?? s.qtestock ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-right font-mono text-sm tabular-nums align-top">
                                            {{ formatCurrency(s.prixunitairettc ?? 0) }}
                                        </td>
                                        <td class="px-3 py-3 text-right font-mono font-bold text-brand-charcoal text-sm tabular-nums align-top">
                                            {{ formatCurrency(s.valeurstockttc ?? 0) }}
                                        </td>
                                    </tr>
                                    <tr v-if="stocks.length === 0">
                                        <td colspan="6" class="px-6 py-20 text-center">
                                            <div class="flex flex-col items-center gap-4 text-[#706f6c]">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="opacity-20"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                                                <p class="font-bold">Aucun produit trouvé avec ces critères</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
