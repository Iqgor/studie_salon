<template>
  <main class="main">
    <p class="typewriter" :style="typewriterStyle">
    </p>
    <Calander />
    <div class="appsHeader">
        <h2 class="appsTitle"><span>Quotes - zoeken - wekkers</span>
          <i @click="isClickedout = !isClickedout;"  class="fa-solid fa-arrow-down" :class="!isClickedout ? 'rotate': ''"></i>
        </h2>
        <div class="views">
          <i @click="changeView('table')" :class="{'isActive' : view == 'table'}" class="fa-solid fa-table"></i>
          <i :class="{ 'isActive': view == 'list' }" @click="changeView('list')" class="fa-solid fa-list"></i>
        </div>
    </div>

    <div class="apps" :class="{ 'isClickedout': isClickedout , 'show' : view === 'list' && isClickedout || view === 'table'}" >
      <div class="quote_container">
        <p class="quote" :class="quote.quote ? 'quoteSlash' : ''">
          {{ quote.quote }}
          <strong>{{ quote.author }}</strong>
        </p>
        <div class="quote_chooser">
          <div @click="currentLanguageCode = code, getQuote()" v-for="(language, code) in languageMap" :key="code"
            :value="code">
            <div :class="currentLanguageCode === code ? 'isActive' : ''" v-html="language[1]"></div>
          </div>
        </div>
      </div>
      <div>
        <SideWekkers :app="true" />
      </div>
      <input type="text" class="search" :input="updateCarouselData()" v-model="searchCarousel" placeholder="Zoek een tekst..." />
    </div>
    <div v-if="Object.keys(updatedCarouselData).length > 0">
      <Carousel :CarouselData="updatedCarouselData" @getCarouselData="getCarouselData"/>
    </div>
    <div v-else class="no-results" style="text-align:center; margin:2rem 0;">
      Geen teksten gevonden.
    </div>
    <SideWekkers />
  </main>
</template>
<script>
import Carousel from '@/components/Carousel.vue';
import Calander from '../components/Calendar.vue';
import SideWekkers from '@/components/SideWekkers.vue';
import { auth } from '@/auth';

export default {
  name: 'HomeView',
  components: {
    Calander,
    Carousel,
    SideWekkers,
  },
  data() {
    return {
      typeofDay: '',
      textLength: 0,
      typewriterStyle: {},
      typeWriterText: '',
      quote: {},
      temp: 0,
      languageMap: {
        'nl-NL': ['Dutch', '<svg viewBox="-3.6 -3.6 43.20 43.20" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--twemoji" preserveAspectRatio="xMidYMid meet" fill="#000000" stroke="#000000" stroke-width="0.00036"><g id="SVGRepo_bgCarrier" stroke-width="0" transform="translate(0,0), scale(1)"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#000000" stroke-width="1.44"><path fill="#EEE" d="M0 14h36v8H0z"></path><path fill="#AE1F28" d="M32 5H4a4 4 0 0 0-4 4v5h36V9a4 4 0 0 0-4-4z"></path><path fill="#20478B" d="M4 31h28a4 4 0 0 0 4-4v-5H0v5a4 4 0 0 0 4 4z"></path></g><g id="SVGRepo_iconCarrier"><path fill="#EEE" d="M0 14h36v8H0z"></path><path fill="#AE1F28" d="M32 5H4a4 4 0 0 0-4 4v5h36V9a4 4 0 0 0-4-4z"></path><path fill="#20478B" d="M4 31h28a4 4 0 0 0 4-4v-5H0v5a4 4 0 0 0 4 4z"></path></g></svg>'],
        'en-US': ['English', '<svg viewBox="-2.16 -2.16 40.32 40.32" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--twemoji" preserveAspectRatio="xMidYMid meet" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#100e0e" stroke-width="1.44"><path fill="#00247D" d="M0 9.059V13h5.628zM4.664 31H13v-5.837zM23 25.164V31h8.335zM0 23v3.941L5.63 23zM31.337 5H23v5.837zM36 26.942V23h-5.631zM36 13V9.059L30.371 13zM13 5H4.664L13 10.837z"></path><path fill="#CF1B2B" d="M25.14 23l9.712 6.801a3.977 3.977 0 0 0 .99-1.749L28.627 23H25.14zM13 23h-2.141l-9.711 6.8c.521.53 1.189.909 1.938 1.085L13 23.943V23zm10-10h2.141l9.711-6.8a3.988 3.988 0 0 0-1.937-1.085L23 12.057V13zm-12.141 0L1.148 6.2a3.994 3.994 0 0 0-.991 1.749L7.372 13h3.487z"></path><path fill="#EEE" d="M36 21H21v10h2v-5.836L31.335 31H32a3.99 3.99 0 0 0 2.852-1.199L25.14 23h3.487l7.215 5.052c.093-.337.158-.686.158-1.052v-.058L30.369 23H36v-2zM0 21v2h5.63L0 26.941V27c0 1.091.439 2.078 1.148 2.8l9.711-6.8H13v.943l-9.914 6.941c.294.07.598.116.914.116h.664L13 25.163V31h2V21H0zM36 9a3.983 3.983 0 0 0-1.148-2.8L25.141 13H23v-.943l9.915-6.942A4.001 4.001 0 0 0 32 5h-.663L23 10.837V5h-2v10h15v-2h-5.629L36 9.059V9zM13 5v5.837L4.664 5H4a3.985 3.985 0 0 0-2.852 1.2l9.711 6.8H7.372L.157 7.949A3.968 3.968 0 0 0 0 9v.059L5.628 13H0v2h15V5h-2z"></path><path fill="#CF1B2B" d="M21 15V5h-6v10H0v6h15v10h6V21h15v-6z"></path></g><g id="SVGRepo_iconCarrier"><path fill="#00247D" d="M0 9.059V13h5.628zM4.664 31H13v-5.837zM23 25.164V31h8.335zM0 23v3.941L5.63 23zM31.337 5H23v5.837zM36 26.942V23h-5.631zM36 13V9.059L30.371 13zM13 5H4.664L13 10.837z"></path><path fill="#CF1B2B" d="M25.14 23l9.712 6.801a3.977 3.977 0 0 0 .99-1.749L28.627 23H25.14zM13 23h-2.141l-9.711 6.8c.521.53 1.189.909 1.938 1.085L13 23.943V23zm10-10h2.141l9.711-6.8a3.988 3.988 0 0 0-1.937-1.085L23 12.057V13zm-12.141 0L1.148 6.2a3.994 3.994 0 0 0-.991 1.749L7.372 13h3.487z"></path><path fill="#EEE" d="M36 21H21v10h2v-5.836L31.335 31H32a3.99 3.99 0 0 0 2.852-1.199L25.14 23h3.487l7.215 5.052c.093-.337.158-.686.158-1.052v-.058L30.369 23H36v-2zM0 21v2h5.63L0 26.941V27c0 1.091.439 2.078 1.148 2.8l9.711-6.8H13v.943l-9.914 6.941c.294.07.598.116.914.116h.664L13 25.163V31h2V21H0zM36 9a3.983 3.983 0 0 0-1.148-2.8L25.141 13H23v-.943l9.915-6.942A4.001 4.001 0 0 0 32 5h-.663L23 10.837V5h-2v10h15v-2h-5.629L36 9.059V9zM13 5v5.837L4.664 5H4a3.985 3.985 0 0 0-2.852 1.2l9.711 6.8H7.372L.157 7.949A3.968 3.968 0 0 0 0 9v.059L5.628 13H0v2h15V5h-2z"></path><path fill="#CF1B2B" d="M21 15V5h-6v10H0v6h15v10h6V21h15v-6z"></path></g></svg>'],
        'fr-FR': ['French', '<svg viewBox="-1.08 -1.08 38.16 38.16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--twemoji" preserveAspectRatio="xMidYMid meet" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#0d0d0d" stroke-width="1.44"><path fill="#ED2939" d="M36 27a4 4 0 0 1-4 4h-8V5h8a4 4 0 0 1 4 4v18z"></path><path fill="#002495" d="M4 5a4 4 0 0 0-4 4v18a4 4 0 0 0 4 4h8V5H4z"></path><path fill="#EEE" d="M12 5h12v26H12z"></path></g><g id="SVGRepo_iconCarrier"><path fill="#ED2939" d="M36 27a4 4 0 0 1-4 4h-8V5h8a4 4 0 0 1 4 4v18z"></path><path fill="#002495" d="M4 5a4 4 0 0 0-4 4v18a4 4 0 0 0 4 4h8V5H4z"></path><path fill="#EEE" d="M12 5h12v26H12z"></path></g></svg>'],
        'de-DE': ['German', '<svg viewBox="-1.08 -1.08 38.16 38.16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--twemoji" preserveAspectRatio="xMidYMid meet" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#080808" stroke-width="1.44"><path fill="#FFCD05" d="M0 27a4 4 0 0 0 4 4h28a4 4 0 0 0 4-4v-4H0v4z"></path><path fill="#ED1F24" d="M0 14h36v9H0z"></path><path fill="#141414" d="M32 5H4a4 4 0 0 0-4 4v5h36V9a4 4 0 0 0-4-4z"></path></g><g id="SVGRepo_iconCarrier"><path fill="#FFCD05" d="M0 27a4 4 0 0 0 4 4h28a4 4 0 0 0 4-4v-4H0v4z"></path><path fill="#ED1F24" d="M0 14h36v9H0z"></path><path fill="#141414" d="M32 5H4a4 4 0 0 0-4 4v5h36V9a4 4 0 0 0-4-4z"></path></g></svg>'],
        'it-IT': ['Italian', '<svg viewBox="-1.08 -1.08 38.16 38.16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" class="iconify iconify--twemoji" preserveAspectRatio="xMidYMid meet" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#080808" stroke-width="1.44"><path fill="#CE2B37" d="M36 27a4 4 0 0 1-4 4h-8V5h8a4 4 0 0 1 4 4v18z"></path><path fill="#009246" d="M4 5a4 4 0 0 0-4 4v18a4 4 0 0 0 4 4h8V5H4z"></path><path fill="#EEE" d="M12 5h12v26H12z"></path></g><g id="SVGRepo_iconCarrier"><path fill="#CE2B37" d="M36 27a4 4 0 0 1-4 4h-8V5h8a4 4 0 0 1 4 4v18z"></path><path fill="#009246" d="M4 5a4 4 0 0 0-4 4v18a4 4 0 0 0 4 4h8V5H4z"></path><path fill="#EEE" d="M12 5h12v26H12z"></path></g></svg>'],
        'ru-RU': ['Russian', '<?xml version="1.0" encoding="utf-8"?><svg style="border: 1px solid black;border-radius: 3px;" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 55.32 38.52" style="enable-background:new 0 0 55.32 38.52" xml:space="preserve"><style type="text/css">.st0{fill:#FFFFFF;} .st1{fill:#D52B1E;} .st2{fill:#0039A6;} .st3{fill:none;stroke:#CCCCCC;stroke-width:0.1199;stroke-miterlimit:2.6131;}</style><g><path class="st0" d="M3.09,0.06h49.13c1.67,0,3.03,1.36,3.03,3.03v16.17H0.06V3.09C0.06,1.42,1.42,0.06,3.09,0.06L3.09,0.06z"/><path class="st1" d="M0.06,19.26h55.2v16.17c0,1.67-1.36,3.03-3.03,3.03H3.09c-1.67,0-3.03-1.37-3.03-3.03V19.26L0.06,19.26z"/><polygon class="st2" points="0.06,12.86 55.26,12.86 55.26,25.66 0.06,25.66 0.06,12.86"/><path class="st3" d="M3.09,0.06h49.13c1.67,0,3.03,1.36,3.03,3.03v32.33c0,1.67-1.36,3.03-3.03,3.03H3.09 c-1.67,0-3.03-1.37-3.03-3.03V3.09C0.06,1.42,1.42,0.06,3.09,0.06L3.09,0.06z"/></g></svg>'],

        // Add more mappings as needed
      },
      currentLanguageCode: navigator.language || navigator.userLanguage,
      loading: true,
      searchCarousel: '',
      CarouselData: [],
      updatedCarouselData: [],
      isClickedout: false,
      view: 'table', // Default view
    };
  },
  mounted() {
    document.title = 'Studie Salon - Home';
    this.getDay();
    this.getQuote();
    this.getWeer();
    this.getCarouselData();
    this.changeView();
  },
  methods: {
    changeView(view = '' , index = 'app') {
      if(view.length !== 0){
        this.view = view;
        localStorage.setItem('viewPreference', JSON.stringify({ index, view }));
      }else{
        const savedView = JSON.parse(localStorage.getItem('viewPreference'));
        if (savedView && savedView.index === index) {
          this.view = savedView.view;
        } else {
          this.view = 'table';
        }
      }

    },
    getCarouselData() {
      fetch(`${import.meta.env.VITE_APP_API_URL}backend/getCarouselItems`,{
        method: 'POST',
        headers: {
          Authorization: auth.bearerToken,
          'Content-Type': 'application/json'
        }
      })
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok');
          }
          return response.json();
        })
        .then(data => {
            // Group items by carousel_name
            const grouped = {};
            data.carouselItems.forEach(item => {
            const key = item.coursel_name || 'Overig';
            if (!grouped[key]) {
              grouped[key] = [];
            }
            grouped[key].push(item);
            });
            this.CarouselData = grouped;
            this.updatedCarouselData = grouped;
            auth.checkAction(data?.action)
        })
        .catch(error => {
          console.error('There was a problem with the fetch operation:', error);
        });
        return;
    },
    updateCarouselData() {
      if(this.searchCarousel.length === 0){
        this.updatedCarouselData = this.CarouselData;
        return;
      }
      // CarouselData is an object, so filter its arrays and keep the structure
      const search = this.searchCarousel.toLowerCase();
      this.updatedCarouselData = Object.fromEntries(
        Object.entries(this.CarouselData).map(([category, items]) => [
          category,
          items.filter(item => item.title.toLowerCase().includes(search))
        ]).filter(([, items]) => items.length > 0)
      );
    },
    getDay() {
      const date = new Date();
      const hours = date.getHours();
      if (hours >= 5 && hours < 12) {
        this.typeofDay = 'Goedemorgen';
      } else if (hours >= 12 && hours < 18) {
        this.typeofDay = 'Goedemiddag';
      } else {
        this.typeofDay = 'Goedenavond';
      }
    },
    calculateTextLength() {
      this.$nextTick(() => {
        const img = document.createElement('img');
        img.src = 'https://openweathermap.org/img/wn/' + this.temp.weather[0].icon + '@2x.png';
        const typewriter = document.querySelector('.typewriter');
        typewriter.innerHTML = `${this.typeofDay} ${auth.user.name ? auth.user.name : 'gebruiker'} ${Math.round(this.temp.main.temp)}°C`;
        typewriter.appendChild(img);
        // Wait for DOM update with this.typeofDay
        this.typeWriterText = `${this.typeofDay} ${auth.user.name ? auth.user.name : 'gebruiker'} ${Math.round(this.temp.main.temp)}°C`;
        this.textLength = this.typeWriterText.length;

        // Set CSS variable
        this.typewriterStyle = {
          '--text-length': this.textLength + 1
        };
      });
    },
    getWeer() {
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
      this.loading = true;
      const currentLanguage = this.languageMap[this.currentLanguageCode][0] || 'English'; // Default to English if not mapped
      const formData = new FormData();
      formData.append('language', currentLanguage);

      fetch(`${import.meta.env.VITE_APP_API_URL}backend/quote`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken,
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

          this.loading = false;
          if (data) {
            this.quote = data.quote;
          }
        })
        .catch(error => {
          this.loading = false;
          console.error('There was a problem with the fetch operation:', error);
        });
    }
  }
}
</script>
<style>
.main {
  width: 100%;
  padding: 0 10rem;
  padding-bottom: 2rem;

}

.apps {
  display: none;
  justify-content: space-between;
  align-items: end;
  width: 100%;
}

.appsHeader{
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: white;
  padding: 1rem 10rem;
  background: var(--color-primary-500);
  margin-left: -10rem;
  margin-right: -10rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}


.appsTitle {
  font-size: 3rem;
  display: flex;
  gap: 2rem;
  align-items: center;
}

.appsTitle i {
  cursor: pointer;
  transition: all 0.3s ease;
  color: white;
  font-size: 75%;
}

.rotate{
  transform: rotate(180deg);
}

.isClickedout{
  flex-direction: column;
  align-items: flex-start;
  gap: 2rem;
}


.views{
  display: flex;
  gap: 1rem;
  font-size:150%;
}

.views > i{
  cursor: pointer;
  transition: all 0.3s ease;
}


.show{
  display: flex;
}


.quote_container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2rem;
  margin-top: 1rem;
  width: 40%;
}

.quote_chooser {
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  width: 100%;
}

.quote_chooser div {
  width: 4rem;
  cursor: pointer;
  transition: all 0.3s ease;
  height: max-content ;

}

.quote_chooser>div:active {
  transform: scale(1.2);
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
  /* white-space: nowrap; */
  position: relative;
  font-size: 300%;
  gap: 1rem;
  align-items: center;
  text-wrap: wrap;
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

.typewriter>img {
  height: 7.5rem;
}


.search {
  width: 40%;
  border: none;
  border-bottom: 0.125rem solid var(--color-text);
  padding-bottom: 0.25rem;
  font-size: 2rem;
  transition: all 0.3s ease;
  background: transparent;
  color: var(--color-text)
}

.search:focus {
  padding-bottom: 0.5rem;
  outline: none;
  border-bottom: 0.25rem solid var(--color-primary-500);
}

.isActive{
  transform: scale(1.2);
  cursor: default !important;
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

  .typewriter {
    font-size: 250%;
  }

}

@media screen and (max-width: 768px) {
  .main {
    padding: 0;
  }

  .apps {
    flex-direction: column;
    align-items: center;
    gap: 2rem;
    padding: 0 1rem;
  }

  .typewriter {
    font-size: clamp(120%, calc(200vw / var(--text-length)), 200%);
    padding-left: 1rem;
    width: 100%;
  }

  .typewriter>img {
    height: 5rem;
  }

  .quote_container {
    width: 100%;
    padding: 0 1rem;
  }

  .quote {
    width: 100%;
    padding-left: 2rem;
  }

  .appsHeader {
    margin-left: 0;
    margin-right: 0;
    padding: 1rem;
    gap: 1rem;
  }

  .appsTitle > span {
    font-size: 1.75rem;
  }

  .search {
    width: 100%;
    font-size: 1.5rem;
    padding-left: 1rem;
  }
}
</style>
