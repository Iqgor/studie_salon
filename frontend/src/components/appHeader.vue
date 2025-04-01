<template>
  <header class="header">
    <div @click="changeColor" class="figTop">
      <p class="figTopMenu">menu</p>
      <div class="topMenu">
        <img src="../../public/logo.png" alt="">
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
      <i class="fa-solid fa-language"></i>
    </div>

  </header>
</template>
<script>

export default {
  name: 'appHeader',
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
  padding-right: 2rem;
}

.headerTitle {
  font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
  cursor: default;
  font-size: 250%;
}

.figTop {
  position: fixed;
  top: 0;
  left: 0;
  width: 10rem;
  height: 10rem;
  background: rgb(65, 184, 131);
  background: linear-gradient(169deg, rgba(65, 184, 131, 1) 10%, rgba(53, 73, 94, 1) 90%);
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
  width: 100%;
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
  width: 12rem;
  height: 10rem;
  font-size: 150%;
  display: flex;
  top: 0;
  right: 0;
  position: fixed;
  justify-content: space-evenly;
  align-items: center;

}

.icons>i {
  transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
}

.iconsClick>i {
  color: white;
}

.icons>i:hover {
  color: var(--color-primary);
  cursor: pointer;
}
</style>
