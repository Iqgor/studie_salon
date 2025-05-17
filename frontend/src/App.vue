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

  <appheader />
  <RouterView />
  <toast/>
  <footer>

  </footer>
</template>

<style scoped></style>
