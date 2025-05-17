<template>
    <main>
        <section class="top">
            <!--hier komt een kaart met een element die van kleur veranderd zodra je over dat kleur hovert-->
        </section>
        <section class="bottom">
            <article
                class="card"
                :class="{ 'card_active': theme.value === currentTheme }"
                v-for="theme in themes"
                :key="theme.value"
                @click="handleClick(theme)"
            >
                <i class="fa-solid fa-check card_icon" v-if="theme.value === currentTheme"></i>
                <div
                    class="card__stripe"
                    v-for="(color, i) in getThemeStripeColors(theme)"
                    :key="i"
                    :style="{ backgroundColor: color }"
                ></div>
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
    }
}
</script>

<style scoped>
.bottom {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    width: 100%;
}

.card {
    width: 30rem;
    height: 30rem;
    margin: 20px;
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
</style>