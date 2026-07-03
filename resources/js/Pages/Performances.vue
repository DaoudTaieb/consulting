<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '../Layouts/MainLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    topCa: Array,
    topQte: Array,
    dormants: Array,
});

const fmt = (v) => {
    const num = parseFloat(v || 0);
    return num.toLocaleString('fr-FR', { minimumFractionDigits: 3, maximumFractionDigits: 3 });
};

const fmtQte = (v) => {
    const num = parseFloat(v || 0);
    return num.toLocaleString('fr-FR', { maximumFractionDigits: 0 });
};

const activeTab = ref('ca'); // 'ca', 'qte', 'dormants'

</script>

<template>
    <MainLayout>
        <Head title="Performances Produits" />
        
        <div class="p-4 sm:p-6 md:p-8 lg:p-12 max-w-[1400px] mx-auto">
            <!-- Header -->
            <header class="mb-8 md:mb-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-gold/10 text-brand-gold text-xs font-bold mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>
                    Module Consulting
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-[#1b1b18] tracking-tight mb-2">Performances Produits</h1>
                <p class="text-[#706f6c] text-sm md:text-base">Analyse des best-sellers et du stock dormant</p>
            </header>

            <!-- Tabs -->
            <div class="flex flex-wrap gap-2 mb-6">
                <button 
                    @click="activeTab = 'ca'" 
                    :class="[
                        'px-5 py-2.5 rounded-xl font-bold text-sm transition-all',
                        activeTab === 'ca' ? 'bg-[#1b1b18] text-white shadow-md' : 'bg-white border border-[#e3e3e0] text-[#706f6c] hover:bg-[#f8f8f7]'
                    ]"
                >
                    Top 50 par Chiffre d'Affaires
                </button>
                <button 
                    @click="activeTab = 'qte'" 
                    :class="[
                        'px-5 py-2.5 rounded-xl font-bold text-sm transition-all',
                        activeTab === 'qte' ? 'bg-[#1b1b18] text-white shadow-md' : 'bg-white border border-[#e3e3e0] text-[#706f6c] hover:bg-[#f8f8f7]'
                    ]"
                >
                    Top 50 par Quantité
                </button>
                <button 
                    @click="activeTab = 'dormants'" 
                    :class="[
                        'px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2',
                        activeTab === 'dormants' ? 'bg-red-500 text-white shadow-md' : 'bg-white border border-[#e3e3e0] text-[#706f6c] hover:bg-red-50 hover:text-red-600 hover:border-red-200'
                    ]"
                >
                    <svg v-if="activeTab !== 'dormants'" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                    Stock Dormant (0 Ventes)
                </button>
            </div>

            <!-- Content Area -->
            <div class="bg-white rounded-2xl border border-[#e3e3e0] shadow-sm overflow-hidden">
                
                <!-- TOP CA TABLE -->
                <table v-if="activeTab === 'ca'" class="w-full text-sm">
                    <thead class="bg-[#f8f8f7] border-b border-[#e3e3e0]">
                        <tr>
                            <th class="w-16 text-center px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">#</th>
                            <th class="text-left px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Code</th>
                            <th class="text-left px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Produit</th>
                            <th class="text-left px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Famille</th>
                            <th class="text-right px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Qté Vendue</th>
                            <th class="text-right px-4 py-4 text-[10px] font-bold text-brand-gold uppercase tracking-widest">CA Généré</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f0f0f0]">
                        <tr v-for="(p, i) in topCa" :key="p.produitcode" class="hover:bg-[#fafafa] transition-colors group">
                            <td class="px-4 py-4 text-center font-black text-[#d0d0cc] group-hover:text-[#1b1b18]">{{ i + 1 }}</td>
                            <td class="px-4 py-4 font-mono text-xs text-[#706f6c]">{{ p.produitcode }}</td>
                            <td class="px-4 py-4 font-bold text-[#1b1b18] max-w-[300px] truncate" :title="p.produitlibelle">{{ p.produitlibelle }}</td>
                            <td class="px-4 py-4 text-xs font-medium text-[#706f6c]">
                                <span class="bg-[#f8f8f7] border border-[#e3e3e0] px-2 py-1 rounded-md">{{ p.famillelibelle || 'Non classé' }}</span>
                            </td>
                            <td class="px-4 py-4 text-right font-medium text-[#706f6c]">{{ fmtQte(p.total_qte) }}</td>
                            <td class="px-4 py-4 text-right font-black text-[#1b1b18]">{{ fmt(p.total_ca) }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- TOP QTE TABLE -->
                <table v-if="activeTab === 'qte'" class="w-full text-sm">
                    <thead class="bg-[#f8f8f7] border-b border-[#e3e3e0]">
                        <tr>
                            <th class="w-16 text-center px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">#</th>
                            <th class="text-left px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Code</th>
                            <th class="text-left px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Produit</th>
                            <th class="text-left px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Famille</th>
                            <th class="text-right px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">CA Généré</th>
                            <th class="text-right px-4 py-4 text-[10px] font-bold text-brand-gold uppercase tracking-widest">Qté Vendue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f0f0f0]">
                        <tr v-for="(p, i) in topQte" :key="p.produitcode" class="hover:bg-[#fafafa] transition-colors group">
                            <td class="px-4 py-4 text-center font-black text-[#d0d0cc] group-hover:text-[#1b1b18]">{{ i + 1 }}</td>
                            <td class="px-4 py-4 font-mono text-xs text-[#706f6c]">{{ p.produitcode }}</td>
                            <td class="px-4 py-4 font-bold text-[#1b1b18] max-w-[300px] truncate" :title="p.produitlibelle">{{ p.produitlibelle }}</td>
                            <td class="px-4 py-4 text-xs font-medium text-[#706f6c]">
                                <span class="bg-[#f8f8f7] border border-[#e3e3e0] px-2 py-1 rounded-md">{{ p.famillelibelle || 'Non classé' }}</span>
                            </td>
                            <td class="px-4 py-4 text-right font-medium text-[#706f6c]">{{ fmt(p.total_ca) }}</td>
                            <td class="px-4 py-4 text-right font-black text-[#1b1b18]">{{ fmtQte(p.total_qte) }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- STOCK DORMANT TABLE -->
                <div v-if="activeTab === 'dormants'">
                    <div class="bg-red-50 p-4 border-b border-red-100 flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-red-500 mt-0.5" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                        <div>
                            <h3 class="font-bold text-red-800 text-sm">Attention : Immobilisation de capital</h3>
                            <p class="text-xs text-red-600 mt-1">Ces produits sont en stock mais n'ont généré <strong>aucune vente</strong>. Ils représentent du capital immobilisé qu'il faudrait peut-être liquider ou promouvoir.</p>
                        </div>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="bg-[#f8f8f7] border-b border-[#e3e3e0]">
                            <tr>
                                <th class="w-16 text-center px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">#</th>
                                <th class="text-left px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Code</th>
                                <th class="text-left px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Produit</th>
                                <th class="text-left px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Famille</th>
                                <th class="text-right px-4 py-4 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Stock Actuel</th>
                                <th class="text-right px-4 py-4 text-[10px] font-bold text-red-500 uppercase tracking-widest">Valeur Immobilisée</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f0f0f0]">
                            <tr v-for="(p, i) in dormants" :key="p.produitcode" class="hover:bg-red-50/30 transition-colors group">
                                <td class="px-4 py-4 text-center font-black text-[#d0d0cc] group-hover:text-red-500">{{ i + 1 }}</td>
                                <td class="px-4 py-4 font-mono text-xs text-[#706f6c]">{{ p.produitcode }}</td>
                                <td class="px-4 py-4 font-bold text-[#1b1b18] max-w-[300px] truncate" :title="p.produitlibelle">{{ p.produitlibelle }}</td>
                                <td class="px-4 py-4 text-xs font-medium text-[#706f6c]">
                                    <span class="bg-[#f8f8f7] border border-[#e3e3e0] px-2 py-1 rounded-md">{{ p.famillelibelle || 'Non classé' }}</span>
                                </td>
                                <td class="px-4 py-4 text-right font-black text-[#1b1b18]">{{ fmtQte(p.qtestock) }}</td>
                                <td class="px-4 py-4 text-right font-black text-red-500">{{ fmt(p.valeurstockttc) }}</td>
                            </tr>
                            <tr v-if="dormants.length === 0">
                                <td colspan="6" class="px-4 py-12 text-center text-[#706f6c]">Aucun produit dormant trouvé.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </MainLayout>
</template>
