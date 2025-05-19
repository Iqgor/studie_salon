<template>
    <main class="main">
        <section class="card">
            <div class="slideshow-container">
                <img src="/logo.png" alt="logo" class="logo">

                <div class="slideshow">
                    <div class="mySlides ">
                        <div class="slidetext">{{ activeSlide }}</div>
                    </div>

                    <div class="bars">
                        <span class="bar" :class="slideIndex == index + 1 ? 'active' : ''"
                            v-for="(slide, index) in slides"></span>
                    </div>
                </div>

            </div>
            <form @submit.prevent="validateInput()" class="form" v-if="showForm === 'login'">

                <div class="login__header">
                    <h1 class="title">Welkom terug</h1>
                    <span class="text__container">
                        <p class="text">Goed om je weer terug te zien 👋</p>
                        <p class="text">Log in om verder te gaan</p>
                    </span>
                </div>


                <div class="input-container">
                    <input type="email" placeholder="Email" class="input" required v-model.trim="email">
                    <div class="form_div">
                        <input type="password" placeholder="Wachtwoord" class="input" required v-model.trim="password"
                            id="password">
                        <i @click="toggleInput('password')" class="fa-solid fa-eye"></i>
                    </div>
                    <label for="remember" class="checkbox-label"><input type="checkbox" name="remember" id="remember"
                            class="checkbox" v-model="remember"> Onthoud mij</label>

                </div>

                <div class="buttons">
                    <button type="submit" class="btn">Inloggen</button>
                    <p class="text">Nog geen account? <a href="/abonnementen" class="link">Neem eerst een abonnement</a></p>
                    <p class="text">Wachtwoord vergeten? <span @click="showForm = 'forgot'" class="link">Krijg een tijdelijk wachtwoord</span></p>

                </div>
            </form>

            <form @submit.prevent="sendOtp()" class="form" v-if="showForm === 'otp'">
                <div class="login__header">
                    <h1 class="title">Verificatie</h1>
                    <span class="text__container">
                        <p class="text">We hebben je een verificatiecode gestuurd naar je email</p>
                        <p class="text">Voer de code in om verder te gaan</p>
                    </span>
                </div>
                <div class="input-container">
                    <input type="text" placeholder="Verificatiecode" class="input" required v-model.trim="otp">
                </div>
                <div class="buttons">
                    <button type="submit" class="btn">Verstuur</button>
                    <p class="text">Nog geen email ontvangen? <span @click="login()" class="link">Stuur opnieuw</span>
                    </p>
                </div>
            </form>

            <form @submit.prevent="forgotPassword()" class="form" v-if="showForm === 'forgot'">

                <div class="login__header">
                    <h1 class="title">Wachtwoord vergeten?</h1>
                    <span class="text__container">
                        <p class="text">Als u uw e-mail invuld krijg je daar in een tijdeljke wachtwoord</p>
                        <p class="text">Daar mee kan je inloggen</p>
                    </span>
                </div>


                <div class="input-container">
                    <input type="email" placeholder="Email" class="input" required v-model.trim="email">
                </div>

                <div class="buttons">
                    <button type="submit" class="btn">Stuur wachtwoord</button>
                    <p class="text">Nog geen account? <a href="/abonnementen" class="link">Neem eerst een abonnement</a></p>
                </div>
            </form>

        </section>
    </main>
</template>
<script>
import { auth } from '@/auth';
import router from '@/router';
import { toastService } from '@/services/toastService';
import { sharedfunctions } from '@/sharedFunctions';
import { jwtDecode } from 'jwt-decode';


export default {
    name: 'HomeView',
    setup() {
        return { auth }
    },
    components: {

    },
    data() {
        return {
            slideIndex: 0,
            slides: [
                "Toegang tot een volledige agenda voor al jouw studeersessies.",
                "Meerdere leerstrategieën tot je beschikking voor hogere cijfers.",
                "Beste tips voor thuis leren zonder afleidingen.",
            ],
            activeSlide: '',

            email: '',
            password: '',
            remember: false,
            showForm: 'login',
            otp: '',
        };
    },
    mounted() {
        this.showSlides();
        if (this.getCookie('email')) {
            let Cookieemail = this.getCookie('email')
            this.email = Cookieemail
            this.remember = true
        }


    },
    methods: {
        getCookie(cookieName) {
            let cookies = document.cookie
            let cookieArray = cookies.split('; ')

            for (let i = 0; i < cookieArray.length; i++) {
                let cookie = cookieArray[i]
                let [name, value] = cookie.split('=')

                if (name === cookieName) {
                    return decodeURIComponent(value)
                }
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
        showSlides() {
            let fadingtext = document.getElementsByClassName("mySlides");
            fadingtext[0].classList.add("fade")

            setTimeout(() => { fadingtext[0].classList.remove("fade") }, 1000);

            if (this.slideIndex >= this.slides.length) { this.slideIndex = 0 }
            if (!this.activeSlide) {
                this.activeSlide = this.slides[0];
            } else {
                this.activeSlide = this.slides[this.slideIndex];
            }

            this.slideIndex++;
            setTimeout(this.showSlides, 5000); // Change image every 5 seconds
        },
        validateInput() {
            if (this.email === '' && this.password === '') {
                // * inputs zijn leeg
                addToast('error', 'Gebruikersnaam', 'Gebruikersnaam mag niet leeg zijn', 4000)
                addToast('error', 'Wachtwoord', 'Wachtwoord mag niet leeg zijn', 4000)
            } else if (this.email === '') {
                addToast('error', 'Gebruikersnaam', 'Gebruikersnaam mag niet leeg zijn', 4000)
            } else if (this.password === '') {
                addToast('error', 'Wachtwoord', 'Wachtwoord mag niet leeg zijn', 4000)
            } else {
                this.login()

                if (this.remember) {
                    var expires = ''
                    var date = new Date()     //dagen*uren*minuten*seconden*miliseconden
                    date.setTime(date.getTime() + 30 * 24 * 60 * 60 * 1000)
                    expires = '; expires=' + date.toUTCString()
                    document.cookie = 'email=' + this.email + expires + '; path=/'

                }
            }
        },

        async login() {
            try {
                const response = await fetch(`${import.meta.env.VITE_APP_API_URL}backend/login`, {
                    method: 'POST',
                    body: JSON.stringify({
                        email: this.email,
                        password: this.password
                    })
                })

                let incommingdata = await response.json()

                if (incommingdata?.title && incommingdata?.message) {
                    toastService.addToast(incommingdata?.title, incommingdata?.message, incommingdata?.type)
                }

                if (incommingdata?.temp_used == true) {
                    auth.temp_used = true
                    localStorage.setItem('temp_used', true)
                }

                if (incommingdata?.token) {

                    auth.setAuth(true, incommingdata?.token)
                    router.push('/')

                }
                else if (incommingdata?.otp_required) {
                    this.showForm = 'otp'
                }

            } catch (err) {
                console.error(err);
            }

        },
        async sendOtp() {
            try {
                const response = await fetch(`${import.meta.env.VITE_APP_API_URL}backend/verify_otp`, {
                    method: 'POST',
                    body: JSON.stringify({
                        email: this.email,
                        otp: this.otp
                    })
                })

                let incommingdata = await response.json()

                if (incommingdata?.title && incommingdata?.message) {
                    toastService.addToast(incommingdata?.title, incommingdata?.message, incommingdata?.type)
                }

                if (incommingdata?.token) {
                    auth.setAuth(true, incommingdata?.token)
                    router.push('/')
                }
            } catch (err) {
                // error handling hier
            }
        },
        async forgotPassword() {
            try {
                const response = await fetch(`${import.meta.env.VITE_APP_API_URL}backend/forgot_password`, {
                    method: 'POST',
                    body: JSON.stringify({
                        email: this.email
                    })
                })

                let incommingdata = await response.json()
                if (incommingdata?.title && incommingdata?.message) {
                    toastService.addToast(incommingdata?.title, incommingdata?.message, incommingdata?.type)
                }
                this.showForm = 'login'

            } catch (err) {
                // error handling hier
            }

        }



    }
}
</script>
<style scoped lang="css">
.main {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    height: 100%;
    width: 100%;
}

/**slideShow */

.logo {
    width: 10rem;
    border-radius: 1rem;
}

.card {
    background: var(--color-primary-500);
    padding: 2rem;
    width: 100%;
    height: 100%;
    border-radius: 1.5rem;
    display: flex;
}

.slideshow-container {
    width: 50%;
    aspect-ratio: 16/9;
    display: flex;
    justify-content: space-between;
    align-items: start;
    padding: 1rem;
    flex-direction: column;
    background-image: url("/Placeholder.svg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 1rem;

    box-shadow: inset 0 -10rem 10rem rgba(0, 0, 0, 0.5);
}

.slideshow {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    width: 100%;
    justify-content: start;
    align-items: start;
}

.slidetext {
    color: white;
    width: 75%;
    text-align: start;
    display: block;
}

.fade {
    animation-name: fade;
    animation-duration: 1.5s;
}

@keyframes fade {
    from {
        opacity: .4
    }

    to {
        opacity: 1
    }
}

.bars {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
    width: 100%;
}

.bar {
    height: 1.5rem;
    width: 5rem;
    background-color: #D9D9D9;
    border-radius: 0.25rem;
    display: inline-block;
    transition: background-color 0.6s ease;
}

.active {
    background-color: var(--color-primary-300);
}

/**login form */

.form {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: start;
    gap: 1rem;
    width: 50%;
    padding: 0rem 2rem;
}

.form_div {
    position: relative;
    width: 100%;
}

.form_div i {
    position: absolute;
    top: 50%;
    right: 1rem;
    transform: translateY(-50%);
    color: var(--color-primary-100);
    font-size: 1.5rem;
}

.form_div i:hover {
    cursor: pointer;
}


.login__header {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    width: 100%;
}

.title {
    font-size: 2rem;
    color: white;
    text-align: start;
}

.text__container {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.text__container .text {
    font-size: 1.5rem;
}

.text {
    color: white;
    font-size: 1.2rem;
    text-align: start;
}

.input-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    width: 100%;
}

.input {
    padding: 1rem;
    border-radius: 0.5rem;
    border: none;
    outline: none;
    width: 100%;
    background-color: var(--color-primary-300);
}

.input:focus {
    background-color: var(--color-primary-200);
    color: white;
}

.input::placeholder {
    color: var(--color-primary-100);
}

.input:focus::placeholder {
    color: --color-background-100;
}

.checkbox {
    width: 1.5rem;
    height: 1.5rem;
    margin-right: 0.5rem;
    cursor: pointer;
}

.checkbox:checked {
    background-color: var(--color-primary-500);
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
    font-size: 1.2rem;
}

.checkbox-label:hover {
    cursor: pointer;
}

.buttons {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    width: 100%;
}


.btn {
    padding: 1rem;
    border-radius: 0.5rem;
    border: none;
    outline: none;
    width: 100%;
    background-color: var(--color-primary-400);
    color: white;
    font-size: 1.25rem;
    cursor: pointer;
}

.btn:hover {
    background-color: var(--color-primary-600);
}

.link {
    cursor: pointer;
    color: var(--color-secondary-500);
}

.link:hover {
    text-decoration: underline;
}

.link:active {
    color: var(--color-secondary-800);
}

@media screen and (max-width: 800px) {

    .card {
        flex-direction: column;
        gap: 2rem;
    }

    .slideshow-container {
        width: 100%;
        aspect-ratio: 16/9;
    }

    .slideshow {
        width: 100%;
        padding: 0rem;
        gap: 1rem;
    }

    .form {
        width: 100%;
        padding: 0rem;
    }

    .logo {
        width: 5rem;
    }

    .slidetext {
        font-size: 1.5rem;
    }

    .bars {
        justify-content: start;
    }

    .bar {
        width: 3rem;
    }
}
</style>
