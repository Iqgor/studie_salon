<template>
    <div class="ww">
        <form @submit.prevent="validateInput()">
            <div class="form_div">

                <label class="form_label" for="current-password">
                    <p>Uw huidige wachtwoord/</p><small>tijdelijke wachtwoord</small>
                </label>
                <div class="form_eyecontainer">
                    <input :class="{ form_input_error: invalidCurrentPassword == true }" class="form_input"
                        id="current-password" name="current-password" type="password" v-model.trim="currentPassword" />
                    <i @click="toggleInput('current-password')" class="fa-solid fa-eye"></i>
                </div>
                <p class="ww_error" v-if="invalidCurrentPassword && currentPassword">
                    Wachtwoord Niet correct
                </p>
                <p class="ww_error" v-if="invalidCurrentPassword && !currentPassword">
                    Mag niet leeg laten
                </p>
            </div>

            <div class="form_div">

                <label class="form_label" for="new-password">Uw nieuwe wachtwoord</label>
                <div class="form_eyecontainer">
                    <input :class="{ form_input_error: newPasswordInvalid == true }" class="form_input"
                        id="new-password" name="new-password" type="password" v-model.trim="newPassword" />
                    <i @click="toggleInput('new-password')" class="fa-solid fa-eye"></i>
                </div>
            </div>


            <div class="ww_wrapper">
                <ul class="ww_list">
                    <li class="ww_listItem" :class="{
                        ww_listItem_error: !minCharacter,
                        ww_listItem_green: minCharacter
                    }">
                        <p class="ww_title">10+ letters</p>
                    </li>
                    <li class="ww_listItem" :class="{
                        ww_listItem_error: !symbol,
                        ww_listItem_green: symbol
                    }">
                        <p class="ww_title">Symbolen</p>
                    </li>
                    <li class="ww_listItem" :class="{
                        ww_listItem_error: !Upper,
                        ww_listItem_green: Upper
                    }">
                        <p class="ww_title">Hoofdletters</p>
                    </li>
                    <li class="ww_listItem" :class="{
                        ww_listItem_error: !Lower,
                        ww_listItem_green: Lower
                    }">
                        <p class="ww_title">Kleine Letters</p>
                    </li>
                    <li class="ww_listItem" :class="{
                        ww_listItem_error: !forbidden,
                        ww_listItem_green: forbidden
                    }">
                        <p class="ww_title">Geen spatie of <strong>|</strong></p>
                    </li>
                </ul>
            </div>

            <div class="form_div">
                <div class="form_eyecontainer">
                    <label class="form_label" for="repeat-password">Herhaal uw nieuwe wachtwoord</label>
                    <input :class="{ form_input_error: newPasswordInvalid }" class="form_input" id="repeat-password"
                        name="repeat-password" type="password" v-model.trim="repeatNewPassword" />
                </div>

            </div>

            <button :type="this.buttonType">Opslaan</button>
        </form>


    </div>
</template>
<script>
import { auth } from '@/auth';
import router from '@/router';
import { toastService } from '@/services/toastService';


export default {
    name: "changePassword",
    components: {
    },
    data() {
        return {
            currentPassword: '',
            invalidCurrentPassword: false,
            newPassword: '',
            repeatNewPassword: '',
            newPasswordInvalid: false,
            minCharacter: false,
            symbol: false,
            Upper: false,
            Lower: false,
            forbidden: false,
            MakeRequest: false,
        };

    },
    mounted() { },
    methods: {
        validateInput() {
            // Reset de validatie
            this.newPasswordInvalid = false;
            this.invalidCurrentPassword = false;

            // kijk of de velden leeg zijn
            if (!this.currentPassword && !this.repeatNewPassword) {
                this.newPasswordInvalid = true;
                this.invalidCurrentPassword = true;
                toastService.addToast(
                    'Wachtwoord niet goed overgenomen',
                    'Vul alle velden in.',
                    'error',
                    4000
                );
                return;
            }

            if (!this.currentPassword) {
                this.invalidCurrentPassword = true;
                toastService.addToast('Huidig wachtwoord ontbreekt', 'Vul je huidige wachtwoord in.', 'error', 4000);
                return;
            }

            if (!this.repeatNewPassword) {
                this.newPasswordInvalid = true;
                toastService.addToast('Nieuw wachtwoord ontbreekt', 'Herhaal je nieuwe wachtwoord.', 'error', 4000);
                return;
            }

            // kijk of de wachtwoorden overeenkomen
            if (this.repeatNewPassword !== this.newPassword) {
                this.newPasswordInvalid = true;
                toastService.addToast('Wachtwoorden komen niet overeen', 'Controleer je nieuwe wachtwoord.', 'error', 4000);
                return;
            }

            // is alles ingevuld
            this.newPasswordInvalid = false;
            this.invalidCurrentPassword = false;

            this.changePass();
        },
        async changePass() {
            try {
                const response = await fetch(`${import.meta.env.VITE_APP_API_URL}backend/change_password`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        userId: auth.user.id,
                        newPassword: this.newPassword,
                        oldPassword: this.currentPassword,
                    }),
                     headers: {
                        'Content-Type': 'application/json',
                        Authorization: auth.bearerToken
                    }
                })

                let incommingdata = await response.json()

                if (incommingdata?.title && incommingdata?.message) {
                    toastService.addToast(incommingdata?.title, incommingdata?.message, incommingdata?.type)
                }

                if (incommingdata?.type == 'success') {
                    localStorage.removeItem('temp_used')
                    auth.temp_used = false
                    router.push('/profiel')
                }

                auth.checkAction(incommingdata?.action)

            } catch (err) {
                console.error(err);
            }
        },
        toggleInput(input) {
            const togglePass = document.getElementById(input)
            if (togglePass.type == 'password') {
                togglePass.type = 'text'
            } else {
                togglePass.type = 'password'
            }
        },
    },
    watch: {
        newPassword(newValue) {
            const symbolRegex = /[!@#$%^&*(),.?":{}|<>_\-+=~`]/;
            const lowerRegex = /[a-z]/;
            const upperRegex = /[A-Z]/;
            const spaceOrPipeRegex = /[ |\s]/;

            if (newValue.length === 0) {
                this.minCharacter = false;
                this.symbol = false;
                this.Upper = false;
                this.Lower = false;
                this.forbidden = false;
            } else {
                this.minCharacter = newValue.length >= 10;
                this.symbol = symbolRegex.test(newValue);
                this.Upper = upperRegex.test(newValue);
                this.Lower = lowerRegex.test(newValue);
                this.forbidden = !spaceOrPipeRegex.test(newValue);
            }

            this.MakeRequest = this.minCharacter &&
                this.symbol &&
                this.Upper &&
                this.Lower &&
                this.forbidden;
        }
    },
    computed: {
        buttonType() {
            if (this.MakeRequest) {
                return 'submit'
            } else {
                return 'inactive'
            }
        }
    }
}
</script>

<style scoped>
.ww {
    display: flex;
    flex-direction: column;
    align-items: start;
    justify-content: center;
    background: var(--color-card-500);
    border-radius: 2rem;
    padding: 2rem;
}

form {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: max-content;
    height: 100%;
    gap: 2rem;
}

.form_div {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
    flex-direction: column;

}

.form_eyecontainer {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    position: relative;
    flex-direction: column;
}

.form_eyecontainer i {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 2rem;
    color: var(--color-text);
    cursor: pointer;
}

.form_label {
    font-size: 1.5rem;
    font-weight: bold;
    text-align: start;
    width: 100%;
    color: var(--color-text);
    margin-bottom: 1rem;
}

.form_input {
    width: 100%;
    height: 4rem;
    padding: 1rem;
    font-size: 1.5rem;
    border-radius: 1rem;
    border: var(--color-primary-500) solid 0.3rem;
    background-color: var(--color-background-500);
    color: var(--color-text);
}

.form_input_error {
    border: var(--color-error) solid 0.3rem;
}

.form_input:focus {
    outline: none;
    border: var(--color-primary-500) solid 0.3rem;
}



.ww_wrapper {
    display: flex;
    justify-content: start;
    align-items: start;
    width: max-content;
    text-align: start;
    width: 100%;
    background: var(--color-background-500);
    border-radius: 2rem;
    padding: 2rem;
    border: var(--color-primary-500) solid 0.3rem;

}

.ww_list {
    display: flex;
    flex-direction: column;
    list-style-type: disc;
    padding-left: 0px;
}

.ww_listItem {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 20%;
}

.ww_listItem_error {
    color: var(--color-error);
    font-size: 1.5rem;
    font-weight: bold;
    text-align: center;
    width: 100%;
}

.ww_listItem_green {
    color: var(--color-success);
    font-size: 1.5rem;
    font-weight: bold;
    text-align: center;
    width: 100%;
}

.ww_title {
    font-size: 1.5rem;
    font-weight: bold;
    text-align: start;
    width: 100%;
}

.ww_error {
    color: var(--color-error);
    font-size: 1.5rem;
    font-weight: bold;
    text-align: start;
    width: 100%;
}

button {
    width: 100%;
    height: 4rem;
    font-size: 1.5rem;
    font-weight: bold;
    border-radius: 1rem;
    background-color: var(--color-primary-500);
    color: var(--color-background-500);
    border: none;
    cursor: pointer;
    transition: all 0.3s ease-in-out;
}

button:hover {
    background-color: var(--color-primary-400);
}

button[type="inactive"] {
    background: var(--color-card-500);
    color: var(--color-text);
    cursor: not-allowed;
}
</style>