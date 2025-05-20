<template>
  <div class="muziek-stemmen-section">
    <div class="muziek-icon" @click="toggleMuziek">
      <p class="muzieknoot">🎶</p>
    </div>
  </div>

  <div class="achtergrondblur" v-if="muziekClicked">
    <div class="muziek-stemmen-popup">
      <div class="muziek-stemmen-header">
        <div class="muziek-stemmen-buttons">
          <p class="muziek-stemmen-button" :class="{ active: selectedTab === 'muziek' }" @click="selectedTab = 'muziek'">Muziek</p>
          <p> / </p>
          <p class="muziek-stemmen-button" :class="{ active: selectedTab === 'stemmen' }" @click="selectedTab = 'stemmen'">Stemmen</p>
        </div>
        <p class="pomodoro-x" @click="toggleMuziek">
          <i class="fa-solid fa-x"></i>
        </p>
      </div>

      <div class="muziek-content">
        <!-- muziek sectie -->
        <div v-if="selectedTab === 'muziek'" class="muziek-sectie">
          <div class="muziek-buttons">
            <p v-for="category in Object.keys(muziekTracks)" :key="category" class="muziek-button" :class="{ active: selectedCategory === category }" @click="selectedCategory = category">
              {{ category }}
            </p>
          </div>
          <div class="muziek-tracklist">
            <div class="muziek-track" v-for="(track, index) in currentTracks" :key="index">
              <p class="track-title">{{ track.title }}</p>
              <div class="custom-player">
                <audio ref="audioElements" :data-index="index" :src="track.url" @timeupdate="updateProgress(index)" @loadedmetadata="setDuration(index)"></audio>
                <div class="controls">
                  <button @click="togglePlay(index)">
                    {{ trackStates[index]?.isPlaying ? '❚❚' : '▶' }}
                  </button>
                  <span>{{ formatTime(trackStates[index]?.currentTime) }}</span>
                  <input type="range" min="0" :max="trackStates[index]?.duration || 0" step="0.1" v-model="trackStates[index].currentTime" @input="seek(index)" class="progress-bar"/>
                  <span>{{ formatTime(trackStates[index]?.duration) }}</span>
                </div>
                <div class="volume">
                  <span>{{ Math.round(trackStates[index]?.volume * 100) }}</span>
                  <input type="range" min="0" max="1" step="0.01" v-model="trackStates[index].volume" @input="changeVolume(index)" class="volume-bar"/>
                  <span>🔊</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- stemmen sectie -->
        <div v-else-if="selectedTab === 'stemmen'" class="stem-sectie">
          <p class="muziek-tekst">Hier komt de stem sectie</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "MuziekPlayer",
  data() {
    return {
      muziekClicked: false,
      selectedTab: 'muziek',
      selectedCategory: 'Concentratie',
      trackStates: [],
      muziekTracks: {
        Concentratie: [
          { title: "Concentratie 1", url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3" },
          { title: "Concentratie 2", url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-2.mp3" },
          { title: "Concentratie 3", url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3" },
          { title: "Concentratie 4", url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3" },
          { title: "Concentratie 5", url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3" },
          { title: "Concentratie 6", url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3" },

        ],
        Ontspanning: [
          { title: "Ontspanning 1", url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3" },
          { title: "Ontspanning 2", url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3" },
          { title: "Ontspanning 3", url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3" },
          { title: "Ontspanning 4", url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3" },
          { title: "Ontspanning 5", url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-6.mp3" },
        ],
        Slaap: [
          { title: "Slaap 1", url: "https://www.soundhelix.com/examples/mp3/SoundHelix-Song-11.mp3" },
        ],
      },
    };
  },
  computed: {
    currentTracks() {
      return this.muziekTracks[this.selectedCategory] || [];
    },
  },
  watch: {
    currentTracks() {
      this.initTrackStates();
    },
  },
  mounted() {
    this.initTrackStates();
  },
  methods: {
    toggleMuziek() {
      this.muziekClicked = !this.muziekClicked;
    },
    initTrackStates() {
      this.trackStates = this.currentTracks.map(() => ({
        isPlaying: false,
        currentTime: 0,
        duration: 0,
        volume: 1,
      }));
    },
    togglePlay(index) {
      const audioEl = this.$refs.audioElements[index];
      const state = this.trackStates[index];

      if (!audioEl) return;

      if (audioEl.paused) {
        audioEl.play();
        state.isPlaying = true;
      } else {
        audioEl.pause();
        state.isPlaying = false;
      }

      this.trackStates.forEach((s, i) => {
        if (i !== index) {
          const el = this.$refs.audioElements[i];
          if (el && !el.paused) el.pause();
          s.isPlaying = false;
        }
      });
    },
    updateProgress(index) {
      const audioEl = this.$refs.audioElements[index];
      if (audioEl) {
        this.trackStates[index].currentTime = audioEl.currentTime;
      }
    },
    setDuration(index) {
      const audioEl = this.$refs.audioElements[index];
      if (audioEl) {
        this.trackStates[index].duration = audioEl.duration;
      }
    },
    seek(index) {
      const audioEl = this.$refs.audioElements[index];
      if (audioEl) {
        audioEl.currentTime = this.trackStates[index].currentTime;
      }
    },
    changeVolume(index) {
      const audioEl = this.$refs.audioElements[index];
      if (audioEl) {
        audioEl.volume = this.trackStates[index].volume;
      }
    },
    formatTime(time) {
      if (!time) return "0:00";
      const minutes = Math.floor(time / 60);
      const seconds = Math.floor(time % 60).toString().padStart(2, "0");
      return `${minutes}:${seconds}`;
    },
  },
};
</script>

<style>

.muziek-stemmen-section {
  position: fixed;
  bottom: 0;
  left: 0;
  background-color: var(--color-background-500);
  border: var(--color-secondary-500) 4px solid;
  border-left: none;
  border-bottom: none;
}

.muziek-stemmen-section:hover {
  cursor: pointer;
}

.achtergrondblur {
  position: fixed;
  z-index: 99;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.8);
}

.muziek-stemmen-popup {
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

.muziek-stemmen-header {
  font-size: 4rem;
  width: 55rem;
  display: flex;
  justify-content: space-between;
}

.fa-x:hover {
  cursor: pointer;
}

.muziek-content {
  width: 100%;
}

.muziek-stemmen-buttons {
  width: 100%;
  display: flex;
  justify-content: left;
  align-items: center;
  gap: 1rem;
}
.muziek-stemmen-button:hover {
  cursor: pointer;
  color: var(--color-primary-500);
}
.muziek-stemmen-button.active {
  color: var(--color-primary-500);
}

.muzieknoot {
  font-size: 3rem;
  padding: 0.5rem;
}

.muziek-buttons {
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.muziek-button {
  width: 15rem;
  background-color: var(--color-primary-500);
  color: var(--color-white);
  border: none;
  padding: 1rem;
  border-radius: 5px;
  font-size: 2rem;
  text-align: center;
}
.muziek-button:hover {
  cursor: pointer;
  background-color: var(--color-primary-600);
}

.muziek-tracklist {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(25rem, 1fr));
  gap: 1rem;
  margin-top: 2rem;
  max-height: 30rem;
  overflow-y: auto;
  padding-right: 1rem;
  overflow-x: hidden;
}

.muziek-track {
  background-color: var(--color-primary-300);
  padding: 1rem;
  border-radius: 1rem;
  color: var(--color-text-500);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.track-title {
  font-size: 1.6rem;
  margin-bottom: 1rem;
}

.custom-player {
  border: 2px solid #000;
  padding: 1rem;
  border-radius: 8px;
  background: white;
  font-family: sans-serif;
  margin-top: 1rem;
}

.controls, .volume {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-top: 1rem;
}

input[type="range"] {
  -webkit-appearance: none;
  width: 100%;
  height: 8px;
  background: white;
  border: 1px solid black;
  border-radius: 4px;
  position: relative;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  height: 20px;
  width: 20px;
  background: black;
  border-radius: 50%;
  cursor: pointer;
  border: 1px solid black;
  margin-top: -6px;
}

input[type="range"]::-moz-range-thumb {
  height: 20px;
  width: 20px;
  background: black;
  border-radius: 50%;
  cursor: pointer;
  border: 1px solid black;
}
</style>
