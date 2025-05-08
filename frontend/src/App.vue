<script>
import { RouterView } from 'vue-router'
import appheader from './components/appHeader.vue'
import SideWekkers from './components/SideWekkers.vue'
import { onMounted, ref } from 'vue';
import { auth } from '@/auth';
import { sharedfunctions } from './sharedFunctions';

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
  <SideWekkers />
  <footer>

  </footer>
</template>

<style scoped></style>
