<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '../Layouts/MainLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    banques: Array,
    echeancesClients: Array,
    echeancesFournisseurs: Array,
    impayesClients: Array,
    impayesFournisseurs: Array,
});

const fmt = (v) => {
    const num = parseFloat(v || 0);
    return num.toLocaleString('fr-FR', { minimumFractionDigits: 3, maximumFractionDigits: 3 });
};

const fmtDate = (d) => {
    if (!d) return '—';
    const date = new Date(d);
    return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

// Calculate totals
const totalBanques = computed(() => props.banques.reduce((sum, b) => sum + parseFloat(b.soldedepart || 0), 0));
const totalEcheancesClients = computed(() => props.echeancesClients.reduce((sum, e) => sum + parseFloat(e.montant || 0), 0));
const totalEcheancesFournisseurs = computed(() => props.echeancesFournisseurs.reduce((sum, e) => sum + parseFloat(e.montant || 0), 0));
const totalImpayesClients = computed(() => props.impayesClients.reduce((sum, e) => sum + parseFloat(e.montant || 0), 0));
const totalImpayesFournisseurs = computed(() => props.impayesFournisseurs.reduce((sum, e) => sum + parseFloat(e.montant || 0), 0));

// Combine and sort future cash flow
const cashflow = computed(() => {
    const all = [
        ...props.echeancesClients.map(e => ({ ...e, type: 'in', amount: parseFloat(e.montant) })),
        ...props.echeancesFournisseurs.map(e => ({ ...e, type: 'out', amount: -parseFloat(e.montant) }))
    ];
    
    return all.sort((a, b) => new Date(a.echeance) - new Date(b.echeance));
});

</script>

<template>
    <MainLayout>
        <Head title="Trésorerie & Échéancier" />
        
        <div class="p-4 sm:p-6 md:p-8 lg:p-12 max-w-[1400px] mx-auto">
            <!-- Header -->
            <header class="mb-8 md:mb-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-gold/10 text-brand-gold text-xs font-bold mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m12 14 4-4"/><path d="M3.34 19a10 10 0 1 1 17.32 0"/></svg>
                    Module Consulting
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-[#1b1b18] tracking-tight mb-2">Trésorerie & Échéancier</h1>
                <p class="text-[#706f6c] text-sm md:text-base">Suivi de liquidité, chèques et traites futures</p>
            </header>

            <!-- Global KPIs -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                <!-- Banques -->
                <div class="bg-gradient-to-br from-[#1b1b18] to-[#2d2d2a] rounded-2xl border border-[#e3e3e0] p-5 md:p-6 shadow-sm">
                    <div class="text-[10px] font-bold text-white/60 uppercase tracking-widest mb-2">Liquidité (Soldes départ)</div>
                    <div class="text-xl md:text-2xl font-black text-white">{{ fmt(totalBanques) }}</div>
                </div>
                
                <!-- Rentrées futures -->
                <div class="bg-white rounded-2xl border border-[#e3e3e0] p-5 md:p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded bg-green-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Rentrées futures</span>
                    </div>
                    <div class="text-xl md:text-2xl font-black text-[#1b1b18]">{{ fmt(totalEcheancesClients) }}</div>
                    <div class="text-xs font-bold text-green-500 mt-1">{{ echeancesClients.length }} traites/chèques</div>
                </div>

                <!-- Sorties futures -->
                <div class="bg-white rounded-2xl border border-[#e3e3e0] p-5 md:p-6 shadow-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded bg-orange-50 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                        </div>
                        <span class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Sorties futures</span>
                    </div>
                    <div class="text-xl md:text-2xl font-black text-[#1b1b18]">{{ fmt(totalEcheancesFournisseurs) }}</div>
                    <div class="text-xs font-bold text-orange-500 mt-1">{{ echeancesFournisseurs.length }} traites/chèques</div>
                </div>
                
                <!-- Impayés globaux -->
                <div class="bg-white rounded-2xl border border-red-200 p-5 md:p-6 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 w-16 h-16 bg-red-50 rounded-full flex items-center justify-center opacity-50"></div>
                    <div class="flex items-center gap-2 mb-2 relative z-10">
                        <span class="text-[10px] font-bold text-red-500 uppercase tracking-widest">Alerte Impayés Clients</span>
                    </div>
                    <div class="text-xl md:text-2xl font-black text-red-500 relative z-10">{{ fmt(totalImpayesClients) }}</div>
                    <div class="text-xs font-bold text-red-400 mt-1 relative z-10">{{ impayesClients.length }} impayés à recouvrer</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Timeline Cashflow -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-[#e3e3e0] shadow-sm overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-[#f0f0f0]">
                        <h2 class="text-lg font-bold text-[#1b1b18]">Calendrier des échéances (Cash Flow)</h2>
                        <p class="text-sm text-[#706f6c]">Traites et chèques à venir, triés chronologiquement.</p>
                    </div>
                    <div class="p-0 overflow-y-auto max-h-[500px]">
                        <table class="w-full text-sm">
                            <thead class="bg-[#f8f8f7] sticky top-0 z-10">
                                <tr>
                                    <th class="text-left px-4 py-3 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Date Échéance</th>
                                    <th class="text-left px-4 py-3 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Sens</th>
                                    <th class="text-left px-4 py-3 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Tiers</th>
                                    <th class="text-left px-4 py-3 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Mode & Statut</th>
                                    <th class="text-right px-4 py-3 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Montant</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#f0f0f0]">
                                <tr v-for="(cf, i) in cashflow" :key="i" class="hover:bg-[#fafafa] transition-colors">
                                    <td class="px-4 py-3 font-bold text-[#1b1b18] whitespace-nowrap">{{ fmtDate(cf.echeance) }}</td>
                                    <td class="px-4 py-3">
                                        <span v-if="cf.type === 'in'" class="inline-flex items-center gap-1 text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-md">
                                            Entrée (Client)
                                        </span>
                                        <span v-else class="inline-flex items-center gap-1 text-xs font-bold text-orange-500 bg-orange-50 px-2 py-1 rounded-md">
                                            Sortie (Fourn.)
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-[#1b1b18] truncate max-w-[200px]">{{ cf.tiers }}</td>
                                    <td class="px-4 py-3">
                                        <div class="text-xs font-bold text-[#706f6c]">{{ cf.mode || '—' }}</div>
                                        <div class="text-[10px] text-brand-gold font-bold">{{ cf.statut }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-black" :class="cf.type === 'in' ? 'text-green-500' : 'text-[#1b1b18]'">
                                        {{ cf.type === 'in' ? '+' : '-' }}{{ fmt(Math.abs(cf.amount)) }}
                                    </td>
                                </tr>
                                <tr v-if="cashflow.length === 0">
                                    <td colspan="5" class="px-4 py-12 text-center text-[#706f6c]">Aucune échéance future trouvée.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Comptes bancaires -->
                    <div class="bg-white rounded-2xl border border-[#e3e3e0] shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-[#f0f0f0]">
                            <h2 class="font-bold text-[#1b1b18]">Comptes & Caisses</h2>
                        </div>
                        <div class="divide-y divide-[#f0f0f0]">
                            <div v-for="b in banques" :key="b.agencebid" class="p-4 flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-[#1b1b18] text-sm">{{ b.libelle }}</div>
                                    <div v-if="b.rib" class="text-[10px] font-mono text-[#706f6c] mt-0.5">{{ b.rib }}</div>
                                </div>
                                <div class="font-black text-[#1b1b18]">{{ fmt(b.soldedepart) }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alertes Impayés -->
                    <div class="bg-white rounded-2xl border border-red-200 shadow-sm overflow-hidden">
                        <div class="p-5 border-b border-red-100 bg-red-50/50">
                            <h2 class="font-bold text-red-600 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                                Derniers Impayés
                            </h2>
                        </div>
                        <div class="divide-y divide-red-100 max-h-[300px] overflow-y-auto">
                            <div v-for="(imp, i) in impayesClients.slice(0, 10)" :key="'cli-'+i" class="p-4 hover:bg-red-50/30 transition-colors">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-bold text-[#1b1b18] text-sm truncate max-w-[70%]">{{ imp.tiers }}</span>
                                    <span class="font-black text-red-500 text-sm">{{ fmt(imp.montant) }}</span>
                                </div>
                                <div class="text-xs text-[#706f6c]">
                                    Client • {{ imp.mode }} • Échu le {{ fmtDate(imp.echeance) }}
                                </div>
                            </div>
                            <div v-for="(imp, i) in impayesFournisseurs.slice(0, 5)" :key="'fou-'+i" class="p-4 hover:bg-red-50/30 transition-colors">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="font-bold text-[#1b1b18] text-sm truncate max-w-[70%]">{{ imp.tiers }}</span>
                                    <span class="font-black text-[#1b1b18] text-sm">{{ fmt(imp.montant) }}</span>
                                </div>
                                <div class="text-xs text-[#706f6c]">
                                    Fournisseur • {{ imp.mode }} • Échu le {{ fmtDate(imp.echeance) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
