<template>
    <main>

        <article class="section">
            <div class="section__header">
                <h3>Persoonlijk informatie</h3>
                <button class="edit__button" @click="editUser = !editUser">wijzig <i
                        class="fa-solid fa-pen-to-square"></i></button>
            </div>
            <hr>

            <form @submit.prevent="updateUser()" class="section_container">
                <label for="naam" class="form__container">
                    Naam
                    <p class="input__fake">{{ auth.user['name'] }}</p>
                    <input v-show="editUser" type="text" name="naam" v-model.trim="name">
                </label>
                <label for="email" class="form__container">
                    Email adress
                    <p class="input__fake">{{ auth.user['email'] }}</p>
                    <input v-show="editUser" type="text" name="email" v-model.trim="email">
                </label>
                <div class="buttons" v-if="editUser">
                    <button class="submit btn" type="submit">Pas aan</button>
                    <button class="cancel btn" type="button" @click="reset">Annuleer</button>
                </div>
            </form>

        </article>
        <article class="section">
            <div class="section__header">
                <h3>abonnement informatie</h3>
            </div>
            <hr>
            <div class="section_container">

                <div class="form__container">
                    naam
                    <p class="input__fake">
                        {{ subscriptionInfo['subscription_name'] }}
                    </p>
                </div>
                <div class="form__container">
                    Begonnen op
                    <p class="input__fake">
                        {{ subscriptionInfo['start_date'] }}
                    </p>
                </div>
                <div class="form__container">
                    eindigt op
                    <p class="input__fake">
                        {{ subscriptionInfo['end_date'] }}
                    </p>
                </div>
            </div>
            <hr>

            <div class="feature">
                <div class="feature__wrapper" v-for="feature in subscriptionInfo['features']" :key="feature.name">
                    <div class="feature__icon" v-html="feature.icon"></div>
                    <div class="feature__name">{{ feature.name }}</div>
                </div>

            </div>



        </article>
    </main>
</template>

<script>
import { auth } from '@/auth';
import { toastService } from '@/services/toastService';

export default {
    name: "profile-info",
    data() {
        return {
            auth,
            editUser: false,
            subscriptionInfo: [],
            email: '',
            name: '',

        }
    },
    mounted() {
        this.getSubscription();
    },
    methods: {
        async getSubscription() {

            try {
                const url = new URL(`${import.meta.env.VITE_APP_API_URL}backend/activeSubscription`);
                url.searchParams.append('user_id', auth.user.id);
                url.searchParams.append('role', auth.user.role);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: auth.bearerToken
                    }
                });

                const data = await response.json();
                this.subscriptionInfo = data
                auth.checkAction(data?.action)


                if (data?.title == 'Geen abonnement') {
                    this.logout()
                }


            } catch (error) {
                console.error(error.message);
            }
        },

        async updateUser() {
            try {
                const url = new URL(`${import.meta.env.VITE_APP_API_URL}backend/update_user`);

                const response = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: auth.bearerToken
                    },
                    body: JSON.stringify({
                        userId: auth.user.id,
                        email: this.email,
                        name: this.name,
                    }),
                });

                const data = await response.json();
                console.log(data);

                if (data?.message && data?.title) {
                    toastService.addToast(data.title, data.message, data.type)
                }

                if (data?.token) {
                    auth.setAuth(true, data.token)
                }



            } catch (error) {
                console.error(error.message);
            }
            this.editUser = false
        },

        reset() {
            this.email = ''
            this.name = ''
            this.editUser = false
        },

    },
}
</script>

<style scoped>
main {
    width: 100%;
    height: 100%;
    padding: 2rem;
    display: flex;
    justify-content: start;
    align-items: start;
    flex-direction: column;
    gap: 2rem;
}

.section {
    padding: 2rem;
    width: 100%;
    background: var(--color-card-500);
    border-radius: 1.5rem;
    display: flex;
    flex-direction: column;
}

.section__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: var(--color-text);
    margin-bottom: 1.5rem;
}

.edit__button {
    background: var(--color-background-500);
    border: none;
    padding: 1rem;
    border-radius: 0.5rem;
    color: var(--color-text);
    transition: 0.5s all ease-in-out;
    cursor: pointer;
}

.edit__button:hover {
    background: var(--color-primary-500);
}

hr {
    margin: 1rem;
}

.section_container {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.form__container {
    display: flex;
    flex-direction: column;
    font-size: 1.4rem;
    gap: 0.5rem;
    min-width: 15rem;
    width: max-content;
}

.input__fake {
    font-size: 1.8rem;
}

input {
    background: var(--color-background-500);
    border: var(--color-text) solid 0.3rem;
    height: 3.5rem;
    border-radius: 0.75rem;
    color: var(--color-text);
    padding-left: 1rem;
    font-size: 1.8rem;
    width: 100%;
}

.feature {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 2rem;
}

.feature__wrapper {
    width: 30rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
}

.feature__icon {
    width: 4rem;
    aspect-ratio: 1/1;
    display: flex;
    justify-content: center;
    align-items: center;
    background: var(--color-background-500);
    color: var(--color-text);
    border-radius: 100%;
}

.feature__name {
    color: var(--color-text);
    font-size: 1.8rem;
    text-align: end;
}

.buttons {
    display: flex;
    justify-content: center;
    align-items: end;
    gap: 2rem;
    width: 100%;
}

.btn {
    border: none;
    width: max-content;
    height: max-content;
    padding: 0.5rem 1rem;
    color: var(--color-text);
    font-size: 1.8rem;
    border-radius: 0.5rem;
    border: solid 0.3rem;
    height: 3.5rem;
    transition: 0.3s all ease-in-out;
    cursor: pointer;
}

.submit {
    background: var(--color-primary-500);
    border-color: var(--color-background-500);
}

.submit:hover {
    border-color: var(--color-text);
}

.cancel {
    background: var(--color-secondary-500);
    border-color: var(--color-background-500);
}

.cancel:hover {
    border-color: var(--color-text);
}
</style>