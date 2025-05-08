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
        <span class="pomodoro-text">{{ pomodoroLabel }}</span>
      </div>
      <div class="pomodoro-buttons">
       <button class="pomodoro-button" @click="startWork">Timer</button>
       <button class="pomodoro-button" @click="startShortBreak">Korte pauze</button>
       <button class="pomodoro-button" @click="startLongBreak">Lange pauze</button>
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
              <p v-if="alarmTime.length > 1 && !alarmRinging" class="timer-alarm"> {{ alarmTime }}</p>
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
      // Wekker
      alarmClicked: false,
      alarmInputTime: '',
      currentTime: '',
      alarmTime: '',
      alarmSet: false,
      alarmRinging: false,
      alarmAudio: null,
      clockInterval: null,

      // Pomodoro
      pomodoroClicked: false,
      pomodoroTime: 1500, // in seconden (25 min)
      pomodoroLabel: '25:00',
      pomodoroRunning: false,
      pomodoroInterval: null,
    };
  },
  methods: {
    // Algemene toggles
    toggleAlarm() {
      this.alarmClicked = !this.alarmClicked;
    },
    togglePomodoro() {
      this.pomodoroClicked = !this.pomodoroClicked;
      if (!this.pomodoroClicked) {
        clearInterval(this.pomodoroInterval);
        this.pomodoroRunning = false;
        this.pomodoroLabel = '25:00';
        this.pomodoroTime = 1500;
      }
    },

    // Wekker
    updateClock() {
      const now = new Date();
      const hours = now.getHours().toString().padStart(2, '0');
      const minutes = now.getMinutes().toString().padStart(2, '0');
      const seconds = now.getSeconds().toString().padStart(2, '0');
      this.currentTime = `${hours}:${minutes}:${seconds}`;
      console.log('Huidige tijd:', this.currentTime);
      if (this.alarmSet && `${hours}:${minutes}` === this.alarmInputTime) {
        this.triggerAlarm();
      }
    },
    setAlarm() {
      if (this.alarmInputTime) {
        let [hours, minutes] = this.currentTime.split(':');
        if (this.alarmInputTime === `${hours}:${minutes}`) {
          this.alarmTime = 'Tijd is hetzelfde als nu, kies een andere tijd.';
        } else {
          this.alarmTime = "Alarm gezet voor " + this.alarmInputTime;
          this.alarmSet = true;
          console.log('Alarm ingesteld voor:', this.alarmTime);
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
      this.alarmSet = false;
      this.alarmTime = '';
      this.alarmRinging = false;
    },

    // Pomodoro-functies
    startPomodoro(duration) {
      clearInterval(this.pomodoroInterval);
      this.pomodoroTime = duration;
      this.pomodoroRunning = true;
      this.updatePomodoroLabel();

      this.pomodoroInterval = setInterval(() => {
        if (this.pomodoroTime > 0) {
          this.pomodoroTime--;
          this.updatePomodoroLabel();
        } else {
          clearInterval(this.pomodoroInterval);
          this.pomodoroRunning = false;
          this.triggerAlarm();
        }
      }, 1000);
    },
    updatePomodoroLabel() {
      const minutes = Math.floor(this.pomodoroTime / 60).toString().padStart(2, '0');
      const seconds = (this.pomodoroTime % 60).toString().padStart(2, '0');
      this.pomodoroLabel = `${minutes}:${seconds}`;
    },
    startWork() {
      this.startPomodoro(25 * 60);
    },
    startShortBreak() {
      this.startPomodoro(5 * 60);
    },
    startLongBreak() {
      this.startPomodoro(15 * 60);
    },
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
    clearInterval(this.pomodoroInterval);
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

.fa-clock, .fa-stopwatch{
  color: var(--color-primary-800);
  margin: 10px;
  transition: all 0.3s ease;
}

.fa-stopwatch:hover, .fa-clock:hover{
  cursor: pointer;
  color: var(--color-secondary-500);
}
.fa-x{
  color: var(--color-text);
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
  background: var(--color-background-400);
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  gap: 2.5rem;
}

.alarm-header, .pomodoro-header{
  font-size: 4rem;
    width: 55rem;
    display: flex;
    justify-content: space-between;
}

.alarm-titel, .pomodoro-titel{
  color: var(--color-text);
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
  background-color: var(--color-background-400);
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
    color: var(--color-text-500);
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
