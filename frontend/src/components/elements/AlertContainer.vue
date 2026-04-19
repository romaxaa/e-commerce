<template>
  <div class="alert-container">
    <TransitionGroup name="list" tag="div">
      <div 
        v-for="alert in alertStore.notifications" 
        :key="alert.id" 
        :class="['alert-item', alert.type]"
      >
        <span class="message">{{ alert.message }}</span>
        <button @click="alertStore.remove(alert.id)" class="close-btn">&times;</button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
import { useAlertStore } from '../../stores/alertStore';
const alertStore = useAlertStore();
</script>

<style scoped>
    .alert-container {
    position: fixed;
    top: 2rem;
    right: 2rem;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    }

    /* Стили в духе твоего GlassUI */
    .alert-item {
    padding: 1rem 1.5rem;
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    min-width: 250px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .success { border-left: 4px solid #4ade80; }
    .error { border-left: 4px solid #f87171; }

    /* Анимации появления */
    .list-enter-active, .list-leave-active {
    transition: all 0.5s ease;
    }
    .list-enter-from {
    opacity: 0;
    transform: translateX(30px);
    }
    .list-leave-to {
    opacity: 0;
    transform: scale(0.9);
    }
</style>