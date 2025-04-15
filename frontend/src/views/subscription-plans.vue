<template>
    <main class="main">
        <div class="container">
            <div class="plans__container">
                <div v-for="plan in plans" :key="plan.id" class="plan__card">
                    <div class="card__header">
                        <span>
                            <i :class="plan.icon"></i>
                            <h2>{{ plan.name }}</h2>
                        </span>
                        <span class="rank">{{ plan.rank }}</span>
                    </div>

                    <p class="description">{{ plan.description }}</p>
                    <p><i class="fa-solid fa-euro-sign"></i><span class="price">{{ plan.price }}</span>/{{ plan.billing_cycle }}</p>
                    <hr>
                    
                    
                    <ul class="card__list">
                        <li v-for="feature in plan.features" :key="feature.name" >
                            <div class="feature__icon" v-html="feature.icon"></div> 
                            <span>
                                <p class="card__list-titel">{{ feature.name }}</p> 
                                <p class="card__list-description">{{ feature.description }}</p>
                            </span> 
                        </li>
                    </ul>
                    <button class="btn">Neem Abonemment</button>
                </div>
            </div>
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
            plans: [
                {
                    id: 1,
                    name: 'Basic Plan',
                    icon: 'fa-solid fa-user',
                    rank: 'Basic',
                    description: 'Basic plan for individuals',
                    price: 10,
                    currency: 'USD',
                    features: [
                        {
                            icon: '<i class="fa-solid fa-stopwatch"></i>',
                            name: 'Stop watch',
                            description: 'Om je tijd bij te houden',
                            active: 1
                        },
                        {
                            icon: '<i class="fa-solid fa-music"></i>',
                            name: 'Muziek',
                            description: 'Om je te helpen studeren',
                            active: 1
                        },
                        {
                            icon: '<i class="fa-solid fa-stopwatch-20"></i>',
                            name: 'Pomodoro',
                            description: 'Om je te helpen studeren',
                            active: 0
                        }
                    ],
                    billing_cycle: 'Monthly',
                    is_active: 1,
                    trial_period: 7,
                    created_at: '2023-01-01',
                    updated_at: '2023-01-01',
                },
                {
                    id: 2,
                    name: 'premium Plan',
                    icon: 'fa-solid fa-user',
                    rank: 'premium',
                    description: 'premium plan for the elite',
                    price: 100,
                    currency: 'euro',
                    features: [
                        {
                            icon: '<i class="fa-solid fa-stopwatch"></i>',
                            name: 'Stop watch',
                            description: 'Om je tijd bij te houden',
                            active: 1
                        },
                        {
                            icon: '<i class="fa-solid fa-music"></i>',
                            name: 'Muziek',
                            description: 'Om je te helpen studeren',
                            active: 1
                        },
                        {
                            icon: '<i class="fa-solid fa-stopwatch-20"></i>',
                            name: 'Pomodoro',
                            description: 'Om je te helpen studeren',
                            active: 0
                        }
                    ],
                    billing_cycle: 'yearly',
                    is_active: 1,
                    trial_period: 7,
                    created_at: '2023-01-01',
                    updated_at: '2023-01-01',
                },
                {
                    id: 2,
                    name: 'premium Plan',
                    icon: 'fa-solid fa-user',
                    rank: 'premium',
                    description: 'premium plan for the elite',
                    price: 100,
                    currency: 'euro',
                    features: [
                        {
                            icon: '<i class="fa-solid fa-stopwatch"></i>',
                            name: 'Stop watch',
                            description: 'Om je tijd bij te houden',
                            active: 1
                        },
                        {
                            icon: '<i class="fa-solid fa-music"></i>',
                            name: 'Muziek',
                            description: 'Om je te helpen studeren',
                            active: 1
                        },
                        {
                            icon: '<i class="fa-solid fa-stopwatch-20"></i>',
                            name: 'Pomodoro',
                            description: 'Om je te helpen studeren',
                            active: 0
                        }
                    ],
                    billing_cycle: 'yearly',
                    is_active: 1,
                    trial_period: 7,
                    created_at: '2023-01-01',
                    updated_at: '2023-01-01',
                },
                {
                    id: 2,
                    name: 'premium Plan',
                    icon: 'fa-solid fa-user',
                    rank: 'premium',
                    description: 'premium plan for the elite',
                    price: 100,
                    currency: 'euro',
                    features: [
                        {
                            icon: '<i class="fa-solid fa-stopwatch"></i>',
                            name: 'Stop watch',
                            description: 'Om je tijd bij te houden',
                            active: 1
                        },
                        {
                            icon: '<i class="fa-solid fa-music"></i>',
                            name: 'Muziek',
                            description: 'Om je te helpen studeren',
                            active: 1
                        },
                        {
                            icon: '<i class="fa-solid fa-stopwatch-20"></i>',
                            name: 'Pomodoro',
                            description: 'Om je te helpen studeren',
                            active: 0
                        }
                    ],
                    billing_cycle: 'yearly',
                    is_active: 1,
                    trial_period: 7,
                    created_at: '2023-01-01',
                    updated_at: '2023-01-01',
                },
                {
                    id: 2,
                    name: 'premium Plan',
                    icon: 'fa-solid fa-user',
                    rank: 'premium',
                    description: 'premium plan for the elite',
                    price: 100,
                    currency: 'euro',
                    features: [
                        {
                            icon: '<i class="fa-solid fa-stopwatch"></i>',
                            name: 'Stop watch',
                            description: 'Om je tijd bij te houden',
                            active: 1
                        },
                        {
                            icon: '<i class="fa-solid fa-music"></i>',
                            name: 'Muziek',
                            description: 'Om je te helpen studeren',
                            active: 1
                        },
                        {
                            icon: '<i class="fa-solid fa-stopwatch-20"></i>',
                            name: 'Pomodoro',
                            description: 'Om je te helpen studeren',
                            active: 0
                        }
                    ],
                    billing_cycle: 'yearly',
                    is_active: 1,
                    trial_period: 7,
                    created_at: '2023-01-01',
                    updated_at: '2023-01-01',
                },
            ],
        };
    },
    mounted() {
        this.showSlides();
    },
    methods: {
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
    background: -webkit-linear-gradient(left,var(--color-primary-500), var(--color-secondary-500));
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

.btn{
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


.fa-euro-sign{
    color: var(--color-primary-500);
    font-size: 1.6rem;
    font-weight: bold;
}

.description{
    font-size: 1.25rem;
}

.price{
    font-size: 2rem;
    font-weight: bold;
    color: var(--color-primary-500);
}


</style>
