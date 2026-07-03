<script setup>
import { Head } from '@inertiajs/vue3';
import MainLayout from '../Layouts/MainLayout.vue';
import { menuSettings } from '../Composables/useSettings';

const settingsList = [
    { id: 'showTableauBord', label: 'Tableau de bord', icon: '<path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>' },
    { id: 'showTresorerie', label: 'Trésorerie & Échéancier', icon: '<rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>' },
    { id: 'showPerformances', label: 'Performances Produits', icon: '<path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>' },
    { id: 'showDepenses', label: 'Dépenses', icon: '<path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a8 8 0 0 1-9 7.96V15h-2v6.96A8 8 0 0 1 3 15V7Z"/>' },
    { id: 'showFournisseurs', label: 'Fournisseurs', icon: '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>' },
    { id: 'showClients', label: 'Clients', icon: '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>' },
    { id: 'showAchats', label: 'Achats', icon: '<path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/>' },
    { id: 'showStock', label: 'Stock', icon: '<path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>' },
    { id: 'showEtatJournee', label: 'État de journée', icon: '<line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/>' },
    { id: 'showJournalCaisse', label: 'Journal caisse', icon: '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>' },
    { id: 'showRapportMensuel', label: 'Rapport mensuel', icon: '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>' },
];

const toggleSetting = (id) => {
    menuSettings[id] = !menuSettings[id];
};
</script>

<template>
    <MainLayout>
        <Head title="Paramètres" />
        
        <div class="p-4 sm:p-6 md:p-8 lg:p-12 max-w-4xl mx-auto">
            <header class="mb-8 md:mb-12">
                <h1 class="text-3xl md:text-4xl font-black text-[#1b1b18] tracking-tight mb-3">Paramètres</h1>
                <p class="text-[#706f6c] text-sm md:text-base">Gérez l'affichage des interfaces dans votre menu de navigation.</p>
            </header>

            <div class="bg-white rounded-3xl border border-[#e3e3e0] shadow-sm overflow-hidden">
                <div class="p-6 md:p-8 border-b border-[#f0f0f0]">
                    <h2 class="text-xl font-bold text-[#1b1b18] mb-1">Menu Principal</h2>
                    <p class="text-sm text-[#706f6c]">Activez ou désactivez les éléments que vous souhaitez voir dans la barre latérale.</p>
                </div>
                
                <div class="divide-y divide-[#f0f0f0]">
                    <div 
                        v-for="item in settingsList" 
                        :key="item.id"
                        class="flex items-center justify-between p-6 md:p-8 hover:bg-[#fafafa] transition-colors"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-[#f8f8f7] flex items-center justify-center text-[#1b1b18]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" v-html="item.icon"></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-[#1b1b18] text-base md:text-lg">{{ item.label }}</h3>
                                <p class="text-xs md:text-sm text-[#706f6c]">Afficher l'interface {{ item.label }} dans le menu</p>
                            </div>
                        </div>
                        
                        <button 
                            @click="toggleSetting(item.id)"
                            :class="[
                                'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-brand-gold focus:ring-offset-2',
                                menuSettings[item.id] ? 'bg-brand-gold' : 'bg-gray-200'
                            ]"
                        >
                            <span class="sr-only">Toggle {{ item.label }}</span>
                            <span 
                                :class="[
                                    'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                                    menuSettings[item.id] ? 'translate-x-6' : 'translate-x-1'
                                ]"
                            />
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-[#e3e3e0] shadow-sm overflow-hidden mt-8">
                <div class="p-6 md:p-8 border-b border-[#f0f0f0]">
                    <h2 class="text-xl font-bold text-[#1b1b18] mb-1">Démarrage</h2>
                    <p class="text-sm text-[#706f6c]">Choisissez l'interface qui s'affichera par défaut lorsque vous ouvrez l'application.</p>
                </div>
                <div class="p-6 md:p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-[#1b1b18] text-base md:text-lg">Interface par défaut</h3>
                        </div>
                        <select 
                            v-model="menuSettings.defaultRoute"
                            class="bg-[#f8f8f7] border border-[#e3e3e0] text-[#1b1b18] text-sm md:text-base font-bold rounded-xl focus:ring-brand-gold focus:border-brand-gold block w-64 p-3 outline-none"
                        >
                            <option value="/tableau-de-bord">Tableau de bord</option>
                            <option value="/tresorerie">Trésorerie & Échéancier</option>
                            <option value="/performances-produits">Performances Produits</option>
                            <option value="/depenses">Dépenses</option>
                            <option value="/dashboard">Fournisseurs</option>
                            <option value="/clients">Clients</option>
                            <option value="/achats">Achats</option>
                            <option value="/stock">Stock</option>
                            <option value="/statistiques?tab=journee">État de journée</option>
                            <option value="/statistiques?tab=journal">Journal caisse</option>
                            <option value="/statistiques?tab=mensuel">Rapport mensuel</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
