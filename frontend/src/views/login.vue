<template>
    <main class="main">
        <section class="card">
            <div class="slideshow-container">
                <img src="/public/logo.png" alt="logo" class="logo">

                <div class="slideshow">
                    <div class="mySlides ">
                        <div class="slidetext">{{activeSlide}}</div>
                    </div>

                    <div class="bars">
                        <span class="bar" :class="slideIndex == index+1 ? 'active' : ''" v-for="(slide, index) in slides" ></span>
                    </div>
                </div>

            </div>
            <form @submit.prevent="login()" class="form">

                <div class="login__header">
                    <h1 class="title">Welkom terug</h1>
                    <span class="text__container">
                        <p class="text">Goed om je weer terug te zien 👋</p>
                        <p class="text">Log in om verder te gaan</p>
                    </span>
                </div>

                
                <div class="input-container">
                    <input type="email" placeholder="Email" class="input" required v-model.trim="email">
                    <input type="password" placeholder="Wachtwoord" class="input" required v-model.trim="password">
                    <label for="remember" class="checkbox-label"><input type="checkbox" name="remember" id="remember" class="checkbox" v-model="remember"> Onthoud mij</label>
                    
                </div>

                <div class="buttons">
                    <button type="submit" class="btn">Inloggen</button>
                    <p class="text">Nog geen account? <a href="/register" class="link">Maak er een aan</a></p>
                </div>
            </form>


        </section>
    </main>
</template>
<script>

export default {
    name: 'HomeView',
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
        };
    },
    mounted() {
        this.showSlides();
    },
    methods: {
        showSlides() {
            let fadingtext = document.getElementsByClassName("mySlides");
            fadingtext[0].classList.add("fade")

            setTimeout(() => {fadingtext[0].classList.remove("fade")}, 1000);
            
            if (this.slideIndex >= this.slides.length) { this.slideIndex = 0 }
            if (!this.activeSlide) {
                this.activeSlide = this.slides[0];
            } else {
                this.activeSlide = this.slides[this.slideIndex];
            }
            
            this.slideIndex++;
            setTimeout(this.showSlides, 5000); // Change image every 5 seconds
        },
        login() {
            console.log(this.email, this.password, this.remember);
            
        },
    }
}      
</script>
<style scoped lang="css" >
    .main {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 100%;
    }

    /**slideShow */

    .logo{
        width: 10rem;
        border-radius: 1rem;
    }

    .card{
        background: var(--color-primary-500);
        padding: 2rem;
        width: 100%;
        height: 100%;
        border-radius: 1.5rem;
        display: flex;
    }

    .slideshow-container{
        width: 50%;
        aspect-ratio: 16/9;
        display: flex;
        justify-content: space-between;
        align-items: start;
        padding: 1rem; 
        flex-direction: column;
        background-image: url("public/Placeholder.svg");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 1rem;

        box-shadow: inset 0 -10rem 10rem rgba(0, 0, 0, 0.5);
    }

    .slideshow{
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
        from {opacity: .4} 
        to {opacity: 1}
    }

    .bars{
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

    .text__container .text{
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
    }
    .btn:hover {
        background-color: var(--color-primary-600);
    }
    .link {
        color: var(--color-secondary-500);
    }
    .link:hover {
        text-decoration: underline;
    }
    .link:visited {
        color: var(--color-secondary-300);
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
        .bars{
            justify-content: start;
        }
        .bar{
            width: 3rem;
        }
    }

</style>
