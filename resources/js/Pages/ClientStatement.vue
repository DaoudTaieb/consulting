<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import MainLayout from '../Layouts/MainLayout.vue';

const props = defineProps({
    client: Object,
    movements: Array,
    summary: Object,
});

// Pre-calculate running balances for better performance in Vue
const movementsWithBalances = computed(() => {
    let running = parseFloat(props.summary.solde_depart || 0);
    const withBalances = props.movements.map(m => {
        const credit = parseFloat(m.credit || 0);
        const debit = parseFloat(m.debit || 0);
        running = Math.round((running + debit - credit) * 1000) / 1000; // For clients: Facture = Debit (+), Règlement = Credit (-)
        if (running === -0) running = 0; // Prevent -0.000
        return { ...m, cumulative: running };
    });
    return withBalances.reverse();
});

const formatCurrency = (val) => {
    if (val == null) return '0.000';
    return Number(val).toFixed(3).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR');
};

const expandedRows = ref(new Set());
const toggleRow = (index) => {
    const newExpanded = new Set(expandedRows.value);
    if (newExpanded.has(index)) {
        newExpanded.delete(index);
    } else {
        newExpanded.add(index);
    }
    expandedRows.value = newExpanded;
};

const printPage = () => {
    window.print();
};
</script>

<template>
    <MainLayout>
        <Head :title="'Relevé ' + client.nom" />
        
        <div class="p-4 sm:p-6 md:p-8 lg:p-12 pb-8 print:p-0 safe-bottom">
            <header class="flex flex-col xl:flex-row xl:items-end justify-between gap-8 mb-8 md:mb-12 print:mb-8">
                <div class="space-y-4">
                    <Link href="/clients" class="inline-flex items-center gap-2 text-[#706f6c] text-[10px] font-bold uppercase tracking-widest hover:text-brand-gold transition-colors print:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                        Retour aux Clients
                    </Link>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black text-[#1b1b18] tracking-tight mb-2">Relevé de Compte Client</h2>
                        <div class="flex flex-wrap items-center gap-4 md:gap-6">
                            <span class="flex items-center gap-2">
                                <span class="text-[9px] md:text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Client</span>
                                <span class="font-black text-[#1b1b18] text-sm md:text-base">{{ client.nom }}</span>
                            </span>
                            <span v-if="client.clientcode" class="flex items-center gap-2">
                                <span class="text-[9px] md:text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Code</span>
                                <span class="px-2 py-0.5 bg-[#f8f8f7] border border-[#e3e3e0] rounded-md font-mono text-xs md:text-sm uppercase leading-none">{{ client.clientcode }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 print:hidden self-start xl:self-auto">
                    <button @click="printPage" class="bg-brand-charcoal text-white px-5 md:px-6 py-2.5 md:py-3 rounded-2xl font-bold flex items-center gap-2 shadow-lg shadow-brand-charcoal/10 hover:shadow-brand-charcoal/20 transition-all active:scale-95 text-sm md:text-base">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/></svg>
                        <span class="hidden md:inline">Imprimer</span>
                    </button>
                </div>
            </header>

            <!-- Summary Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-7 gap-3 mb-8 md:mb-12 print:mb-8 print:gap-2">
                <div v-for="(val, key) in {
                    'Départ': summary.solde_depart,
                    'Factures': summary.factures,
                    'Bons Livraison': summary.bons_sortie,
                    'Règlements': summary.reglements,
                    'Bons Retour': summary.bons_entree,
                    'Avoirs': summary.avoirs,
                    'FINAL': summary.solde_final
                }" :key="key" class="bg-white p-3 md:p-4 rounded-2xl border border-[#e3e3e0] flex flex-col items-center justify-center text-center" :class="{ 'hidden sm:flex': key !== 'FINAL' }">
                    <span class="text-[8px] md:text-[9px] font-bold text-[#706f6c] uppercase tracking-widest mb-1">{{ key }}</span>
                    <span :class="['font-black text-xs md:text-sm tabular-nums', key === 'FINAL' ? (parseFloat(val) > 0 ? 'text-brand-gold' : parseFloat(val) < 0 ? 'text-[#10b981]' : 'text-[#f59e0b]') : 'text-brand-charcoal']">
                        {{ formatCurrency(val) }}
                    </span>
                </div>
            </div>

            <!-- Ledger Table -->
            <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] border border-[#e3e3e0] shadow-sm overflow-hidden print:border-none print:shadow-none">
                <div class="overflow-x-auto table-scroll custom-scrollbar">
                    <table class="w-full text-left min-w-[520px]">
                        <thead>
                            <tr class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest bg-[#fdfdfc] border-b border-[#f0f0f0]">
                                <th class="px-4 md:px-8 py-5 hidden md:table-cell">Date</th>
                                <th class="px-4 md:px-8 py-5 hidden sm:table-cell">Opération</th>
                                <th class="px-4 md:px-8 py-5">Pièce</th>
                                <th class="px-4 md:px-8 py-5 text-right hidden lg:table-cell">Débit (Dû)</th>
                                <th class="px-4 md:px-8 py-5 text-right hidden lg:table-cell">Crédit (Payé)</th>
                                <th class="px-4 md:px-8 py-5 text-right">Solde</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f0f0f0]">
                            <!-- List of movements -->
                            <template v-for="(m, index) in movementsWithBalances" :key="index">
                                <tr :class="{'bg-[#fcfcfb]/50': expandedRows.has(index), 'cursor-pointer hover:bg-[#fafafa] transition-colors': m.details || m.extra}" @click="(m.details || m.extra) && toggleRow(index)">
                                    <td class="px-4 md:px-8 py-5 md:py-6 text-[10px] md:text-sm font-medium text-[#706f6c] hidden md:table-cell">{{ formatDate(m.date) }}</td>
                                    <td class="px-4 md:px-8 py-5 md:py-6 hidden sm:table-cell">
                                        <span class="px-2 py-1 rounded-lg bg-[#f8f8f7] text-[9px] md:text-[10px] font-black text-[#1b1b18] uppercase tracking-wider border border-[#e3e3e0] truncate max-w-[85px] block text-center">{{ m.libelle }}</span>
                                    </td>
                                    <td class="px-4 md:px-8 py-5 md:py-6">
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-[#1b1b18] text-[10px] md:text-base leading-none">#{{ m.numero }}</span>
                                                <span class="md:hidden text-[9px] text-[#706f6c]">{{ formatDate(m.date) }}</span>
                                            </div>
                                            <span class="sm:hidden text-[8px] font-bold text-[#706f6c] uppercase mt-1">{{ m.libelle }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-8 py-5 md:py-6 text-right tabular-nums text-sm font-bold text-[#1b1b18] hidden lg:table-cell">{{ m.debit > 0 ? formatCurrency(m.debit) : '—' }}</td>
                                    <td class="px-4 md:px-8 py-5 md:py-6 text-right tabular-nums text-sm font-bold text-[#10b981] hidden lg:table-cell">{{ m.credit > 0 ? formatCurrency(m.credit) : '—' }}</td>
                                    <td class="px-4 md:px-8 py-5 md:py-6 text-right font-black tabular-nums tracking-tighter text-xs md:text-lg">
                                        <div class="flex flex-col items-end" :class="[
                                            parseFloat(m.cumulative) > 0 ? 'text-brand-gold' : 
                                            parseFloat(m.cumulative) < 0 ? 'text-[#10b981]' : 
                                            'text-[#f59e0b]'
                                        ]">
                                            <span>{{ formatCurrency(m.cumulative) }}</span>
                                            <span v-if="m.debit > 0" class="lg:hidden text-[9px] font-bold mt-0.5 text-[#1b1b18]">+ {{ formatCurrency(m.debit) }}</span>
                                            <span v-if="m.credit > 0" class="lg:hidden text-[9px] font-bold mt-0.5 text-[#10b981]">- {{ formatCurrency(m.credit) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Automatic Details -->
                                <tr v-if="expandedRows.has(index) && (m.details || m.extra)" class="bg-[#fcfcfb]">
                                    <td colspan="6" class="p-0">
                                        <div class="border-l-4 border-brand-gold bg-white m-3 md:m-4 md:ml-8 p-4 md:p-8 rounded-2xl shadow-sm border border-[#e3e3e0]">
                                            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6 md:mb-8">
                                                <div>
                                                    <h4 class="font-black text-lg md:text-xl text-[#1b1b18] mb-1">{{ m.libelle }} #{{ m.numero }}</h4>
                                                    <p v-if="m.header?.numerointerne" class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Réf Interne: {{ m.header?.numerointerne || '—' }}</p>
                                                    <p v-if="m.extra" class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Mode: {{ m.extra?.mode_libelle || '—' }}</p>
                                                </div>
                                                <div class="sm:text-right">
                                                    <p class="text-[9px] md:text-[10px] font-bold text-[#706f6c] uppercase tracking-widest mb-1">Total Document</p>
                                                    <p class="text-xl md:text-2xl font-black text-brand-gold">{{ formatCurrency(m.header?.totalttc || m.extra?.montant) }} TND</p>
                                                </div>
                                            </div>

                                            <div v-if="m.details" class="overflow-x-auto table-scroll custom-scrollbar">
                                                <table class="w-full text-[10px] md:text-xs min-w-[320px]">
                                                    <thead>
                                                        <tr class="border-b border-[#f0f0f0] text-[#706f6c] font-black uppercase tracking-widest">
                                                            <th class="py-3 text-left">Référence</th>
                                                            <th class="py-3 text-left">Désignation</th>
                                                            <th class="py-3 text-right">Qté</th>
                                                            <th class="py-3 text-right">P.U HT</th>
                                                            <th class="py-3 text-right">Total TTC</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="d in m.details" :key="d.produitid" class="border-b border-[#f8f8f7] last:border-0 hover:bg-[#fafafa]">
                                                            <td class="py-3 font-mono text-[#706f6c] uppercase">{{ d.produitcode }}</td>
                                                            <td class="py-3 font-bold text-[#1b1b18]">{{ d.produitlibelle }}</td>
                                                            <td class="py-3 text-right font-bold">{{ d.qte }}</td>
                                                            <td class="py-3 text-right text-[#706f6c]">{{ formatCurrency(d.ht) }}</td>
                                                            <td class="py-3 text-right font-black text-[#1b1b18]">{{ formatCurrency(d.totalttc) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div v-if="m.header" class="mt-6 md:mt-8 flex flex-wrap justify-end gap-6 md:gap-12 border-t border-[#f0f0f0] pt-6">
                                                <div class="text-right">
                                                    <p class="text-[8px] md:text-[9px] font-bold text-[#706f6c] uppercase tracking-widest leading-none mb-1">Total HT</p>
                                                    <p class="font-bold text-[#1b1b18] text-xs md:text-sm">{{ formatCurrency(m.header?.totalhtnet || m.header?.totalnetht) }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-[8px] md:text-[9px] font-bold text-[#706f6c] uppercase tracking-widest leading-none mb-1">Calcul TVA</p>
                                                    <p class="font-bold text-[#1b1b18] text-xs md:text-sm">{{ formatCurrency(m.header?.totaltva) }}</p>
                                                </div>
                                                <div v-if="parseFloat(m.header?.timbre || m.header?.droittimbre) > 0" class="text-right">
                                                    <p class="text-[8px] md:text-[9px] font-bold text-[#706f6c] uppercase tracking-widest leading-none mb-1">Timbre</p>
                                                    <p class="font-bold text-[#1b1b18] text-xs md:text-sm">{{ formatCurrency(m.header?.timbre || m.header?.droittimbre) }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-[8px] md:text-[9px] font-bold text-[#706f6c] uppercase tracking-widest leading-none mb-1">Total TTC</p>
                                                    <p class="font-black text-lg md:text-xl text-brand-gold">{{ formatCurrency(m.header?.totalttc) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style>
@media print {
    body { background: white !important; }
    main { padding: 0 !important; margin-left: 0 !important; }
    .bg-white { border: none !important; box-shadow: none !important; }
    .divide-y > * { border-bottom-width: 1px !important; border-color: #eee !important; }
}
</style>
