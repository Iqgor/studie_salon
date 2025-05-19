<template>
  <div class="carousel">
    <div v-for="(info, index,i) in CarouselData" :key="index" class="carousel-inner">
      <CarouselItem :info="info" :index="index" :indexNumber="i" />
    </div>
  </div>
</template>

<script>
import CarouselData from "../assets/carousel.json"
import CarouselItem from "./carouselItem.vue";
export default {
  name: "ImageCarousel",
  components: {
    CarouselItem,
  },
  data() {
    return {
      CarouselData: CarouselData,
    };
  },
};
</script>

<style scoped>

.carousel {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap:2rem;
  padding: 2rem 0;
}
</style>
