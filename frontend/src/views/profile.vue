<template>
    <main class="main">
        <section class="profiel">

            <!--lijst met profile options-->

                <ul class="profiel_list">
                    <li class="profiel_list_item" :class="{ profiel_list_item_active: activeName == option.name }"
                        v-for="option in options" :key="option.id" @click="changeActive(option)">
                        {{ option.name }}
                    </li>
                </ul>

            <section class="profiel_content">
                <component :is="active"></component>
            </section>
        </section>
    </main>
</template>

<script>
import Changepassword from '@/components/profile/changepassword.vue';

export default {
    name: "profile",
    components: {
    },
    data() {
        return {
            options: [
                { name: 'Profiel', component: 'profiel' },
                { name: 'Kleuren wijzigen', component: '' },
                { name: 'Wachtwoord wijzigen', component: Changepassword },
                { name: 'Profiel verwijderen', component: 'profielVerwijderen' },
                
            ],
            active: Changepassword,
            activeName: 'Wachtwoord wijzigen',
            dialog: false,
        };

    },
    mounted() { },
    methods: {
        changeActive(option) {
            this.active = option.component
            this.activeName = option.name
            console.log(option.component, option.name);
            
        }
    },

};
</script>

<style scoped>

.main {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem;
}
.profiel {
    border: var(--color-primary-500) solid 0.3rem;
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: space-between;
    border-radius: 2rem;
    overflow: auto;
}

.profiel_content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    position: relative;
    overflow: auto;
    min-height: 80vh;
}

.profiel_list {
    list-style: none;
    display: flex;
    justify-content: start;
    flex-direction: row;
    flex-direction: column;
    max-width: 20rem;
        border-right: var(--color-primary-500) solid 0.1rem;
}

.profiel_list_item {
    font-size: 150%;
    display: flex;
    flex-direction: column;
    justify-content: start;
    align-items: start;
    border-bottom: var(--color-primary-500) solid 0.1rem;

    padding: 1rem 2rem;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
}


.profiel_list_item:hover {
    cursor: pointer;
    color: var(--color-background-500);
    background: var(--color-primary-500);
}
.profiel_list_item_active {
    background: var(--color-secondary-500);
    font-weight: bold;
}
</style>
