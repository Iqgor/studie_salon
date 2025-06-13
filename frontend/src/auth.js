// auth.js.
import { reactive } from 'vue'
import { jwtDecode } from 'jwt-decode'
import { toastService } from './services/toastService'


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

            this.isLoggedIn = logged
            if (this.isLoggedIn) {
                this.token = token
                localStorage.setItem('token', token)
                this.check()
            }
        } else if (token) {
            this.token = token
            this.bearerToken = `Bearer ${this.token}`
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

            if (this.bearerToken !== ``) {
                this.getSubscription()
            }



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
            if (data.title && data.message) {
                toastService.addToast(
                    data.title,
                    data.message,
                    data.type || 'info'
                );

            }
            this.checkAction(data?.action)



            if (data?.title == 'Geen abonnement') {
                this.logout()
            }


        } catch (error) {
            console.error(error.message);
        }
    },
    getFeatureAccess(ft) {
        let feature = {};
        const isAdmin = this.user.role === 'admin';

        if(this.subscriptionFeatures !== undefined) {
             feature = this.subscriptionFeatures.find(
                f => f.feature.toLowerCase() === ft.toLowerCase()
            );
        }else if(this.isAdmin = isAdmin){
            feature = { access_level: 'onbeperkt' };
        }
        if (!feature) return null;

        const level = feature.access_level;


        const isFullAccess = isAdmin || level === 'onbeperkt';
     


        const isNiveau = level.includes('niveau');
        const isS = level === 'S_niveau';
        const isSM = level === 'S+M_niveau';

        const tegelMatch = level.match(/^(\d+)_tegels$/);
        const tileCount = tegelMatch ? parseInt(tegelMatch[1]) : null;

        return {
            access_level: level,
            isFullAccess,
            isNiveau,
            isS,
            isSM,
            tileCount
        };
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
