<script>
import { RouterView } from 'vue-router'
import appheader from './components/appHeader.vue'
import SideWekkers from './components/SideWekkers.vue'
import { onMounted, ref } from 'vue';
import { auth } from '@/auth';
import Toast from './components/Toast.vue';
import { sharedfunctions } from './sharedFunctions';

export default {
  name: 'App',
  components: {
    appheader,
    RouterView,
    Toast
  },
  data() {
    return {
      currentTheme: '',
    }
  }
  ,
  methods: {


    checkTheme() {
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      if (localStorage.getItem('theme') === 'dark') {
        this.switchTheme('dark');
        this.currentTheme = 'dark';
      } else {
        this.switchTheme('light');
        this.currentTheme = 'light';
      }
    } else if (window.matchMedia('(prefers-color-scheme: light)').matches) {
      if (localStorage.getItem('theme') === 'light') {
        this.switchTheme('light');
        this.currentTheme = 'light';
      } else {
        this.switchTheme('dark');
        this.currentTheme = 'dark';
      }
    } else if (localStorage.getItem('theme') === 'dark') {
      this.switchTheme('dark');
      this.currentTheme = 'dark';
    } else {
      this.switchTheme('light');
      this.currentTheme = 'light';
    }
    }
  },
  mounted() {
    auth.check()
    
    const savedTheme = localStorage.getItem('theme') || 'light'
    sharedfunctions.switchTheme(savedTheme)
  }
}

</script>

<template>

  <appheader :switchTheme="switchTheme" :currentTheme="currentTheme"/>
  <RouterView />
  <toast/>
  <footer>

  </footer>
</template>

<style scoped></style>
