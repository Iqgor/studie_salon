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
import router from '@/router';
import PrivacyVerklaring from './privacyVerklaring.vue';
import GebruikersVoorwaarden from './gebruikersVoorwaarden.vue';
import Changecolor from '@/components/profile/changecolor.vue';

export default {
    name: "profile",
    props: ['slug'],
    data() {
        return {
            options: [
                { name: 'Profiel', slug: 'profiel', component: 'profiel' },
                { name: 'Kleuren wijzigen', slug: 'kleur-wijzigen', component: Changecolor },
                { name: 'Wachtwoord wijzigen', slug: 'wachtwoord-wijzigen', component: Changepassword },
                { name: 'Privacy verklaring', slug: 'privacy-verklaring', component: PrivacyVerklaring },
                { name: 'Gebruikers voorwaarden', slug: 'gebruikersvoorwaarden', component: GebruikersVoorwaarden },
                { name: 'Profiel verwijderen', slug: 'profiel-verwijderen', component: '' },

            ],
            active: '',
            activeName: '',
            dialog: false,
        }
    },
    mounted() {
        this.setActiveOption()
    },
    watch: {
        slug() {
            this.setActiveOption()
        }
    },
    methods: {
        setActiveOption() {
            const slugLower = (this.slug || '').toLowerCase()

            // kijk of slug overeenkomt met een van de opties
            const matched = this.options.find(opt => opt.slug === slugLower)

            if (matched) {
                this.active = matched.component
                this.activeName = matched.name
                document.title = `Profiel - ${matched.name}`;
            } else {
                // slug matched niet, zet default option
                const defaultOption = this.options[0]
                this.active = defaultOption.component
                this.activeName = defaultOption.name
                document.title = `Profiel - ${defaultOption.name}`;
            }
        },
        changeActive(option) {
            router.push(`/profiel/${option.slug}`)
        }
    },
}
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

    text-overflow: ellipsis;
    overflow: hidden;
    white-space: wrap;
    word-break: break-word;
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

@media screen and (max-width: 700px) {
    .profiel {
        flex-direction: column;
        width: 100%;
        height: 100%;
    }

    .profiel_list {
        max-width: 100%;
        border-right: none;
        border-bottom: var(--color-primary-500) solid 0.1rem;
    }

    .profiel_list_item {
        border: none;
    }
}
</style>
