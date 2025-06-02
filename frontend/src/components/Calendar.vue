<template>
  <div class="calendar">
    <aside v-if="!newActivityClicked && !activityClicked" class="calendar-sidebar">
      <button v-if="!isMobiel" @click="makeActivity(this.getDaysOfWeek().findIndex(day =>
        day.day === (this.setDate ? this.setDate.getDate() : this.shownDate.getDate()) &&
        day.month === (this.setDate ? this.setDate.getMonth() : this.shownDate.getMonth()) &&
        day.year === (this.setDate ? this.setDate.getFullYear() : this.shownDate.getFullYear())
      ), new Date().getHours())" class="createActivityButton"><span>+</span> Maak Activiteit</button>
      <div class="calendar-header">
        <button @click="changeMonth(-1)">
          <i class="fa-solid fa-arrow-left"></i>
        </button>
        <h2>
          <input type="month" @change="selectDate(1,$event.target.value.split('-')[1],$event.target.value.split('-')[0])" :value="`${shownDate.getFullYear()}-${shownDate.getMonth() - 1 < 0 ? '12' : shownDate.getMonth() < 9 ? '0' + (shownDate.getMonth() + 1) : shownDate.getMonth() + 1}`" class="calendar-monthInput">
        </h2>
        <button @click="changeMonth(1)">
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </div>
      <div class="calendar-grid">
        <div class="calendar-dayName" v-for="dayName in ['Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za', 'Zo']" :key="dayName">
          <strong>{{ dayName }}</strong>
        </div>
        <!-- Day numbers -->
        <!-- Previous month's days -->
        <div class="calendar-day" :class="{
          'current-day': isCurrentDay(day.day, -1), 'clicked-day': isClickedDay(day.day, -1),
          'week-day': currentWeekDays.some(weekDay => weekDay.day === day.day && weekDay.month === day.month)
        }" v-for="day in previousMonthDays" :key="'prev-' + day.day" style="opacity: 0.5;"
          @click="selectDate(day.day, day.month, day.year), unFocusDay()">
          {{ day.day }}
        </div>
        <div class="calendar-day" :class="{
          'current-day': isCurrentDay(day.day), 'clicked-day': isClickedDay(day.day),
          'week-day': currentWeekDays.some(weekDay => weekDay.day === day.day && weekDay.month === day.month)
        }" v-for="(day, index) in daysInMonth" :key="day.day"
          @click="selectDate(day.day, day.month, day.year), unFocusDay()"
          :style="{ gridColumnStart: index === 0 ? (new Date(shownDate.getFullYear(), shownDate.getMonth(), 1).getDay() || 7) : 'auto' }">
          {{ day.day }}
        </div>
        <!-- Next month's days -->
        <div class="calendar-day" :class="{
          'current-day': isCurrentDay(day.day, 1), 'clicked-day': isClickedDay(day.day, 1),
          'week-day': currentWeekDays.some(weekDay => weekDay.day === day.day && weekDay.month === day.month)
        }" v-for="day in nextMonthDays" @click="selectDate(day.day, day.month, day.year), unFocusDay()"
          :key="'next-' + day.day" style="opacity: 0.5;">
          {{ day.day }}
        </div>
      </div>
    </aside>
    <div class="activityInfo" v-else-if="activityClicked && !isMobiel">
            {{ this.activityClickedInfo.maakwerk }}

      <h3 v-if="!editActivity">{{ this.activityClickedInfo.title }}</h3>
      <label v-else for="editTitel">Titel:<input v-model="this.activityClickedInfo.title" id="editTitel" type="text"></label>
      <p >Vak: {{ !editActivity ? this.activityClickedInfo.vak : '' }}</p>
      <select v-if="editActivity" v-model="this.activityClickedInfo.vak" @change="editActivityVak = $event.target.value">
        <option :key="index" :value="vak" v-for="(vak, index) in vakken">{{ vak }}</option>
      </select>
      <p>Maakwerk: {{ !editActivity ? this.activityClickedInfo.maakwerk: '' }}</p>
      <div v-if="editActivity" class="activityRadio">
        <label for="Maakwerk">Maakwerk</label>
        <input :checked="this.activityClickedInfo.maakwerk === 'Maakwerk'"  @input="this.activityClickedInfo.maakwerk = 'Maakwerk'" type="radio" name="maakwerk" id="Maakwerk">
        <label for="Leerwerk">Leerwerk</label>
        <input :checked="this.activityClickedInfo.maakwerk === 'Leerwerk'" @input="this.activityClickedInfo.maakwerk = 'Leerwerk'" type="radio" name="maakwerk" id="Leerwerk">
        <label  for="Te-doen">Te doen</label>
        <input :checked="this.activityClickedInfo.maakwerk === 'Te doen'" @input="this.activityClickedInfo.maakwerk = 'Te doen'" type="radio" name="maakwerk" id="Te-doen">
        <label for="Anders">Anders</label>
        <input :checked="this.activityClickedInfo.maakwerk === 'Anders'" @input="this.activityClickedInfo.maakwerk = 'Anders'" type="radio" name="maakwerk" id="Anders">
      </div>
      <p v-if="!editActivity">Tijd: {{ this.activityClickedInfo.start_datetime.split(' ')[1].split(':')[0] }}:{{ this.activityClickedInfo.start_datetime.split(' ')[1].split(':')[1] }} - {{ this.activityClickedInfo.end_datetime.split(' ')[1].split(':')[0] }}:{{ this.activityClickedInfo.end_datetime.split(' ')[1].split(':')[1] }}</p>
      <div class="editTime time" v-else>
          Tijd:
          <input @input="this.activityClickedInfo.start_datetime = this.activityClickedInfo.start_datetime.split(' ')[0] + ' ' + $event.target.value + ':00'"
        :value="`${this.activityClickedInfo.start_datetime.split(' ')[1].split(':')[0] }:${ this.activityClickedInfo.start_datetime.split(' ')[1].split(':')[1] }`" type="time" >
          <span>-</span>
            <input
            type="time"
            :value="`${this.activityClickedInfo.end_datetime.split(' ')[1].split(':')[0]}:${this.activityClickedInfo.end_datetime.split(' ')[1].split(':')[1]}`"
            @input="this.activityClickedInfo.end_datetime = this.activityClickedInfo.end_datetime.split(' ')[0] + ' ' + $event.target.value + ':00'"
            >
      </div>
      <p >Gedaan: <span v-if="this.activityClickedInfo.done === 1 && !editActivity">Ja</span> <span v-else-if="!editActivity">nee</span></p>
      <div class="editDone" v-if="editActivity">
        <label for="notdone">Ja</label>
        <input @input="this.activityClickedInfo.done = 1" :checked="this.activityClickedInfo.done === 1" type="radio" name="done" id="notdone">
        <label for="done">Nee</label>
        <input @input="this.activityClickedInfo.done = 0" :checked="this.activityClickedInfo.done !== 1" type="radio" name="done" id="done">
      </div>
      <div class="buttons"><i @click="editActivity = !editActivity" class="fa-solid fa-pen-to-square"></i><i v-if="!editActivity" @click="activityClicked = false" class="fa-regular fa-circle-xmark"></i><i v-else @click="sendEdit" class="fa-regular fa-circle-check"></i>  <i @click="deleteAcitivity" class="fa-solid fa-trash"></i></div>

    </div>
    <div class="createActivity" v-else-if="!isMobiel">
      <h3>Nieuwe activiteit</h3>
      <input id="activityName" type="text" v-model="newActivityName" placeholder="Titel toevoegen"
        @input="newActivityName = $event.target.value" />
      <select @change="newVak = $event.target.value">
        <option :key="index" :value="vak" v-for="(vak, index) in vakken">{{ vak }}</option>
      </select>
      <label for="">Soort huiswerk:</label>
      <div class="activityRadio">
        <label for="Maakwerk">Maakwerk</label>
        <input checked @input="maakWerk = 'Maakwerk'" type="radio" name="maakwerk" id="Maakwerk">
        <label for="Leerwerk">Leerwerk</label>
        <input @input="maakWerk = 'Leerwerk'" type="radio" name="maakwerk" id="Leerwerk">
        <label for="Te-doen">Te doen</label>
        <input @input="maakWerk = 'Te doen'" type="radio" name="maakwerk" id="Te-doen">
        <label for="Anders">Anders</label>
        <input @input="maakWerk = 'Anders'" type="radio" name="maakwerk" id="Anders">

      </div>
      <div class="time">
        <input type="date" v-model="newActivityDate" @change="handleDateChange">
        <div>
          <input type="time" v-model="newActivityBegintime">
          <span>-</span>
          <input type="time" v-model="newActivityEndTime">
        </div>
      </div>
      <button @click="newActivityClicked = false, newActivityName = null, newClass = null"><i
          class="fa-regular fa-circle-xmark"></i></button>
      <button @click="sendActivity"><i class="fa-regular fa-circle-check"></i></button>
    </div>

    <div class="calendar-week">
      <div class="calendar-weekTop">
        <div v-if="oneDay" class="backTo" @click="unFocusDay()">Terug naar week</div>
        <div v-for="(days, i) in getDaysOfWeek()" :key="i"
          @click="selectDate(days.day, days.month, days.year), focusDay()">
          <span class="dayNumber"
            :class="{ 'current-day': isCurrentDay(days.day, 0, days.month), 'clicked-day': isClickedDay(days.day, 0, days.month) }">{{
              days.day
            }}</span>

          <span>{{ days.name }}</span>
        </div>
      </div>
      <div class="calendar-weekBottom">
        <ul class="calendar-weekBottomList">
          <div :title="newActivityName || `(afspraaknaam)`" v-if="newActivityClicked" class="newActivity"
            ref="newActivity" @resize="handleResize">
            {{ newActivityName || `(afspraaknaam)` }}
            <p class="activityTime">
              {{ newActivityBegintime }} - {{ newActivityEndTime }}
            </p>
          </div>
          <!--Voor nu nog standaard gewoon 7 dagen zonder iets er in -->
          <li v-for="(days, i) in getDaysOfWeek()" :id="`day-${days.day}-${days.month}`"
            :class="{ 'currentlist-day': isCurrentDay(days.day, 0, days.month) || isClickedDay(days.day, 0, days.month) }"
            :key="i">
            <div :id="`hour-${hour}`" v-for="hour in 24" :key="hour" class="calendar-hourSlot"
              @click="makeActivity(i, hour - 1)">
              <figure :style="timeLineStyle()" class="hourLine"
                v-if="new Date().getHours() === (hour - 1) && isCurrentDay(days.day, 0, days.month)">
                <figure></figure>
              </figure>
              <p v-if="i === 0 || oneDay">
                <span class="hourSlot">
                  {{ hour < 10 ? '0' + hour - 1 : hour - 1 }}:00 </span>
              </p>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <button v-if="isMobiel && !newActivityClicked" @click="makeActivity(this.getDaysOfWeek().findIndex(day =>
      day.day === (this.setDate ? this.setDate.getDate() : this.shownDate.getDate()) &&
      day.month === (this.setDate ? this.setDate.getMonth() : this.shownDate.getMonth()) &&
      day.year === (this.setDate ? this.setDate.getFullYear() : this.shownDate.getFullYear())
    ), new Date().getHours())" class="createActivityButton"><span>+</span> Maak Activiteit</button>
    <div class="activityInfo" v-else-if="activityClicked && isMobiel">

            <h3 v-if="!editActivity">{{ this.activityClickedInfo.title }}</h3>
      <label v-else for="editTitel">Titel:<input v-model="this.activityClickedInfo.title" id="editTitel" type="text"></label>
      <p >Vak: {{ !editActivity ? this.activityClickedInfo.vak : '' }}</p>
      <select v-if="editActivity" v-model="this.activityClickedInfo.vak" @change="editActivityVak = $event.target.value">
        <option :key="index" :value="vak" v-for="(vak, index) in vakken">{{ vak }}</option>
      </select>
      <p>Maakwerk: {{ !editActivity ? this.activityClickedInfo.maakwerk: '' }}</p>
      <div v-if="editActivity" class="activityRadio">
        <label for="Maakwerk">Maakwerk</label>
        <input :checked="this.activityClickedInfo.maakwerk === 'Maakwerk'"  @input="this.activityClickedInfo.maakwerk = 'Maakwerk'" type="radio" name="maakwerk" id="Maakwerk">
        <label for="Leerwerk">Leerwerk</label>
        <input :checked="this.activityClickedInfo.maakwerk === 'Leerwerk'" @input="this.activityClickedInfo.maakwerk = 'Leerwerk'" type="radio" name="maakwerk" id="Leerwerk">
        <label  for="Te-doen">Te doen</label>
        <input :checked="this.activityClickedInfo.maakwerk === 'Te doen'" @input="this.activityClickedInfo.maakwerk = 'Te doen'" type="radio" name="maakwerk" id="Te-doen">
        <label for="Anders">Anders</label>
        <input :checked="this.activityClickedInfo.maakwerk === 'Anders'" @input="this.activityClickedInfo.maakwerk = 'Anders'" type="radio" name="maakwerk" id="Anders">
      </div>
      <p v-if="!editActivity">Tijd: {{ this.activityClickedInfo.start_datetime.split(' ')[1].split(':')[0] }}:{{ this.activityClickedInfo.start_datetime.split(' ')[1].split(':')[1] }} - {{ this.activityClickedInfo.end_datetime.split(' ')[1].split(':')[0] }}:{{ this.activityClickedInfo.end_datetime.split(' ')[1].split(':')[1] }}</p>
      <div class="editTime time" v-else>
          Tijd:
          <input @input="this.activityClickedInfo.start_datetime = this.activityClickedInfo.start_datetime.split(' ')[0] + ' ' + $event.target.value + ':00'"
        :value="`${this.activityClickedInfo.start_datetime.split(' ')[1].split(':')[0] }:${ this.activityClickedInfo.start_datetime.split(' ')[1].split(':')[1] }`" type="time" >
          <span>-</span>
            <input
            type="time"
            :value="`${this.activityClickedInfo.end_datetime.split(' ')[1].split(':')[0]}:${this.activityClickedInfo.end_datetime.split(' ')[1].split(':')[1]}`"
            @input="this.activityClickedInfo.end_datetime = this.activityClickedInfo.end_datetime.split(' ')[0] + ' ' + $event.target.value + ':00'"
            >
      </div>
      <p >Gedaan: <span v-if="this.activityClickedInfo.done === 1 && !editActivity">Ja</span> <span v-else-if="!editActivity">nee</span></p>
      <div class="editDone" v-if="editActivity">
        <label for="notdone">Ja</label>
        <input @input="this.activityClickedInfo.done = 1" :checked="this.activityClickedInfo.done === 1" type="radio" name="done" id="notdone">
        <label for="done">Nee</label>
        <input @input="this.activityClickedInfo.done = 0" :checked="this.activityClickedInfo.done !== 1" type="radio" name="done" id="done">
      </div>
      <div class="buttons"><i @click="editActivity = !editActivity" class="fa-solid fa-pen-to-square"></i><i v-if="!editActivity" @click="activityClicked = false" class="fa-regular fa-circle-xmark"></i><i v-else @click="sendEdit" class="fa-regular fa-circle-check"></i>  <i @click="deleteAcitivity" class="fa-solid fa-trash"></i></div>

    </div>
    <div class="createActivity" v-else-if="isMobiel && newActivityClicked">
      <h3>Nieuwe activiteit</h3>
      <input id="activityName" type="text" v-model="newActivityName" placeholder="Titel toevoegen"
        @input="newActivityName = $event.target.value" />
      <select @change="newVak = $event.target.value">
        <option :key="index" :value="vak" v-for="(vak, index) in vakken">{{ vak }}</option>
      </select>
      <label for="">Soort huiswerk:</label>
      <div class="activityRadio">
        <label for="Maakwerk">Maakwerk</label>
        <input checked @input="maakWerk = 'Maakwerk'" type="radio" name="maakwerk" id="Maakwerk">
        <label for="Leren">Leerwerk</label>
        <input @input="maakWerk = 'Leerwerk'" type="radio" name="maakwerk" id="Leerwerk">
        <label for="Leren">Te doen</label>
        <input @input="maakWerk = 'Te doen'" type="radio" name="maakwerk" id="Te-doen">
        <label for="Leren">Anders</label>
        <input @input="maakWerk = 'Anders'" type="radio" name="maakwerk" id="Anders">
      </div>
      <div class="time">
        <input type="date" v-model="newActivityDate" @change="handleDateChange">
        <div>
          <input type="time" v-model="newActivityBegintime">
          <span>-</span>
          <input type="time" v-model="newActivityEndTime">
        </div>
      </div>
      <button @click="newActivityClicked = false, newActivityName = null, newClass = null"><i
          class="fa-regular fa-circle-xmark"></i></button>
      <button @click="sendActivity"><i class="fa-regular fa-circle-check"></i></button>
    </div>
  </div>
</template>
<script>
import vakken from '../assets/vakken.json'
import { auth } from '@/auth';
import { toastService } from '@/services/toastService';

export default {
  // eslint-disable-next-line vue/multi-word-component-names
  name: 'Calendar',
  data() {
    return {
      vakken: vakken,
      shownDate: new Date(), // Used for navigating the calendar
      setDate: null, // Used for selecting a specific day for activities
      daysInMonth: [],
      previousMonthDays: [],
      nextMonthDays: [],
      currentTime: new Date(),
      intervalId: null, // Store the interval ID for clearing it later
      newActivityClicked: false, // Flag to prevent multiple activity creation
      activityClicked: false,
      activityClickedInfo: [],
      newActivityDate: null,
      newActivityBegintime: null,
      newActivityEndTime: null,
      newActivityName: null,
      newVak: vakken[0],
      maakWerk: "Maakwerk",
      loading: true,
      isMobiel: false,
      oneDay: false,
      editActivity: false,
    };
  },
  components: {
    auth
  },
  computed: {
    currentWeekDays() {
      return this.getDaysOfWeek().map(day => ({ day: day.day, month: day.month }));
    }
  },
  mounted() {
    this.initializeCalendar();
    this.daysInMonth = this.getDaysInMonth(this.shownDate);
    this.intervalId = setInterval(() => {
      this.currentTime = new Date()
    }, 60000) // Update every minute
    this.isMobiel = window.innerWidth < 768;
    this.$nextTick(() => {
      const currentHourLine = document.querySelector('.hourLine');
      if (currentHourLine) {
        const calendarWeekBottom = document.querySelector('.calendar-weekBottomList');
        const parentElement = currentHourLine.parentElement;
        if (calendarWeekBottom) {
          calendarWeekBottom.scrollTo({
            top: parentElement.offsetTop - calendarWeekBottom.offsetTop + 150,
            behavior: 'smooth',
          });
        }

      }
    });
  },
  beforeUnmount() {
    clearInterval(this.intervalId)
  },
  watch: {
    newActivityBegintime(newValue) {
      const newActivityElement = document.querySelector('.newActivity');
      if (newActivityElement && this.newActivityEndTime) {
        const [startHours, startMinutes] = newValue.split(':').map(Number);
        const [endHours, endMinutes] = this.newActivityEndTime.split(':').map(Number);

        const startTimeInMinutes = startHours * 60 + startMinutes;
        const endTimeInMinutes = endHours * 60 + endMinutes;

        if (startTimeInMinutes >= endTimeInMinutes) {
          const adjustedEndTimeInMinutes = startTimeInMinutes + 60; // Add 60 minutes
          const adjustedEndHours = Math.floor(adjustedEndTimeInMinutes / 60);
          const adjustedEndMinutes = adjustedEndTimeInMinutes % 60;
          this.newActivityEndTime = `${adjustedEndHours < 10 ? '0' + adjustedEndHours : adjustedEndHours}:${adjustedEndMinutes < 10 ? '0' + adjustedEndMinutes : adjustedEndMinutes}`;
        }

        const topPosition = startHours * 6 + (startMinutes / 10); // Convert time to position (6rem per hour)
        newActivityElement.style.top = `${topPosition}rem`;
        const durationInMinutes = endTimeInMinutes - startTimeInMinutes;
        const calculatedHeight = (durationInMinutes / 10); // Assuming 6rem per hour (60 minutes)
        if (Math.abs(calculatedHeight - newActivityElement.offsetHeight / 10) > 0.1) {
          newActivityElement.style.height = `${calculatedHeight}rem`;
        }

        const calendarWeekBottom = document.querySelector('.calendar-weekBottomList');
        if (calendarWeekBottom) {
          calendarWeekBottom.scrollTo({
            top: newActivityElement.offsetTop - calendarWeekBottom.offsetTop + 150,
            behavior: 'smooth',
          });
        }

      }
    },
    newActivityEndTime(newValue) {
      const newActivityElement = document.querySelector('.newActivity');
      if (newActivityElement && this.newActivityBegintime) {
        const [startHours, startMinutes] = this.newActivityBegintime.split(':').map(Number);
        const [endHours, endMinutes] = newValue.split(':').map(Number);

        const startTimeInMinutes = startHours * 60 + startMinutes;
        const endTimeInMinutes = endHours * 60 + endMinutes;

        if (endTimeInMinutes <= startTimeInMinutes) {
          const adjustedEndTimeInMinutes = startTimeInMinutes + 30; // Add 30 minutes
          const adjustedEndHours = Math.floor(adjustedEndTimeInMinutes / 60);
          const adjustedEndMinutes = adjustedEndTimeInMinutes % 60;
          this.newActivityEndTime = `${adjustedEndHours < 10 ? '0' + adjustedEndHours : adjustedEndHours}:${adjustedEndMinutes < 10 ? '0' + adjustedEndMinutes : adjustedEndMinutes}`;
          return;
        }

        const durationInMinutes = endTimeInMinutes - startTimeInMinutes;
        const heightInRem = (durationInMinutes / 10); // Assuming 6rem per hour (60 minutes)

        newActivityElement.style.height = `${heightInRem}rem`;
      }
    },
    newActivityDate() {
      const slotElement = document.getElementsByClassName('calendar-weekBottomList')[0];
      this.$nextTick(() => {
        const newElement = document.getElementsByClassName('newActivity')[0];
        const dayIndex = this.getDaysOfWeek().findIndex(day =>
          `${day.year}-${day.month < 9 ? '0' + (day.month + 1) : day.month + 1}-${day.day < 10 ? '0' + day.day : day.day}` === this.newActivityDate
        );
        const [year, month, day] = this.newActivityDate.split('-').map(Number);
        this.setDate = new Date(year, month - 1, day)
        if (dayIndex === -1) {
          this.shownDate = new Date(year, month - 1, day);
          this.daysInMonth = this.getDaysInMonth(this.shownDate);
          const dayIndex = this.getDaysOfWeek().findIndex(day =>
            `${day.year}-${day.month < 9 ? '0' + (day.month + 1) : day.month + 1}-${day.day < 10 ? '0' + day.day : day.day}` === this.newActivityDate
          );
          newElement.style.left = `${(slotElement.clientWidth / 7) * dayIndex}px`;
        }
        if (dayIndex !== -1 && slotElement && newElement) {
          newElement.style.left = `${(slotElement.clientWidth / 7) * dayIndex}px`;
        }
      });

    }
  },
  methods: {
    sendEdit(){
      const formData = new FormData();
      formData.append('shownActivity', JSON.stringify(this.activityClickedInfo));
      fetch(`${import.meta.env.VITE_APP_API_URL}backend/edit_activity`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      })
        .then(response => response.json())
        .then(() => {
          this.editActivity = false;
          this.activityClicked = false;
          toastService.addToast('Activiteit aangepast',`Activiteit is zojuist aangepast door ${auth.user.name} `, 'success');
          this.fetchActivities();
        })
        .catch(error => {
          console.error('Error creating activity:', error);
        });
    },
    deleteAcitivity(){
      const formData = new FormData();
      formData.append('activity_id', JSON.stringify(this.activityClickedInfo.id));
      fetch(`${import.meta.env.VITE_APP_API_URL}backend/delete_activity`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      })
        .then(response => response.json())
        .then(() => {
          this.editActivity = false;
          this.activityClicked = false;
          toastService.addToast('Activiteit verwijderd',`Activiteit is zojuist verwijderd door ${auth.user.name} `, 'success');
          this.fetchActivities();
        })
        .catch(error => {
          console.error('Error creating activity:', error);
        });
    },
    focusDay(){
      this.oneDay = true;
      const slotElement = document.getElementsByClassName('calendar-weekBottomList')[0];
      const dayIndex = this.getDaysOfWeek().findIndex(day =>
        day.day === this.setDate.getDate() &&
        day.month === this.setDate.getMonth() &&
        day.year === this.setDate.getFullYear()
      );
      if (dayIndex !== -1 && slotElement) {
        const weekTopDays = document.querySelectorAll('.calendar-weekTop > div');
        weekTopDays.forEach((day, index) => {
          day.style.display = index === dayIndex ? 'flex' : 'none'; // Show only the current day
          day.style.gridColumn = '2/8'
        });
        slotElement.style.gridTemplateColumns = '1fr'; // Change to one column
        const listItems = slotElement.querySelectorAll('li');
        listItems.forEach((item, index) => {
          item.style.display = index === dayIndex ? 'block' : 'none'; // Show only the current day
        });
      }
    },
    unFocusDay() {
      this.oneDay = false;
      const slotElement = document.getElementsByClassName('calendar-weekBottomList')[0];
      const weekTopDays = document.querySelectorAll('.calendar-weekTop > div');
      weekTopDays.forEach(day => {
        day.style.display = 'flex'; // Show all days
        day.style.width = 'auto'; // Reset width
        day.style.gridColumn = '';
      });
      slotElement.style.gridTemplateColumns = 'repeat(7, 1fr)'; // Change back to 7 columns
      const listItems = slotElement.querySelectorAll('li');
      listItems.forEach(item => {
        item.style.display = 'block'; // Show all days
      });
    },
    handleDateChange() {
      const existingActivities = document.querySelectorAll('.activity');
      existingActivities.forEach(activity => activity.remove());
      this.fetchActivities();
    },
    sendActivity() {
      const formData = new FormData();
      formData.append('userId', auth.user.id);
      formData.append('title', this.newActivityName);
      formData.append('vakName', this.newVak);
      formData.append('maakwerk', this.maakWerk);
      formData.append('startDate', this.newActivityDate + ' ' + this.newActivityBegintime);
      formData.append('endDate', this.newActivityDate + ' ' + this.newActivityEndTime);

      fetch(`${import.meta.env.VITE_APP_API_URL}backend/create_activity`, {
        method: 'POST',
        body: formData,
        headers: {
          Authorization: auth.bearerToken
        }
      })
        .then(response => response.json())
        .then(() => {
          this.newActivityClicked = false;
          this.newActivityName = null;
          this.newClass = null;
          toastService.addToast('Activiteit aangemaakt', `Activiteit is zojuist aangemaakt door ${auth.user.name} `, 'success');
          this.fetchActivities();
          auth.checkAction(response?.action)
        })
        .catch(error => {
          console.error('Error creating activity:', error);
        });
    },
    makeActivity(day, hour) {

      if (!this.newActivityClicked && !this.activityClicked ) {
        this.newActivityClicked = true;
        this.newActivityDate = `${this.getDaysOfWeek()[day].year}-${(this.getDaysOfWeek()[day].month + 1) < 10 ? '0' + (this.getDaysOfWeek()[day].month + 1) : (this.getDaysOfWeek()[day].month + 1)}-${this.getDaysOfWeek()[day].day > 10 ? this.getDaysOfWeek()[day].day : '0' + this.getDaysOfWeek()[day].day}`;
        this.$nextTick(() => {
          const slotElement = document.getElementsByClassName('calendar-weekBottomList')[0];
          const newElement = document.getElementsByClassName('newActivity')[0];
          newElement.style.left = `${(slotElement.clientWidth / 7) * day}px`;
          newElement.style.top = `${hour * 6}rem`;
          newElement.draggable = true;


          newElement.addEventListener('dragend', () => {

            const element = document.elementsFromPoint(event.clientX, event.clientY)
            if (element[0].className !== 'calendar-hourSlot') {
              this.newActivityClicked = false;
              this.newActivityName = null;
              this.newClass = null;
              return;
            }

            for (const dayOfWeek of this.getDaysOfWeek()) {
              if (dayOfWeek.day === parseInt(element[0].parentElement.id.split('-')[1])) {
                this.newActivityDate = `${dayOfWeek.year}-${dayOfWeek.month < 9 ? '0' + (dayOfWeek.month + 1) : dayOfWeek.month + 1}-${dayOfWeek.day < 10 ? '0' + dayOfWeek.day : dayOfWeek.day}`;
                break;
              }
            }

            newElement.style.left = `${element[0].offsetLeft}px`;
            newElement.style.top = `${element[0].offsetTop}px`;
            const snappedHour = parseInt(element[0].id.split('-')[1]) - 1;
            const newHeight = newElement.clientHeight;
            const extraHours = Math.floor(newHeight / 60); // Each 96px corresponds to 1 hour
            const extraMinutes = Math.round((newHeight % 60) * 60 / 60); // Each px corresponds to a fraction of a minute
            const endHour = snappedHour + extraHours;
            const endMinute = extraMinutes === 60 ? 0 : extraMinutes;
            const adjustedEndHour = extraMinutes === 60 ? endHour + 1 : endHour;

            this.newActivityBegintime = `${snappedHour < 10 ? '0' + snappedHour : snappedHour}:00`;
            this.newActivityEndTime = `${adjustedEndHour < 10 ? '0' + adjustedEndHour : adjustedEndHour}:${endMinute < 10 ? '0' + endMinute : endMinute}`


          });

          slotElement.addEventListener('dragover', (e) => {
            e.preventDefault();
          });
          this.newActivityBegintime = `${hour < 10 ? '0' + hour : hour}:00`;
          this.newActivityEndTime = `${hour + 1 < 10 ? '0' + (hour + 1) : hour + 1}:00`
          // werkt op het momement niet
          // const resize = new ResizeObserver(function (entries) {

          //   const newHeight = newElement.offsetHeight;
          //   const extraHours = Math.floor(newHeight / 60); // Each 96px corresponds to 1 hour
          //   const extraMinutes = Math.round((newHeight % 60) * 60 / 60); // Each px corresponds to a fraction of a minute
          //   const endHour = hour + extraHours;
          //   const endMinute = extraMinutes === 60 ? 0 : extraMinutes;
          //   const adjustedEndHour = extraMinutes === 60 ? endHour + 1 : endHour;

          //   this.newActivityEndTime = `${adjustedEndHour < 10 ? '0' + adjustedEndHour : adjustedEndHour}:${endMinute < 10 ? '0' + endMinute : endMinute}`

          // });

          // resize.observe(newElement);

        });
      }


    },
    timeLineStyle() {
      const minutes = this.currentTime.getMinutes()

      return {
        top: `${minutes}px`,
        left: '0',
        right: '0'
      }
    },
    isCurrentDay(day, monthOffset = 0, month) {
      const today = new Date();
      if (month !== undefined) {
        return (
          today.getDate() === day &&
          today.getMonth() === month &&
          today.getFullYear() === this.shownDate.getFullYear()
        );
      }
      return (
        today.getDate() === day &&
        today.getMonth() === this.shownDate.getMonth() + monthOffset &&
        today.getFullYear() === this.shownDate.getFullYear()
      );
    },
    isClickedDay(day, monthOffset = 0, month) {
      if (this.setDate && month !== undefined) {
        this.setDate &&
          this.setDate.getDate() === day &&
          this.setDate.getMonth() === month &&
          this.setDate.getFullYear() === this.shownDate.getFullYear()
      }
      return (
        this.setDate &&
        this.setDate.getDate() === day &&
        this.setDate.getMonth() === this.shownDate.getMonth() + monthOffset &&
        this.setDate.getFullYear() === this.shownDate.getFullYear()
      );
    },
    async fetchActivities() {
      this.loading = true;
      const existingActivities = document.querySelectorAll('.activity');
      existingActivities.forEach(activity => activity.remove());
      const formData = new FormData();
      formData.append('userId', auth.user.id);
      const daysOfWeek = this.getDaysOfWeek();
      formData.append('startDate', `${daysOfWeek[0].year}-${daysOfWeek[0].month + 1}-${daysOfWeek[0].day}`);
      formData.append('endDate', `${daysOfWeek[6].year}-${daysOfWeek[6].month + 1}-${daysOfWeek[6].day}`);

      const response = await fetch(`${import.meta.env.VITE_APP_API_URL}backend/activities`, {
        method: 'POST',
        body: formData,
        headers: {
            Authorization: auth.bearerToken
        }

      });
      if (!response.ok) {
        auth.checkAction('logout')
        this.loading = false;
        console.error('Failed to fetch activities:', response.statusText);
        return;
      }
      this.loading = false;
      const data = await response.json();

      data.activities.forEach(activity => {
        const dayIndex = this.getDaysOfWeek().findIndex(day =>
          activity.start_datetime && `${day.year}-${day.month < 9 ? '0' + (day.month + 1) : day.month + 1}-${day.day < 10 ? '0' + day.day : day.day}` === activity.start_datetime.split(' ')[0]
        );
        if (dayIndex !== -1) {
          const slotElement = document.getElementsByClassName('calendar-weekBottomList')[0];
          const activityElement = document.createElement('div');
          activityElement.className = 'activity';

          const [startHour, startMinute] = activity.start_datetime.split(' ')[1].split(':').slice(0, 2).map(Number);
          const [endHour, endMinute] = activity.end_datetime.split(' ')[1].split(':').slice(0, 2).map(Number);

          const startTimeInMinutes = startHour * 60 + startMinute + (activity.start_datetime.split(' ')[1].split(':')[2] / 60);
          const endTimeInMinutes = endHour * 60 + endMinute + (activity.end_datetime.split(' ')[1].split(':')[2] / 60);

          const topPosition = startTimeInMinutes / 10; // Assuming 6rem per hour (60 minutes)
          const height = Math.max((endTimeInMinutes - startTimeInMinutes) / 10, 0); // Ensure height is non-negative and accurate for minute differences
          activityElement.style.top = `${topPosition}rem`;
          activityElement.style.height = `${height}rem`;
          if (height > 0.5) {
            activityElement.style.padding = '0.5rem';
          }

          if (activity.done === 1) {
            activityElement.style.backgroundColor = 'var(--color-primary-500)';
            activityElement.innerHTML = `
            <strong>${activity.title}</strong>
            <p style="font-size:60%">${activity.vak}</p>
            <p style="font-size:60%">${activity.maakwerk}</p>
            <i class="afvinken uitvinken fa-solid fa-circle-check"></i>
          `;
          }else{
            activityElement.innerHTML = `
            <strong>${activity.title}</strong>
            <p style="font-size:60%">${activity.vak}</p>
            <p style="font-size:60%">${activity.maakwerk}</p>
            <i class="afvinken fa-regular fa-circle-check"></i>
          `;
          }
          activityElement.addEventListener('mouseenter', () => {
            if (activityElement.scrollHeight > activityElement.offsetHeight) {
              activityElement.style.height = 'auto';
            }
          });

          activityElement.addEventListener('mouseleave', () => {
            activityElement.style.height = `${height}rem`;
          });

          activityElement.addEventListener('click', (event) => {
            // Prevent click if the afvinken icon was clicked
            if (event.target.classList.contains('afvinken')) return;
            this.activityClicked = true;
            this.activityClickedInfo = activity;
          })
          slotElement.appendChild(activityElement);

            // Add click event listener to the "afvinken" (check) icon
            const afvinkenIcon = activityElement.querySelector('.afvinken');
            if (afvinkenIcon) {
            afvinkenIcon.addEventListener('click', (e) => {
              e.stopPropagation();
              // Toggle done state and update on server
              const updatedActivity = { ...activity, done: activity.done === 1 ? 0 : 1 };
              const formData = new FormData();
              formData.append('shownActivity', JSON.stringify(updatedActivity));
              fetch(`${import.meta.env.VITE_APP_API_URL}backend/edit_activity`, {
              method: 'POST',
              body: formData,
              headers: {
                Authorization: auth.bearerToken
              }
              })
              .then(response => response.json())
              .then(() => {
                toastService.addToast(
                updatedActivity.done === 1 ? 'Afgevinkt!' : 'Niet afgevinkt!',
                `Activiteit is ${updatedActivity.done === 1 ? 'afgevinkt' : 'niet afgevinkt'} door ${auth.user.name}`,
                'success'
                );
                this.fetchActivities();
              })
              .catch(error => {
                console.error('Error updating activity:', error);
              });
            });
            }

          const correspondingLi = slotElement.querySelectorAll('li')[dayIndex];
          if (correspondingLi) {
            correspondingLi.appendChild(activityElement);
          }
        }
      });

    },
    initializeCalendar() {
      this.fetchActivities();
    },
    getDaysInMonth(date) {
      const year = date.getFullYear();
      const month = date.getMonth();

      // Calculate days from the previous month to display
      const previousMonthTotalDays = new Date(year, month, 0).getDate();
      const firstDayOfMonth = new Date(year, month, 1).getDay() || 7; // 1 = Monday, 7 = Sunday
      this.previousMonthDays = Array.from(
        { length: firstDayOfMonth - 1 },
        (_, i) => ({
          day: previousMonthTotalDays - (firstDayOfMonth - 2 - i),
          month: date.getMonth() - 1 === -1 ? 11 : date.getMonth() - 1
          , year: year - (date.getMonth() - 1 === -1 ? 1 : 0)
        })
      );

      // Calculate days from the next month to display
      const lastDayOfMonth = new Date(year, month + 1, 0).getDay() || 7; // 1 = Monday, 7 = Sunday
      this.nextMonthDays = Array.from(
        { length: 7 - lastDayOfMonth },
        (_, i) => ({ day: i + 1, month: date.getMonth() + 1 === 12 ? 0 : date.getMonth() + 1, year: year + (date.getMonth() + 1 === 12 ? 1 : 0) })
      );

      // Calculate days in the current month
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      return Array.from({ length: daysInMonth }, (_, i) => ({ day: i + 1, month: date.getMonth(), year: year }));
    },
    getDaysOfWeek() {
      const startOfWeek = new Date(this.shownDate);
      startOfWeek.setDate(this.shownDate.getDate() - (this.shownDate.getDay() || 7) + 1); // Adjust to Monday
      const days = [];
      for (let i = 0; i < 7; i++) {
        const date = new Date(startOfWeek);
        date.setDate(date.getDate() + i);
        days.push({ day: date.getDate(), month: date.getMonth(), name: date.toLocaleString('default', { weekday: 'short' }), year: date.getFullYear() });
      }
      return days;
    },
    changeMonth(offset) {
      this.shownDate = new Date(
        this.shownDate.getFullYear(),
        this.shownDate.getMonth() + offset,
        1
      );
      this.daysInMonth = this.getDaysInMonth(this.shownDate);
      this.fetchActivities();

    },
    selectDate(day, month, year) {
      if(!day || !month || !year) {
        console.error('Invalid date selection:', { day, month, year });
        return;
      }
      if(typeof month === 'string') {
        month = parseInt(month) - 1;
        year = parseInt(year);
      }

      const isInCurrentWeek = this.getDaysOfWeek().some(weekDay =>
        weekDay.day === day && weekDay.month === month && weekDay.year === year
      );


      this.setDate = new Date(
        year,
        month,
        day
      );
      this.shownDate = this.setDate
      this.daysInMonth = this.getDaysInMonth(this.shownDate);

      if (!isInCurrentWeek) {
        this.fetchActivities();
      }
    },
  },
};
</script>
<style>
.calendar {
  display: flex;
  padding: 2rem 0;
}

.calendar-sidebar {
  border-radius: 8px;
  width: 30rem;
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  padding: 0 1.25rem;
  padding-bottom: 1rem;
}

.calendar-header>button {
  background-color: var(--color-background-500);
  border: none;
  color: var(--color-primary-500);
  font-size: 125%;
  cursor: pointer;
  transition: background-color 0.3s ease;

}

.calendar-header input[type="month"]{
  background-color: var(--color-background-500);
  border: none;
  color: var(--color-primary-500);
  font-size: 90%;
  cursor: pointer;
  transition: background-color 0.3s ease;
  font-weight: bold;
}

.calendar-header input[type="month"]:focus {
  outline: none;
  background-color: var(--color-secondary-500);
  color: white;
}


.calendar-grid {
  width: 100%;
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
}

.calendar-day {
  background-color: var(--color-background-500);
  padding: 1rem;
  width: max-content;
  aspect-ratio: 1/1;
  text-align: center;
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s ease;

}

.calendar-dayName {
  cursor: default;
  text-align: center;
  padding-bottom: 0.5rem;
}



.calendar-day:hover {
  background-color: var(--color-secondary-500);
  color: white;
  opacity: 1 !important;
}

.week-day {
  background-color: var(--color-secondary-500);
  color: white;
  font-weight: bold;
  opacity: 0.5;
}

.current-day {
  background-color: var(--color-primary-500) !important;
  color: white;
  font-weight: bold;
  transition: 0.3s opacity;
  opacity: 1 !important;
}

.clicked-day {
  background-color: var(--color-secondary-400);
  color: white;
  font-weight: bold;
  opacity: 1 !important;
}

.current-day:hover {
  background-color: var(--color-primary-700);
  color: white;

}



.calendar-week {
  width: 100%;
  margin-left: 4rem;
  border-radius: 1rem;
}

.calendar-weekTop {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  overflow-y: scroll;
  width: 100%;
}


.calendar-weekTop::-webkit-scrollbar {
  /* For Chrome, Safari, and Opera */
  opacity: 0;
}

.calendar-weekTop>div {
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  gap: 0.25rem;
  padding: 1rem 0;
}

.calendar-weekTop>div:not(:last-child) {
  border-right: 0.125rem solid var(--color-text);
}

.backTo {
  grid-column: 1 / 2;
  justify-content: center;
  font-weight: bold;
}

.dayNumber {
  font-size: 150%;
  font-weight: bold;
  width: max-content;
  aspect-ratio: 1/1;
  padding: 0.5rem;
  text-align: center;
  border-radius: 50%;
}

.calendar-weekBottom {
  width: 100%;
  height: 50vh;
  border-top: 0.125rem solid var(--color-text);

}

.calendar-weekBottomList {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  list-style: none;
  height: 100%;
  overflow: auto scroll;
  position: relative;
}

.calendar-weekBottomList>li:not(:last-child) {
  border-right: 0.125rem solid var(--color-text);
}

.calendar-weekBottomList>li {
  position: relative;
}

.calendar-hourSlot {
  border-bottom: 0.125rem solid var(--color-text);
  width: 100%;
  height: 6rem;
  position: relative;
}

.hourSlot {
  position: absolute;
  left: 0.5rem;
  top: 0.25rem;
  opacity: 0.65;
  font-size: 80%;
  z-index: -1;
}

.hourLine {
  position: absolute;
  width: 100%;
  height: 2px;
  background-color: var(--color-primary-500);
  z-index: 2;
}

.hourLine>figure {
  position: absolute;
  top: -0.75rem;
  height: 1.5rem;
  left: -0.75rem;
  aspect-ratio: 1/1;
  background-color: var(--color-primary-500);
  border-radius: 50%;
}

.newActivity {
  position: absolute;
  z-index: 3;
  min-height: 3rem;
  height: 6rem;
  background-color: var(--color-secondary-500);
  border-radius: 0.5rem;
  box-shadow: 0px 6px 10px 0px rgba(0, 0, 0, .14), 0px 1px 18px 0px rgba(0, 0, 0, .12), 0px 3px 5px -1px rgba(0, 0, 0, .2);
  padding: 0.5rem;
  color: white;
  overflow: hidden;
  cursor: grab;
}

.activity {
  position: absolute;
  z-index: 1;
  min-height: 3rem;
  height: 6rem;
  background-color: var(--color-secondary-500);
  border-radius: 0.5rem;
  box-shadow: 0px 6px 10px 0px rgba(0, 0, 0, .14), 0px 1px 18px 0px rgba(0, 0, 0, .12), 0px 3px 5px -1px rgba(0, 0, 0, .2);
  padding: 0.5rem;
  color: white;
  overflow: hidden;
  cursor: pointer;
  width: 90%;
  margin-left: 0.5rem;
}

.activity > .afvinken{
  position: absolute;
  right: -0.5rem;
  top: -0.5rem;
  font-size: 150%;
  color: var(--color-primary-500);
  cursor: pointer;
  z-index: 2;
}

.activity > .uitvinken{
  color: var(--color-secondary-500);
}


.createActivity {
  width: 30rem;
  box-shadow: 0px 6px 10px 0px rgba(0, 0, 0, .14), 0px 1px 18px 0px rgba(0, 0, 0, .12), 0px 3px 5px -1px rgba(0, 0, 0, .2);
  height: 100%;
  padding: 1rem;
  border-radius: 0.25rem;
}

.createActivity>h3 {
  margin-bottom: 1rem;
  font-size: 150%;

}

.createActivity>#activityName {
  padding-left: 0.25rem;
  width: 100%;
  background-color: transparent;
  border: none;
  border-bottom: 0.125rem solid var(--color-text);
  font-size: 125%;
  font-weight: bold;
  transition: all 0.3s ease;
  padding-bottom: 0.5rem;
  color: var(--color-text);
}

.createActivity>#activityName:focus {
  outline: none;
  border-bottom: 0.25rem solid var(--color-primary-500);
}

.activityRadio {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.5rem;
  margin-top: 1rem;
  align-items: center;
  padding: 0 0.5rem;
}

.activityRadio>input {
  margin-right: 0.2rem;
}

.createActivityButton {
  border: 0.25rem solid var(--color-primary-500);
  background: none;
  border-radius: 0.5rem;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  font-size: 125%;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  margin-bottom: 2rem;
  color: var(--color-text);
}

.createActivityButton>span {
  font-size: 150%;
  color: var(--color-primary);
  font-weight: bold;
  transition: all 0.3s ease;

}

.createActivityButton:hover {
  background-color: var(--color-primary-500);
  color: white;
}

.createActivityButton:hover>span {
  color: white;
}

.createActivity>button,
.activityInfo>button {
  background-color: transparent;
  border: none;
  color: var(--color-primary-500);
  font-size: 200%;
  cursor: pointer;
  margin-top: 2rem;
  border-radius: 50%;
}


.createActivity>button>i,
.activityInfo>button>i {
  border-radius: 50%;
  transition: all 0.3s ease;

}

.createActivity>button:first-of-type,
.activityInfo>button:first-of-type {
  color: var(--color-error);
  margin-right: 1rem;
}

.createActivity>button>i:hover,
.activityInfo>button>i:hover {
  background-color: #eee;
}


.time {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
  justify-content: space-between;
}

.time input {
  border: none;
  border-bottom: 0.125rem solid var(--color-text);
  transition: all 0.3s ease;
  padding-bottom: 0.5rem;
  font-size: 90%;
  background: transparent;
  color: var(--color-text);
}

.time input:focus {
  outline: none;
  border-bottom: 0.125rem solid var(--color-primary-500);
}

.time>div {
  display: flex;
  gap: 0.5rem;
}

.activityInfo {
  min-width: 30rem;
  display: flex;
  gap: 1rem;
  flex-direction: column;
  box-shadow: 0px 6px 10px 0px rgba(0, 0, 0, .14), 0px 1px 18px 0px rgba(0, 0, 0, .12), 0px 3px 5px -1px rgba(0, 0, 0, .2);
  padding: 1rem;
  height: 100%;
  border-radius: 0.25rem;
}

.activityInfo>h3 {
  font-size: 150%;
  margin-bottom: 1rem;
}


.activityInfo > .buttons{

  width: 100%;
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  font-size: 150%;
  margin-top: 2rem;
}


.activityInfo > .buttons > i{
  cursor: pointer;
  transition: all 0.3s ease;
}


.activityInfo > .buttons > i:hover{
  color: var(--color-primary-500);
}

.activityInfo input[type='text']{
border: none;
  border-bottom: 0.125rem solid var(--color-text);
  width: 100% !important;
  font-size: 100%;
  margin-top: 0.5rem;
  padding-left: 0.5rem;
}

.activityInfo input[type='text']:focus{
  outline: none;
  border-bottom: 0.125rem solid var(--color-primary-500);
}

.activityInfo select{
  margin: 0;
}

.activityInfo >.activityRadio{
  margin: 0;
  padding-left: 0.5rem;
}

.activityInfo > .editTime , .activityInfo > .editDone{
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
  justify-content: flex-start;
  align-items: center;
}
.activityInfo > .editDone{
  margin-top: -0.5rem;
  padding-left: 0.5rem;
}

@media (max-width: 768px) {
  .calendar {
    flex-direction: column-reverse;
    padding: 4rem 0;
    gap: 2rem;
    align-items: center;
  }

  .calendar-sidebar {
    width: 100%;
    padding: 0 1rem;
  }

  .createActivityButton {
    width: 80%;
    margin: 0 auto;
    margin-bottom: 2rem;
  }

  .createActivity {
    width: 90%;
  }

  .activityInfo {
    width: 90%;
  }

  .calendar-week {
    margin: 0;
    width: 100%;
  }

  .calendar-weekTop {
    padding: 0;
    overflow-y: hidden;
  }



}
</style>
