<template>
  <div v-if="visible" :class="['toast', type]" class="toast-container">
    <p>{{ message }}</p>
    <button @click="closeToast"><i class="fa-solid fa-x"></i></button>
  </div>
</template>

<script>
export default {
  name: "Toast",
  props: {
    type: {
      type: String,
      default: "info", // Possible values: 'info', 'success', 'warning', 'error'
    },
    message: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      visible: true,
    };
  },
  methods: {
    closeToast() {
      this.visible = false;
    },
  },
};
</script>

<style scoped>
.toast-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  padding: 15px;
  border-radius: 5px;
  color: #fff;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-width: 200px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.toast.info {
  background-color: #007bff;
}

.toast.success {
  background-color: #28a745;
}

.toast.warning {
  background-color: #ffc107;
  color: #212529;
}

.toast.error {
  background-color: #dc3545;
}

button {
  background: none;
  border: none;
  color: inherit;
  font-size: 16px;
  cursor: pointer;
}
</style>
