import { reactive } from 'vue'

export const sharedfunctions = reactive({

    daysBetween(date1, date2) {
        console.log('date1', date1, 'date2', date2)	;
        
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
        }
})