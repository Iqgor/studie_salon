<template>
  <main class="main">
    <p class="entry">
      <span class="typewriter" :style="typewriterStyle">
      </span>
      <span class="quote" :class="quote.quote ? 'quoteSlash' : ''">
        {{ quote.quote }}
        <strong>{{ quote.author }}</strong>
      </span>
    </p>
    <Calander />
    <Carousel />
  </main>
</template>
<script>
import Carousel from '@/components/Carousel.vue';
import Calander from '../components/Calendar.vue';

export default {
  name: 'HomeView',
  components: {
    Calander,
    Carousel
  },
  data() {
    return {
      typeofDay: '',
      textLength: 0,
      typewriterStyle: {},
      typeWriterText: '',
      quote: {},
      temp: 0
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
      const savedQuote = localStorage.getItem('dailyQuote');
      const savedDate = localStorage.getItem('quoteDate');
      const today = new Date().toISOString().split('T')[0];

      if (savedQuote && savedDate === today) {
        this.quote = JSON.parse(savedQuote);
      } else {
        fetch('https://api.api-ninjas.com/v1/quotes', {
          method: 'GET',
          headers: {
            'X-Api-Key': import.meta.env.VITE_APP_API_KEY_QUOTE
          }
        })
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then(data => {
            if (data && data.length > 0) {
              this.quote = data[0];
              localStorage.setItem('dailyQuote', JSON.stringify(data[0]));
              localStorage.setItem('quoteDate', today);
            }
          })
          .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
          });
      }
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

.quote {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  width: 30%;
  padding: 0 2rem;
  margin-top: 1rem;
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
