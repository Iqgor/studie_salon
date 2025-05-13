import { reactive } from 'vue'

export const sharedfunctions = reactive({
    currentTheme: 'light',

    themes: [
        {name: 'Licht', value: 'light', primary: '#ffffff', background: '#0065ff'},
        {name: 'Donker', value: 'dark', primary: '#1e1e1e', background: '#9b59b6'},
        {name: 'Groen', value: 'green', primary: '#2f3327', background: '#83b551'},
        {name: 'Blauw', value: 'blue', primary: '#84ccd7', background: '#0e788a'},
        {name: 'Roze', value: 'pink', primary: '#7d494a', background: '#ca7a79'},
    ],

    daysBetween(date1, date2) {
        console.log('date1', date1, 'date2', date2);

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

    switchTheme(theme) {
        console.log('Switching theme to:', theme);
        
        document.documentElement.className = `theme-${theme}`;
        localStorage.setItem('theme', theme);
        this.selectedTheme = theme;
        this.currentTheme = this.selectedTheme;
        console.log('Theme switched to:', theme);
        
    },
})