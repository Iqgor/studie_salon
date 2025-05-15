<template>
    <section class="views">
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
    </section>
</template>

<script>
export default {
    name: "profile",
    components: {
    },
    data() {
        return {
            options: [
                { name: 'Profiel', component: 'profiel' },
                { name: 'Kleuren wijzigen', component: '' },
                { name: 'Wachtwoord wijzigen', component: 'wachtwoordWijzigen' },
                { name: 'Profiel verwijderen', component: 'profielVerwijderen' },
                
            ],
            active: 'profiel',
            activeName: 'profiel',
            dialog: false,
        };

    },
    mounted() { },
    methods: {
        changeActive(option) {
            this.active = option.component
            this.activeName = option.name
        }
    },

};
</script>

<style scoped>
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
    background: red;
    display: flex;
    flex-direction: column;
    justify-content: space-evenly;
    align-items: center;
    width: 100%;
    position: relative;
    overflow: auto;
}

.profiel_list {
    list-style: none;
    display: flex;
    justify-content: center;
    flex-direction: row;
    flex-direction: column;
    height: 100%;
    max-width: 20rem;
    
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
}

.profiel_list_item:last-child {
    border-bottom: none;
}

.profiel_list_item:hover {
    cursor: pointer;
    color: var(--color-primary-500);
    background: var(--color-text);
}
.profiel_list_item_active {
    color: #4CAF50;
    font-weight: bold;
}
</style>
