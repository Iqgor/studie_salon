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
          <p>/</p>
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
            <p
              v-for="category in Object.keys(muziekTracks)"
              :key="category"
              class="muziek-button"
              :class="{ active: selectedCategory === category }"
              @click="selectedCategory = category"
            >
              {{ category }}
            </p>
          </div>

          <div class="muziek-tracklist">
            <div
              class="muziek-track"
              v-for="(track, index) in currentTracks"
              :key="index"
              @click="selectTrack(index)"
            >
              <p class="track-title">{{ track.title }}</p>
            </div>
          </div>
          <div class="muziek-balk">
  <audio
    ref="audioPlayer"
    v-if="currentTrack"
    :src="currentTrack.url"
    @timeupdate="updateProgress"
    @loadedmetadata="setDuration"
    @ended="handleEnded"
  ></audio>

  <div v-if="currentTrack" class="controls">
    <button @click="togglePlay">
      {{ isPlaying ? '❚❚' : '▶' }}
    </button>
    <span>{{ formatTime(currentTime) }}</span>
    <input
      type="range"
      min="0"
      :max="duration"
      step="0.1"
      v-model="currentTime"
      @input="seek"
      class="progress-bar"
    />
    <span>{{ formatTime(duration) }}</span>
  </div>

  <div v-else class="controls">
    <p>Selecteer een nummer om af te spelen 🎶</p>
  </div>

  <div class="volume">
    <span>{{ Math.round(volume * 100) }}</span>
    <input
      type="range"
      min="0"
      max="1"
      step="0.01"
      v-model="volume"
      @input="changeVolume"
      class="volume-bar"
    />
    <span>🔊</span>
  </div>
</div>

        </div>

        <!-- stemmen sectie -->
        <div v-else-if="selectedTab === 'stemmen'" class="stem-sectie">
          <div>
            <p class="stemmen-text">Kies de stem waarnaar je graag luistert.</p>
          </div>
          <div class="stemmen-keuzes">
            <div
              v-for="(keuze, index) in stemmenKeuzes"
              :key="index"
              class="stemmen-keuze"
              :class="{ 'stemmen-keuze-active': selectedKeuzeIndex === index }"
              @click="selecteerKeuze(index)"
            >
              <img :src="keuze.img" :alt="keuze.naam" />
              <p>{{ keuze.naam }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import Chris from '@/assets/Chris.png';
import John from '@/assets/John.png';
import Lily from '@/assets/Lily.png';
import Yasmin from '@/assets/Yasmin.png';


export default {
  name: "MuziekPlayer",
  data() {
    return {
      muziekClicked: false,
      selectedTab: 'muziek',
      selectedCategory: 'Concentratie',
      muziekTracks: {},
      currentTrack: null,
      isPlaying: false,
      currentTime: 0,
      duration: 0,
      volume: 1,
      stemmenKeuzes: [
        { naam: "John", img: John },
        { naam: "Chris", img: Chris },
        { naam: "Yasmin", img: Yasmin },
        { naam: "Lily", img: Lily },
      ],
      selectedKeuzeIndex: 0,
    };
  },
  computed: {
    currentTracks() {
      return this.muziekTracks[this.selectedCategory] || [];
    },
  },
  methods: {
    toggleMuziek() {
      this.muziekClicked = !this.muziekClicked;
    },
    selecteerKeuze(index) {
      this.selectedKeuzeIndex = index;
    },
    selectTrack(index) {
      this.currentTrack = this.currentTracks[index];
      this.$nextTick(() => {
        const audio = this.$refs.audioPlayer;
        if (audio) {
          audio.currentTime = 0;
          audio.volume = this.volume;
          audio.play();
          this.isPlaying = true;
        }
      });
    },
    togglePlay() {
      const audio = this.$refs.audioPlayer;
      if (!audio) return;
      if (audio.paused) {
        audio.play();
        this.isPlaying = true;
      } else {
        audio.pause();
        this.isPlaying = false;
      }
    },
    updateProgress() {
      const audio = this.$refs.audioPlayer;
      if (audio) {
        this.currentTime = audio.currentTime;
      }
    },
    setDuration() {
      const audio = this.$refs.audioPlayer;
      if (audio) {
        this.duration = audio.duration;
      }
    },
    seek() {
      const audio = this.$refs.audioPlayer;
      if (audio) {
        audio.currentTime = this.currentTime;
      }
    },
    changeVolume() {
      const audio = this.$refs.audioPlayer;
      if (audio) {
        audio.volume = this.volume;
      }
    },
    handleEnded() {
      this.isPlaying = false;
      this.currentTime = 0;
    },
    formatTime(time) {
      if (!time) return "0:00";
      const minutes = Math.floor(time / 60);
      const seconds = Math.floor(time % 60).toString().padStart(2, "0");
      return `${minutes}:${seconds}`;
    },
  },
  watch: {
    muziekClicked(newVal) {
      document.documentElement.style.overflow = newVal ? 'hidden' : 'auto';
    }
  },
  mounted() {
  fetch('/muziek.json')
    .then(res => res.json())
    .then(data => {
      this.muziekTracks = {};
for (const category in data) {
  this.muziekTracks[category] = data[category].map(track => ({
    ...track,
    url: `/songs/${category}/${track.filename}`
  }));
}

    })

}


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
  width: 65rem;
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
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-top: 2rem;
  max-height: 25rem;
  overflow-y: auto;
  overflow-x: hidden;
}

.muziek-track {
  background-color: var(--color-primary-300);
  padding: 1.5rem;
  border-radius: 1rem;
  color: var(--color-text-500);
  display: flex;
  flex-direction: column;

}

.muziek-track:hover {
  cursor: pointer;
  background-color: var(--color-primary-400);
  transition: 0.3s ease;
}

.track-title {
  font-size: 1.6rem;
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
  height: 2rem;
  width: 2rem;
  background: black;
  border-radius: 50%;
  cursor: pointer;
  border: 1px solid black;
  margin-top: -6px;
}

input[type="range"]::-moz-range-thumb {
  height: 2rem;
  width: 2rem;
  background: black;
  border-radius: 50%;
  cursor: pointer;
  border: 1px solid black;
}

.muziek-balk {
  bottom: 0;
  padding: 1rem;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.controls, .volume {
  display: flex;
  align-items: center;
  gap: 1rem;
}



/* stemmen */

.stemmen-text {
  font-size: 2.5rem;
  text-align: center;
  margin: 2rem;
}

.stemmen-keuzes {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  justify-items: center;
  padding: 2rem;
}

.stemmen-keuze  {
  text-align: center;
}

.stemmen-keuze > img {
  width: 8rem;
  height: 8rem;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid transparent;
  transition: 0.3s ease;
  object-position: 100% 10%;
}

.stemmen-keuze > img:hover {
  cursor: pointer;
  transform: scale(1.05);
}

.stemmen-keuze-active img {
  box-shadow: 0 0 12px 5px var(--color-secondary-300);
}

.stemmen-keuze > p {
  margin-top: 0.5rem;
}

</style>
