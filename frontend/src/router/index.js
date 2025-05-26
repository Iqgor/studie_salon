import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Login from '@/views/login.vue'
import SubscriptionPlans from '@/views/subscription-plans.vue'
import PrivacyVerklaring from '@/views/privacyVerklaring.vue'
import Landingspage from '@/views/Landingspage.vue'
import GebruikersVoorwaarden from '@/views/gebruikersVoorwaarden.vue'
import Profile from '@/views/profile.vue'
import { auth } from '@/auth'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { requiresAuth: true }
    },
    {
      path: '/index',
      name: 'index',
      component: Landingspage,
    },
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/abonnementen',
      name: 'subscription plans',
      component: SubscriptionPlans
    },
    {
      path: '/privacy-verklaring',
      name: 'privacy-verklaring',
      component: PrivacyVerklaring
    },
    {
      path: '/gebruikers-voorwaarden',
      name: 'gebruikers-voorwaarden',
      component: GebruikersVoorwaarden
    },
    {
      path: '/profiel',
      name: 'profiel',
      component: Profile,
      meta: { requiresAuth: true }
    },
    {
      path: '/profiel/:slug',
      name: 'profiel-slug',
      component: Profile,
      meta: { requiresAuth: true },
      props: true
    },
    {
      path:'/admin',
      name: 'admin',
      component: () => import('@/views/Admin.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/:slug',
      name: 'voor af',
      component: () => import('@/views/VoorAf.vue')
    },
    {
      path: '/:slug:/:slug',
      name: 'tekst',
      component: () => import('@/views/Tekst.vue')
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/views/404.vue')
    }
  ],
})


// global navigatie beveiliging
router.beforeEach((to, from, next) => {

  // redirect in gelogde gebruiker naar profiel
  if (to.path === '/login' && auth.isLoggedIn) {
    return next('/profiel')
  }

  // forceer profiel pagina als gebruiker temp_password heeft gebruikt
  if (auth.temp_used && to.path !== '/profiel/wachtwoord-wijzigen') {
    return next('/profiel/wachtwoord-wijzigen')
  }

  // redirect naar login pagina als gebruiker niet is ingelogd op pagina's die auth vereisen
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!auth.isLoggedIn) {
      return next('/login')
    }
  }

  next()
})


export default router
