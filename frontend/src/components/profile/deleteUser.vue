<template>

    <div class="card">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div class="title">
            <h2>Verwijder account</h2>
        </div>
        <p class="text">
            Weet je zeker dat je je account wilt verwijderen? Voordat je doorgaat, is het belangrijk dat je weet wat dit
            betekent:
        </p>
        <ul>
            <li>Je gegevens worden permanent verwijderd</li>
            <li>Je huidige abonnement wordt stopgezet</li>
            <li>Facturen blijven bewaard voor administratie</li>
        </ul>
        <p class="text">
            Als je zeker bent van je keuze, bevestig dan door je wachtwoord hieronder in te vullen.
        </p>

        <form @submit.prevent="deleteAccount()" class="form">
            <div class="form__wrapper">
                <input type="password" name="password" id="password" placeholder="Wachtwoord" v-model.trim="password"
                    required>
                <i class="fa-solid fa-eye" @click="toggleInput('password')"></i>
            </div>
            <button type="submit" class="btn">Verwijder account</button>
        </form>
    </div>


</template>

<script>
import { auth } from '@/auth';
import { toastService } from '@/services/toastService';


export default {
    name: "deleteUser",
    components: {
    },
    data() {
        return {
            password: ''
        };
    },
    computed: {
    },
    watch: {
    },
    methods: {
        toggleInput(input) {
            const togglePass = document.getElementById(input)
            if (togglePass.type == 'password') {
                togglePass.type = 'text'
            } else {
                togglePass.type = 'password'
            }
        },
        async deleteAccount() {

            try {
                const response = await fetch(`${import.meta.env.VITE_APP_API_URL}backend/Delete_account`, {
                    method: 'DELETE',
                    body: JSON.stringify({
                        password: this.password
                    }),
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: auth.bearerToken
                    }
                })

                let incommingdata = await response.json()

                console.log(incommingdata);
                if (incommingdata?.title && incommingdata?.message) {
                    toastService.addToast(incommingdata.title, incommingdata.message, incommingdata.type)
                }

                if (incommingdata?.type == 'success') {
                    auth.logout()
                }
                

            } catch (err) {
                console.error(err);
            }

        }

    }
}
</script>

<style scoped>
.card {
    max-width: 40rem;
    background-color: #fff;
    border: 0.3rem solid var(--color-warning);
    border-radius: 2.5rem;
    padding: 2.4rem;
    box-shadow: 0 0.8rem 2rem var(--color-card-500);
    color: black;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    gap: 1.5rem;
}

.card i.fa-triangle-exclamation {
    background-color: var(--color-warning);
    color: white;
    font-size: 5rem;
    display: block;
    text-align: center;
    width: max-content;
    padding: 2rem;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    animation: breathe 1.5s ease-in-out infinite;
}

@keyframes breathe {

    0%,
    100% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.1);
    }
}

.title {
    text-align: center;
}

.title h2 {
    font-size: 2.4rem;
}

.text {

    line-height: 1.5;
}

ul {
    padding-left: 2rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}


.form {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
    width: 100%;
}

.form__wrapper {
    position: relative;
}

input {
    width: 100%;
    height: 4rem;
    padding: 0rem 1.5rem;
    border: 0.3rem solid var(--color-warning);
    border-radius: 8px;
    font-size: 16px;
    box-sizing: border-box;
    transition: all ease-in-out 0.3s;
    color: black;
}

input::placeholder {
    color: #808080;
}

input:focus {
    outline: none;
    padding-bottom: 0rem;
    border-bottom: none;
    border: 0.3rem solid var(--color-warning);
}

.form__wrapper i.fa-eye {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: black;
}

.btn {
    background-color: red;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    margin-top: 12px;
    transition: all 0.3s ease-in-out;
}

.btn:hover {
    background: maroon;
}
</style>