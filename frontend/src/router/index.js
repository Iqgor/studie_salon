import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Login from '@/views/login.vue'
import SubscriptionPlans from '@/views/subscription-plans.vue'
import PrivacyVerklaring from '@/views/privacyVerklaring.vue'
import Landingspage from '@/views/Landingspage.vue'
import GebruikersVoorwaarden from '@/views/gebruikersVoorwaarden.vue'
import Profile from '@/views/profile.vue'
import { auth } from '@/auth'
import Disclaimer from '@/views/disclaimer.vue'


// Object to store scroll positions by route fullPath
const savedScrollPositions = {};

const router = createRouter({
  history: createWebHistory(),
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
      path: '/disclaimer',
      name: 'disclaimer',
      component: Disclaimer
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
      meta: { requiresAuth: true, requiresAdmin: true }
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
  scrollBehavior(to, from, savedPosition) {
    // Restore scroll position for the route we are entering, if it exists
    const pos = savedScrollPositions[to.fullPath];
    if (pos) {
      // Remove the saved position after using it
      delete savedScrollPositions[to.fullPath];
      return new Promise((resolve) => {
        setTimeout(() => {
          resolve({ left: pos.left, top: pos.top, behavior: 'smooth' });
        }, 300);
      });
    }
    // Default behavior
    return new Promise((resolve) => {
      setTimeout(() => {
        if (savedPosition) {
          resolve({ left: savedPosition.left, top: savedPosition.top, behavior: 'smooth' });
        } else {
          resolve(false); // Don't change scroll position
        }
      }, 300);
    });
  }
})

// Admin route beveiliging en scroll position saving
router.beforeEach((to, from, next) => {
    if (from && from.fullPath) {
    savedScrollPositions[from.fullPath] = {
      left: window.scrollX,
      top: window.scrollY
    };
  }
  if (to.matched.some(record => record.meta.requiresAdmin)) {
    if (!auth.isLoggedIn || !auth.user || auth.user.role !== 'admin') {
      return next('/login')
    }
  }
  next()
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
