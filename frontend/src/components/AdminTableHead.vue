<template>
  <div class="tableHeaderItem" @click="toggleHeader">
    <span>{{ header }}</span>
    <i v-if="initialClick && nameHeaderClicked === header" class="fa-solid fa-arrow-down" :class="{'rotate': isClicked}"></i>
  </div>

</template>
<script>
export default {
  name: 'AdminTableHead',
  props: {
    header: {
      type: String,
      required: true
    },
    nameHeaderClicked: {
      type: String,
      default: ''
    }
  },
  emits: ['header-clicked'],
  data() {
    return {
      isClicked: false,
      initialClick:false
    };
  },
  methods: {
    toggleHeader() {
      this.isClicked = !this.isClicked;
      this.initialClick = true;
      this.$emit('header-clicked', this.header, this.isClicked);
    }
  }
};
</script>
<style scoped>
.tableHeaderItem {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.tableHeaderItem .fa-solid {
  transition: transform 0.3s ease;
}
.tableHeaderItem .fa-solid.rotate {
  transform: rotate(180deg);
}
</style>
