import { reactive } from 'vue'

let id = 0

export const toastService = reactive({
  toasts: [],

  addToast(title, description, type = 'message', time = 4000) {
    const toast = {
      id,
      title,
      description,
      type
    }
    this.toasts.push(toast)
    const toastId = id
    id++

    // setTimeout(() => {
    //   this.removeToast(toastId)
    // }, time)
    
  },

  removeToast(id) {
    const el = document.getElementById(`toast-${id}`)
    if (el) {
      el.classList.add('toast-anim')
    }

    setTimeout(() => {
      this.toasts = this.toasts.filter(t => t.id !== id)
    }, 1000)
  }
})
