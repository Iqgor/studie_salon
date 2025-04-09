<template>
    <main class="main">
        <section class="card">
            <div class="slideshow-container">
                <img src="/public/logo.png" alt="logo" class="logo">

                <div class="slideshow">
                    <div class="mySlides fade">
                        <div class="text">{{activeSlide }}{{ slideIndex }}</div>
                    </div>

                    <div class="bars">
                        <span class="bar" :class="slideIndex == index+1 ? 'active' : ''" v-for="(slide, index) in slides" ></span>
                    </div>
                </div>

            </div>



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
        };
    },
    mounted() {
        this.showSlides();
    },
    methods: {
        showSlides() {
            if (this.slideIndex >= this.slides.length) { this.slideIndex = 0 }
            let i;
            if (!this.activeSlide) {
                this.activeSlide = this.slides[0];
            } else {
                this.activeSlide = this.slides[this.slideIndex];
            }
            this.slideIndex++;
            setTimeout(this.showSlides, 5000); // Change image every 5 seconds
        },
    }
}      
</script>
<style>
    .main {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 100%;
    }

    .logo{
        width: 10rem;
        border-radius: 1rem;
    }

    .card{
        background: red;
        padding: 2rem;
        width: 100%;
        height: 100%;
        border-radius: 1.5rem;
    }

    .slideshow-container{
        width: 50%;
        height: 30rem;
        display: flex;
        justify-content: space-between;
        align-items: start;
        padding: 1rem; 
        flex-direction: column;
        background-image: url('https://images.pexels.com/photos/957040/night-photograph-starry-sky-night-sky-star-957040.jpeg?auto=compress&cs=tinysrgb&w=600');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 1rem;
    }

    .slideshow{
        display: flex;
        flex-direction: column;
        gap: 2rem;
        width: 100%;
        justify-content: start;
        align-items: start;
    }

    .text {
        color: white;
        width: 75%;
        text-align: start;
        display: block;
    }

    .fade {
        animation-name: fade;
        animation-duration: 3s;
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
        background-color: #24ABCD;
    }
</style>
