import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Login from '@/views/login.vue'
import SubscriptionPlans from '@/views/subscription-plans.vue'
import PrivacyVerklaring from '@/views/privacyVerklaring.vue'
import Landingspage from '@/views/Landingspage.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
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
      path: '/plans',
      name: 'subscription plans',
      component: SubscriptionPlans
    },
    {
      path: '/privacy-verklaring',
      name: 'privacy-verklaring',
      component: PrivacyVerklaring
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

export default router
