import { ref } from 'vue';
import { toastService } from './services/toastService';

//maak het watchable over de gehele aplicatie
export const currentTheme = ref(localStorage.getItem('theme') || 'light');

export const sharedfunctions = {
    //als er gekeken moet worden tussen dagen
    daysBetween(date1, date2) {

        const normalize = (date) => {
            // Als er al een "T" of " " in zit, dan is het datetime
            if (date.includes(' ')) {
                return new Date(date.replace(' ', 'T'));
            } else {
                return new Date(date); // Alleen YYYY-MM-DD
            }
        }

        const d1 = normalize(date1);
        const d2 = normalize(date2);

        const diffTime = d2 - d1; // je kan hier ook Math.abs() gebruiken als je alleen positieve dagen wilt
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        return diffDays;
    },
    
    // verander de thema van de website
    switchTheme(theme, name = null) {
        document.documentElement.className = `theme-${theme}`;
        localStorage.setItem('theme', theme);
        currentTheme.value = theme;

        if (name) {
            toastService.addToast('Veranderd', 'Kleurenpalette veranderd naar ' + name, 'success');
        }
    },
};
