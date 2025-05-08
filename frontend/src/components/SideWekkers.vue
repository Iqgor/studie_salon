<template>
  <div class="wekkers">
    <div class="clock-icon" @click="togglePomodoro"><i class="fa-solid fa-stopwatch "></i></div>
    <div class="alarm-icon" @click="toggleAlarm"><i class="fa-solid fa-clock"></i></div>
  </div >

  <!-- pomodoro -->
  <div class="achtergrondblur" v-if="pomodoroClicked">
    <div class="pomodoro">
      <div class="pomodoro-header">
        <p class="pomodoro-titel">Pomodoro timer</p>
        <p class="pomodoro-x" @click="togglePomodoro"><i class="fa-solid fa-x"></i></p>
      </div>
      <div class="pomodoro-box">
        <span class="pomodoro-text">25:00</span>
      </div>
      <div class="pomodoro-buttons">
        <button class="pomodoro-button">Timer</button>
        <button class="pomodoro-button">Korte pauze</button>
        <button class="pomodoro-button">Lange pauze</button>
      </div>
    </div>
  </div>

  <!-- wekker -->
  <div class="achtergrondblur" v-if="alarmClicked">
    <div class="alarm">
      <div class="alarm-header">
        <p class="alarm-titel">Wekker</p>
        <p class="alarm-x" @click="toggleAlarm"><i class="fa-solid fa-x"></i></p>
      </div>
          <div class="timer-box">
              <span class="timer-text">{{ currentTime }}</span>
              <p v-if="alarmSet" class="timer-alarm">Alarm gezet voor {{ alarmTime }}</p>
              <button class="alarm-button" v-if="alarmRinging" @click="stopAlarm">🔕 Stop Alarm</button>
          </div>
        <div class="alarm-setter">
          <label class="alarm-label">
          Voeg alarm toe:
          <input type="time" v-model="alarmInputTime" />
        </label>
        <button class="alarm-button" @click="setAlarm" >Toevoegen</button>
      </div>
    </div>
  </div>

</template>

<script>
import alarmSoundFile from '@/assets/sounds/alarm-clock-90867.mp3';

export default {
  name: 'SideWekkers',
  data() {
    return {
      alarmClicked: false,
      alarmInputTime: '',
      currentTime: '',
      alarmTime: '',
      alarmSet: false,
      alarmRinging: false,
      alarmAudio: null,
      clockInterval: null,
      pomodoroClicked: false,
    };
  },
  methods: {
    toggleAlarm() {
      this.alarmClicked = !this.alarmClicked;
    },
    togglePomodoro() {
      this.pomodoroClicked = !this.pomodoroClicked;
    },
    updateClock() {
      const now = new Date();
      const hours = now.getHours().toString().padStart(2, '0');
      const minutes = now.getMinutes().toString().padStart(2, '0');
      const seconds = now.getSeconds().toString().padStart(2, '0');
      this.currentTime = `${hours}:${minutes}:${seconds}`;

      if (this.alarmSet && `${hours}:${minutes}` === this.alarmTime) {
        this.triggerAlarm();
      }
    },
    setAlarm() {
      if (this.alarmInputTime) {
        let [hours, minutes] = this.currentTime.split(':');
        if(this.alarmInputTime === `${hours}:${minutes}`) {
          console.log('tijd is het zelfde');
        }else{
          this.alarmTime = this.alarmInputTime;
          this.alarmSet = true;
          console.log('Alarm ingesteld voor:', this.currentTime);
        }

      }
    },
    triggerAlarm() {
      this.alarmSet = false;
      this.alarmRinging = true;
      this.alarmAudio = new Audio(alarmSoundFile);
      this.alarmAudio.loop = true;
      this.alarmAudio.play().catch((err) => {
        console.error('Kan alarmgeluid niet afspelen', err);
      });
    },
    stopAlarm() {
      if (this.alarmAudio) {
        this.alarmAudio.pause();
        this.alarmAudio.currentTime = 0;
        this.alarmAudio = null;
      }
      this.alarmRinging = false;
    }
  },
  watch: {
    alarmClicked(newVal) {
      document.documentElement.style.overflow = newVal ? 'hidden' : 'auto';
    }
  },
  mounted() {
    this.updateClock();
    this.clockInterval = setInterval(this.updateClock, 1000);
  },
  beforeUnmount() {
    clearInterval(this.clockInterval);
  }
};

</script>

<style>
.wekkers{
  position: fixed;
  top: 50%;
  right: 0;
  background-color: var(--color-primary-500);
  border: var(--color-secondary-500) 4px solid;
  border-right: 0;
  border-radius: 20px 0 0 20px;
  padding: 0.5rem;
}

.achtergrondblur{
  position: fixed;
  z-index: 99;
  top: 0;
  left: 0;
  width:100%;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.797);
}

.clock-icon{
  border-bottom: var(--color-secondary-500) 2px solid;
  ;
}

.fa-solid{
  color: var(--color-black);
  margin: 10px;
  transition: all 0.3s ease;
}

.fa-stopwatch:hover, .fa-clock:hover{
  cursor: pointer;
  color: var(--color-white);
}

.fa-clock{
  font-size: 30px;
}
.fa-stopwatch{
  font-size: 35px;
}

.alarm, .pomodoro{
  z-index: 100;
  min-height: 30rem;
  padding: 5rem;
  min-width: 60rem;
  border-radius: 5px;
  position: fixed;
  background: var(--color-white);
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  gap: 2.5rem;
  box-shadow: 0.4rem 0.4rem 0 0 rgba(0,0,0,0.2);
}

.alarm-header, .pomodoro-header{
  font-size: 4rem;
    width: 55rem;
    display: flex;
    justify-content: space-between;
}

.alarm-x:hover, .pomodoro-x:hover{
  cursor: pointer;

}

.pomodoro-buttons{
  width: 55rem;
  display: flex;
  justify-content: space-between;
}
.pomodoro-button{
  width: 15rem;
  background-color: var(--color-primary-500);
  color: var(--color-white);
  border: none;
  padding: 1rem;
  border-radius: 5px;
  font-size: 2rem;
}

.pomodoro-button:hover{
  cursor: pointer;
  background-color: var(--color-primary-600);
}

.timer-box, .pomodoro-box{
  width: 55rem;
  height: 20rem;
  background-color: var(--color-white);
  border: var(--color-black) 1px solid;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}

.timer-alarm{
  font-size: 2rem;
  margin-top: 2rem;
}
.timer-text, .pomodoro-text{
    font-size: 8rem;
}

.alarm-button{
  background-color: var(--color-primary-500);
  color: var(--color-white);
  border: none;
  padding: 1rem;
  border-radius: 5px;
  font-size: 2rem;
}
.alarm-button:hover{
  cursor: pointer;
  background-color: var(--color-primary-600);
}
.alarm-setter{
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.alarm-label{
  font-size: 2.5rem;
}

</style>
