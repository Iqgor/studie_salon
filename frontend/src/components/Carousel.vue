<template>
  <div class="carousel">
    <div class="carousel-inner">
      <h2 class="carousel-titel">Leren hoe te leren</h2>
      <div class="carousel-container">
        <div class="carousel-inhoud">
          <p class="carousel-informatie">
            Actief leren algemeen
          </p>
        </div>
        <div class="carousel-inhoud">
          <p class="carousel-informatie">
            Actief leren aadrijkskunde
          </p>
        </div>
        <div class="carousel-inhoud">
          <p class="carousel-informatie">
            Actief leren biologie
          </p>
        </div>
        <div class="carousel-inhoud">
          <p class="carousel-informatie">
            Actief leren duits
          </p>
        </div>
        <div class="carousel-inhoud">
          <p class="carousel-informatie">
            Actief leren algemeen
          </p>
        </div>
        <div class="carousel-inhoud">
          <p class="carousel-informatie">
            Actief leren algemeen
          </p>
        </div>
        <div class="carousel-inhoud">
          <p class="carousel-informatie">
            Actief leren algemeen
          </p>
        </div>
        <div class="carousel-inhoud">
          <p class="carousel-informatie">
            Actief leren algemeen
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ImageCarousel",
  props: {

  },
  data() {
    return {

    };
  },
  methods: {

  },
};
</script>

<style>
.carousel {
  width: 100%;
}

.carousel-container {
  display: flex;
  flex-direction: row;
  gap: 1.2rem;
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
