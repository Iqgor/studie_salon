<template>
      <div class="carousel-top">
        <div @click="changeIsClicked(indexNumber)"  class="carousel-titel">
            <h2 >
              {{ index }}
            </h2>
            <i v-if="isOverflowing"  class="fa-solid fa-arrow-down" :class="!isClickedout ? 'rotate': ''"></i>
        </div>

        <div v-if="isOverflowing" class="views">
          <i @click="changeView('table',indexNumber)" :class="{'isActive' : view == 'table'}" class="fa-solid fa-table"></i>
          <i :class="{ 'isActive': view == 'list' }" @click="changeView('list')" class="fa-solid fa-list"></i>
        </div>
      </div>
      <div v-if="isClickedout && view === 'list' || view === 'table'" :class="{'clickedOut':isClickedout && view === 'table' }" :id="`carousel-${indexNumber}`"  class="carousel-container">
        <router-link v-for="(text,i) in info" :to="text.url" class="carousel-inhoud">
          <p class="carousel-informatie">
          {{ text.title }}
          </p>
        </router-link>
      </div>
</template>
<script>
export default {
  name: "CarouselItem",
  props: {
    info: {
      type: Array,
      required: true
    },
    index: {
      type: String,
      required: true
    },
    indexNumber: {
      type: Number,
      required: true
    }
  },
  mounted(){
    this.changeIsClicked()
    this.checkOverflowing()
    this.initOverflowing()
  },
  data() {
    return {
      view: 'table',
      isClickedout:false,
      isOverflowing: false,
    };
  },
  methods: {
    initOverflowing(){
      const carouselContainer = document.getElementById(`carousel-${this.indexNumber}`);
      if (carouselContainer.scrollWidth > carouselContainer.clientWidth) {
        this.isOverflowing = true;
      } else {
        this.isOverflowing = false;
      }
    },
    checkOverflowing() {
      const carouselContainer = document.getElementById(`carousel-${this.indexNumber}`);
      if (carouselContainer.scrollWidth > carouselContainer.clientWidth) {
        carouselContainer.classList.add('showScroll');
      } else {
        carouselContainer.classList.remove('showScroll');
      }

    },
    changeView(view = '' , index) {
      if(view.length !== 0){
        this.view = view;
        localStorage.setItem('viewPreference', JSON.stringify({ index, view }));
        this.$nextTick(() => {
          this.checkOverflowing();
        });
      }else{
        const savedView = JSON.parse(localStorage.getItem('viewPreference'));
        if (savedView && savedView.index === index) {
          this.view = savedView.view;
        } else {
          this.view = 'list';
        }
      }

    },
    changeIsClicked(index = null) {
      if(typeof index === 'number'){
        this.isClickedout = !this.isClickedout;
        localStorage.setItem('isClickedout', JSON.stringify({ index, isClickedout: this.isClickedout }));
        this.$nextTick(() => {
          this.checkOverflowing();
       });
      }else{
        const savedClicked = JSON.parse(localStorage.getItem('isClickedout'));
        if (savedClicked && savedClicked.index === this.indexNumber) {
          this.isClickedout = savedClicked.isClickedout;
        } else {
          this.isClickedout = false;
        }
      }

    },
  }
};
</script>
<style scoped>
.carousel-container {
  display: flex;
  flex-direction: row;
  gap: 2rem;
  margin-right: -10rem;
  margin-left: -10rem;
  padding-left:10rem;

}

.showScroll {
  overflow-x: scroll;
}

.views {
  display: flex;
  gap: 1rem;
  font-size: 200%;
}
.views > .fa-solid {
  color: white;
  transition: all 0.3s ease;
  cursor: pointer;
}

.views > .fa-solid:hover {
  color: var(--color-primary-700);
}

.rotate{
  transform: rotate(180deg);
}
.fa-solid{
  font-size: 75%;
  cursor: pointer;
  transition: transform 0.4s ease;
}

.carousel-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: white;
  padding: 1rem 10rem;
  background-color: var(--color-primary-500);
  margin-left: -10rem;
  margin-right: -10rem;
  margin-bottom: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}
.carousel-titel{
  display: flex;
  gap: 2rem;
  align-items: center;
  font-size: 3rem;
  width: 100%;
}

.carousel-titel h2 {
  font-size: 3rem;
}

.carousel-top:hover {
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
    .carousel-container::-webkit-scrollbar {
    display: none;
  }
  .carousel-top{
    margin-right: 0;
    margin-left: 0;
    padding: 1rem 1rem;
  }
}
</style>
