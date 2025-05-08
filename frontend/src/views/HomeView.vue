<template>
  <main class="main">
    <div class="entry">
      <p class="typewriter" :style="typewriterStyle">
      </p>
      <div class="quote_container">
      <select v-model="currentLanguageCode" @change="getQuote">
        <option v-for="(language, code) in languageMap" :key="code" :value="code">
          {{ language }}
        </option>
      </select>
        <p class="quote" :class="quote.quote ? 'quoteSlash' : ''">
          {{ quote.quote }}
          <strong>{{ quote.author }}</strong>
        </p>
      </div>
    </div>
    <Calander />
    <Carousel />
    <SideWekkers />
  </main>
</template>
<script>
import Carousel from '@/components/Carousel.vue';
import Calander from '../components/Calendar.vue';
import SideWekkers from '@/components/SideWekkers.vue';


export default {
  name: 'HomeView',
  components: {
    Calander,
    Carousel,
    SideWekkers
  },
  data() {
    return {
      typeofDay: '',
      textLength: 0,
      typewriterStyle: {},
      typeWriterText: '',
      quote: {},
      temp: 0,
      languageMap : {
        'nl-NL': 'Dutch',
        'en-US': 'English',
        'fr-FR': 'French',
        'de-DE': 'German',
        'es-ES': 'Spanish',
        'it-IT': 'Italian',
        'ru-RU': 'Russian',
        // Add more mappings as needed
      },
      currentLanguageCode :navigator.language || navigator.userLanguage

    };
  },
  mounted() {
    this.getDay();
    this.getQuote();
    this.getWeer();
  },
  methods: {
    getDay() {
      const date = new Date();
      const hours = date.getHours();
      if (hours >= 5 && hours < 12) {
        this.typeofDay = 'Goede morgen';
      } else if (hours >= 12 && hours < 18) {
        this.typeofDay = 'Goede middag';
      } else {
        this.typeofDay = 'Goede avond';
      }
    },
    calculateTextLength() {
      this.$nextTick(() => {
        const img = document.createElement('img');
        img.src = 'https://openweathermap.org/img/wn/' + this.temp.weather[0].icon + '@2x.png';
        const typewriter = document.querySelector('.typewriter');
        // waar igor staat later de username neerzetten die opgehaald wordt uit de api
        typewriter.innerHTML = `${this.typeofDay} Igor ${Math.round(this.temp.main.temp)}°C`;
        typewriter.appendChild(img);
        // Wait for DOM update with this.typeofDay
        this.typeWriterText = `${this.typeofDay} Igor ${Math.round(this.temp.main.temp)}°C`;
        this.textLength = this.typeWriterText.length;

        // Set CSS variable
        this.typewriterStyle = {
          '--text-length': this.textLength + 1
        };
      });
    },
    getWeer(){
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          position => {
            const longitude = position.coords.longitude;
            const latitude = position.coords.latitude;
            this.fetchWeather(latitude, longitude);
          },
          error => {
            console.error('Error getting location:', error);
            // Fallback to default location (Amsterdam)
            const longitude = 4.8952;
            const latitude = 52.3702;
            this.fetchWeather(latitude, longitude);
          }
        );
      } else {
        console.error('Geolocation is not supported by this browser.');
        // Fallback to default location (Amsterdam)
        const longitude = 4.8952;
        const latitude = 52.3702;
        this.fetchWeather(latitude, longitude);
      }
    },
    fetchWeather(latitude, longitude) {
      fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${latitude}&lon=${longitude}&units=metric&appid=8a490c6651725451fa03123bd0d7b472`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
          this.temp = data;
          this.calculateTextLength();
        })
        .catch(error => {
          console.error('There was a problem with the fetch operation:', error);
        });
    },
    getQuote() {
      const currentLanguage = this.languageMap[this.currentLanguageCode] || 'English'; // Default to English if not mapped
      const formData = new FormData();
      formData.append('language', currentLanguage);

        fetch('http://localhost/studie_salon/backend/quote', {
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
            if (data) {
              this.quote = data.quote;

            }
          })
          .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
          });
    }
  }
}
</script>
<style>
.main {
  width: 100%;
  height: 100rem;
  padding: 0 10rem;
}

.entry {
  margin: 2rem 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 2rem;
}

.quote_container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  margin-top: 1rem;
  width: 30%;
}

.quote {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  width: 100%;
  padding: 0 2rem;
}

.quoteSlash {
  position: relative;
}

.quoteSlash::before,
.quoteSlash::after {
  position: absolute;
  color: #777;
}

.quoteSlash::before {
  content: '//';
  transform: rotate(90deg);
  left: 0;
  top: -0.5rem;
}

.quoteSlash::after {
  content: "";
  left: 5px;
  top: 20px;
  width: 1px;
  height: 80%;
  background-color: currentColor;
}


.quote>strong {
  font-style: italic;
  font-size: 1.5rem;
}

.typewriter {
  font-family: 'Courier New', Courier, monospace;
  display: inline-flex;
  overflow: hidden;
  white-space: nowrap;
  position: relative;
  font-size: 300%;
  gap: 1rem;
  align-items: center;

}


.typewriter::before,
.typewriter::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  left: 0;
  bottom: 0;

}


.typewriter::before {
  background: var(--color-background-500);
  animation: typewriter 2s steps(var(--text-length)) 0.5s forwards;
}

.typewriter::after {
  width: 0.2rem;
  background: black;
  animation: typewriter 2s steps(var(--text-length)) 0.5s forwards, blinken 700ms steps(var(--text-length)) infinite;
}

.typewriter > img{
  height: 7.5rem;
}

@keyframes blinken {
  to {
    background-color: transparent;
  }
}

@keyframes typewriter {
  to {
    left: 100%;
  }

}

@media screen and (max-width: 1200px) {
  .main {
    padding: 0 5rem
  }

  .entry {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }

  .typewriter {
    font-size: 250%;
  }

}

@media screen and (max-width: 768px) {
  .main {
    padding: 0 1rem
  }

  .typewriter {
    font-size: 200%;
  }

  .quote {
    width: 100%;
    padding-left: 2rem;
  }

}
</style>
