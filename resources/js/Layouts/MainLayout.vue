<script setup>
import { ref } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { menuSettings } from '../Composables/useSettings';

const isSidebarOpen = ref(false);
const isPasswordModalOpen = ref(false);
const passwordInput = ref('');
const passwordError = ref(false);

const logoutForm = useForm({});
const logout = () => logoutForm.post('/logout');

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const openPasswordModal = () => {
    passwordInput.value = '';
    passwordError.value = false;
    isPasswordModalOpen.value = true;
};

const submitPassword = () => {
    if (passwordInput.value === '44134396Guthy2017') {
        isPasswordModalOpen.value = false;
        router.visit('/parametres');
    } else {
        passwordError.value = true;
    }
};

const closePasswordModal = () => {
    isPasswordModalOpen.value = false;
};
</script>

<template>
    <div class="min-h-screen min-h-[100dvh] bg-[#fdfdfc] flex">
        <!-- Sidebar Backdrop (Mobile only) -->
        <div 
            v-if="isSidebarOpen" 
            @click="isSidebarOpen = false"
            class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40 lg:hidden transition-all duration-300"
        ></div>

        <!-- Sidebar -->
        <aside 
            :class="[
                'w-72 max-w-[85vw] border-r border-[#e3e3e0] flex flex-col fixed h-full max-h-[100dvh] bg-white z-50 transition-transform duration-300 lg:translate-x-0 safe-left safe-top safe-bottom',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
        >
            <div class="p-4 sm:p-6 lg:p-8 h-full flex flex-col overflow-y-auto">
                <div class="flex items-center justify-between mb-10">
                    <Link :href="menuSettings.defaultRoute || '/statistiques'" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                        <img :src="'/logo'" alt="VECTOR" class="w-12 h-12 object-contain rounded-lg shadow-sm" />
                        <div class="flex flex-col">
                            <span class="font-bold text-lg tracking-tighter leading-none text-brand-charcoal">{{ $page.props.societe?.nom || "VECTOR" }}</span>
                            <span class="text-[10px] font-bold text-brand-gold tracking-[0.2em]">{{ $page.props.societe?.activite || "STE L'Innovation De Mode" }}</span>
                        </div>
                    </Link>
                    <!-- Close button for mobile -->
                    <button @click="isSidebarOpen = false" class="lg:hidden p-2 text-[#706f6c]">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <nav class="space-y-1">

                    <Link 
                        v-if="menuSettings.showTableauBord"
                        href="/tableau-de-bord" 
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all',
                            $page.component === 'DashboardGlobal' ? 'bg-brand-gold/10 text-brand-gold' : 'text-[#706f6c] hover:bg-[#f8f8f7]'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                        <span>Tableau de bord</span>
                    </Link>

                    <Link 
                        v-if="menuSettings.showTresorerie"
                        href="/tresorerie" 
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all',
                            $page.component === 'Tresorerie' ? 'bg-brand-gold/10 text-brand-gold' : 'text-[#706f6c] hover:bg-[#f8f8f7]'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                        <span>Trésorerie & Échéancier</span>
                    </Link>

                    <Link 
                        v-if="menuSettings.showPerformances"
                        href="/performances-produits" 
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all',
                            $page.component === 'Performances' ? 'bg-brand-gold/10 text-brand-gold' : 'text-[#706f6c] hover:bg-[#f8f8f7]'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        <span>Performances Produits</span>
                    </Link>

                    <Link 
                        v-if="menuSettings.showDepenses"
                        href="/depenses" 
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all',
                            $page.component === 'Depenses' ? 'bg-brand-gold/10 text-brand-gold' : 'text-[#706f6c] hover:bg-[#f8f8f7]'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a8 8 0 0 1-9 7.96V15h-2v6.96A8 8 0 0 1 3 15V7Z"/></svg>
                        <span>Dépenses</span>
                    </Link>

                    <Link 
                        v-if="menuSettings.showFournisseurs"
                        href="/dashboard" 
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all',
                            $page.component === 'Dashboard' ? 'bg-brand-gold/10 text-brand-gold' : 'text-[#706f6c] hover:bg-[#f8f8f7]'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                        <span>Fournisseurs</span>
                    </Link>
                    
                    <Link 
                        v-if="menuSettings.showClients"
                        href="/clients" 
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all',
                            $page.component === 'Clients' ? 'bg-brand-gold/10 text-brand-gold' : 'text-[#706f6c] hover:bg-[#f8f8f7]'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>Clients</span>
                    </Link>
                    
                    <Link 
                        v-if="menuSettings.showAchats"
                        href="/achats" 
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all',
                            $page.component === 'Purchases' ? 'bg-brand-gold/10 text-brand-gold' : 'text-[#706f6c] hover:bg-[#f8f8f7]'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                        <span>Achats</span>
                    </Link>

                    <Link 
                        v-if="menuSettings.showStock"
                        href="/stock" 
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all',
                            $page.component === 'Stock' ? 'bg-brand-gold/10 text-brand-gold' : 'text-[#706f6c] hover:bg-[#f8f8f7]'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                        <span>Stock</span>
                    </Link>

                    <Link 
                        v-if="menuSettings.showEtatJournee"
                        href="/statistiques?tab=journee" 
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all',
                            $page.component === 'Statistics' && $page.url.includes('tab=journee') ? 'bg-brand-gold/10 text-brand-gold' : 'text-[#706f6c] hover:bg-[#f8f8f7]'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>
                        <span>État de journée</span>
                    </Link>

                    <Link 
                        v-if="menuSettings.showJournalCaisse"
                        href="/statistiques?tab=journal" 
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all',
                            $page.component === 'Statistics' && $page.url.includes('tab=journal') ? 'bg-brand-gold/10 text-brand-gold' : 'text-[#706f6c] hover:bg-[#f8f8f7]'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <span>Journal caisse</span>
                    </Link>

                    <Link 
                        v-if="menuSettings.showRapportMensuel"
                        href="/statistiques?tab=mensuel" 
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all',
                            $page.component === 'Statistics' && $page.url.includes('tab=mensuel') ? 'bg-brand-gold/10 text-brand-gold' : 'text-[#706f6c] hover:bg-[#f8f8f7]'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>Rapport mensuel</span>
                    </Link>
                </nav>

                <div class="mt-auto pt-6 border-t border-[#f0f0f0] space-y-2">
                    <button 
                        @click="openPasswordModal"
                        :class="[
                            'flex items-center gap-3 px-4 py-3 rounded-xl font-semibold transition-all w-full leading-none text-left',
                            $page.component === 'Settings' ? 'bg-[#1b1b18] text-white' : 'text-[#706f6c] hover:bg-[#f8f8f7]'
                        ]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span>Paramètres</span>
                    </button>

                    <button @click="logout" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#ef4444] hover:bg-[#ef4444]/5 font-semibold transition-all w-full leading-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        <span>Déconnexion</span>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Mobile Header -->
            <header class="lg:hidden flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 bg-white border-b border-[#e3e3e0] sticky top-0 z-30 shadow-sm safe-top safe-left safe-right">
                <Link :href="menuSettings.defaultRoute || '/statistiques'" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <img :src="'/logo'" alt="VECTOR" class="w-10 h-10 object-contain rounded-md shadow-sm" />
                    <span class="font-bold text-base tracking-tighter leading-none text-brand-charcoal">{{ $page.props.societe?.nom || "VECTOR" }}</span>
                </Link>
                <button @click="toggleSidebar" class="p-2 text-[#1b1b18] hover:bg-[#f8f8f7] rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                </button>
            </header>

            <main class="flex-1 bg-[#fdfdfc] lg:pl-[288px] min-h-[100dvh] flex flex-col relative">
                <slot />
            </main>
        </div>

        <!-- Password Modal for Settings -->
        <div 
            v-if="isPasswordModalOpen" 
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
        >
            <div 
                class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                @click="closePasswordModal"
            ></div>
            
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm relative z-10 overflow-hidden transform transition-all">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-black text-[#1b1b18]">Accès restreint</h3>
                        <button @click="closePasswordModal" class="text-[#706f6c] hover:text-[#1b1b18] transition-colors p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>
                    </div>
                    
                    <p class="text-sm text-[#706f6c] mb-6">Veuillez saisir le mot de passe pour accéder aux paramètres :</p>
                    
                    <form @submit.prevent="submitPassword" class="space-y-4">
                        <div>
                            <input 
                                type="password" 
                                v-model="passwordInput"
                                placeholder="Mot de passe"
                                :class="[
                                    'w-full px-4 py-3 bg-[#f8f8f7] border rounded-xl font-medium focus:ring-2 focus:outline-none transition-all',
                                    passwordError ? 'border-red-500 focus:ring-red-500/20' : 'border-[#e3e3e0] focus:ring-brand-gold/30 focus:border-brand-gold'
                                ]"
                            />
                            <p v-if="passwordError" class="text-red-500 text-xs font-bold mt-2">Mot de passe incorrect.</p>
                        </div>
                        
                        <div class="flex gap-3 pt-2">
                            <button 
                                type="button" 
                                @click="closePasswordModal"
                                class="flex-1 px-4 py-3 rounded-xl font-bold text-[#1b1b18] bg-[#f8f8f7] hover:bg-[#f0f0f0] transition-colors"
                            >
                                Annuler
                            </button>
                            <button 
                                type="submit" 
                                class="flex-1 px-4 py-3 rounded-xl font-bold text-white bg-brand-charcoal hover:bg-black transition-colors"
                            >
                                Valider
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
