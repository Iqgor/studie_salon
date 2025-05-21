<template>
      <div class="carousel-top">
        <div   class="carousel-titel">
            <h2 >
              <span @click="changeIsClicked(indexNumber)" v-if="!isEditClicked[index]">{{ index }}</span>
              <input v-else type="text" v-model="lastTitle" class="editLink" />
              <span>
                <i v-if="!isAdmin" @click="isEditClicked[index] = !isEditClicked[index], lastTitle = index" class="fa-solid fa-pen"></i>
                <i v-if="isEditClicked[index]" @click="editLink(index)" class="fa-regular fa-circle-check"></i>
              </span>
            </h2>
            <i v-if="isOverflowing" @click="changeIsClicked(indexNumber)"  class="fa-solid fa-arrow-down" :class="!isClickedout ? 'rotate': ''"></i>
        </div>

        <div v-if="isOverflowing" class="views">
          <i @click="changeView('table',indexNumber)" :class="{'isActive' : view == 'table'}" class="fa-solid fa-table"></i>
          <i :class="{ 'isActive': view == 'list' }" @click="changeView('list')" class="fa-solid fa-list"></i>
        </div>
      </div>
      <div v-if="isClickedout && view === 'list' || view === 'table'" :class="{'clickedOut':isClickedout && view === 'table' }" :id="`carousel-${indexNumber}`"  class="carousel-container">
        <p v-for="(text,i) in info" class="carousel-inhoud">
          <router-link v-if="!isEditClicked[i]"  :to="text.url"  class="carousel-informatie">
            <span >{{ text.title }}</span>
          </router-link>
          <input v-else type="text" v-model="lastTitle" class="editLink" />
          <span>
            <i v-if="!isAdmin" @click="isEditClicked[i] = !isEditClicked[i], lastTitle = text.title" class="fa-solid fa-pen"></i>
            <i v-if="isEditClicked[i]" @click="editLink(i)" class="fa-regular fa-circle-check"></i>
          </span>
        </p>
      </div>
</template>
<script>
import { toastService } from '@/services/toastService';
import { auth } from '@/auth';
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
  emits:['getCarouselData'],
  mounted(){
    this.changeIsClicked()
    this.checkOverflowing()
    this.initOverflowing()
  },
  watch:{
    carouselData: {
      handler(newValue) {
        this.$nextTick(() => {
          this.checkOverflowing();
        });
      },
      deep: true
    },
  },
  data() {
    return {
      view: 'table',
      isClickedout:false,
      isOverflowing: false,
      isEditClicked: [],
      isAdmin: false,
      lastTitle:'',
    };
  },
  methods: {
    editLink(index) {
      const formData = new FormData();

      if(typeof index !== 'string'){
        const item = this.info[index];
        formData.append('id', item.id);
        formData.append('title', this.lastTitle);
      }else{
        const carouselName = index;
        formData.append('carouselName', carouselName);
        formData.append('newCarouselName', this.lastTitle);
      }

      fetch(`${import.meta.env.VITE_APP_API_URL}backend/editCarouselLink`,{
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      }).then(response => {
        if (response.ok) {
          this.$emit('getCarouselData');
          this.isEditClicked[index] = false;
          this.lastTitle = '';
          toastService.addToast('Naam aangepast',`Naam is zojuist aangepast`, 'success');

        } else {
          console.error('Error updating link:', response.statusText);
        }
      }).catch(error => {
        console.error('Error:', error);
      });
    },
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
      if(!this.isOverflowing){
        this.isClickedout = false;
        return;
      }
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
.carousel input[type="text"]{
  padding-bottom: 0.25rem;
  font-size: 2rem;
  transition: all 0.3s ease;
  background: transparent;
  color: white;
}

.carousel input[type="text"]:focus{
  padding-bottom: 0.5rem;
  outline: none;
}
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
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 2rem;
}

.carousel-titel h2 > span{
  display: flex;
  gap: 1rem;
  align-items: center;
  justify-content: center;
}




.carousel-inhoud {
  text-align: center;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 1rem;
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


.carousel  span{
  display: flex;
  gap: 1rem;
  align-items: center;
  justify-content: center;

}
.carousel  span > i{
  font-size: 75%;
  cursor: pointer;
  transition: transform 0.4s ease;
  color: white;
}
.carousel span > i:hover{
  transform: scale(1.2);
}

.carousel-inhoud > a:hover{
  color:var(--color-text);
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
