<template>

  <div v-if="!app" class="wekkers">
    <div class="clock-icon" @click="togglePomodoro"><p class="tomaat">🍅</p></div>
    <div class="alarm-icon" @click="toggleAlarm"><p class="klok">⏰</p></div>
  </div >
  <div v-else class="wekkers-app">
    <div class="clock-icon" @click="togglePomodoro"><p class="tomaat">🍅</p></div>
    <div class="alarm-icon" @click="toggleAlarm"><p class="klok">⏰</p></div>
  </div>

  <!-- pomodoro -->
  <div class="achtergrondblur" v-if="pomodoroClicked">
    <div class="pomodoro">
      <div class="pomodoro-header">
        <div class="pomodoro-header-top">
            <p class="pomodoro-titel">Pomodoro timer</p>
            <div class="pomodoro-header-icons">
              <p class="pomodoro-settingsIcon" @click="togglePomodoroSettings"><i class="fa-solid fa-gear"></i></p>
              <p class="pomodoro-x" @click="togglePomodoro"><i class="fa-solid fa-x"></i></p>
            </div>
          </div>
          <div class="pomodoro-header-bottom">
            <div v-if="showPomodoroSettings" class="pomodoro-settings-menu">
              <div class="pomodoro-settings-menuTimer">
                <label for="Timer" class="pomodoro-pauze-titel">Timer:</label>
                <select id="Timer" class="pomodoro-pauze-keuze" v-model.number="TimerDuration">
                  <option :value="900">15 minuten</option>
                  <option :value="1200">20 minuten</option>
                  <option :value="1500">25 minuten</option>
                  <option :value="1800">30 minuten</option>
                  <option :value="2400">40 minuten</option>
                  <option :value="2700">45 minuten</option>
                </select>
              </div>
             <div class="pomodoro-settings-menuKortepauze">
              <label for="longBreak" class="pomodoro-pauze-titel">Lange pauze:</label>
                <select id="longBreak" class="pomodoro-pauze-keuze" v-model.number="longBreakDuration">
                 <option :value="600">10 minuten</option>
                 <option :value="900">15 minuten</option>
                 <option :value="1200">20 minuten</option>
                </select>
              </div>

            </div>
          </div>
      </div>
      <div class="pomodoro-box">
        <span class="pomodoro-text">{{ pomodoroLabel }}</span>
<button
  class="pomodoro-button pomodoro-button-start" v-if="!pomodoroRunning && !pomodoroFinished" @click="startPomodoro(pomodoroTime)">Start</button>
        <button class="pomodoro-button pomodoro-button-stop" v-if="pomodoroRunning" @click="stopPomodoro">Stop</button>
        <button class="alarm-button" v-if="alarmRinging" @click="stopAlarm">🔕 Stop Alarm</button>
      </div>
      <div class="pomodoro-buttons">
        <button class="pomodoro-button" @click="selectPomodoroDuration(TimerDuration)">Timer</button>
        <button class="pomodoro-button" @click="selectPomodoroDuration(5 * 60)">Korte pauze</button>
        <button class="pomodoro-button" @click="selectPomodoroDuration(longBreakDuration)">Lange pauze</button>
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
  props:{
    app:{
      type:Boolean,
      default:false
    }
  },
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
      showPomodoroSettings: false,
      longBreakDuration: 15 * 60, // standaard 15 minuten
      pomodoroClicked: false,
      pomodoroTime: 1500, // in seconden (25 min)
      TimerDuration: 1500,
      pomodoroLabel: '25:00',
      pomodoroRunning: false,
      pomodoroInterval: null,
      pomodoroFinished: false,

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
    this.pomodoroTime = this.TimerDuration;
    this.updatePomodoroLabel();
    }
  },

    togglePomodoroSettings() {
    this.showPomodoroSettings = !this.showPomodoroSettings;
    },
      stopPomodoro() {
      clearInterval(this.pomodoroInterval);
      this.pomodoroRunning = false;
    },


    // Wekker
    updateClock() {
      const now = new Date();
      const hours = now.getHours().toString().padStart(2, '0');
      const minutes = now.getMinutes().toString().padStart(2, '0');
      const seconds = now.getSeconds().toString().padStart(2, '0');
      this.currentTime = `${hours}:${minutes}:${seconds}`;
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
  this.pomodoroFinished = false; // reset zodat de knop weer verschijnt
},


    selectPomodoroDuration(duration) {
  clearInterval(this.pomodoroInterval); // reset lopende timer
  this.pomodoroRunning = false;
  this.pomodoroTime = duration;
  this.updatePomodoroLabel();
},startPomodoro() {
  if (this.pomodoroRunning || this.pomodoroTime <= 0) return;

  this.pomodoroFinished = false;
  this.pomodoroRunning = true;

  this.pomodoroInterval = setInterval(() => {
    if (this.pomodoroTime > 0) {
      this.pomodoroTime--;
      this.updatePomodoroLabel();
    } else {
      clearInterval(this.pomodoroInterval);
      this.pomodoroRunning = false;
      this.pomodoroFinished = true; // timer is klaar
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

.wekkers-app{
  display: flex;
  gap: 1rem;
  align-items: center;
}

.achtergrondblur{
  position: fixed;
  z-index: 99;
  top: 0;
  left: 0;
  width:100%;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.8);
}

.wekkers > .clock-icon{
  border-bottom: var(--color-secondary-500) 2px solid;
  ;
}


.wekkers .fa-clock, .wekkers .fa-stopwatch{
  color: var(--color-primary-800);
  margin: 10px;
  transition: all 0.3s ease;
}

.wekkers-app .fa-stopwatch , .wekkers-app .fa-clock{
  color: var(--color-primary-400);
  transition: all 0.3s ease;

}

.wekkers-app .fa-clock{
  margin-top: 0.25rem;
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
.tomaat, .klok{
  font-size: 35px;
  margin-bottom: 10px;
}

.tomaat:hover, .klok:hover{
  cursor: pointer;
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

.alarm-header, .pomodoro-header-top{
  font-size: 4rem;
    width: 55rem;
    display: flex;
    justify-content: space-between;
}

.pomodoro-header-bottom{
  width: 55rem;
  display: flex;
  justify-content: space-between;
}

.alarm-titel, .pomodoro-titel{
  color: var(--color-text);
}

.pomodoro-header-icons{
  display: flex;
  gap: 2rem;
  flex-direction: row;
}

.pomodoro-settingsIcon{
  color: var(--color-text);
}

.pomodoro-settingsIcon:hover{
  cursor: pointer;
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

.pomodoro-button-start{
  background-color: var(--color-success);
}
.pomodoro-button-start:hover{
  background-color: var(--color-success);
}
.pomodoro-button-stop{
  background-color: var(--color-error);
}
.pomodoro-button-stop:hover{
  background-color: var(--color-error);
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


.pomodoro-settings-menu {
  width: 55rem;
  display: flex;
  gap: 1rem;
  align-items: center;
}

.pomodoro-settings-menuKortepauze, .pomodoro-settings-menuTimer{
  width: 50%;
}



.pomodoro-pauze-titel{
  font-size: 2.5rem;
  color: var(--color-text);
  width: 40%;

}

.pomodoro-pauze-keuze{
  font-size: 2.5rem;
  color: var(--color-text);
  background-color: var(--color-background-400);
}

</style>
