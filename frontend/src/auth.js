// auth.js.
import { reactive } from 'vue'
import { jwtDecode } from 'jwt-decode'


//TODO import { getCookie, deleteCookie } from "./cookie.js"

export const auth = reactive({
    //* is user logged in
    isLoggedIn: false,
    //* data van user (id, email, etc)
    user: {},
    //* de abonnement van de user
    subscriptionId: null,
    subscriptionName: null,
    subscriptionFeatures: [],

    //* token die je binnenkrijgt
    token: null,
    //* om dingen te verstoppen als je niet op localhost bent
    hidden: false,
    //* om te checken of je je temp was gebruikt
    temp_used: false,
    //*
    bearerToken: ``,




    //? zodra je wilt inloggen, krijg je binnen of je bent ingelogged en krijg je de token
    setAuth(logged, token) {
        if (logged === true) {

            //* Meld dat je bent ingelogd
            this.isLoggedIn = logged


            //* Krijgt je tokens binnen en word het leesbaar
            if (this.isLoggedIn) {
                this.token = token
                localStorage.setItem('token', token)
                this.check()
            }
        } else if (token) {
            this.token = token
            this.check()
        }
        else {
            this.isLoggedIn = false
            this.token = null
        }
    },
    check() {
        //* haal data uit je token van de local als die er is
        const storageToken = this.token || localStorage.getItem('token')
        if (storageToken) {
            this.isLoggedIn = true
            this.token = storageToken
            localStorage.setItem('token', this.token)
            let data = jwtDecode(storageToken)
            this.user = data.user
            this.temp_used = localStorage.getItem('temp_used') || false
            this.bearerToken = `Bearer ${this.token}`

            //this.getSubscription()


        }


    },
    reset() {
        this.isLoggedIn = false
        this.user = {}
        this.token = null
        this.subscriptionFeatures = []
        this.subscriptionId = null
        this.subscriptionName = null
        this.bearerToken = ''

    },
    async logout() {
        localStorage.removeItem('token')
        localStorage.removeItem('temp_used')
        this.reset()
        window.location.href = '/index';
    },
    isLocalHost() {
        const currentUrl = window.location.href
        if (currentUrl.includes('localhost')) {
            this.hidden = true
        }
    },
    async getSubscription() {

        try {
            const url = new URL(`${import.meta.env.VITE_APP_API_URL}backend/activeSubscription`);
            url.searchParams.append('user_id', this.user.id);

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: this.bearerToken
                }
            });

            const data = await response.json();
            this.subscriptionId = data.id
            this.subscriptionName = data.name
            this.subscriptionFeatures = data.features
            this.checkAction(data?.action)


            if (data?.title == 'Geen abonnement') {
                this.logout()
            }


        } catch (error) {
            console.error(error.message);
        }
    },
    hasFeature(feature) {
        return this.subscriptionFeatures.includes(feature)
    },
    isRole(role) {
        return this.user.role === role
    },
    checkAction(action) {
        if (action == 'logout') {
            this.logout()
        }
        else {
            return
        }
    }
})