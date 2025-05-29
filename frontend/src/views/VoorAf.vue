<template>
  <main v-if="succes" class="main">
    <RouterLink class="waterFallLink" :to="('/')">
      < Terug naar Home</RouterLink>
        <div class="title-views">
          <h2>
            <span v-if="!isTitleEditClicked">{{  tegelTitle.length>0  ? tegelTitle : capitalizeWords(slug.replaceAll('-', ' ')) }}</span>
            <input v-else type="text" @change="titleEdited = $event.target.value" :value="tegelTitle.length>0  ? tegelTitle : capitalizeWords(slug.replaceAll('-', ' '))" class="editLink" />
            <i v-if="isAdmin" @click="isTitleEditClicked = !isTitleEditClicked" class="fa-solid fa-pen"></i>
            <i v-if="isTitleEditClicked" @click="editTitleLink" class="fa-regular fa-circle-check"></i>
          </h2>
          <span class="views"><i @click="changeView('table')" :class="{ 'isActive': view == 'table' }"
              class="fa-solid fa-table"></i>
            <i :class="{ 'isActive': view == 'list' }" @click="changeView('list')" class="fa-solid fa-list"></i>
          </span>
        </div>
        <div v-if="links.l">
          <div class="niveaus">
            <div v-for="niveau in niveaus">
                <button :class="{ 'isActive': clickedNiveau === niveau }"v-if="links[niveau.toLowerCase()].length > 1"  @click="changeNiveau(niveau)">
                  {{ niveau }}
                </button>
            </div>
          </div>
          <div :key="type" v-for="(link, type) in links">
            <div v-if="link.length !== 0 && type.toUpperCase() === clickedNiveau" class="links">
              <h3>Teksten voor niveau {{ type.toUpperCase() }}</h3>
              <ul class="view" :class="{ 'tableView': view === 'table' }">
                <li v-for="(item, index) in link" :key="index">
                  <voorAfLinks :item="item" :slug="slug" :isAdmin="isAdmin" />
                  <i v-if="!likes.find(liked => liked.slug === item.slug)" @click="likeLink(item,$event)" class="fa-regular fa-heart"></i>
                  <i v-else @click="likeLink(item,$event)" class="fa-solid fa-heart"></i>
                </li>
              </ul>
              <div v-if="isAdmin" class="addLink">
                <h4>Voeg meer links toe</h4>
                <input type="text" v-model="newLink" class="editLink" placeholder="Plaats hier de naam van de link" />
                <button @click="addLink">Voeg link toe</button>
              </div>
            </div>
            <div v-else-if="link.length !== 0 && type === 'anders'" class="links">
              <h3>Links naar:</h3>
              <ul class="view" :class="{ 'tableView': view === 'table' }">
                <li v-for="(item, index) in link" :key="index">
                  <voorAfLinks :item="item" :slug="slug" :isAdmin="isAdmin" />
                  <i v-if="!likes.find(liked => liked.slug === item.slug)" @click="likeLink(item,$event)" class="fa-regular fa-heart"></i>
                  <i v-else @click="likeLink(item,$event)" class="fa-solid fa-heart"></i>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div v-else-if="links.length <= 4" class="links">
          <ul class="view" :class="{ 'tableView': view === 'table' }">
            <li v-for="(item, index) in links" :key="index">
              <voorAfLinks :item="item" :slug="slug" :isAdmin="isAdmin" />
                <i v-if="!likes.find(liked => liked.slug === item.slug)" @click="likeLink(item,$event)" class="fa-regular fa-heart"></i>
                <i v-else @click="likeLink(item,$event)" class="fa-solid fa-heart"></i>
            </li>
          </ul>
          <div v-if="isAdmin" class="addLink">
            <h4>Voeg meer links toe</h4>
            <input type="text" v-model="newLink" class="editLink" placeholder="Plaats hier de naam van de link" />
            <button @click="addLink">Voeg link toe</button>
          </div>
        </div>
  </main>

  <main class="main" v-else-if="isAdmin && !loading">
    <div class="adminText">
      <h2>Geen links gevonden</h2>
      <p>Je kan hieronder een link toevoegen naar een tekst, wanneer je dat hebt gedaan kan je er altijd nog meer
        plaatsen. Sluit elke link af met een -s of -m, etc. (In de editor zie je als placeholder een voorbeeld). Al is
        de tekst alleen op één niveau klik dan <button @click="makeSinglefile">hier</button> Wanneer je de links wil
        plaatsen druk je op de knop hieronder</p>
      <input type="text" v-model="newLink" class="editLink" placeholder="Plaats hier de naam van de link" />
      <button @click="addLink">Voeg link toe</button>
    </div>
  </main>
  <div v-else-if="!loading && !succes" class="not-found">
    <h1>404</h1>
    <p>Oops! Die pagina die je wil bezoeken bestaat niet</p>
    <router-link to="/">Ga terug naar de home pagina</router-link>
  </div>
</template>
<script>

import { auth } from '@/auth';
import VoorAfLinks from '@/components/voorAfLinks.vue'
import { toastService } from '@/services/toastService';

export default {
  name: "SlugView",
  components: {
    VoorAfLinks
  },
  data() {
    return {
      slug: this.$route.params.slug,
      links: [],
      succes: false,
      loading: true,
      niveaus: ['S', 'M', 'L'],
      clickedNiveau: 'S',
      view: 'table',
      isAdmin: false,
      newLink: '',
      likes: [],
      oldLikes: [],
      isTitleEditClicked: false,
      titleEdited: '',
      tegelTitle: '',
    };
  },
  unmounted() {
    this.sendLikes()
  },
  mounted() {
    document.title = `Studie Salon - ${this.slug}`;
    this.getTekstLinks()
    this.changeView();
    this.changeNiveau();
    this.getLikes()
    this.isAdmin = auth.user.role === 'admin';
  },
  methods: {
    makeSinglefile(){
      const formData = new FormData();
      formData.append('slug', this.slug);
      fetch(`${import.meta.env.VITE_APP_API_URL}backend/makeSinglefile`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      })
        .then(response => response.json())
        .then(data => {
          if (data && data.new_url) {
            window.location.href = data.new_url;
          }
          })
        .catch(error => {
          console.error('Error sending text', error);
        });
    },
    editTitleLink() {
      console.log('hoi')
      const formData = new FormData();
      formData.append('slug', this.slug);
      formData.append('tegel_naam', this.titleEdited);
      fetch(`${import.meta.env.VITE_APP_API_URL}backend/editTitleLink`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      })
        .then(response => response.json())
        .then(() => {
          toastService.addToast('Titel aangepast', 'De titel is aangepast', 'success');
          this.isTitleEditClicked = false;
          this.getTekstLinks(); // Refresh the links to reflect the new title
        })
        .catch(error => {
          console.error('Error sending text', error);
        });
    },
    likeLink(item, event) {
      // Toggle like status in the likes array
      const index = this.likes.findIndex(liked => liked.slug === item.slug);
      if (index !== -1) {
        // Unlike: remove from likes
        this.likes.splice(index, 1);
        if (event && event.target) {
          event.target.classList.remove('fa-solid');
          event.target.classList.add('fa-regular');
        }
      } else {
        // Like: add to likes
        this.likes.push({'slug':item.slug});
        if (event && event.target) {
          event.target.classList.remove('fa-regular');
          event.target.classList.add('fa-solid');
        }
      }
    },
    sendLikes() {
      // Compare oldLikes and likes arrays deeply
      if (JSON.stringify(this.oldLikes) === JSON.stringify(this.likes)) {
        console.log('No changes in likes, skipping send.');
        return;
      }

      const formData = new FormData();

      formData.append('userId', auth.user.id);
      formData.append('slug', this.slug);
      formData.append('likes', JSON.stringify(this.likes));
      fetch(`${import.meta.env.VITE_APP_API_URL}backend/sendLikes`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          console.log('Likes sent successfully:', data);
        })
        .catch(error => {
          console.error('There was a problem with the fetch operation:', error);
        });
    },
    getLikes() {
      const formData = new FormData();
      formData.append('slug', this.slug);
      fetch(`${import.meta.env.VITE_APP_API_URL}backend/getLikes`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          auth.checkAction(data?.action)
          if (data.length !== 0) {
            this.likes = data.likes.map(item => ({ slug: item }));
            this.oldLikes = JSON.parse(JSON.stringify(data.likes.map(item => ({ slug: item })))); // Store the old likes for comparison
          }
        })
        .catch(error => {
          console.error('There was a problem with the fetch operation:', error);
        });
    },
    changeNiveau(niveau = '') {
      if (niveau.length !== 0) {
        this.clickedNiveau = niveau;
        localStorage.setItem('niveauPreference', JSON.stringify({ slug: this.slug, niveau }));

      } else {
        const niveauPreference = localStorage.getItem('niveauPreference');
        if (niveauPreference !== null) {
          const parsedPreference = JSON.parse(niveauPreference);
          if (parsedPreference.slug === this.slug) {
            this.clickedNiveau = parsedPreference.niveau;
          }
        }
      }
    },
    changeView(view = 'table') {
      if (view.length !== 0) {
        localStorage.setItem('viewPreference', JSON.stringify({ slug: this.slug, view }));
        this.view = view;
      } else {
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
      if (!str) return '';
      return str.charAt(0).toUpperCase() + str.slice(1);
    },
    async getTekstLinks() {
      const formData = new FormData();
      formData.append('slug', this.slug);
      await fetch(`${import.meta.env.VITE_APP_API_URL}backend/getTekstLinks`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
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
            this.tegelTitle = data[0].tegel_naam;
            let categorized;
            if (data.length > 4) {
              categorized = {
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
                  if (!categorized['anders']) {
                    categorized['anders'] = [];
                  }
                  categorized['anders'].push(item);
                }
              });
            } else {
              categorized = data;
            }
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
    addLink() {
      const formData = new FormData();
      formData.append('slug', this.slug);
      formData.append('link', this.newLink);

      fetch(`${import.meta.env.VITE_APP_API_URL}backend/addLinks`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          if (data.success) {
            this.succes = true;
            this.loading = false;
            this.getTekstLinks();
            this.newLink = ''; // Clear the input field after adding the link
            toastService.addToast('Links toegevoegd', 'De links zijn toegevoegd', 'success');
          } else {
            this.succes = false;
            this.loading = false;
          }
        })
        .catch(error => {
          this.loading = false;
          toastService.addToast('Fout', 'Er is een fout opgetreden bij het toevoegen van de links', 'error');
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

.title-views >  h2 {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.title-views >  h2 > i{
  color: var(--color-primary-400);
  transition: all 0.3s ease;
  cursor: pointer;
}
.title-views >  h2 > i:hover {
  color: var(--color-primary-700);
}


.views {
  display: flex;
  gap: 2rem;
  font-size: 175%;

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
  padding: 2rem 0rem;
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

.view>li {
  display: flex;
  gap: 1rem;
  align-items: center;
}



.tableView {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  padding-left: 0;
  gap: 2rem;
}

.tableView>li {
  position: relative;
  white-space: normal;
  word-wrap: break-word;
  background-color: var(--color-card-500);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
  margin-bottom: 1rem;
  border-radius: 1.5rem;
  padding: 1rem;
  transition: background-color 0.4s ease;
}

.tableView>li>i {
  position: absolute;
  top: -0.75rem;
  right: -0.75rem;
  cursor: pointer;
  font-size: 175%;
  transition: all 0.3s ease;
}

.niveaus {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
  align-items: center;
  width: 100%;
  justify-content: space-evenly;
}

.niveaus button {
  background: none;
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 1rem 3rem;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 125%;
  color: var(--color-text);
}

.isActive {
  color: var(--color-primary-400) !important;
}

.niveaus button:hover {
  color: var(--color-primary-400) !important;

}


.addLink {
  display: inline-flex;
  gap: 1rem;
  margin-top: 2rem;
  flex-direction: column;
}

.addLink>button {
  width: max-content;
  background: var(--color-primary-500);
  color: white;
  border: none;
  border-radius: 0.25rem;
  padding: 1rem 2rem;
  font-size: 110%;
  cursor: pointer;
  transition: all 0.3s ease;
}

.addLink>button:hover {
  background: var(--color-primary-700);
}

@media screen and (max-width: 768px) {
  .links {
    padding: 2rem 1rem;
  }

}
</style>
