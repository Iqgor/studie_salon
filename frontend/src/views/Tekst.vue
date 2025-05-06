<template>
  <main v-if="succes" class="main">
    <a :href="('/'+slug.split('/')[0])">Terug naar {{slug.split('/')[0]  }}</a>
    <div v-html="tekst"></div>
    <a :href="('/'+slug.split('/')[0])">Terug naar {{slug.split('/')[0]  }}</a>
  </main>
  <div v-else-if="!loading && !succes" class="not-found">
      <h1>404</h1>
      <p>Oops! The page you are looking for does not exist.</p>
      <router-link to="/">Go back to Home</router-link>
    </div>
    <Toast v-if="loading" type="info" message="Laden..." />
</template>
<script>
import Toast from '../components/Toast.vue'

export default{
  name: "TekstView",
  components: {
    Toast
  },
  data() {
    return {
      slug: window.location.href.replace(window.location.origin, '').replace('/',''),
      tekst: '',
      succes: false,
      loading: true,
    };
  },
  mounted() {
    this.getTekst();
  },
  methods: {
    async getTekst() {
      const formData = new FormData();
      formData.append('slug', this.slug.replace('/','-'));
      await fetch(`${import.meta.env.VITE_APP_API_URL}backend/getTekst`, {
        method: 'POST',
        body: formData,
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          if (data.tekst.length !== 0) {
            this.succes = true;
            this.loading = false;



            this.tekst = data.tekst;
          } else {
            this.succes = false;
            this.loading = false;
          }
        })
        .catch(error => {
          this.loading = false;

          console.error('Er is een fout opgetreden:', error);
        });
    }
  }
}

</script>
