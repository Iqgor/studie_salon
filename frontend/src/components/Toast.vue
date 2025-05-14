<template>
  <div class="toast__container">
    <article
      v-for="toast in toastService.toasts"
      :key="toast.id"
      :id="'toast-' + toast.id"
      class="toast"
      :class="{
        'toast-green': toast['type'] === 'success',
        'toast-blue': toast['type'] === 'message',
        'toast-orange': toast['type'] === 'warning',
        'toast-red': toast['type'] === 'error'
      }"
    >
      <div
        class="toast__type"
        :class="{
          'toast__type-green': toast['type'] === 'success',
          'toast__type-blue': toast['type'] === 'message',
          'toast__type-orange': toast['type'] === 'warning',
          'toast__type-red': toast['type'] === 'error'
        }"
      >
        <i
          class="fa-solid toast__type_icon"
          :class="{
            'fa-check': toast['type'] === 'success',
            'fa-bell': toast['type'] === 'message',
            'fa-triangle-exclamation': toast['type'] === 'warning',
            'fa-xmark': toast['type'] === 'error'
          }"
        ></i>
      </div>
      <div class="toast__text">
        <h4>{{ toast['title'] }}</h4>
        <p>{{ toast['description'] }}</p>
      </div>
      <div class="toast__close">
        <i class="fa-solid fa-xmark" @click="toastService.removeToast(toast.id)"></i>
      </div>
    </article>
  </div>
</template>


<script>
import { toastService } from '../services/toastService'

export default {
  name: 'Toast',
  data() {
    return {
      toastService
    }
  }
}
</script>

<style scoped>
.toast {
  min-width: 40rem;
  max-width: 40rem;
  background: rgb(255, 255, 255);
  display: flex;
  gap: 2rem;
  border-bottom-left-radius: 1rem;
  border-top-left-radius: 5rem;
  overflow: hidden;
  padding-right: 3rem;
  animation: fadeFromRight 0.5s ease-in-out;

}

.toast-anim {
    animation: fadeOut 1s ease-in-out;
  }

  .toast-green {
    outline: solid 0.4rem var(--color-success);
    border-color: var(--color-success);
  }

  .toast-blue {
    outline: solid 0.4rem var(--color-info);
    border-color: var(--color-info);
  }

  .toast-orange {
    outline: solid 0.4rem var(--color-warning);
    border-color: var(--color-warning);
  }

  .toast-red {
    outline: solid 0.4rem var(--color-error);
    border-color: var(--color-error);
  }

.toast__container {
  position: fixed;
  right: 0rem;
  bottom: 1rem;
  z-index: 1000000;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  gap: 1.5rem;
}

.toast__type {
  border-radius: 5rem;
  border-bottom-left-radius: 1rem;
  overflow: hidden;
  min-width: 7.5rem;
  min-height: 7.5rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

.toast__type-green {
  background: var(--color-success);
}

.toast__type-blue {
  background: var(--color-info); 
}

.toast__type-orange {
  background: var(--color-warning);
}

.toast__type-red {
  background: var(--color-error);
}

.toast__type_icon {
  color: white;
  font-size: 3.5rem;
}

.toast__text {
  width: 100%;
}

.toast__text > h4 {
  font-weight: 700;
  font-size: 2rem;
  margin: 0;
  padding: 0;
  color: black;
}

.toast__text > p {
  font-size: 1.6rem;
  margin: 0;
  padding: 0;
  font-weight: 400;
  color: #4f4f4f;
}

.toast__close {
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 3rem;
  cursor: pointer;
}

.toast__close > i {
  color: #7d7d7d;
  transition: color 0.3s ease-in-out;
}

.toast__close > i:hover {
  color: black;
}


/* === Animations === */
@keyframes fadeFromRight {
  from {
    opacity: 0;
    transform: translateX(100%);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fadeOut {
  from {
    opacity: 1;
  }
  to {
    opacity: 0;
  }
}

/* === Media Queries === */
/* =width= */
@media screen and (max-width: 600px) {
  .toast__type{
    display: none;
  }
  .toast{
    border-radius: 0;
    padding: 1rem;
    min-width: 100%;
    max-width: 100%;
    outline: none;
    border: inset 1rem ;

  }

  .toast-green {
    border-color: var(--color-success);
  }

  .toast-blue {
    border-color: var(--color-info);
  }

  .toast-orange {
    border-color: var(--color-warning);
  }

  .toast-red {
    border-color: var(--color-error);
  }
  .toast__text > h4 {
    font-size: 1.8rem;
    max-width: max-content;
  }
  .toast__text > p {
    font-size: 1.4rem;
    max-width: max-content;
  }
  
}

@media screen and (max-width: 350px) {
  .toast__text > h4 {
    font-size: 1.6rem;
    max-width: max-content;
  }
  .toast__text > p {
    font-size: 1.2rem;
    max-width: max-content;
  }
}

/* =height= */
@media screen and (max-height: 600px) {

  .toast__container{
    bottom: initial;
    top: 0rem;
    flex-direction: column-reverse;
  }
  .toast__type{
    display: none;
  }
  .toast{
    border-radius: 0;
    padding: 1rem;
    min-width: 100%;
    max-width: 100%;
    outline: none;
    border: inset 1rem ;

  }

  .toast-green {
    border-color: var(--color-success);
  }

  .toast-blue {
    border-color: var(--color-info);
  }

  .toast-orange {
    border-color: var(--color-warning);
  }

  .toast-red {
    border-color: var(--color-error);
  }
  .toast__text > h4 {
    font-size: 1.8rem;
    max-width: max-content;
  }
  .toast__text > p {
    font-size: 1.4rem;
    max-width: max-content;
  }
  
}

</style>
