<template>
  <main v-if="succes" class="main">
    <a class="waterFallLink" :href="('/')">
      < Terug naar Home</a>
        <div class="title-views">
          <h2>{{ capitalizeWords(slug.replaceAll('-', ' ')) }}</h2>
          <span class="views"><i :class="{ 'isActive': view == 'list' }" @click="changeView('list')" class="fa-solid fa-list"></i><i
              @click="changeView('table')" :class="{'isActive' : view == 'table'}" class="fa-solid fa-table"></i></span>
        </div>
        <div class="niveaus">
          <button :class="{ 'isActive': clickedNiveau === niveau }" @click="clickedNiveau = niveau"
            v-for="niveau in niveaus">{{ niveau }}</button>
        </div>
        <div :key="type" v-for="(link, type) in links">
          <div v-if="link.length !== 0 && type.toUpperCase() === clickedNiveau" class="links">
            <h3>Teksten voor niveau {{ type.toUpperCase() }}</h3>
            <ul class="view" :class="{'tableView': view === 'table'}">
              <li v-for="(item, index) in link" :key="index">
                <a :href="`${slug}/${item.slug.replace(slug, '').replace('-', '')}`">{{ item.name }}</a>
              </li>
            </ul>
          </div>
        </div>
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

export default {
  name: "SlugView",
  components: {
    Toast
  },
  data() {
    return {
      slug: this.$route.params.slug,
      links: [],
      succes: false,
      loading: true,
      niveaus: ['S', 'M', 'L'],
      clickedNiveau: 'S',
      view: 'list'
    };
  },
  mounted() {
    this.getTekstLinks()
    this.changeView();
  },
  methods: {
    changeView(view = '') {
      if(view.length !== 0){
        localStorage.setItem('viewPreference', JSON.stringify({ slug: this.slug, view }));
        this.view = view;
      }else{
        const viewPreference = localStorage.getItem('viewPreference');
        if (viewPreference !== null) {
          const parsedPreference = JSON.parse(viewPreference);
          if (parsedPreference.slug === this.slug) {
            this.view = parsedPreference.view;
          } else {
            this.view = 'list';
          }
        } else {
          this.view = 'list';
        }
      }
    },
    capitalizeWords(str) {
      return str.replace(/\b\w/g, char => char.toUpperCase());
    },
    async getTekstLinks() {
      const formData = new FormData();
      formData.append('slug', this.slug);
      await fetch(`${import.meta.env.VITE_APP_API_URL}backend/getTekstLinks`, {
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
          if (data.length !== 0) {
            this.succes = true;
            this.loading = false;

            const categorized = {
              l: [],
              m: [],
              s: []

            };

            data.forEach(item => {
              const parts = item.slug.split("-");
              const lastPart = parts[parts.length - 1];

              // Controleer of laatste onderdeel precies één letter is (s, l, m)
              if (['l', 's', 'm'].includes(lastPart)) {
                categorized[lastPart].push(item);
              } else {
                console.warn(`Slug heeft geen herkenbare categorie: ${item.slug}`);
              }
            });
            console.log(categorized);

            this.links = categorized;

          } else {
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
    reformatedLinks(link) {
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
.title-views {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.views {
  display: flex;
  gap: 1rem;
  font-size: 150%;

}

.views>.fa-solid {
  color: var(--color-primary-400);
  transition: all 0.3s ease;
  cursor: pointer;
}

.views>.fa-solid:hover {
  color: var(--color-primary-700);
}

.links {
  margin-top: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.view {
  margin-top: 1rem;
  padding-left: 2rem;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  list-style: none;
}

.tableView {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  padding-left: 0;
  gap: 2rem;
}

.tableView>li {
  flex-shrink: 0;
  white-space: normal;
  word-wrap: break-word;
  background-color: var(--color-primary-300);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
  margin-bottom: 1rem;
  border-radius: 1.5rem;
  padding: 1rem;
  transition: background-color 0.4s ease;

}

.tableView>li:hover {
  background-color: var(--color-primary-200);
}


.tableView a:hover{
  color: var(--color-text);

}


.niveaus {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
  align-items: center;
}

.niveaus>button {
  background: none;
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 1rem 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 150%;

  color: var(--color-text);
}

.isActive{
  color: var(--color-primary-400) !important;

}
.niveaus>button:hover {
  color: var(--color-primary-400) !important;

}
</style>
