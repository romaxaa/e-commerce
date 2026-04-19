import { defineStore } from 'pinia';

export const useAlertStore = defineStore('alerts', {
  state: () => ({
    notifications: []
  }),
  actions: {
    show(message, type = 'info', timeout = 3000) {
      const id = Date.now();
      this.notifications.push({ id, message, type });

      // Автоматическое удаление через N секунд
      setTimeout(() => {
        this.remove(id);
      }, timeout);
    },
    remove(id) {
      this.notifications = this.notifications.filter(n => n.id !== id);
    }
  }
});