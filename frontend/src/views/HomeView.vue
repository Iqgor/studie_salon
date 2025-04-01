<template>
  <main class="main">
    <p class="entry">
      <span class="typewriter">{{ typeofDay }} Igor</span>
    </p>
  </main>
</template>
<script>
export default {
  name: 'HomeView',
  data() {
    return {
      typeofDay: ''
    };
  },
  mounted() {
    this.getDay();
    const textLength = this.$el.querySelector('.typewriter').textContent.length;
    console.log(this.$el.querySelector('.typewriter').innerText.length + this.typeofDay.length + 1);
  },
  methods: {
    getDay() {
      const date = new Date();
      const hours = date.getHours();
      if (hours >= 5 && hours < 12) {
        this.typeofDay = 'Goedemorgen';
      } else if (hours >= 12 && hours < 18) {
        this.typeofDay = 'Goedemiddag';
      } else {
        this.typeofDay = 'Goedeavond';
      }
    }
  }
}
</script>
<style>
.main {
  width: 100%;
  height: 100rem;
  padding: 0 6rem;
}

.entry {
  font-family: 'Courier New', Courier, monospace;
  font-size: 300%;
  text-align: left;
  width: max-content;
  margin-top: 2rem;
}

.typewriter {
  display: inline-block;
  overflow: hidden;
  white-space: nowrap;
  position: relative;
  z-index: -1;

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
  animation: typewriter 2s steps(16) 0.5s forwards;
}

.typewriter::after {
  width: 0.2rem;
  background: black;
  animation: typewriter 2s steps(16) 0.5s forwards, blinken 700ms steps(16) infinite;
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
