<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '../Layouts/MainLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    kpis: Object,
    topClients: Array,
    topFournisseurs: Array,
    caMensuel: Array,
    achatsMensuel: Array,
    depensesParFamille: Array,
});

const fmt = (v) => {
    const num = parseFloat(v || 0);
    return num.toLocaleString('fr-FR', { minimumFractionDigits: 3, maximumFractionDigits: 3 });
};

const pctChange = (current, previous) => {
    if (!previous || previous === 0) return null;
    return ((current - previous) / previous * 100).toFixed(1);
};

const pctVentes = computed(() => pctChange(props.kpis.ca_ventes_mois, props.kpis.ca_ventes_last_mois));
const pctAchats = computed(() => pctChange(props.kpis.ca_achats_mois, props.kpis.ca_achats_last_mois));
const pctDepenses = computed(() => pctChange(props.kpis.depenses_mois, props.kpis.depenses_last_mois));

// Chart: find max value for bar scaling
const chartMax = computed(() => {
    const allValues = [
        ...props.caMensuel.map(m => parseFloat(m.total)),
        ...props.achatsMensuel.map(m => parseFloat(m.total))
    ];
    return Math.max(...allValues, 1);
});

// Build unified months list
const chartMonths = computed(() => {
    const monthsSet = new Set();
    props.caMensuel.forEach(m => monthsSet.add(m.mois));
    props.achatsMensuel.forEach(m => monthsSet.add(m.mois));
    const sorted = Array.from(monthsSet).sort();
    
    const ventesMap = {};
    props.caMensuel.forEach(m => ventesMap[m.mois] = parseFloat(m.total));
    const achatsMap = {};
    props.achatsMensuel.forEach(m => achatsMap[m.mois] = parseFloat(m.total));
    
    return sorted.map(mois => {
        const [y, m] = mois.split('-');
        const monthNames = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
        return {
            mois,
            label: monthNames[parseInt(m) - 1] + ' ' + y.slice(2),
            ventes: ventesMap[mois] || 0,
            achats: achatsMap[mois] || 0,
        };
    });
});

// Dépenses chart max
const depMax = computed(() => {
    if (!props.depensesParFamille.length) return 1;
    return Math.max(...props.depensesParFamille.map(d => parseFloat(d.total)));
});

const depColors = ['#D4A843', '#e67e22', '#e74c3c', '#9b59b6', '#3498db', '#1abc9c', '#2ecc71', '#f39c12', '#95a5a6', '#34495e', '#e91e63', '#00bcd4'];
</script>

<template>
    <MainLayout>
        <Head title="Tableau de Bord" />
        
        <div class="p-4 sm:p-6 md:p-8 lg:p-12 max-w-[1400px] mx-auto">
            <!-- Header -->
            <header class="mb-8 md:mb-10">
                <h1 class="text-3xl md:text-4xl font-black text-[#1b1b18] tracking-tight mb-2">Tableau de Bord</h1>
                <p class="text-[#706f6c] text-sm md:text-base">Vue d'ensemble de votre activité</p>
            </header>

            <!-- KPI Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                <!-- CA Ventes -->
                <div class="bg-white rounded-2xl border border-[#e3e3e0] p-5 md:p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Ventes (mois)</span>
                        <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                    </div>
                    <div class="text-xl md:text-2xl font-black text-[#1b1b18]">{{ fmt(kpis.ca_ventes_mois) }}</div>
                    <div v-if="pctVentes !== null" class="mt-1 text-xs font-bold" :class="parseFloat(pctVentes) >= 0 ? 'text-green-500' : 'text-red-500'">
                        {{ parseFloat(pctVentes) >= 0 ? '↑' : '↓' }} {{ Math.abs(pctVentes) }}% vs mois dernier
                    </div>
                </div>

                <!-- CA Achats -->
                <div class="bg-white rounded-2xl border border-[#e3e3e0] p-5 md:p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Achats (mois)</span>
                        <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                        </div>
                    </div>
                    <div class="text-xl md:text-2xl font-black text-[#1b1b18]">{{ fmt(kpis.ca_achats_mois) }}</div>
                    <div v-if="pctAchats !== null" class="mt-1 text-xs font-bold" :class="parseFloat(pctAchats) >= 0 ? 'text-red-500' : 'text-green-500'">
                        {{ parseFloat(pctAchats) >= 0 ? '↑' : '↓' }} {{ Math.abs(pctAchats) }}% vs mois dernier
                    </div>
                </div>

                <!-- Dépenses -->
                <div class="bg-white rounded-2xl border border-[#e3e3e0] p-5 md:p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Dépenses (mois)</span>
                        <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                    </div>
                    <div class="text-xl md:text-2xl font-black text-[#1b1b18]">{{ fmt(kpis.depenses_mois) }}</div>
                    <div v-if="pctDepenses !== null" class="mt-1 text-xs font-bold" :class="parseFloat(pctDepenses) >= 0 ? 'text-red-500' : 'text-green-500'">
                        {{ parseFloat(pctDepenses) >= 0 ? '↑' : '↓' }} {{ Math.abs(pctDepenses) }}% vs mois dernier
                    </div>
                </div>

                <!-- Marge -->
                <div class="bg-gradient-to-br from-[#1b1b18] to-[#2d2d2a] rounded-2xl p-5 md:p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-bold text-white/60 uppercase tracking-widest">Marge nette (mois)</span>
                    </div>
                    <div class="text-xl md:text-2xl font-black text-brand-gold">{{ fmt(kpis.ca_ventes_mois - kpis.ca_achats_mois - kpis.depenses_mois) }}</div>
                    <div class="mt-1 text-xs font-bold text-white/50">Ventes − Achats − Dépenses</div>
                </div>
            </div>

            <!-- Second Row KPIs -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 md:gap-6 mb-8">
                <div class="bg-white rounded-2xl border border-[#e3e3e0] p-4 md:p-5 shadow-sm text-center">
                    <div class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest mb-2">Créances clients</div>
                    <div class="text-lg md:text-xl font-black text-orange-500">{{ fmt(kpis.total_creances_clients) }}</div>
                </div>
                <div class="bg-white rounded-2xl border border-[#e3e3e0] p-4 md:p-5 shadow-sm text-center">
                    <div class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest mb-2">Dettes fournisseurs</div>
                    <div class="text-lg md:text-xl font-black text-blue-500">{{ fmt(kpis.total_dettes_fournisseurs) }}</div>
                </div>
                <div class="bg-white rounded-2xl border border-[#e3e3e0] p-4 md:p-5 shadow-sm text-center">
                    <div class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest mb-2">Clients</div>
                    <div class="text-lg md:text-xl font-black text-[#1b1b18]">{{ kpis.nb_clients }}</div>
                </div>
                <div class="bg-white rounded-2xl border border-[#e3e3e0] p-4 md:p-5 shadow-sm text-center">
                    <div class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest mb-2">Fournisseurs</div>
                    <div class="text-lg md:text-xl font-black text-[#1b1b18]">{{ kpis.nb_fournisseurs }}</div>
                </div>
                <div class="bg-white rounded-2xl border border-[#e3e3e0] p-4 md:p-5 shadow-sm text-center col-span-2 lg:col-span-1">
                    <div class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest mb-2">Produits</div>
                    <div class="text-lg md:text-xl font-black text-[#1b1b18]">{{ kpis.nb_produits }}</div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- CA Evolution Chart (2 cols) -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-[#e3e3e0] p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-[#1b1b18] mb-6">Évolution CA (12 mois)</h2>
                    <div class="flex items-center gap-6 mb-4 text-xs font-bold">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-brand-gold"></span> Ventes</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#3b82f6]"></span> Achats</span>
                    </div>
                    <div class="overflow-x-auto">
                        <div class="flex items-end gap-1.5 min-w-[600px]" style="height: 220px;">
                            <div v-for="m in chartMonths" :key="m.mois" class="flex-1 flex items-end gap-0.5">
                                <div class="flex-1 rounded-t-md bg-brand-gold/80 hover:bg-brand-gold transition-colors relative group" 
                                     :style="{ height: (m.ventes / chartMax * 200) + 'px', minHeight: m.ventes > 0 ? '4px' : '0' }">
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 bg-[#1b1b18] text-white text-[10px] font-bold px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                        {{ fmt(m.ventes) }}
                                    </div>
                                </div>
                                <div class="flex-1 rounded-t-md bg-blue-400/80 hover:bg-blue-500 transition-colors relative group" 
                                     :style="{ height: (m.achats / chartMax * 200) + 'px', minHeight: m.achats > 0 ? '4px' : '0' }">
                                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 bg-[#1b1b18] text-white text-[10px] font-bold px-2 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                                        {{ fmt(m.achats) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-1.5 min-w-[600px] mt-2 border-t border-[#f0f0f0] pt-2">
                            <div v-for="m in chartMonths" :key="'l-'+m.mois" class="flex-1 text-center text-[9px] font-bold text-[#706f6c]">
                                {{ m.label }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dépenses par famille -->
                <div class="bg-white rounded-2xl border border-[#e3e3e0] p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-[#1b1b18] mb-6">Dépenses par famille</h2>
                    <div class="space-y-3">
                        <div v-for="(dep, i) in depensesParFamille" :key="dep.famille" class="group">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold text-[#1b1b18] truncate max-w-[60%]">{{ dep.famille }}</span>
                                <span class="text-xs font-bold text-[#706f6c]">{{ fmt(dep.total) }}</span>
                            </div>
                            <div class="h-2 bg-[#f8f8f7] rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 group-hover:opacity-80" 
                                     :style="{ width: (parseFloat(dep.total) / depMax * 100) + '%', backgroundColor: depColors[i % depColors.length] }">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Clients & Fournisseurs -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Clients -->
                <div class="bg-white rounded-2xl border border-[#e3e3e0] shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-[#f0f0f0]">
                        <h2 class="text-lg font-bold text-[#1b1b18]">Top 5 Clients (créances)</h2>
                    </div>
                    <div v-if="topClients.length === 0" class="p-8 text-center text-sm text-[#706f6c]">Aucune créance client</div>
                    <div v-else class="divide-y divide-[#f0f0f0]">
                        <div v-for="(c, i) in topClients" :key="c.clientid" class="flex items-center gap-4 p-4 md:p-5 hover:bg-[#fafafa] transition-colors">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-sm"
                                 :class="i === 0 ? 'bg-brand-gold/20 text-brand-gold' : 'bg-[#f8f8f7] text-[#706f6c]'">
                                {{ i + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-sm text-[#1b1b18] truncate">{{ c.nom }}</div>
                            </div>
                            <div class="font-black text-orange-500 text-sm">{{ fmt(c.solde) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Top Fournisseurs -->
                <div class="bg-white rounded-2xl border border-[#e3e3e0] shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-[#f0f0f0]">
                        <h2 class="text-lg font-bold text-[#1b1b18]">Top 5 Fournisseurs (dettes)</h2>
                    </div>
                    <div v-if="topFournisseurs.length === 0" class="p-8 text-center text-sm text-[#706f6c]">Aucune dette fournisseur</div>
                    <div v-else class="divide-y divide-[#f0f0f0]">
                        <div v-for="(f, i) in topFournisseurs" :key="f.fournisseurid" class="flex items-center gap-4 p-4 md:p-5 hover:bg-[#fafafa] transition-colors">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-black text-sm"
                                 :class="i === 0 ? 'bg-red-100 text-red-500' : 'bg-[#f8f8f7] text-[#706f6c]'">
                                {{ i + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-sm text-[#1b1b18] truncate">{{ f.nom }}</div>
                            </div>
                            <div class="font-black text-blue-500 text-sm">{{ fmt(f.solde) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
