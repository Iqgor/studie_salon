<template>
  <div class="carousel">
    <div v-for="(info, index,i) in CarouselData" :key="index" class="carousel-inner">
      <h2 class="carousel-titel" @click="changeIsClicked(i)">{{ index }}
        <i  @click.stop="changeIsClicked(i)" class="fa-solid fa-arrow-down" :class="!isClickedout[i] ? 'rotate': ''"></i>
      </h2>
      <div :class="{'clickedOut':isClickedout[i]}" :id="`carousel-${i}`"  class="carousel-container">
        <router-link :key="i" v-for="(text,i) in info" :to="text.url" class="carousel-inhoud">
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
    this.checkOverflowing()
  },
  methods: {
    checkOverflowing() {
      const carouselContainers = document.getElementsByClassName(`carousel-container`);
      Array.from(carouselContainers).forEach((carouselContainer) => {
        if (carouselContainer.scrollWidth > carouselContainer.clientWidth) {
          carouselContainer.classList.add('showScroll');
        } else {
          carouselContainer.classList.remove('showScroll');
        }
      });
    },
    changeIsClicked(index) {
      this.isClickedout[index] = !this.isClickedout[index];
      this.$nextTick(() => {
        this.checkOverflowing();
      });
      localStorage.setItem('isClickedout', JSON.stringify(this.isClickedout));
    },
  },
};
</script>

<style scoped>
.rotate{
  transform: rotate(180deg);
}
.fa-solid{
  font-size: 75%;
  cursor: pointer;
  transition: transform 0.4s ease;
}
.carousel {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap:2rem;
  padding: 2rem 0;
}

.carousel-container {
  display: flex;
  flex-direction: row;
  gap: 2rem;
  margin-right: -10rem;
  margin-left: -10rem;
  padding-left: 2rem;

}

.showScroll {
  overflow-x: scroll;
}


@media (max-width: 768px) {
  .carousel-container::-webkit-scrollbar {
    display: none;
  }
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
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);

}

.carousel-titel:hover {
  cursor: pointer;
  background-color: var(--color-primary-400);
  transition: background-color 0.4s ease;
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
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
  margin-bottom: 1rem;
  color: var(--color-text);
  border-radius: 1.5rem;
  padding: 1rem;
}
.carousel-inhoud:hover {
  cursor: pointer;
  background-color: var(--color-primary-200);
  transition: background-color 0.4s ease;
}

.clickedOut {
  overflow: hidden;
  flex-wrap: wrap;
  flex-shrink: 1;
  justify-content: center;
  padding: 0;

}

@media screen and (max-width: 768px) {
  .carousel-container {
    padding-left: 1rem;
    margin-right: 0;
    margin-left: 0;
  }
  .carousel-titel {
    margin-left: 0;
    margin-right: 0;
    padding-left: 1rem;
  }

}
</style>
