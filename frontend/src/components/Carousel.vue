<template>
  <div class="carousel">
    <div v-for="(info, index,i) in CarouselData" :key="index" class="carousel-inner">
      <h2 class="carousel-titel">{{ index }}
      <i v-if="isClickedout[i]" @click="changeIsClicked(i)" class="fa-solid fa-arrow-down"></i>
      <i v-else @click="isClickedout[i] = true" class="fa-solid fa-arrow-up"></i>      </h2>
      <div v-if="isClickedout[i]" class="carousel-container">
        <router-link v-for="text in info" :to="text.url" class="carousel-inhoud">
          <p class="carousel-informatie">
          {{ text.title }}
          </p>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import CarouselData from "../assets/carousel.json"
export default {
  name: "ImageCarousel",
  props: {

  },
  data() {
    return {
      CarouselData: CarouselData,
      isClickedout:[], // Initialize all keys to true
    };
  },
  mounted(){
    // Check if isClickedout exists in localStorage
    const storedIsClickedout = localStorage.getItem('isClickedout');
    if (storedIsClickedout) {
      this.isClickedout = JSON.parse(storedIsClickedout);
    } else {
      // Initialize isClickedout with default values
      this.isClickedout = Array(Object.keys(this.CarouselData).length).fill(false);
    }
  },
  methods: {
    changeIsClicked(index) {
      this.isClickedout[index] = !this.isClickedout[index];
      localStorage.setItem('isClickedout', JSON.stringify(this.isClickedout));
    },
  },
};
</script>

<style scoped>
.fa-solid{
  font-size: 75%;
  cursor: pointer;
}
.carousel {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap:3rem;
  padding: 2rem 0;
}

.carousel-container {
  display: flex;
  flex-direction: row;
  gap: 2rem;
  overflow-x: scroll;
  margin-right: -10rem;
  margin-left: -10rem;
  padding-left: 10rem;

}

.carousel-container::-webkit-scrollbar {
  display: none;
}

.carousel-titel {
  font-size: 3rem;
  color: white;
  padding: 1rem;
  background-color: var(--color-primary-500);
  margin-left: -10rem;
  margin-right: -10rem;
  margin-bottom: 1rem;
  padding-left: 10rem;
}

.carousel-inhoud {
  text-align: center;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: row;
  height: 13rem;
  width: 23rem;
  font-size: 2rem;
  background-color: var(--color-primary-300);
  color: #000;
  border-radius: 1.5rem;
  padding: 1rem;
}
</style>
