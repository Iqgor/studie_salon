<template>
  <div class="calendar">
    <aside class="calendar-sidebar">
      <!-- Example calendar grid -->
      <div class="calendar-header">
        <button @click="changeDate(new Date(currentDate.setMonth(currentDate.getMonth() - 1)))"><i
            class="fa-solid fa-arrow-left"></i></button>
        <h2>{{ currentDate.toLocaleString('default', { month: 'long' }) }} {{ currentDate.getFullYear() }}</h2>
        <button @click="changeDate(new Date(currentDate.setMonth(currentDate.getMonth() + 1)))"><i
            class="fa-solid fa-arrow-right"></i></button>
      </div>
      <div class="calendar-grid">
        <!-- Day names -->
        <div class="calendar-dayName " v-for="dayName in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
          :key="dayName">
          <strong>{{ dayName }}</strong>
        </div>
        <!-- Day numbers -->
        <!-- Previous month's days -->
        <div class="calendar-day" v-for="day in previousMonthDays" :key="'prev-' + day" style="opacity: 0.5;"
          @click="changeDate(new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, day))">
          {{ day }}
        </div>
        <!-- Current month's days -->
        <div class="calendar-day" :class="{ 'current-day': isCurrentDay(day), 'clicked-day': isClickedDay(day) }"
          v-for="(day, index) in daysInMonth" :key="day"
          @click="changeDate(new Date(currentDate.getFullYear(), currentDate.getMonth(), day))"
          :style="{ gridColumnStart: index === 0 ? (new Date(currentDate.getFullYear(), currentDate.getMonth(), 1).getDay() || 7) : 'auto' }">
          {{ day }}
        </div>
        <!-- Next month's days -->
        <div class="calendar-day" :class="{ 'current-day': isCurrentDay(day, 1) }" v-for="day in nextMonthDays"
          @click="changeDate(new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, day))" :key="'next-' + day"
          style="opacity: 0.5;">
          {{ day }}
        </div>
      </div>
    </aside>
    <div>

    </div>
  </div>
</template>
<script>
export default {
  name: 'Calendar',
  data() {
    return {
      currentDate: new Date(),
      daysInMonth: [],
      previousMonthDays: [],
      nextMonthDays: [],
    };
  },
  mounted() {
    // Initialize calendar or fetch data if needed
    this.initializeCalendar();
    this.daysInMonth = this.getDaysInMonth(this.currentDate);

  },
  methods: {
    isCurrentDay(day, monthOffset = 0) {
      const today = new Date();
      return (
        today.getDate() === day &&
        today.getMonth() === this.currentDate.getMonth() + monthOffset &&
        today.getFullYear() === this.currentDate.getFullYear()
      );
    },
    isClickedDay(day) {
      return this.currentDate.getDate() === day
    },
    // Add any methods needed for the calendar functionality
    initializeCalendar() {
      // Example: Fetch events or set up the calendar view
      console.log('Calendar initialized with date:', this.currentDate);
    },
    getDaysInMonth(date) {
      const year = date.getFullYear();
      const month = date.getMonth();

      // Calculate days from the previous month to display
      const previousMonthTotalDays = new Date(year, month, 0).getDate();
      const firstDayOfMonth = new Date(year, month, 1).getDay() || 7; // 1 = Monday, 7 = Sunday
      this.previousMonthDays = Array.from(
        { length: firstDayOfMonth - 1 },
        (_, i) => previousMonthTotalDays - (firstDayOfMonth - 2 - i)
      );

      // Calculate days from the next month to display
      const lastDayOfMonth = new Date(year, month + 1, 0).getDay() || 7; // 1 = Monday, 7 = Sunday
      this.nextMonthDays = Array.from(
        { length: 7 - lastDayOfMonth },
        (_, i) => i + 1
      );

      // Calculate days in the current month
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      return Array.from({ length: daysInMonth }, (_, i) => i + 1);
    },
    // Example method to change the current date
    changeDate(newDate) {
      this.currentDate = new Date(newDate);
      this.daysInMonth = this.getDaysInMonth(this.currentDate);
      console.log('Current date changed to:', this.currentDate);
    },
    // Example method to fetch events for the current date
    fetchEvents() {
      // Fetch events from an API or data source
      console.log('Fetching events for date:', this.currentDate);
      // Example: return axios.get(`/api/events?date=${this.currentDate}`);
    },

  }
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
  transition: background-color 0.3s ease;
}

.calendar-dayName {
  cursor: default;
  text-align: center;
  padding-bottom: 0.5rem;
}



.calendar-day:hover {
  background-color: var(--color-secondary);
  color: white;
}

.current-day {
  background-color: var(--color-primary) !important;
  color: white;
  font-weight: bold;
  opacity: 0.5;
  transition: 0.3s opacity;
}

.clicked-day {
  background-color: var(--color-secondary);
  color: white;
  font-weight: bold;
  opacity: 1;
}

.current-day:hover {
  background-color: var(--color-primary);
  color: white;
  opacity: 1;
}
</style>
