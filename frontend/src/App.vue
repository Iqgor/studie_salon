<script setup>
  import { RouterLink, RouterView } from 'vue-router'
import appheader from './components/appHeader.vue'
import { onMounted } from 'vue';

onMounted(() => {
    switchTheme('light');
  });

  function switchTheme(theme) {
    document.documentElement.className = `theme-${theme}`;
  }

//voor de kleuren schema
//    if (currentUrl.includes('converzion')) {
//     body.classList.add('converzion')
//     favicon.href = 'images/converzion.png'
//     setCookie('converziontoken', true)
//    } else {
//     body.classList.remove('converzion')
//     favicon.href = 'images/Logo.png'
//     setCookie('converziontoken', false)
//    }
</script>

<template>
  <appheader />
  <RouterView />
  <footer>

  </footer>
</template>

<style scoped></style>
