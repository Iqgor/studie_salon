<template>
    <main class="main">
        <div class="container">

            <section class="getplan" v-if="selectedPlan.id">
                <form @submit.prevent="" class="form"></form>


                <article class="detail" >
                    <h1 class="detail__name">{{ selectedPlan.name }}</h1>
                    <div class="detail__buttons">

                        <button class="detail__button" :class="{ 'detail__button_active': selectedperiode == 'maandelijks' }" @click="changePeriod('maandelijks')">	
                            <h4 class="detail__button_period">Maandelijks</h4>
                            <h3 class="detail__button_price"><i class="fa-solid fa-euro-sign"></i>{{ selectedPlan.price
                                }}</h3>
                            <h5 class="detail__button_description">tekst</h5>
                        </button>

                        <button :class="{ 'detail__button_active': selectedperiode == 'jaarlijks' }" class="detail__button" @click="changePeriod('jaarlijks')">
                            <h4 class="detail__button_period">jaarlijks</h4>
                            <h3 class="detail__button_price"><i class="fa-solid fa-euro-sign"></i>{{ selectedPlan.price
                                * 12 }}</h3>
                            <h5 class="detail__button_description">tekst</h5>
                        </button>
                    </div>
                    <div class="detail__pricing">
                        <div class="detail__pricing_wrapper">
                            <div class="detail__pricing_name">subtotaal</div>
                            <div class="detail__pricing_price">prijs</div>
                        </div>
                        <div class="detail__pricing_wrapper">
                            <div class="detail__pricing_name">korting?</div>
                            <div class="detail__pricing_price">-prijs</div>
                        </div>
                        <div class="detail__pricing_wrapper">
                            <div class="detail__pricing_name">totaal</div>
                            <div class="detail__pricing_price">prijs</div>
                        </div>
                    </div>
                    <hr>
                    <div class="detail__feature">
                        <div class="detail__feature_wrapper" v-for="feature in selectedPlan.features"
                            :key="feature.name">
                            <div class="detail__feature_icon" v-html="feature.icon"></div>
                            <div class="detail__feature_name">{{ feature.name }}</div>
                        </div>

                    </div>
                </article>
            </section>




            <section class="plans__container">
                <div v-for="plan in plans" :key="plan.id" class="plan__card">
                    <div class="card__header">
                        <span>
                            <i v-html="plan.icon"></i>
                            <h2>{{ plan.name }}</h2>
                        </span>
                        <span class="rank">{{ plan.rank }}</span>
                    </div>

                    <p class="description">{{ plan.description }}</p>
                    <p><i class="fa-solid fa-euro-sign"></i><span class="price">{{ plan.price }}</span></p>
                    <hr>


                    <ul class="card__list">
                        <li v-for="feature in plan.features" :key="feature.name">
                            <div class="feature__icon" v-html="feature.icon"></div>
                            <span>
                                <p class="card__list-titel">{{ feature.name }}</p>
                                <p class="card__list-description">{{ feature.description }}</p>
                            </span>
                        </li>
                    </ul>
                    <button class="btn" @click="choosePlan(plan)">Neem Abonemment</button>
                </div>
            </section>
        </div>
    </main>
</template>
<script>

export default {
    name: 'HomeView',
    components: {
    },
    data() {
        return {
            plans: [],
            selectedPlan: [],
            selectedperiode: 'maandelijks',
        };
    },
    mounted() {
        // Fetch plans from the API
        this.fetchPlans();
    },
    methods: {
        async fetchPlans() {
            try {
                const response = await fetch('http://localhost/studie_salon/backend/plans');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                this.plans = data.plans
            } catch (error) {
                console.error('Error fetching plans:', error);
            }
        },

        choosePlan(plan) {
            console.log(plan);
            this.selectedPlan = plan;
            this.changePrice()
        },

        changePeriod(period) {
            this.selectedperiode = period;
            
        }
    }
}      
</script>
<style scoped lang="css">
.main {
    padding: 2rem;
}

.container {
    display: flex;
    flex-direction: column;
}

.plans__container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    flex-direction: row;
    gap: 2rem;
    width: 100%;
    height: 100%;
}

.plan__card {
    background-color: var(--color-background-400);
    border-radius: 8px;
    box-shadow: 0 0.2rem 0.4rem rgba(0, 0, 0, 0.1);
    padding: 2rem;
    min-width: 30rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.card__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card__header span {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.card__header span h2 {
    font-size: 1.5rem;
    margin: 0;
}

.card__header span i {
    font-size: 2rem;
    color: var(--color-primary-500);
}

.card__header .rank {
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--color-background-100);
    background-color: var(--color-primary-500);
    padding: 0.5rem 1rem;
    border-radius: 4px;
}

.plan__card ul {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.plan__card li {
    display: flex;
    align-items: center;
    margin: 0.5rem 0;
    gap: 2rem;

}

.feature__icon {
    max-width: 2.5rem;
    min-width: 2.5rem;
    max-height: 2rem;
    margin-right: 0.5rem;
    background: -webkit-linear-gradient(left, var(--color-primary-500), var(--color-secondary-500));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    overflow: hidden;
}

.card__list-titel {
    font-size: 1.75rem;
    font-weight: bold;
    color: var(--color-primary-500);
}

.card__list-description {
    font-size: 1.2rem;
    color: var(--color-text);
    font-weight: bold;
}


.plan__card li span {
    display: flex;
    align-items: start;
    flex-direction: column;
}

.btn {
    background-color: var(--color-primary-500);
    color: var(--color-background-100);
    padding: 1rem 2rem;
    border-radius: 0.4rem;
    border: none;
    cursor: pointer;
    font-size: 1.2rem;
    font-weight: bold;
    transition: all 0.3s ease-in-out;
    margin-top: auto;
}

.btn:hover {
    background-color: var(--color-primary-600);
}

.btn:active {
    background-color: var(--color-primary-700);
}

.fa-euro-sign {
    color: var(--color-primary-500);
    font-size: 1.6rem;
    font-weight: bold;
}

.description {
    font-size: 1.25rem;
}

.price {
    font-size: 2rem;
    font-weight: bold;
    color: var(--color-primary-500);
}

.getplan {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
    margin-bottom: 2rem;
}

.form {
    display: flex;
    flex-direction: column;
    gap: 2rem;


    min-width: 5rem;
    background: blue;
}



.detail {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    max-width: max-content;
    height: 100%;
    background: var(--color-primary-500);
    border-radius: 2.5rem;
    padding: 2rem;
}

.detail__name {
    font-size: 2rem;
    font-weight: bold;
    color: var(--color-text);
}

.detail__buttons {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.detail__button {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: start;
    gap: 1rem;
    padding: 2rem;
    border-radius: 1.5rem;
    background-color: var(--color-background-400);
    color: var(--color-text);
    font-size: 1.5rem;
    font-weight: bold;
    transition: all 0.3s ease-in-out;
    cursor: pointer;
    border: none;
    width: 100%;
    min-width: 15rem;
}

.detail__button:hover {
    background-color: var(--color-primary-500);
    color: var(--color-background-100);
}

.detail__button:focus {
    background-color: var(--color-primary-600);
    border: none;
    outline: none;
    color: var(--color-background-500);
}
.detail__button_active {
    background-color: var(--color-primary-700);
    color: var(--color-background-100);
}

.detail__button_period {
    font-size: 1.5rem;
    font-weight: bold;
}

.detail__button_price {
    font-size: 2rem;
    font-weight: bold;
}

.detail__button_description {
    font-size: 1.2rem;
    font-weight: bold;
}


.detail__pricing {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.detail__pricing_wrapper {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.detail__pricing_name {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--color-text);
}

.detail__pricing_price {
    font-size: 2rem;
    font-weight: bold;
    color: var(--color-text);
}


.detail__feature {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.detail__feature_wrapper {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.detail__feature_icon {
    font-size: 3.5rem;
    overflow: hidden;
}

.detail__feature_name {
    font-size: 2rem;
    font-weight: bold;
    color: var(--color-text);
}
</style>
