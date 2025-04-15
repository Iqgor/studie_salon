<script>
import { RouterLink, RouterView } from 'vue-router'
import appheader from './components/appHeader.vue'
import { onMounted, ref } from 'vue';


export default {
  name: 'App',
  components: {
    appheader,
    RouterLink,
    RouterView
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
      this.currentTheme = theme;
      console.log('hoi')
    }
  },
  mounted() {
    this.switchTheme('dark');
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      this.switchTheme('dark');
      this.currentTheme = 'dark';
    } else {
      this.switchTheme('light');
      this.currentTheme = 'light';
    }
  }
}

</script>

<template>
  <appheader :switchTheme="switchTheme" :currentTheme="currentTheme" />
  <RouterView />
  <footer>

  </footer>
</template>

<style scoped></style>
