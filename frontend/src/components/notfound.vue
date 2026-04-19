<!-- NotFound.vue - Простая страница 404 -->
<template>
  <div class="not-found-page">
    <div class="not-found-container glass-panel">
      <!-- 404 код -->
      <div class="error-code">
        <span class="digit">4</span>
        <span class="digit">0</span>
        <span class="digit">4</span>
      </div>

      <!-- Иконка -->
      <div class="error-icon">
        🔍
      </div>

      <!-- Заголовок -->
      <h1 class="error-title">Страница не найдена</h1>
      
      <!-- Описание -->
      <p class="error-description">
        Извините, но страница, которую вы ищете, не существует или была перемещена.
      </p>

      <!-- Кнопки -->
      <div class="action-buttons">
        <router-link to="/" class="primary-btn">
          🏠 На главную
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.not-found-page {
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.not-found-container {
  max-width: 500px;
  width: 100%;
  padding: 3rem;
  background: rgba(20, 20, 30, 0.7);
  backdrop-filter: blur(12px);
  border-radius: 32px;
  text-align: center;
}

/* 404 код */
.error-code {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.digit {
  font-size: 5rem;
  font-weight: 800;
  background: linear-gradient(135deg, #c084fc, #60a5fa);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Иконка */
.error-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.7;
}

/* Заголовок */
.error-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1rem;
  color: white;
}

/* Описание */
.error-description {
  color: rgba(255, 255, 255, 0.6);
  line-height: 1.5;
  margin-bottom: 2rem;
}

/* Кнопки */
.action-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.primary-btn, .secondary-btn {
  padding: 0.75rem 1.5rem;
  border-radius: 30px;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.primary-btn {
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  color: white;
}

.primary-btn:hover {
  transform: scale(1.02);
  opacity: 0.9;
}

.secondary-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: white;
}

.secondary-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: translateY(-2px);
}

/* Адаптивность */
@media (max-width: 768px) {
  .not-found-container {
    padding: 2rem;
  }
  
  .digit {
    font-size: 3.5rem;
  }
  
  .error-icon {
    font-size: 3rem;
  }
  
  .error-title {
    font-size: 1.2rem;
  }
  
  .action-buttons {
    flex-direction: column;
  }
  
  .primary-btn, .secondary-btn {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .not-found-container {
    padding: 1.5rem;
  }
  
  .digit {
    font-size: 2.5rem;
  }
}
</style>