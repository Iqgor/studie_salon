<template>
    <main>
        <section class="demo">
            <div class="demo__container" :style="{ backgroundColor: demoTheme.background }">
                <div class="demo_card" :style="{ backgroundColor: demoTheme.card }">

                    <div class="demo_sale" :style="{ backgroundColor: demoTheme.secondary }">
                        <p class="demo_sale_p" :style="{ color: demoTheme.text }">100%</p>
                    </div>

                    <div class="demo_header" :style="{ color: demoTheme.text }">
                        <span>
                            <i class="fa-solid fa-palette" :style="{ color: demoTheme.secondary }"></i>
                            <h2>demo voor de kleuren</h2>
                        </span>
                        <span class="demo_rank"
                            :style="{ backgroundColor: demoTheme.secondary, color: demoTheme.text }">hey</span>
                    </div>

                    <p class="demo_description" :style="{ color: demoTheme.text }">hier wat uitleg</p>
                    <p class="demo_price__container">
                        <span class="demo_price" :style="{ color: demoTheme.text }"><i class="fa-solid fa-euro-sign"
                                :style="{ color: demoTheme.text }"></i> $$$</span>
                    </p>
                    <hr :style="{ borderColor: demoTheme.text }">

                    <button class="demo_btn" :style="{ backgroundColor: demoTheme.primary, color: demoTheme.text }">Neem
                        Abonemment</button>
                </div>
            </div>

        </section>

        <section class="cards">
            <article class="card" :class="{ 'card_active': theme.value === currentTheme }" v-for="theme in themes"
                :key="theme.value" @click="handleClick(theme)" @mouseenter="showDemo(theme)"
                v-on:mouseleave="removeDemo()">
                <i class="fa-solid fa-check card_icon" v-if="theme.value === currentTheme"></i>
                <div class="card__stripe" v-for="(color, i) in getThemeStripeColors(theme)" :key="i"
                    :style="{ backgroundColor: color }"></div>
            </article>

        </section>
    </main>
</template>

<script>
import { auth } from '@/auth';
import { toastService } from '@/services/toastService';
import themes from '@/assets/themes.json';
import { currentTheme, sharedfunctions } from '@/sharedFunctions';

export default {
    name: "changeColor",
    components: {
    },
    data() {
        return {
            themes: themes.themes,
            demoTheme: {}
        };
    },
    computed: {
        currentTheme() {
            return currentTheme.value;
        }
    },
    watch: {
        currentTheme(newVal) {
            console.log('Theme changed in watch:', newVal);
            // do something on theme change
        }
    },
    methods: {
        handleClick(theme) {
            sharedfunctions.switchTheme(theme.value, theme.name);
            this.setPageTheme(theme.value);
        },
        getThemeStripeColors(theme) {
            return [
                theme.primary,
                theme.secondary,
                theme.card,
                theme.background
            ];
        },
        showDemo(theme) {
            this.demoTheme = theme
            console.log(this.demoTheme);
        },
        removeDemo() {
            this.demoTheme = {}
            console.log(this.demoTheme);
        }
    }
}
</script>

<style scoped>
hr {
    border: 1px solid var(--color-text);
    width: 100%;
}

main {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    padding: 2rem;
    gap: 4rem;
    flex-wrap: nowrap;
    height: 100%;
}


.demo {
    width: max-content;
    border-radius: 2rem;
    padding: 2rem 0rem;
}

.demo__container {
    padding: 3rem;
    border-radius: 2rem;
    border: black 0.3rem solid;
    box-shadow: black 0.5rem 0.5rem 1rem;
    background-color: var(--color-background-500);
}

.demo_card {
    background-color: var(--color-card-500);
    border-radius: 8px;
    box-shadow: 0 0.2rem 0.4rem rgba(0, 0, 0, 0.1);
    padding: 2rem;
    min-width: 30rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-width: 30rem;
    position: relative;
}

.demo_header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.demo_header span {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.demo_header span h2 {
    font-size: 1.5rem;
    margin: 0;
}

.demo_header span i {
    font-size: 2rem;
    color: var(--color-secondary-500);
}

.demo_header .demo_rank {
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--color-text);
    background-color: var(--color-secondary-500);
    padding: 0.5rem 1rem;
    border-radius: 4px;
}

.demo_btn {
    background-color: var(--color-primary-500);
    color: var(--color-text);
    padding: 1rem 2rem;
    border-radius: 0.4rem;
    border: none;
    cursor: pointer;
    font-size: 1.2rem;
    font-weight: bold;
    margin-top: auto;
}

.demo_description {
    font-size: 1.25rem;
}

.demo_price {
    font-size: 2rem;
    font-weight: bold;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
}

.demo_price__container {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.demo_sale {
    position: absolute;
    top: -1.5rem;
    right: -1.5rem;
    background: var(--color-secondary-500);
    padding: 0.5rem;
    border-radius: 0.5rem;
}

.demo_sale_p {
    font-size: 1.2rem;
    font-weight: bold;
}





.cards {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    width: 100%;
    overflow-y: auto;
    max-height: 80vh;
    gap: 1rem;
    padding: 1.5rem;
}

.card {

    width: 30rem;
    aspect-ratio: 1/1;
    background-color: #fff;
    border-radius: 1.6rem;
    cursor: pointer;
    padding: 1rem;
    transition: all 0.3s ease-in-out;
    position: relative;


}

.card_active {
    box-shadow: 1rem 1rem 1rem var(--color-card-600);
}

.card:hover {
    transform: scale(1.05);
}

.card_icon {
    position: absolute;
    top: -1rem;
    right: -1rem;
    font-size: 2.5rem;
    color: #fff;
    background: var(--color-success);
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: all 0.3s ease-in-out;
    border: solid 0.3rem var(--color-background-500);
}

.card__stripe {
    width: 100%;
    height: 25%;
    border: black solid 0.1rem;

}

.card__stripe:first-child {
    border-radius: 1rem 1rem 0 0;
}

.card__stripe:last-child {
    border-radius: 0 0 1rem 1rem;
}

@media screen and (max-width: 1750px) {
    .card {
        max-width: 25rem;
    }
}

@media screen and (max-width: 1160px) {
    .card {
        max-width: 17rem;
    }
}

@media screen and (max-width: 600px) {
    .card {
        max-width: 13rem;
    }
}
</style>