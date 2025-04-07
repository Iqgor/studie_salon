<template>
  <main class="main">
    <p class="entry">
      <span class="typewriter" :style="typewriterStyle">{{ typeWriterText }}
      </span>
      <span class="quote" :class="quote.quote ? 'quoteSlash' : ''">
        {{ quote.quote }}
        <strong>{{ quote.author }}</strong>
      </span>
    </p>
    <Calander />
  </main>
</template>
<script>
import Calander from '../components/Calendar.vue';

export default {
  name: 'HomeView',
  components: {
    Calander
  },
  data() {
    return {
      typeofDay: '',
      textLength: 0,
      typewriterStyle: {},
      typeWriterText: '',
      quote: {}
    };
  },
  mounted() {
    this.getDay();
    this.calculateTextLength();
    this.getQuote();
  },
  methods: {
    getDay() {
      const date = new Date();
      const hours = date.getHours();
      if (hours >= 5 && hours < 12) {
        this.typeofDay = 'Goodmorning';
      } else if (hours >= 12 && hours < 18) {
        this.typeofDay = 'Goodafternoon';
      } else {
        this.typeofDay = 'Goodevening';
      }
    },
    calculateTextLength() {
      this.$nextTick(() => {
        // Wait for DOM update with this.typeofDay
        this.typeWriterText = `${this.typeofDay} Igor ${this.typeofDay === 'Goodmorning' ? '☀️' :
          this.typeofDay === 'Goodafternoon' ? '🌤️' : '🌙'}`;
        this.textLength = this.typeWriterText.length;

        // Set CSS variable
        this.typewriterStyle = {
          '--text-length': this.textLength
        };
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
            'X-Api-Key': 'O6v0Bj/8HG7sCs4NuNZPXA==GzcsUltYZfXLxQnH'
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
  margin-top: 2rem;
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
  display: inline-block;
  overflow: hidden;
  white-space: nowrap;
  position: relative;
  font-size: 300%;

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
  background: white;
  animation: typewriter 2s steps(var(--text-length)) 0.5s forwards;
}

.typewriter::after {
  width: 0.2rem;
  background: black;
  animation: typewriter 2s steps(var(--text-length)) 0.5s forwards, blinken 700ms steps(var(--text-length)) infinite;
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
</style>
