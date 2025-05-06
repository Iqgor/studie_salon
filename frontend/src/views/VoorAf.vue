<template>
    <main v-if="succes" class="main">
      <h2>Alle links naar de teksten voor {{ slug.replaceAll('-',' ') }}</h2>
      <div class="links">
        <div :key="type" v-for="(link,type) in links" class="link">
          <h3 v-if="link.length !== 0">Teksten voor {{ type.toUpperCase() }}</h3>
          <ul>
            <li v-for="(linkje, index) in link" :key="index">
              <a :href="`${slug}/${linkje.replace(slug,'').replace('-','')}`">{{ reformatedLinks(linkje) }}</a>
            </li>
          </ul>
        </div>
      </div>
    </main >
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
    name: "SlugView",
    components: {
        Toast
    },
    data() {
        return {
            slug: this.$route.params.slug,
            links:[],
            succes: false,
            loading: true,
        };
    },
    mounted(){
      this.getTekstLinks()
    },
    methods: {
      async getTekstLinks(){
        const formData = new FormData();
        formData.append('slug', this.slug);
        await fetch(`${import.meta.env.VITE_APP_API_URL}backend/getTekstLinks`,{
          method: 'POST',
          body:formData,
        })
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then(data => {
            if(data.slugs.length !== 0){
              this.succes = true;
              this.loading = false;

              const categorized = {
                l: [],
                s: [],
                m: []
              };

              data.slugs.forEach(slug => {
                const parts = slug.split("-");
                const lastPart = parts[parts.length - 1];

                // Controleer of laatste onderdeel precies één letter is (s, l, m)
                if (['l', 's', 'm'].includes(lastPart)) {
                  categorized[lastPart].push(slug);
                } else {
                  console.warn(`Slug heeft geen herkenbare categorie: ${slug}`);
                }
              });


              this.links = categorized;

            }else{
              this.succes = false;
              this.loading = false;
            }
            // Process the data as needed
          })
          .catch(error => {
            this.loading = false;
            console.error('There was a problem with the fetch operation:', error);
          });
      },
      reformatedLinks(link){
        link = link.replace(this.$route.params.slug, ''); // Remove the slug from the link
        const parts = link.split('-');
        parts.pop(); // Remove the last index
        return parts.join(' '); // Join the remaining parts back together
      }
        // Add any methods you need here
    },
}
</script>
<style scoped>
.links{
  margin-top: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.links ul{
  margin-top: 1rem;
  padding-left: 4rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}
</style>
