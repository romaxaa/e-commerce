<template>
  <div class="auth-page">
    <div class="auth-container glass-panel">
      
      <!-- Переключатель между входом и регистрацией -->
      <div class="auth-tabs">
        <button class="tab-btn" :class="{ active: isLogin }" @click="isLogin = true">Вход</button>
        <button class="tab-btn" :class="{ active: !isLogin }" @click="isLogin = false">Регистрация</button>
      </div>

      <!-- Форма входа -->
      <form v-if="isLogin" @submit.prevent="handleLogin" class="auth-form">
        <div class="form-group">
          <label>Email</label>
          <input 
            type="email" 
            v-model="email" 
            placeholder="example@mail.com" 
            required
            class="glass-input"
          >
        </div>

        <div class="form-group">
          <label>Пароль</label>
          <div class="password-wrapper">
            <input 
              :type="showPassword ? 'text' : 'password'" 
              v-model="password" 
              placeholder="••••••••" 
              required
              class="glass-input"
            >
            <button type="button" class="toggle-password" @click="showPassword = !showPassword">
              {{ showPassword ? '🙈' : '👁️' }}
            </button>
          </div>
        </div>

        <div class="form-options">
          <label class="checkbox-label">
            <input type="checkbox" v-model="rememberMe">
            <span>Запомнить меня</span>
          </label>
          <router-link to="/forgot-password" class="forgot-link">Забыли пароль?</router-link>
        </div>

        <button type="submit" class="auth-btn" :disabled="loading">
          <span v-if="!loading" >Войти</span>
          <span v-else class="loader"></span>
        </button>

        <div class="social-auth">
          <p>или войдите через</p>
          <div class="social-buttons">
            <button type="button" class="social-btn google" @click="socialLogin('google')">
              <svg width="20" height="20" viewBox="0 0 24 24">
                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              Google
            </button>
            <button type="button" class="social-btn vk" @click="socialLogin('vk')">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21.579 6.855c.14-.465 0-.805-.662-.805h-2.193c-.558 0-.813.295-.953.62 0 0-1.115 2.719-2.695 4.482-.51.51-.742.673-1.02.673-.139 0-.34-.163-.34-.628V6.855c0-.558-.161-.805-.626-.805H9.642c-.348 0-.558.258-.558.504 0 .528.79.65.87 2.135v3.228c0 .707-.128.836-.408.836-.742 0-2.546-2.727-3.616-5.846-.21-.6-.422-.857-.982-.857H2.754c-.627 0-.754.295-.754.62 0 .582.742 3.468 3.455 7.281 1.81 2.62 4.362 4.04 6.687 4.04 1.393 0 1.564-.313 1.564-.85v-1.96c0-.627.133-.752.577-.752.327 0 .887.163 2.197 1.423 1.495 1.495 1.74 2.163 2.583 2.163h2.193c.626 0 .94-.313.758-.93-.197-.616-.907-1.51-1.849-2.57-.511-.6-1.277-1.246-1.51-1.568-.327-.418-.233-.603 0-.977 0 0 2.667-3.755 2.944-5.027z"/>
              </svg>
              VK
            </button>
          </div>
        </div>
      </form>

      <!-- Форма регистрации -->
      <form v-else @submit.prevent="handleRegister" class="auth-form">
        <div class="form-group">
          <label>Юзернейм</label>
          <input 
            type="text" 
            v-model="name" 
            placeholder="Иван Иванов" 
            required
            class="glass-input"
          >
        </div>

        <div class="form-group">
          <label>Email</label>
          <input 
            type="email" 
            v-model="reg_email" 
            placeholder="example@mail.com" 
            required
            class="glass-input"
          >
        </div>

        <div class="form-group">
          <label>Пароль</label>
          <div class="password-wrapper">
            <input 
              :type="showRegisterPassword ? 'text' : 'password'" 
              v-model="reg_password" 
              placeholder="••••••••" 
              required
              class="glass-input"
            >
            <button type="button" class="toggle-password" @click="showRegisterPassword = !showRegisterPassword">
              {{ showRegisterPassword ? '🙈' : '👁️' }}
            </button>
          </div>
        </div>

        <div class="form-group">
          <label>Подтвердите пароль</label>
          <div class="password-wrapper">
            <input 
              :type="showConfirmPassword ? 'text' : 'password'" 
              v-model="reg_password_repeat" 
              placeholder="••••••••" 
              required
              class="glass-input"
            >
            <button type="button" class="toggle-password" @click="showConfirmPassword = !showConfirmPassword">
              {{ showConfirmPassword ? '🙈' : '👁️' }}
            </button>
          </div>
        </div>

        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" v-model="agreeTerms" required>
            <span>Я согласен с <router-link to="/terms">условиями использования</router-link> и 
            <router-link to="/privacy">политикой конфиденциальности</router-link></span>
          </label>
        </div>

        <button type="submit" class="auth-btn" :disabled="loading">
          <span v-if="!loading">Зарегистрироваться</span>
          <span v-else class="loader"></span>
        </button>

        <div class="social-auth">
          <p>или зарегистрируйтесь через</p>
          <div class="social-buttons">
            <button type="button" class="social-btn google" @click="socialLogin('google')">
              <svg width="20" height="20" viewBox="0 0 24 24">
                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
              </svg>
              Google
            </button>
            <button type="button" class="social-btn vk" @click="socialLogin('vk')">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21.579 6.855c.14-.465 0-.805-.662-.805h-2.193c-.558 0-.813.295-.953.62 0 0-1.115 2.719-2.695 4.482-.51.51-.742.673-1.02.673-.139 0-.34-.163-.34-.628V6.855c0-.558-.161-.805-.626-.805H9.642c-.348 0-.558.258-.558.504 0 .528.79.65.87 2.135v3.228c0 .707-.128.836-.408.836-.742 0-2.546-2.727-3.616-5.846-.21-.6-.422-.857-.982-.857H2.754c-.627 0-.754.295-.754.62 0 .582.742 3.468 3.455 7.281 1.81 2.62 4.362 4.04 6.687 4.04 1.393 0 1.564-.313 1.564-.85v-1.96c0-.627.133-.752.577-.752.327 0 .887.163 2.197 1.423 1.495 1.495 1.74 2.163 2.583 2.163h2.193c.626 0 .94-.313.758-.93-.197-.616-.907-1.51-1.849-2.57-.511-.6-1.277-1.246-1.51-1.568-.327-.418-.233-.603 0-.977 0 0 2.667-3.755 2.944-5.027z"/>
              </svg>
              VK
            </button>
          </div>
        </div>
      </form>

      <!-- Сообщение об ошибке -->
      <transition name="fade">
        <div v-if="errorMessage" class="error-message">
          {{ errorMessage }}
        </div>
      </transition>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios';
import { useAuthStore } from '../../stores/authStore';

const authStore = useAuthStore();
const email = ref('');
const password = ref('');
const reg_email = ref('');
const reg_password = ref('');
const reg_password_repeat = ref('');
const name = ref('');


const router = useRouter()

const handleLogin = async () => {
    try 
    {
        const response = await axios.post('/api/json.php', {
        type: 'login',
        email: email.value,
        password: password.value
        });
        if(response.data.result == "auth")
        {
          console.log("auth");
          authStore.setUser(response.data.user);
          router.push('/');
        }
    } 
    catch (error) 
    {
      console.error("Ошибка!", error);
    }
}

const handleRegister = async () => {
    try 
    {
        const response = await axios.post('/api/json.php', {
        type: 'register',
        name: name.value,
        email: reg_email.value,
        password: reg_password.value,
        password_repeat: reg_password.value
        });
        if(response.data.result == "good")
        {
          authStore.setUser(response.data.user);
          router.push('/');
        }
    } 
    catch (error) 
    {
      console.error("Ошибка!", error);
    }
}

// Состояние
const isLogin = ref(true)
const loading = ref(false)
const showPassword = ref(false)
const showRegisterPassword = ref(false)
const showConfirmPassword = ref(false)
const rememberMe = ref(false)
const agreeTerms = ref(false)
const errorMessage = ref('')

// Социальная авторизация
const socialLogin = (provider) => 
{
  console.log(`Login with ${provider}`)
  // Здесь будет логика OAuth
}
</script>

<style scoped>
.auth-page {
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: linear-gradient(135deg, rgba(0,0,0,0.8), rgba(20,20,30,0.9));
}

.auth-container {
  max-width: 450px;
  width: 100%;
  padding: 2rem;
  background: rgba(20, 20, 30, 0.7);
  backdrop-filter: blur(12px);
  border-radius: 32px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

/* Табы */
.auth-tabs {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 60px;
  padding: 0.25rem;
}

.tab-btn {
  flex: 1;
  padding: 0.75rem;
  background: transparent;
  border: none;
  border-radius: 60px;
  color: rgba(255, 255, 255, 0.6);
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.tab-btn.active {
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  color: white;
  box-shadow: 0 4px 15px rgba(147, 51, 234, 0.3);
}

/* Форма */
.auth-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.9rem;
  font-weight: 500;
}

.glass-input {
  padding: 0.8rem 1rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  font-size: 1rem;
  outline: none;
  transition: all 0.3s;
}

.glass-input:focus {
  border-color: #c084fc;
  box-shadow: 0 0 15px rgba(192, 132, 252, 0.2);
}

.glass-input::placeholder {
  color: rgba(255, 255, 255, 0.3);
}

/* Поле с паролем */
.password-wrapper {
  position: relative;
}

.password-wrapper .glass-input {
  width: 100%;
  padding-right: 3rem;
}

.toggle-password {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
  opacity: 0.6;
  transition: opacity 0.2s;
}

.toggle-password:hover {
  opacity: 1;
}

/* Опции формы */
.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.85rem;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  color: rgba(255, 255, 255, 0.7);
}

.checkbox-label input {
  width: 16px;
  height: 16px;
  cursor: pointer;
  accent-color: #9333ea;
}

.forgot-link {
  color: #c084fc;
  text-decoration: none;
  transition: color 0.2s;
}

.forgot-link:hover {
  color: #a855f7;
}

/* Кнопка авторизации */
.auth-btn {
  padding: 0.9rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 30px;
  color: white;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  margin-top: 0.5rem;
}

.auth-btn:hover:not(:disabled) {
  transform: scale(1.02);
  box-shadow: 0 10px 25px -5px rgba(147, 51, 234, 0.4);
}

.auth-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Социальная авторизация */
.social-auth {
  margin-top: 1rem;
  text-align: center;
}

.social-auth p {
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.85rem;
  margin-bottom: 1rem;
  position: relative;
}

.social-auth p::before,
.social-auth p::after {
  content: '';
  position: absolute;
  top: 50%;
  width: 30%;
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
}

.social-auth p::before {
  left: 0;
}

.social-auth p::after {
  right: 0;
}

.social-buttons {
  display: flex;
  gap: 1rem;
}

.social-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.7rem;
  border: none;
  border-radius: 30px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s;
}

.social-btn.google {
  background: rgba(255, 255, 255, 0.05);
  color: white;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.social-btn.google:hover {
  background: rgba(255, 255, 255, 0.1);
  transform: translateY(-2px);
}

.social-btn.vk {
  background: rgba(75, 105, 180, 0.2);
  color: #4b6db4;
  border: 1px solid rgba(75, 105, 180, 0.3);
}

.social-btn.vk:hover {
  background: rgba(75, 105, 180, 0.3);
  transform: translateY(-2px);
}

/* Сообщение об ошибке */
.error-message {
  margin-top: 1rem;
  padding: 0.75rem;
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 30px;
  color: #f87171;
  text-align: center;
  font-size: 0.85rem;
}

/* Лоадер */
.loader {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Анимации */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* Адаптивность */
@media (max-width: 480px) {
  .auth-container {
    padding: 1.5rem;
  }
  
  .social-buttons {
    flex-direction: column;
  }
  
  .form-options {
    flex-direction: column;
    gap: 0.75rem;
    align-items: flex-start;
  }
  
  .auth-tabs {
    margin-bottom: 1.5rem;
  }
  
  .tab-btn {
    padding: 0.6rem;
  }
}
</style>