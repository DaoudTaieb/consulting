<script setup>
import { Head, Link } from '@inertiajs/vue3';
import MainLayout from '../Layouts/MainLayout.vue';

const props = defineProps({
    purchases: Array,
});

const formatCurrency = (val) => {
    if (val == null) return '0.000';
    return Number(val).toFixed(3).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};
</script>

<template>
    <MainLayout>
        <Head title="Achats" />
        
        <div class="p-4 sm:p-6 md:p-8 lg:p-12 pb-8 safe-bottom">
            <header class="mb-8 md:mb-12">
                <h2 class="text-2xl md:text-3xl font-extrabold text-[#1b1b18] tracking-tight mb-1">Journal des Achats</h2>
                <p class="text-sm md:text-base text-[#706f6c] font-medium">Historique global des factures et bons d'entrée</p>
            </header>

            <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] border border-[#e3e3e0] shadow-sm overflow-hidden">
                <div class="overflow-x-auto table-scroll custom-scrollbar">
                    <table class="w-full text-left min-w-[480px]">
                        <thead>
                            <tr class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest bg-[#fdfdfc] border-b border-[#f0f0f0]">
                                <th class="px-4 md:px-8 py-5 hidden md:table-cell">Date</th>
                                <th class="px-4 md:px-8 py-5 hidden sm:table-cell">Type</th>
                                <th class="px-4 md:px-8 py-5">N° / Date</th>
                                <th class="px-4 md:px-8 py-5">Fournisseur</th>
                                <th class="px-4 md:px-8 py-5 text-right">Net</th>
                                <th class="px-4 md:px-8 py-5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f0f0f0]">
                            <tr v-for="p in purchases" :key="p.type_slug + p.id" class="hover:bg-[#fcfcfb] group transition-colors">
                                <td class="px-4 md:px-8 py-5 md:py-6 text-[10px] md:text-sm font-medium text-[#706f6c] hidden md:table-cell">{{ formatDate(p.date) }}</td>
                                <td class="px-4 md:px-8 py-5 md:py-6 hidden sm:table-cell">
                                    <span :class="[
                                        'px-2.5 py-1 rounded-lg text-[9px] md:text-[10px] font-black uppercase tracking-wider border',
                                        p.type_slug === 'facture' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-orange-50 text-orange-600 border-orange-100'
                                    ]">{{ p.type_libelle }}</span>
                                </td>
                                <td class="px-4 md:px-8 py-5 md:py-6">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-[#1b1b18] text-[10px] md:text-base leading-none">#{{ p.numero }}</span>
                                        <span class="md:hidden text-[9px] text-[#706f6c] font-medium mt-1">{{ formatDate(p.date) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 md:px-8 py-5 md:py-6 text-sm">
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-bold text-[#1b1b18] leading-tight text-xs md:text-base truncate max-w-[100px] md:max-w-none">{{ p.fournisseur_nom }}</span>
                                    </div>
                                </td>
                                <td class="px-4 md:px-8 py-5 md:py-6 text-right font-black text-[#1b1b18] tabular-nums tracking-tighter text-sm md:text-lg">
                                    {{ formatCurrency(p.totalttc) }}
                                </td>
                                <td class="px-4 md:px-8 py-5 md:py-6 text-right">
                                    <Link :href="`/fournisseur/${p.fournisseurid}/releve`" class="inline-flex items-center justify-center w-8 h-8 md:w-10 md:h-10 rounded-xl bg-[#f8f8f7] text-[#1b1b18] hover:bg-brand-gold hover:text-white transition-all shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
