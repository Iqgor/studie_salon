<template>
  <header class="header">
    <div @click="changeColor" class="figTop">
      <p class="figTopMenu">menu</p>
      <div class="topMenu">
        <img @click="$router.push('/')" src="../../public/logo.png" alt="">
        <nav class="topMenuNav">
          <ul>
            <li><a href="#">About</a></li>
            <li><a href="#">Functies</a></li>
            <li><a href="#">Contact</a></li>
          </ul>
        </nav>
        <div style="width: 14.2rem; height: 100%;"></div>
      </div>
    </div>
    <h1 class="headerTitle">Study Saloon</h1>
    <div class="icons">
      <i class="fa-solid fa-user"></i>
      <i class="fa-solid fa-flag" @onclick=""></i>
      <i class="fa-solid fa-sun" v-if="currentTheme === 'dark'" @click="switchTheme('light')"></i>
      <i class="fa-solid fa-moon" v-else @click="switchTheme('dark')"></i>
    </div>

  </header>
</template>
<script>

export default {
  name: 'appHeader',
  props: {
    switchTheme: {
      type: Function,
      required: true
    },
    currentTheme: {
      type: String,
      required: true
    }
  },
  methods: {
    changeColor() {
      document.getElementsByClassName('figTop')[0].classList.toggle('figTopClick')
      document.getElementsByClassName('topMenu')[0].classList.toggle('topMenuClick')
      document.getElementsByClassName('icons')[0].classList.toggle('iconsClick')
    }
  }
}
</script>
<style scoped>
.header {
  width: 100%;
  height: 10rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

.headerTitle {
  font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
  cursor: default;
  font-size: 250%;
}

.figTop {
  position: fixed;
  z-index: 3;
  top: 0;
  left: 0;
  width: 10rem;
  height: 10rem;
  background: var(--color-secondary-500);
  background: linear-gradient(169deg, var(--color-secondary-500) 10%, var(--color-primary-500) 90%);
  border-bottom-right-radius: 60%;
  cursor: pointer;
  transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
  display: flex;
  justify-content: center;
  padding-top: 3rem;
}

.figTop:hover {
  background-color: var(--color-secondary);
}

.figTopClick:hover {
  background-color: var(--color-primary);
}

.figTopClick {
  border-bottom-right-radius: 0%;
  width: 100% !important;
  justify-content: flex-start;
  padding-top: 0;
}

.figTopClick>.figTopMenu {
  display: none;
}

.figTopMenu {
  font-size: 2rem;
  color: var(--color-background-100);
  text-align: center;
  font-weight: bold;
  text-transform: uppercase;
}

.topMenu>img {
  height: 100%;
}

.topMenu {
  display: none;
  width: 100%;
  padding: 0 2rem;
  justify-content: space-between;
  align-items: center;
}

.topMenuClick {
  display: flex;
}

.topMenuNav>ul {
  display: flex;
  list-style: none;
  padding: 0;
  margin: 0;
  list-style: none;
  gap: 2rem;
}

.topMenuNav>ul a {
  color: white;
  font-size: 120%;
  font-weight: 200;

}

.icons {
  width: 12rem;
  height: 10rem;
  font-size: 150%;
  display: flex;
  top: 0;
  right: 0;
  position: fixed;
  justify-content: space-evenly;
  align-items: center;
  z-index: 4;

}

.icons>i {
  transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
}

.iconsClick>i {
  color: white;
}

.iconsClick>i:hover{
  color: var(--color-secondary-500) !important;
  cursor: pointer;
}
.icons>i:hover {
  color: var(--color-primary-500);
  cursor: pointer;
}

@media screen and (max-width: 768px) {

  .header {
    height: 8rem;
    padding-right: 0;
  }

  .figTop {
    height: 8rem;
    width: 8rem;
  }

  .headerTitle {
    font-size: 150%;
  }

  .topMenu>img {
    display: none;
  }

  .icons {
    height: 8rem;
  }
}
</style>
