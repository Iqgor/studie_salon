<template>
  <header class="header">
    <div @click="changeColor" class="figTop">
      <p class="figTopMenu">menu</p>
      <div class="topMenu">
        <img @click="$router.push('/')" src="/logo.png" alt="">
        <nav class="topMenuNav">
          <ul>
            <li><RouterLink to="/">Home</RouterLink></li>
            <li><RouterLink to="/abonnementen">Abonnementen</RouterLink></li>
            <li><RouterLink to="/">Disclaimer</RouterLink></li>
          </ul>
        </nav>
        <div style="width: 14.2rem; height: 100%;"></div>
      </div>
    </div>
    <h1 class="headerTitle"><RouterLink to="/">Studie Salon</RouterLink></h1>
    <div class="icons">


      <RouterLink :to="auth.isLoggedIn ? '/profiel' : '/login'"><i class="fa-solid fa-user"></i></RouterLink>


      <i class="fa-solid fa-language" title="Translate page" @click="toggleTranslate = !toggleTranslate">

        <div v-show="toggleTranslate" id="google_translate_element"></div>
      </i>

      <div class="dropdown_wrapper" ref="dropdown" @click.stop="isDropdownVisible = !isDropdownVisible">

        <div class="dropdown">
          <i class="fa-solid fa-palette"></i>
          <div class="options_wrapper" v-if="isDropdownVisible">
            <div class="options" v-for="theme in themes" :key="theme"
              @click="sharedfunctions.switchTheme(theme.value, theme.name)" >
                <div class="option__stripe" v-for="(color, i) in getThemeStripeColors(theme)" :key="i"
                    :style="{ backgroundColor: color }">
                </div>
            </div>
          </div>
        </div>

        

      </div>
<i class="fa-solid fa-right-from-bracket" v-if="auth.isLoggedIn" @click="auth.logout"></i>
    </div>
  </header>
</template>
<script>
import { sharedfunctions } from '../sharedFunctions';
import { auth } from '@/auth';
import themes from '@/assets/themes.json';


export default {
  setup() {
    return { sharedfunctions, auth }
  },

  name: 'appHeader',
  props: {

  },
  data() {
    return {
      toggleTranslate: false,
      isDropdownVisible: false,
      themes: themes.themes,
    }
  },
  mounted() {
    const script = document.createElement('script');
    script.src = "//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";
    script.async = true;
    document.body.appendChild(script);

  },
  methods: {
    changeColor() {
      document.getElementsByClassName('figTop')[0].classList.toggle('figTopClick')
      document.getElementsByClassName('topMenu')[0].classList.toggle('topMenuClick')
      document.getElementsByClassName('icons')[0].classList.toggle('iconsClick')
    },

    getThemeStripeColors(theme) {
      return [
        theme.primary,
        theme.secondary,
        theme.card,
        theme.background
      ];
    },

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
  color: white;
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
  width: max-content;
  height: 10rem;
  font-size: 150%;
  gap: 1rem;
  padding: 2rem;
  display: flex;
  top: 0;
  right: 0;
  position: fixed;
  justify-content: space-evenly;
  align-items: center;
  z-index: 4;
  color: white;
  background: linear-gradient(235deg, var(--color-secondary-500) 10%, var(--color-primary-500) 90%);
  border-bottom-left-radius: 5rem;

}

.icons>i {
  transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
  color: white;
}

.icons a{
  color: white;
}

.iconsClick i {
  color: white;
}

.iconsClick>i:hover {
  color: var(--color-secondary-500) !important;
  cursor: pointer;
}

.icons>i:hover {
  color: var(--color-primary-500);
  cursor: pointer;
}


.fa-language {
  position: relative;
}

.fa-language>div {
  position: absolute;
  top: 4rem;
  left: -12rem;
  background-color: white;
  border-radius: 1rem;
  box-shadow: var(--shadow-1);
}

.dropdown_wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.dropdown {
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  position: relative;
  font-size: 2rem;
}

.options_wrapper {
  position: absolute;
  top: 3rem;
  left: -12rem;
  background-color: white;
  border-radius: 1rem;
  z-index: 10;
}

.options {
  padding: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  height: 4rem;
  width: 15rem;
  display: flex;
}

.option__stripe {
    width: 100%;
    height: 100%;
    border: black solid 0.1rem;

}

.option__stripe:first-child {
    border-radius: 1rem 0 0 1rem;
}

.option__stripe:last-child {
    border-radius: 0 1rem 1rem 0;
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
