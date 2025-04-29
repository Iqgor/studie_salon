// auth.js.
import { reactive } from 'vue'
import { jwtDecode } from 'jwt-decode'

//TODO import { getCookie, deleteCookie } from "./cookie.js"

export const auth = reactive({
    //* is user logged in
    isLoggedIn: false,
    //* data van user (id, organisatie, project)
    user: {},
    //* tokens die je binnenkrijgt
    token: null,
    //* alleen de rollen
    role: [],
    //* om dingen te verstoppen als je niet op localhost bent
    hidden: false,




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
        } else if(token) {
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
        }
        
        
    },

    reset() {
        (this.isLoggedIn = false),
            (this.user = {}),
            (this.token = null),
            (this.role = [])
    },


    logout() {
        this.isLoggedIn = false
        this.user = {}
        this.token = null
        this.role = []
        localStorage.removeItem('token')
    },
    isLocalHost() {
        const currentUrl = window.location.href
        if (currentUrl.includes('localhost')) {
            this.hidden = true
        }
    }
})