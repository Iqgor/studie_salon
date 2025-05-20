<script>
import { RouterView } from 'vue-router'
import appheader from './components/appHeader.vue'
import { auth } from '@/auth';
import Toast from './components/Toast.vue';
import { sharedfunctions } from './sharedFunctions';
import Muziek from './components/Muziek.vue';

export default {
  name: 'App',
  components: {
    appheader,
    RouterView,
    Toast,
    Muziek
  },
  watch: {
    $route(to, from) {
      if (to.name === 'Landingspage') {
        this.isLoggedIn = false
      } else {
        this.isLoggedIn = auth.isLoggedIn
      }
    }
  },
  data() {
    return {
      isLoggedIn: false,
    }
  },
  mounted() {
    auth.check()
    this.isLoggedIn = auth.isLoggedIn
    const savedTheme = localStorage.getItem('theme') || 'light'
    sharedfunctions.switchTheme(savedTheme)
  }
}

</script>

<template>

  <appheader />
  <RouterView />
  <toast/>
  <Muziek v-if="isLoggedIn"/>
  <footer>

  </footer>
</template>

<style scoped></style>
