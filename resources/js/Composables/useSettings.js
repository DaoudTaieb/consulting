import { reactive, watch } from 'vue';

const defaultSettings = {
    showTableauBord: true,
    showTresorerie: true,
    showPerformances: true,
    showDepenses: true,
    showFournisseurs: true,
    showClients: true,
    showAchats: true,
    showStock: true,
    showEtatJournee: true,
    showJournalCaisse: true,
    showRapportMensuel: true,
    defaultRoute: '/tableau-de-bord',
};

// Check if localStorage is available
const isBrowser = typeof window !== 'undefined';

let savedSettings = {};
if (isBrowser) {
    try {
        const stored = localStorage.getItem('gfm_menu_settings');
        if (stored) {
            savedSettings = JSON.parse(stored);
        }
    } catch (e) {
        console.error("Could not parse settings from localStorage", e);
    }
}

export const menuSettings = reactive({ ...defaultSettings, ...savedSettings });

if (isBrowser) {
    watch(menuSettings, (newSettings) => {
        localStorage.setItem('gfm_menu_settings', JSON.stringify(newSettings));
        // Sync default route to a cookie so Laravel backend can read it for the '/' redirect
        document.cookie = `gfm_default_route=${newSettings.defaultRoute};path=/;max-age=31536000`;
    }, { deep: true });
}

