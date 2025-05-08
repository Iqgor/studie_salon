<template>
  <main v-if="succes" class="main">
    <div>
      <a class="waterFallLink" :href="('/'+slug.split('/')[0])">< Terug naar {{slug.split('/')[0]  }}</a>
      <!-- <button @click="playTekst">Speel teksts af</button>
      <button @click="stopSpeech">Stop</button>
      <button @click="changeSpeed(0.5)">0.5x</button>
      <button @click="changeSpeed(1)">1x</button>
      <button @click="changeSpeed(1.5)">1.5x</button>
      <button @click="changeSpeed(2)">2x</button>
      <button @click="skipBackward"><<</button>
      <button @click="skipForward">>></button> -->
    </div>
    <div class="containerTekst" v-html="tekst"></div>
    <a class="waterFallLink" :href="('/'+slug.split('/')[0])">< Terug naar {{slug.split('/')[0]  }}</a>
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
      isSpeaking: false,
      utterance: null,
    };
  },
  mounted() {
    this.getTekst();
  },

  methods: {
    stopSpeech(){
      console.log('stopSpeech');
      window.speechSynthesis.cancel();
      this.isSpeaking = false;
    },
    pauseSpeech(){
      window.speechSynthesis.pause();
    },
    resumeSpeech(){
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
    playTekst(){
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
<style>
.containerTekst {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 3rem 20rem;
  font-size: 2rem;

}

.containerTekst  p{
  line-height: 1.5;
  margin-bottom: 2rem;
}

.containerTekst  h1{
  font-size: 3rem;
  line-height: 1.5;
  margin-bottom: 2rem;
}

.containerTekst  h2{
  font-size: 2.5rem;
  line-height: 1.5;
  margin: 2rem 0 ;
}
.containerTekst  h3{
  font-size: 2rem;
  line-height: 1.5;
  margin: 0.5rem 0 ;
}
.containerTekst ul{
  list-style: none;
}

.containerTekst ol{
  list-style: upper-alpha ;
  margin-left: 4rem;
  line-height: 1.5;
}
.containerTekst ul li{
  line-height: 1.5;
  margin-bottom: 2rem;
}

.containerTekst table{
  border-collapse: collapse;
  width: 100%;
  margin: 1rem 0;
  border: 1px solid #ccc;

}
.containerTekst table th,
.containerTekst table td{
  border: 1px solid #ccc;
  padding: 0.5rem;
  text-align: left;
}
</style>
