<template>
  <div class="calendar">
    <aside class="calendar-sidebar">
      <div class="calendar-header">
        <button @click="changeMonth(-1)">
          <i class="fa-solid fa-arrow-left"></i>
        </button>
        <h2>
          {{ shownDate.toLocaleString('default', { month: 'long' }) }}
          {{ shownDate.getFullYear() }}
        </h2>
        <button @click="changeMonth(1)">
          <i class="fa-solid fa-arrow-right"></i>
        </button>
      </div>
      <div class="calendar-grid">
        <div class="calendar-dayName" v-for="dayName in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
          :key="dayName">
          <strong>{{ dayName }}</strong>
        </div>
        <!-- Day numbers -->
        <!-- Previous month's days -->
        <div class="calendar-day" :class="{
          'current-day': isCurrentDay(day.day, -1), 'clicked-day': isClickedDay(day.day, -1),
          'week-day': currentWeekDays.some(weekDay => weekDay.day === day.day && weekDay.month === day.month)
        }" v-for="day in previousMonthDays" :key="'prev-' + day.day" style="opacity: 0.5;"
          @click="selectDate(day.day, day.month, day.year)">
          {{ day.day }}
        </div>
        <div class="calendar-day" :class="{
          'current-day': isCurrentDay(day.day), 'clicked-day': isClickedDay(day.day),
          'week-day': currentWeekDays.some(weekDay => weekDay.day === day.day && weekDay.month === day.month)
        }" v-for="(day, index) in daysInMonth" :key="day.day" @click="selectDate(day.day, day.month, day.year)"
          :style="{ gridColumnStart: index === 0 ? (new Date(shownDate.getFullYear(), shownDate.getMonth(), 1).getDay() || 7) : 'auto' }">
          {{ day.day }}
        </div>
        <!-- Next month's days -->
        <div class="calendar-day" :class="{
          'current-day': isCurrentDay(day.day, 1), 'clicked-day': isClickedDay(day.day, 1),
          'week-day': currentWeekDays.some(weekDay => weekDay.day === day.day && weekDay.month === day.month)
        }" v-for="day in nextMonthDays" @click="selectDate(day.day, day.month, day.year)" :key="'next-' + day.day"
          style="opacity: 0.5;">
          {{ day.day }}
        </div>
      </div>
    </aside>
    <div class="calendar-week">
      <div class="calendar-weekTop">
        <div v-for="days in getDaysOfWeek()" @click="selectDate(days.day, days.month, days.year)"><span
            :class="{ 'current-day': isCurrentDay(days.day, 0, days.month), 'clicked-day': isClickedDay(days.day, 0, days.month) }">{{
              days.day
            }}</span><span>{{ days.name }}</span></div>
      </div>
      <div class="calendar-weekBottom">

        <ul class="calendar-weekBottomList">
          <!--Voor nu nog standaard gewoon 7 dagen zonder iets er in -->
          <li v-for="(days, i) in getDaysOfWeek()"
            :class="{ 'currentlist-day': isCurrentDay(days.day, 0, days.month) || isClickedDay(days.day, 0, days.month) }"
            :key="i">
            <div v-for="hour in 24" :key="hour" class="calendar-hourSlot">
              <figure :style="timeLineStyle()" class="hourLine"
                v-if="new Date().getHours() === (hour - 1) && isCurrentDay(days.day, 0, days.month)">
                <figure></figure>
              </figure>
              <p v-if="i === 0">

                <span class="hourSlot">
                  {{ hour < 10 ? '0' + hour - 1 : hour - 1 }}:00 </span>
              </p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'Calendar',
  data() {
    return {
      shownDate: new Date(), // Used for navigating the calendar
      setDate: null, // Used for selecting a specific day for activities
      daysInMonth: [],
      previousMonthDays: [],
      nextMonthDays: [],
      currentTime: new Date(),
      intervalId: null, // Store the interval ID for clearing it later
    };
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
    this.$nextTick(() => {
      const currentHourLine = document.querySelector('.hourLine');
      if (currentHourLine) {
        const calendarWeekBottom = document.querySelector('.calendar-weekBottomList');
        const parentElement = currentHourLine.parentElement;
        if (calendarWeekBottom) {
          calendarWeekBottom.scrollTo({
            top: parentElement.offsetTop - calendarWeekBottom.offsetTop - 150,
            behavior: 'smooth',
          });
        }

      }
    });
  },
  beforeDestroy() {
    clearInterval(this.intervalId)
  },
  methods: {
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
    initializeCalendar() {
      console.log('Calendar initialized with date:', this.shownDate);
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

    },
    selectDate(day, month, year) {
      this.setDate = new Date(
        year,
        month,
        day
      );
      this.shownDate = this.setDate
      this.daysInMonth = this.getDaysInMonth(this.shownDate);

      console.log('Selected date for activities:', this.setDate);
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
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  padding: 0 1.25rem;
  padding-bottom: 1rem;
}

.calendar-header>button {
  background-color: var(--color-background);
  border: none;
  color: var(--color-primary);
  font-size: 125%;
  cursor: pointer;
  transition: background-color 0.3s ease;

}

.calendar-grid {
  width: 100%;
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 0.5rem;
}

.calendar-day {
  background-color: var(--color-background);
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
  background-color: var(--color-secondary);
  color: white;
  opacity: 1 !important;
}

.week-day {
  background-color: var(--color-secondary);
  color: white;
  font-weight: bold;
  opacity: 0.5;
}

.current-day {
  background-color: var(--color-primary) !important;
  color: white;
  font-weight: bold;
  transition: 0.3s opacity;
  opacity: 1 !important;
}

.clicked-day {
  background-color: var(--color-secondary);
  color: white;
  font-weight: bold;
  opacity: 1 !important;
}

.current-day:hover {
  background-color: var(--color-primary);
  color: white;

}



.calendar-week {
  width: 100%;
  margin: 0 4rem;
  background: rgba(0, 0, 0, 0.037);
  box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
  border-radius: 1rem;
}

.calendar-weekTop {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  overflow-y: scroll;
  border-bottom: 0.125rem solid rgba(0, 0, 0, 0.2);
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
  border-right: 0.125rem solid rgba(0, 0, 0, 0.2);
}

.calendar-weekTop>div>span:first-of-type {
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
}

.calendar-weekBottomList {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  list-style: none;
  height: 100%;
  overflow: auto scroll;
}

.calendar-weekBottomList>li:not(:last-child) {
  border-right: 0.125rem solid rgba(0, 0, 0, 0.2);
}

.calendar-hourSlot {
  border-bottom: 0.125rem solid rgba(0, 0, 0, 0.2);
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

.currentlist-day {
  box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
}

.hourLine {
  position: absolute;
  width: 100%;
  height: 2px;
  background-color: var(--color-primary);
}

.hourLine>figure {
  position: absolute;
  top: -0.75rem;
  height: 1.5rem;
  left: -0.75rem;
  aspect-ratio: 1/1;
  background-color: var(--color-primary);
  border-radius: 50%;
}
</style>
