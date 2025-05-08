<script>
import { RouterView } from 'vue-router'
import appheader from './components/appHeader.vue'
import SideWekkers from './components/SideWekkers.vue'
import { onMounted, ref } from 'vue';
import { auth } from '@/auth';

export default {
  name: 'App',
  components: {
    appheader,
    RouterView,
    SideWekkers
  },
  data() {
    return {
      currentTheme: '',
    }
  }
  ,
  methods: {
    switchTheme(theme) {
      document.documentElement.className = `theme-${theme}`;
      localStorage.setItem('theme', theme);
      this.currentTheme = theme;
    },

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
    this.checkTheme();
    auth.check()
  }
}

</script>

<template>

  <appheader :switchTheme="switchTheme" :currentTheme="currentTheme"/>
  <RouterView />
  <SideWekkers />
  <footer>

  </footer>
</template>

<style scoped></style>
