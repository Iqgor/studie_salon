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

</script>

<template>
  <appheader />
  <RouterView />
  <footer>

  </footer>
</template>

<style scoped></style>
