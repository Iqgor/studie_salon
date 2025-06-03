<template>
  <div id="carousel" class="carousel">
    <div v-for="(info, index,i) in CarouselData" :key="index" class="carousel-inner">
      <CarouselItem :info="info" :index="index" :indexNumber="i" @getCarouselData="$emit('getCarouselData')"/>
    </div>
  </div>
</template>

<script>

import { auth } from '@/auth';
import CarouselItem from '@/components/CarouselItem.vue';

export default {
  name: "ImageCarousel",
  components: {
    CarouselItem,
  },
  props:{
    CarouselData: {
      type: Object,
      required: true,
    },
  },
  emits: ['getCarouselData'],
};
</script>

<style scoped>

.carousel {
  width: 100%;
  display: flex;
  flex-direction: column;
  padding: 2rem 0;
  gap: 1rem;
}
</style>
