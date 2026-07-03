<script setup>
import { Head, router } from '@inertiajs/vue3';
import MainLayout from '../Layouts/MainLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    depenses: Array,
    familles: Array,
    totauxParFamille: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const familleFilter = ref(props.filters?.famille || '');
const dateDebut = ref(props.filters?.date_debut || '');
const dateFin = ref(props.filters?.date_fin || '');

const applyFilters = () => {
    const params = {};
    if (search.value) params.search = search.value;
    if (familleFilter.value) params.famille = familleFilter.value;
    if (dateDebut.value) params.date_debut = dateDebut.value;
    if (dateFin.value) params.date_fin = dateFin.value;
    router.get('/depenses', params, { preserveState: true });
};

const clearFilters = () => {
    search.value = '';
    familleFilter.value = '';
    dateDebut.value = '';
    dateFin.value = '';
    router.get('/depenses', {}, { preserveState: true });
};

const totalGeneral = computed(() => {
    return props.depenses.reduce((sum, d) => sum + parseFloat(d.montant || 0), 0);
});

const depMax = computed(() => {
    if (!props.totauxParFamille.length) return 1;
    return Math.max(...props.totauxParFamille.map(d => parseFloat(d.total)));
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

const depColors = ['#D4A843', '#e67e22', '#e74c3c', '#9b59b6', '#3498db', '#1abc9c', '#2ecc71', '#f39c12', '#95a5a6', '#34495e', '#e91e63', '#00bcd4'];

// Expanded row
const expandedId = ref(null);
const toggleExpand = (id) => {
    expandedId.value = expandedId.value === id ? null : id;
};
</script>

<template>
    <MainLayout>
        <Head title="Dépenses" />
        
        <div class="p-4 sm:p-6 md:p-8 lg:p-12 max-w-[1400px] mx-auto">
            <!-- Header -->
            <header class="mb-6 md:mb-8">
                <h1 class="text-3xl md:text-4xl font-black text-[#1b1b18] tracking-tight mb-2">Dépenses</h1>
                <p class="text-[#706f6c] text-sm md:text-base">Suivi et analyse de toutes vos dépenses</p>
            </header>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-2xl border border-[#e3e3e0] p-5 shadow-sm">
                    <div class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest mb-2">Total affiché</div>
                    <div class="text-xl font-black text-[#1b1b18]">{{ fmt(totalGeneral) }}</div>
                    <div class="text-xs font-bold text-[#706f6c] mt-1">{{ depenses.length }} dépenses</div>
                </div>
                <div v-for="(dep, i) in totauxParFamille.slice(0, 3)" :key="dep.famille"
                     class="bg-white rounded-2xl border border-[#e3e3e0] p-5 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-widest mb-2" :style="{ color: depColors[i] }">{{ dep.famille }}</div>
                    <div class="text-xl font-black text-[#1b1b18]">{{ fmt(dep.total) }}</div>
                    <div class="text-xs font-bold text-[#706f6c] mt-1">{{ dep.nb }} dépenses</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Sidebar: Chart + Filters -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Filters -->
                    <div class="bg-white rounded-2xl border border-[#e3e3e0] p-5 shadow-sm">
                        <h3 class="font-bold text-[#1b1b18] mb-4">Filtres</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest block mb-1">Recherche</label>
                                <input v-model="search" @keyup.enter="applyFilters" type="text" placeholder="Libellé, bénéficiaire..."
                                    class="w-full px-3 py-2 bg-[#f8f8f7] border border-[#e3e3e0] rounded-xl text-sm font-medium focus:ring-2 focus:ring-brand-gold/30 focus:border-brand-gold outline-none" />
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest block mb-1">Famille</label>
                                <select v-model="familleFilter"
                                    class="w-full px-3 py-2 bg-[#f8f8f7] border border-[#e3e3e0] rounded-xl text-sm font-medium focus:ring-2 focus:ring-brand-gold/30 focus:border-brand-gold outline-none">
                                    <option value="">Toutes</option>
                                    <option v-for="f in familles" :key="f.familledepenseid" :value="f.familledepenseid">{{ f.libelle }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest block mb-1">Date début</label>
                                <input v-model="dateDebut" type="date"
                                    class="w-full px-3 py-2 bg-[#f8f8f7] border border-[#e3e3e0] rounded-xl text-sm font-medium focus:ring-2 focus:ring-brand-gold/30 focus:border-brand-gold outline-none" />
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-[#706f6c] uppercase tracking-widest block mb-1">Date fin</label>
                                <input v-model="dateFin" type="date"
                                    class="w-full px-3 py-2 bg-[#f8f8f7] border border-[#e3e3e0] rounded-xl text-sm font-medium focus:ring-2 focus:ring-brand-gold/30 focus:border-brand-gold outline-none" />
                            </div>
                            <div class="flex gap-2 pt-2">
                                <button @click="applyFilters" class="flex-1 px-3 py-2 bg-brand-charcoal text-white rounded-xl text-sm font-bold hover:bg-black transition-colors">
                                    Appliquer
                                </button>
                                <button @click="clearFilters" class="px-3 py-2 bg-[#f8f8f7] text-[#706f6c] rounded-xl text-sm font-bold hover:bg-[#f0f0f0] transition-colors">
                                    Effacer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Répartition -->
                    <div class="bg-white rounded-2xl border border-[#e3e3e0] p-5 shadow-sm">
                        <h3 class="font-bold text-[#1b1b18] mb-4">Répartition</h3>
                        <div class="space-y-3">
                            <div v-for="(dep, i) in totauxParFamille" :key="dep.famille" class="group">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[11px] font-bold text-[#1b1b18] truncate max-w-[55%]">{{ dep.famille }}</span>
                                    <span class="text-[11px] font-bold text-[#706f6c]">{{ fmt(dep.total) }}</span>
                                </div>
                                <div class="h-1.5 bg-[#f8f8f7] rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500" 
                                         :style="{ width: (parseFloat(dep.total) / depMax * 100) + '%', backgroundColor: depColors[i % depColors.length] }">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main: Table -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-2xl border border-[#e3e3e0] shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-[#e3e3e0]">
                                        <th class="text-left px-4 py-3 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Date</th>
                                        <th class="text-left px-4 py-3 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Libellé</th>
                                        <th class="text-left px-4 py-3 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest hidden md:table-cell">Famille</th>
                                        <th class="text-left px-4 py-3 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest hidden lg:table-cell">Bénéficiaire</th>
                                        <th class="text-left px-4 py-3 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest hidden lg:table-cell">Mode</th>
                                        <th class="text-right px-4 py-3 text-[10px] font-bold text-[#706f6c] uppercase tracking-widest">Montant</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#f0f0f0]">
                                    <template v-for="d in depenses" :key="d.depenseid">
                                        <tr @click="toggleExpand(d.depenseid)" 
                                            class="hover:bg-[#fafafa] cursor-pointer transition-colors"
                                            :class="expandedId === d.depenseid ? 'bg-brand-gold/5' : ''">
                                            <td class="px-4 py-3 font-medium text-[#1b1b18] whitespace-nowrap">{{ fmtDate(d.date) }}</td>
                                            <td class="px-4 py-3 font-bold text-[#1b1b18] max-w-[200px] truncate">{{ d.libelle || '—' }}</td>
                                            <td class="px-4 py-3 hidden md:table-cell">
                                                <span v-if="d.famille" class="px-2 py-0.5 rounded-md bg-brand-gold/10 text-brand-gold text-xs font-bold">{{ d.famille }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-[#706f6c] hidden lg:table-cell truncate max-w-[150px]">{{ d.benificiaire || '—' }}</td>
                                            <td class="px-4 py-3 text-[#706f6c] hidden lg:table-cell">{{ d.mode_reglement || '—' }}</td>
                                            <td class="px-4 py-3 text-right font-black text-red-500 whitespace-nowrap">{{ fmt(d.montant) }}</td>
                                        </tr>
                                        <tr v-if="expandedId === d.depenseid" class="bg-[#fafafa]">
                                            <td colspan="6" class="px-4 py-4">
                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                                                    <div>
                                                        <span class="font-bold text-[#706f6c] block mb-0.5">N°</span>
                                                        <span class="font-bold text-[#1b1b18]">{{ d.numero || '—' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="font-bold text-[#706f6c] block mb-0.5">Échéance</span>
                                                        <span class="font-bold text-[#1b1b18]">{{ fmtDate(d.echeance) }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="font-bold text-[#706f6c] block mb-0.5">Bénéficiaire</span>
                                                        <span class="font-bold text-[#1b1b18]">{{ d.benificiaire || '—' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="font-bold text-[#706f6c] block mb-0.5">Mode règlement</span>
                                                        <span class="font-bold text-[#1b1b18]">{{ d.mode_reglement || '—' }}</span>
                                                    </div>
                                                    <div v-if="d.description" class="col-span-2 md:col-span-4">
                                                        <span class="font-bold text-[#706f6c] block mb-0.5">Description</span>
                                                        <span class="font-bold text-[#1b1b18]">{{ d.description }}</span>
                                                    </div>
                                                    <div v-if="d.observation" class="col-span-2 md:col-span-4">
                                                        <span class="font-bold text-[#706f6c] block mb-0.5">Observation</span>
                                                        <span class="font-bold text-[#1b1b18]">{{ d.observation }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr v-if="depenses.length === 0">
                                        <td colspan="6" class="px-4 py-12 text-center text-[#706f6c]">
                                            Aucune dépense trouvée
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot v-if="depenses.length > 0" class="border-t-2 border-[#e3e3e0]">
                                    <tr class="bg-[#f8f8f7]">
                                        <td colspan="5" class="px-4 py-3 font-black text-[#1b1b18] text-right uppercase text-xs tracking-widest">Total</td>
                                        <td class="px-4 py-3 text-right font-black text-red-500">{{ fmt(totalGeneral) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
