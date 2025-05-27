<template>
  <main v-if="succes" class="main">
    <div class="link">
      <RouterLink class="waterFallLink" :to="('/' + firstSlug)">
        < Terug naar {{ firstSlug }}</RouterLink>
          <!-- <button @click="playTekst">Speel teksts af</button>
      <button @click="stopSpeech">Stop</button>
      <button @click="changeSpeed(0.5)">0.5x</button>
      <button @click="changeSpeed(1)">1x</button>
      <button @click="changeSpeed(1.5)">1.5x</button>
      <button @click="changeSpeed(2)">2x</button>
      <button @click="skipBackward"><<</button>
      <button @click="skipForward">>></button> -->
        <i v-if="isAdmin && !isEditClicked" @click="isEditClicked = !isEditClicked" class="fa-solid fa-pen"></i>
    </div>
    <div v-if="!isEditClicked" class="containerTekst" v-html="tekst"></div>
    <div class="adminText" v-else>
      <jodit-editor  v-model="editedContent" ></jodit-editor>
      <div class="buttonsTekst">
        <button @click="sendEdit">Verander tekst</button>
        <i @click="isEditClicked = false, editedContent = tekst" class="fa-regular fa-circle-xmark"></i>
      </div>
    </div>
    <RouterLink class="waterFallLink" :to="('/' + firstSlug)">
      < Terug naar {{ firstSlug }}</RouterLink>
  </main>
  <main class="main" v-else-if="isAdmin && !loading">
    <div class="adminText">
      <h2>Geen tekst gevonden</h2>
      <p>Je kan deze tekst aanmaken door tekst toe te voegen via dit tekst veld en op de knop er onder te klikken</p>
      <jodit-editor v-model="content" />
      <button @click="addTekst">Voeg tekst toe</button>
    </div>
  </main>
  <div v-else-if="!loading && !succes" class="not-found">
    <h1>404</h1>
    <p>Oops! The page you are looking for does not exist.</p>
    <router-link to="/">Go back to Home</router-link>
  </div>
</template>
<script>
import { toastService } from '@/services/toastService';
import 'jodit/build/jodit.min.css'
import { JoditEditor } from 'jodit-vue'
import { auth } from '@/auth';
export default {
  name: "TekstView",
  components: {
    JoditEditor

  },
  data() {
    return {
      slug: this.$route.params.slug,
      firstSlug: window.location.pathname.split('/')[1],
      tekst: '',
      succes: false,
      loading: true,
      isSpeaking: false,
      utterance: null,
      content: '',
      editedContent: '',
      isEditClicked: false,
      isAdmin: false,
    };
  },
  mounted() {
    document.title = `Studie Salon - Tekst ${this.firstSlug}`;
    this.getTekst();
    this.isAdmin = auth.user.role === 'admin';
  },

  methods: {
    sendEdit(){
      const formData = new FormData();
      formData.append('editedContent', this.editedContent);
      formData.append('slug', this.slug.replace('/', '-'));
      fetch(`${import.meta.env.VITE_APP_API_URL}backend/editTekst`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      })
        .then(response => response.json())
        .then(() => {
          this.isEditClicked = false;
          toastService.addToast('Tekst aangepast',`Tekst is zojuist aangepast door ${auth.user.name} `, 'success');
          this.getTekst();
        })
        .catch(error => {
          console.error('Error creating activity:', error);
        });
    },
    addTekst() {
      const formData = new FormData();
      formData.append('slug', this.slug.replace('/', '-'));
      formData.append('tekst', this.content);
      formData.append('carouselName',window.location.pathname.split('/')[1])
      if (this.content.length === 0) {
        toastService.addToast('Geen tekst toegevoegd', 'Vul eers tekst in om te verzenden', 'error');
        return;
      }
      fetch(`${import.meta.env.VITE_APP_API_URL}backend/addText`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      })
        .then(response => response.json())
        .then(() => {
          this.getTekst();
          this.loading = false;
        })
        .catch(error => {
          console.error('Error sending text', error);
        });
    },
    stopSpeech() {
      window.speechSynthesis.cancel();
      this.isSpeaking = false;
    },
    pauseSpeech() {
      window.speechSynthesis.pause();
    },
    resumeSpeech() {
      window.speechSynthesis.resume();
    },
    skipForward() {
      if (this.isSpeaking) {
        window.speechSynthesis.cancel();
        this.utterance.text = this.utterance.text.slice(100); // Skip 10 characters
        window.speechSynthesis.speak(this.utterance);
      }
    },
    skipBackward() {
      if (this.isSpeaking) {
        window.speechSynthesis.cancel();
        this.utterance.text = this.tekst; // Reset to the original text
        window.speechSynthesis.speak(this.utterance);
      }
    },
    changeSpeed(speed) {
      if (this.isSpeaking) {
        window.speechSynthesis.pause();
        this.utterance.rate = speed;
        window.speechSynthesis.resume();
      } else {
        this.utterance.rate = speed;
      }
    },
    playTekst() {
      const cleanTekst = this.tekst
        .replace(/<\/?[^>]+(>|$)/g, "") // Remove HTML tags
        .replace(/&[^;]+;/g, "") // Remove HTML entities
        .replace(/[\u{1F600}-\u{1F64F}\u{1F300}-\u{1F5FF}\u{1F680}-\u{1F6FF}\u{2600}-\u{26FF}\u{2700}-\u{27BF}]/gu, ""); // Remove emojis
      this.utterance = new SpeechSynthesisUtterance(cleanTekst);
      this.utterance.lang = 'nl-NL'; // Set the language to Dutch
      window.speechSynthesis.speak(this.utterance);
      this.utterance.onend = () => {
        this.isSpeaking = false;
      };
      this.isSpeaking = true;

    },
    async getTekst() {
      const formData = new FormData();
      formData.append('slug', this.slug.replace('/', '-'));
      await fetch(`${import.meta.env.VITE_APP_API_URL}backend/getTekst`, {
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
          if (data.tekst.length !== 0) {
            this.succes = true;
            this.loading = false;
            this.tekst = data.tekst;
            this.editedContent = data.tekst;
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
<style>
.containerTekst {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 3rem 20rem;
  font-size: 2rem;
}

.containerTekst p {
  line-height: 1.5;
  margin-bottom: 2rem;
}

.containerTekst h1 {
  font-size: 3rem;
  line-height: 1.5;
  margin-bottom: 2rem;
}

.containerTekst h2 {
  font-size: 2.5rem;
  line-height: 1.5;
  margin: 2rem 0;
}

.containerTekst h3 {
  font-size: 2rem;
  line-height: 1.5;
  margin: 0.5rem 0;
  font-weight: normal;
}

.containerTekst ul {
  list-style: none;
}

.containerTekst ol {
  list-style: upper-alpha;
  margin-left: 4rem;
  line-height: 1.5;
}

.containerTekst ul li {
  line-height: 1.5;
  margin-bottom: 2rem;
}

.containerTekst table {
  border-collapse: collapse;
  width: 100%;
  margin: 1rem 0;
  border: 1px solid #ccc;

}

.containerTekst table th,
.containerTekst table td {
  border: 1px solid #ccc;
  padding: 0.5rem;
  text-align: left;
}


.jodit-workplace {
  height: 40vh !important;
}

.link {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-right: 1rem;
}

.link > i{
  font-size: 2.5rem;
  cursor: pointer;
  color: var(--color-primary-500);
  transition: all 0.3s ease;
}

.link > i:hover{
  color: var(--color-primary-400);
}


.jodit-container{
  margin: 2rem 0;
}

@media screen and (max-width: 768px) {
  .containerTekst {
    padding: 3rem 2rem;
    font-size: 1.5rem;
  }
  .containerTekst h1{
    font-size: 2.5rem;
  }
  .containerTekst h2{
    font-size: 2rem;
  }
  .containerTekst h3{
    font-size: 1.5rem;
  }
  .waterFallLink{
    margin-left: 2rem;
  }

}
</style>
