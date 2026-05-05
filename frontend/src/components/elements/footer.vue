<!-- Footer.vue -->
<template>
  <footer class="site-footer">
    <div class="footer-container">
      <div class="footer-grid">
        <!-- Колонка 1: Логотип и описание -->
        <div class="footer-col">
          <router-link to="/" class="footer-logo">
            <span class="logo-icon">✨</span>
            <span class="logo-text">Glass<span class="gradient">Shop</span></span>
          </router-link>
          <p class="footer-description">
            Премиальная электроника для тех, кто ценит качество и стиль. 
            Лучшие технологии по доступным ценам.
          </p>
          <div class="social-links">
            <a href="#" class="social-link" aria-label="Telegram">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21.5 4.5L2 12.5L8.5 15.5L12 22L16 16L21.5 4.5Z"></path>
                <path d="M8.5 15.5L12 22L16 16L21.5 4.5L8.5 15.5Z"></path>
              </svg>
            </a>
            <a href="#" class="social-link" aria-label="VK">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 2H7C4.2 2 2 4.2 2 7v10c0 2.8 2.2 5 5 5h10c2.8 0 5-2.2 5-5V7c0-2.8-2.2-5-5-5z"></path>
                <path d="M15.5 12c-1 0-1.5.5-1.5 1.5v2c0 1 .5 1.5 1.5 1.5s1.5-.5 1.5-1.5v-2c0-1-.5-1.5-1.5-1.5z"></path>
                <path d="M8.5 12c-1 0-1.5.5-1.5 1.5v2c0 1 .5 1.5 1.5 1.5s1.5-.5 1.5-1.5v-2c0-1-.5-1.5-1.5-1.5z"></path>
                <circle cx="12" cy="9" r="1.5"></circle>
              </svg>
            </a>
            <a href="#" class="social-link" aria-label="YouTube">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
                <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
              </svg>
            </a>
          </div>
        </div>

        <!-- Колонка 2: Каталог -->
        <div class="footer-col">
          <h3 class="footer-title">Каталог</h3>
          <ul class="footer-links">
            <li><router-link to="/catalog/smartphones">Смартфоны</router-link></li>
            <li><router-link to="/catalog/laptops">Ноутбуки</router-link></li>
            <li><router-link to="/catalog/headphones">Наушники</router-link></li>
            <li><router-link to="/catalog/watches">Умные часы</router-link></li>
            <li><router-link to="/catalog/cameras">Камеры</router-link></li>
            <li><router-link to="/catalog/accessories">Аксессуары</router-link></li>
          </ul>
        </div>

        <!-- Колонка 3: Информация -->
        <div class="footer-col">
          <h3 class="footer-title">Информация</h3>
          <ul class="footer-links">
            <li><router-link to="/about">О компании</router-link></li>
            <li><router-link to="/delivery">Доставка и оплата</router-link></li>
            <li><router-link to="/returns">Возврат товара</router-link></li>
            <li><router-link to="/warranty">Гарантия</router-link></li>
            <li><router-link to="/blog">Блог</router-link></li>
          </ul>
        </div>

        <!-- Колонка 4: Контакты -->
        <div class="footer-col">
          <h3 class="footer-title">Контакты</h3>
          <ul class="footer-contacts">
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
              </svg>
              <a href="tel:+78001234567">8 (800) 123-45-67</a>
            </li>
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
              </svg>
              <a href="mailto:info@glassshop.ru">info@glassshop.ru</a>
            </li>
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
              <span>г. Москва, ул. Тверская, 15</span>
            </li>
            <li>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>Ежедневно: 10:00 - 22:00</span>
            </li>
          </ul>
        </div>
      </div>

      <!-- Подписка на рассылку -->
      <div class="footer-newsletter">
        <div class="newsletter-content">
          <h3>Подпишитесь на новости</h3>
          <p>Будьте в курсе новинок и получайте персональные скидки</p>
        </div>
        <form class="newsletter-form" @submit.prevent="subscribe">
          <input type="email" v-model="newsletterEmail" placeholder="Ваш email" required>
          <button type="submit">Подписаться</button>
        </form>
      </div>

      <!-- Нижняя часть -->
      <div class="footer-bottom">
        <div class="copyright">
          © 2026 GlassShop. Все права защищены.
        </div>
        <div class="payment-methods">
          <span class="payment-icon">💳 Mastercard</span>
          <span class="payment-icon">💳 МИР</span>
          <span class="payment-icon">📱 SBP</span>
        </div>
        <div class="footer-bottom-links">
          <router-link to="/privacy">Политика конфиденциальности</router-link>
          <router-link to="/terms">Условия использования</router-link>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'

const newsletterEmail = ref('')

const subscribe = () => {
  if (newsletterEmail.value) {
    console.log('Подписка:', newsletterEmail.value)
    // Здесь будет API запрос
    newsletterEmail.value = ''
    alert('Спасибо за подписку!')
  }
}
</script>

<style scoped>
.site-footer {
  background: linear-gradient(180deg, rgba(10, 10, 15, 0.95), rgba(5, 5, 10, 0.98));
  backdrop-filter: blur(10px);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  margin-top: 4rem;
}

.footer-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 3rem 2rem 1.5rem;
}

.footer-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  margin-bottom: 3rem;
}

/* Логотип в футере */
.footer-logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.footer-logo .logo-text {
  color: white;
}

.footer-logo .gradient {
  background: linear-gradient(135deg, #c084fc, #60a5fa);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.footer-description {
  color: rgba(255, 255, 255, 0.6);
  line-height: 1.6;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
}

/* Социальные ссылки */
.social-links {
  display: flex;
  gap: 1rem;
}

.social-link {
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 50%;
  color: rgba(255, 255, 255, 0.7);
  transition: all 0.2s;
}

.social-link:hover {
  background: rgba(139, 92, 246, 0.3);
  color: #c084fc;
  transform: translateY(-2px);
}

/* Заголовки колонок */
.footer-title {
  color: white;
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 1.2rem;
  position: relative;
  display: inline-block;
}

.footer-title::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 30px;
  height: 2px;
  background: linear-gradient(90deg, #c084fc, #60a5fa);
  border-radius: 2px;
}

/* Ссылки */
.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-links li {
  margin-bottom: 0.75rem;
}

.footer-links a {
  color: rgba(255, 255, 255, 0.6);
  text-decoration: none;
  font-size: 0.9rem;
  transition: all 0.2s;
}

.footer-links a:hover {
  color: #c084fc;
  transform: translateX(4px);
  display: inline-block;
}

/* Контакты */
.footer-contacts {
  list-style: none;
  padding: 0;
  margin: 0;
}

.footer-contacts li {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.9rem;
}

.footer-contacts svg {
  flex-shrink: 0;
  color: rgba(139, 92, 246, 0.8);
}

.footer-contacts a {
  color: rgba(255, 255, 255, 0.6);
  text-decoration: none;
  transition: color 0.2s;
}

.footer-contacts a:hover {
  color: #c084fc;
}

/* Подписка */
.footer-newsletter {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 2rem;
  padding: 2rem 0;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.newsletter-content h3 {
  color: white;
  font-size: 1.2rem;
  margin-bottom: 0.25rem;
}

.newsletter-content p {
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.85rem;
}

.newsletter-form {
  display: flex;
  gap: 0.5rem;
}

.newsletter-form input {
  padding: 0.75rem 1rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  outline: none;
  width: 250px;
  transition: all 0.2s;
}

.newsletter-form input:focus {
  border-color: #c084fc;
}

.newsletter-form button {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 30px;
  color: white;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.newsletter-form button:hover {
  transform: scale(1.02);
  opacity: 0.9;
}

/* Нижняя часть */
.footer-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
  padding-top: 1rem;
  font-size: 0.8rem;
}

.copyright {
  color: rgba(255, 255, 255, 0.5);
}

.payment-methods {
  display: flex;
  gap: 1rem;
}

.payment-icon {
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.8rem;
}

.footer-bottom-links {
  display: flex;
  gap: 1.5rem;
}

.footer-bottom-links a {
  color: rgba(255, 255, 255, 0.5);
  text-decoration: none;
  transition: color 0.2s;
}

.footer-bottom-links a:hover {
  color: #c084fc;
}

/* Адаптивность */
@media (max-width: 1024px) {
  .footer-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
  }
}

@media (max-width: 768px) {
  .footer-container {
    padding: 2rem 1rem 1rem;
  }
  
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .footer-newsletter {
    flex-direction: column;
    text-align: center;
  }
  
  .newsletter-form {
    width: 100%;
  }
  
  .newsletter-form input {
    flex: 1;
  }
  
  .footer-bottom {
    flex-direction: column;
    text-align: center;
  }
  
  .footer-bottom-links {
    flex-direction: column;
    gap: 0.5rem;
  }
}
</style>