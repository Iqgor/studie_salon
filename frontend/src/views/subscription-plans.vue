<template>
    <main class="main">
        <div class="container">

            <section class="getplan" v-if="selectedPlan.id">
                <form @submit.prevent="checkForm()" class="form">
                    <div class="getplan__subject" v-if="!donthaveAccount">
                        <p>Login gegevens</p>
                        <p class="getplan__subject_btn" @click="donthaveAccount = true">Geen account?</p>
                    </div>

                    <div class="getplan__subject" v-else>
                        <p>Maak een account aan</p>
                        <p class="getplan__subject_btn" @click="donthaveAccount = false">Heb je al een account?</p>
                    </div>

                    <div class="getplan__input_wrapper">
                        <input v-if="donthaveAccount" type="text" placeholder="Naam" :required="donthaveAccount"
                            v-model="name">
                        <input type="email" placeholder="Email" required v-model="email">
                        <input type="password" placeholder="Wachtwoord" required v-model="password"
                            v-if="!donthaveAccount">
                        <input v-if="showOtp" type="text" placeholder="OTP code" :required="showOtp" v-model="otp">



                        <label v-if="donthaveAccount" for="terms" class="getplan__input_label">
                            <input type="checkbox" name="terms" id="terms" v-model="acceptTerms" required>
                            <p>Ik ga akkoord met de <RouterLink to="/privacy-verklaring" target="_blank">privacy verklaring</RouterLink>
                                en <RouterLink target="_blank" to="/gebruikers-voorwaarden">gebruikersvoorwaarden</RouterLink></p>
                        </label>

                    </div>

                    <div class="getplan__subject">
                        <p>{{ selectedPlan.is_trial || donthaveAccount ? '' : 'Betaal gegevens' }}</p>
                    </div>

                    <div class="getplan__buttons" v-if="!selectedPlan.is_trial && !donthaveAccount">
                        <button class="getplan__button" type="button" @click="paymentChoice = 'ideal'"
                            :class="{ 'getplan__button_active': paymentChoice == 'ideal' }">
                            <i class="fa-brands fa-ideal"></i>
                            <p>Ideal</p>
                        </button>
                        <button class="getplan__button" type="button" @click="paymentChoice = 'creditcard'"
                            :class="{ 'getplan__button_active': paymentChoice == 'creditcard' }">
                            <i class="fa-solid fa-credit-card"></i>
                            <p>Creditcard</p>
                        </button>


                    </div>

                    <div class="small_txt">
                        <p v-if="donthaveAccount">
                            Als je een account aanmaakt krijg je een tijdelijk wachtwoord toegestuurd naar je email.

                        </p>
                    </div>

                    <button class="getplan__send">{{ donthaveAccount ? 'Registreer' : 'Neem abonnement' }}</button>
                </form>


                <article class="detail">

                    <h1 class="detail__name">{{ selectedPlan.name }}</h1>
                    <div class="detail__buttons" v-if="!selectedPlan.is_trial">

                        <button class="detail__button"
                            :class="{ 'detail__button_active': selectedperiode == 'maandelijks' }"
                            @click="changePeriod('maandelijks')">

                            <div class="detail__sale" v-if="selectedPlan.sale && selectedPlan.sale_type">
                                <p class="detail__sale_p">{{ selectedPlan.sale }}{{ selectedPlan.sale_type }}</p>
                            </div>
                            <h4 class="detail__button_period">Maandelijks</h4>
                            <h3 class="detail__button_price"><i class="fa-solid fa-euro-sign"></i>{{ selectedPlan.price
                            }}</h3>
                            <h5 class="detail__button_description"></h5>
                        </button>

                        <button :class="{ 'detail__button_active': selectedperiode == 'jaarlijks' }"
                            class="detail__button" @click="changePeriod('jaarlijks')">

                            <div class="detail__sale" v-if="selectedPlan.sale && selectedPlan.sale_type">
                                <p class="detail__sale_p">{{ selectedPlan.sale_type == '$' ? selectedPlan.sale * 12 :
                                    selectedPlan.sale }}{{ selectedPlan.sale_type }}</p>
                            </div>
                            <h4 class="detail__button_period">jaarlijks</h4>
                            <h3 class="detail__button_price"><i class="fa-solid fa-euro-sign"></i>{{ selectedPlan.price
                                * 12 }}</h3>
                            <h5 class="detail__button_description"></h5>
                        </button>
                    </div>
                    <div class="detail__pricing" v-if="!selectedPlan.is_trial">
                        <div class="detail__pricing_wrapper">
                            <div class="detail__pricing_name">Subtotaal</div>
                            <div class="detail__pricing_price" v-if="selectedperiode == 'maandelijks'">{{
                                selectedPlan.price }}</div>
                            <div class="detail__pricing_price" v-else>{{ selectedPlan.price * 12 }}</div>
                        </div>
                        <div class="detail__pricing_wrapper">
                            <div class="detail__pricing_name">korting</div>
                            <div class="detail__pricing_price"> -{{ saleAmount }} {{ selectedPlan.sale_type }}</div>
                        </div>
                        <div class="detail__pricing_wrapper">
                            <div class="detail__pricing_name">totaal</div>

                            <div class="detail__pricing_price">
                                {{ finalPrice }}
                            </div>

                        </div>
                    </div>
                    <p v-if="selectedPlan.is_trial">
                        neem een tijdelijk abbonement van 3 dagen
                    </p>
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




            <section class="subscription__container">

                <div v-for="plan in subscription" :key="plan.id" class="plan__card">

                    <div class="plan__sale" v-if="plan.sale && plan.sale_type">
                        <p class="plan__sale_p">{{ plan.sale }}{{ plan.sale_type }}</p>
                    </div>

                    <div class="card__header">
                        <span>
                            <i v-html="plan.icon"></i>
                            <h2>{{ plan.name }}</h2>
                        </span>
                        <span class="rank">{{ plan.rank }}</span>
                    </div>

                    <p class="description">{{ plan.description }}</p>
                    <p class="price__container">
                        <span class="price" v-if="plan.sale && plan.sale_type"
                            :class="{ 'price__inactive': plan.sale && plan.sale_type }"><i
                                class="fa-solid fa-euro-sign"></i> {{ plan.price }}</span>

                        <span class="price" v-if="plan.sale && plan.sale_type === '%'"><i
                                class="fa-solid fa-euro-sign"></i>{{
                                    (plan.price - (plan.price * plan.sale / 100)).toFixed(2) }}</span>

                        <span class="price" v-else><i class="fa-solid fa-euro-sign"></i>{{
                            (plan.price - plan.sale).toFixed(2) }}</span>
                    </p>
                    <hr>


                    <ul class="card__list">
                        <li v-for="feature in plan.features" :key="feature.name" v-show="feature.display">
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
import { auth } from '@/auth';
import { sharedfunctions } from '@/sharedFunctions';
import { toastService } from '@/services/toastService';

export default {

    name: 'HomeView',
    setup() {
        return { auth }
    },
    components: {
    },
    data() {
        return {
            subscription: [],
            selectedPlan: [],
            selectedperiode: 'maandelijks',
            donthaveAccount: false,
            name: '',
            email: '',
            password: '',
            repeatPassword: '',
            paymentChoice: '',
            showOtp: false,
            otp: '',
            canPay: false,
            acceptTerms: false,
        };
    },
    mounted() {
        this.fetchsubscription();
    },
    methods: {


        async fetchsubscription() {
            try {
                const response = await fetch(`${import.meta.env.VITE_APP_API_URL}backend/subscriptions`);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                this.subscription = data.subscriptions;
            } catch (error) {
                console.error('Error fetching subscriptions:', error);
            }
        },

        choosePlan(plan) {
            this.selectedPlan = plan;
        },

        changePeriod(period) {
            this.selectedperiode = period;
        },

        checkForm() {

            if (this.donthaveAccount) {
                this.createAccount()
            } else if (this.showOtp) {
                this.sendOtp()
            }
            else {
                this.checkAccount()
            }
        },
        async checkAccount() {

            try {
                const response = await fetch(`${import.meta.env.VITE_APP_API_URL}backend/login`, {
                    method: 'POST',
                    body: JSON.stringify({
                        email: this.email,
                        password: this.password,
                        gettingSub: true
                    })
                })

                let incommingdata = await response.json()
                if (incommingdata?.title && incommingdata?.message) {
                    toastService.addToast(incommingdata?.title, incommingdata?.message, incommingdata?.type)
                }


                if (incommingdata?.token) {
                    auth.setAuth(true, incommingdata?.token)
                    this.checkhowToSub()

                }
                else if (incommingdata?.otp_required) {
                    this.showOtp = true

                }

                if (incommingdata?.temp_used == true) {
                    auth.temp_used = true
                    localStorage.setItem('temp_used', true)
                }

            } catch (err) {
                // error handling hier
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
                toastService.addToast(incommingdata?.title, incommingdata?.message, incommingdata?.type)


                if (incommingdata?.token) {
                    auth.setAuth(true, incommingdata?.token)
                    this.checkhowToSub()
                }
            } catch (err) {
                // error handling hier
            }
        },
        async createAccount() {
            try {
                const response = await fetch(`${import.meta.env.VITE_APP_API_URL}backend/register`, {
                    method: 'POST',
                    body: JSON.stringify({
                        name: this.name,
                        email: this.email
                    })
                })

                let incommingdata = await response.json()
                toastService.addToast(incommingdata?.title, incommingdata?.message, incommingdata?.type)
                this.donthaveAccount = false
            } catch (err) {
                // error handling hier
            }
        },
        checkhowToSub() {

            if (this.selectedPlan.is_trial) {
                this.subscribeToTrial()
            } else {
                if (this.paymentChoice == 'ideal') {
                    this.subscribeToIdeal()
                } else if (this.paymentChoice == 'creditcard') {
                    this.subscribeToCreditCard()
                }
            }
        },
        async subscribeToTrial() {

            try {
                const response = await fetch(`${import.meta.env.VITE_APP_API_URL}backend/subscribeTrial`, {
                    method: 'POST',
                    body: JSON.stringify({
                        plan_id: this.selectedPlan.id,
                        user_id: auth.user.id,
                    })
                });

                let incommingdata = await response.json();
                auth.checkAction(incommingdata?.action)
                toastService.addToast(incommingdata?.title, incommingdata?.message, incommingdata?.type)
                if (incommingdata?.type == 'success') {
                    this.$router.push('/')
                }
            } catch (err) {
                console.error('Error subscribing to trial:', err);
            }
        },



    },
    computed: {
        finalPrice() {
            let basePrice = this.selectedPlan.price;
            const sale = this.selectedPlan.sale || 0;
            const type = this.selectedPlan.sale_type;

            const isMonthly = this.selectedperiode === 'maandelijks';

            if (!isMonthly) {
                basePrice *= 12;
            }

            let discount = 0;

            if (type === '%') {
                discount = basePrice * (sale / 100);
            } else if (type === '$') {
                discount = isMonthly ? sale : sale * 12;
            }

            const finalPrice = basePrice - discount;

            return finalPrice > 0 ? finalPrice.toFixed(2) : '0.00';
        },
        saleAmount() {
            const sale = this.selectedPlan.sale || 0;
            const type = this.selectedPlan.sale_type;
            const isMonthly = this.selectedperiode === 'maandelijks';


            if (type === '$') {
                return (isMonthly ? sale : sale * 12)
            } else {
                return sale
            }
        },
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
    gap: 2rem;
}

.subscription__container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    flex-direction: row;
    gap: 3.5rem;
    width: 100%;
    height: 100%;

}

.plan__card {
    background-color: var(--color-card-500);
    border-radius: 8px;
    box-shadow: 0 0.2rem 0.4rem rgba(0, 0, 0, 0.1);
    padding: 2rem;
    min-width: 30rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-width: 30rem;
    position: relative;
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
    color: var(--color-secondary-500);
}

.card__header .rank {
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--color-text);
    background-color: var(--color-secondary-500);
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
    max-width: max-content;
    max-height: max-content;
    aspect-ratio: 1/1;
    border-radius: 100%;
    padding: 1rem;
    margin-right: 0.5rem;
    background: var(--color-secondary-500);
}

.card__list-titel {
    font-size: 1.75rem;
    font-weight: bold;
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
    color: var(--color-text);
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
    color: var(--color-secondary-500);
    font-size: 1.6rem;
    font-weight: bold;
}

.description {
    font-size: 1.25rem;
}

.price {
    font-size: 2rem;
    font-weight: bold;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
}

.price__container {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.price__inactive {
    text-decoration: line-through;
    color: var(--color-text);
    text-decoration-color: var(--color-primary-500);

}

.price__inactive i {
    text-decoration: line-through;
    text-decoration-color: var(--color-primary-500);
    color: var(--color-text);
}

.getplan {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: start;
    gap: 2rem;
    margin-bottom: 2rem;
}

.plan__sale {
    position: absolute;
    top: -2rem;
    right: -2rem;
    background-color: var(--color-secondary-500);
    color: var(--color-text);
    padding: 1rem;
    border-radius: 0.4rem;
}

.plan__sale_p {
    font-size: 1.5rem;
    font-weight: bold;
}



.form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    height: 100%;
    width: max-content;
}

.getplan__subject {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
    font-size: 1.5rem;
    font-weight: bold;
}

.getplan__subject_btn {

    color: var(--color-secondary-500);
    cursor: pointer;
    transition: all 0.3s ease-in-out;
}

.getplan__subject_btn:hover {
    color: var(--color-secondary-600);
}

.getplan__subject_btn:focus {
    color: var(--color-secondary-700);
}

.getplan__input_wrapper {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: start;
    gap: 2rem;
}

.getplan__input_wrapper input {
    padding: 1rem;
    border-radius: 0.4rem;
    border: none;
    background-color: var(--color-card-500);
    color: var(--color-text);
    font-size: 1.5rem;
    font-weight: bold;
    width: 100%;
    transition: all 0.3s ease-in-out;
}

.getplan__input_wrapper input:focus {
    background-color: var(--color-card-700);
    border: none;
    outline: none;
    color: var(--color-background-100);
}

.getplan__input_wrapper input::placeholder {
    color: var(--color-text);
    font-size: 1.5rem;
    font-weight: bold;
}

.getplan__input_wrapper input:focus::placeholder {
    color: var(--color-background-100);
    font-size: 1.5rem;
    font-weight: bold;
}

.getplan__input_label {
    display: flex;
    flex-direction: row;
    justify-content: start;
    align-items: start;
    gap: 1rem;
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--color-text);
    width: 35rem;
}

.getplan__input_label input {
    max-width: 2rem;
    height: 2rem;
    border-radius: 0.4rem;
    border: none;
    background-color: var(--color-background-400);
    color: var(--color-text);
    font-size: 1.5rem;
    font-weight: bold;
}

.getplan__input_label input:focus {
    background-color: var(--color-primary-500);
    border: none;
    outline: none;
    color: var(--color-background-100);
}

.getplan__input_label input:checked {
    background-color: var(--color-primary-500);
    border: none;
    outline: none;
    color: var(--color-background-100);
}

.getplan__input_label p {
    font-size: 1.5rem;
    font-weight: bold;
    color: var(--color-text);
}

.getplan__input_label a {
    color: var(--color-secondary-500);
    font-size: 1.5rem;
    font-weight: bold;
    text-decoration: none;
}

.getplan__input_label a:hover {
    color: var(--color-secondary-600);
}

.getplan__buttons {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
}

.getplan__button {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    padding: 2rem;
    border-radius: 1.5rem;
    background-color: var(--color-card-500);
    color: var(--color-text);
    font-size: 1.5rem;
    font-weight: bold;
    transition: all 0.3s ease-in-out;
    cursor: pointer;
    border: none;
    min-width: 15rem;
}

.getplan__button i {
    font-size: 3.5rem;
    overflow: hidden;
    color: var(--color-primary-500);
}

.getplan__button p {
    font-size: 2rem;
}

.getplan__button:hover {
    background-color: var(--color-card-300);
    color: var(--color-text);
}

.getplan__button:focus {
    background-color: var(--color-card-600);
    border: none;
    outline: none;
}

.getplan__button_active {
    background-color: var(--color-card-700);
}

.getplan__send {
    background-color: var(--color-primary-500);
    color: var(--color-text);
    padding: 1rem 2rem;
    border-radius: 0.4rem;
    border: none;
    cursor: pointer;
    font-size: 1.2rem;
    font-weight: bold;
    transition: all 0.3s ease-in-out;
    justify-self: end;
}

.getplan__send:hover {
    background-color: var(--color-primary-600);
}

.getplan__send:active {
    background-color: var(--color-primary-700);
}

.getplan__send:focus {
    background-color: var(--color-primary-600);
    border: none;
    outline: none;
    color: var(--color-background-500);
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
    background-color: var(--color-card-500);
    color: var(--color-text);
    font-size: 1.5rem;
    font-weight: bold;
    transition: all 0.3s ease-in-out;
    cursor: pointer;
    border: none;
    width: 100%;
    min-width: 15rem;
    position: relative;
}

.detail__button .detail__sale {
    position: absolute;
    top: -2rem;
    right: -1rem;
    background-color: var(--color-secondary-500);
    color: var(--color-text);
    padding: 1rem;
    border-radius: 0.4rem;
}

.detail__button:hover {
    background-color: var(--color-card-300);
    color: var(--color-text);
}

.detail__button:focus {
    background-color: var(--color-card-600);
    border: none;
    outline: none;
    color: var(--color-text);
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

.small_txt {
    font-size: 0.8rem;
    color: var(--color-text);
}

hr {
    border: 1px solid var(--color-text);
    width: 100%;
}
</style>
