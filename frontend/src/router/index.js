import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Login from '@/views/login.vue'
import SubscriptionPlans from '@/views/subscription-plans.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
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
  ],
})

export default router
