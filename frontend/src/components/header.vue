<!-- Header.vue -->
<template>
  <header class="site-header glass-panel">
    <div class="header-container">
      <!-- Логотип -->
      <router-link to="/" class="logo">
        <span class="logo-icon">✨</span>
        <span class="logo-text">Glass<span class="gradient">Shop</span></span>
      </router-link>

      <!-- Десктопная навигация -->
      <nav class="desktop-nav">
        <router-link to="/" class="nav-link" :class="{ active: isActive('/') }">
          Главная
        </router-link>
        <router-link to="/catalog" class="nav-link" :class="{ active: isActive('/catalog') }">
          Каталог
        </router-link>
        <router-link to="/blog" class="nav-link" :class="{ active: isActive('/blog') }">
          Блог
        </router-link>
        <router-link to="/about" class="nav-link" :class="{ active: isActive('/about') }">
          О нас
        </router-link>
      </nav>

      <!-- Действия пользователя -->
      <div class="user-actions">
        <!-- Поиск -->
        <button class="action-btn search-btn" @click="openSearch">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="10" cy="10" r="7"></circle>
            <line x1="21" y1="21" x2="15" y2="15"></line>
          </svg>
        </button>

        <!-- Избранное -->
        <router-link to="/favorites" class="action-btn favorites-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
          </svg>
          <span v-if="favoritesCount > 0" class="badge">{{ favoritesCount }}</span>
        </router-link>

        <!-- Корзина -->
        <button class="action-btn cart-btn" @click="toggleCart">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          <span v-if="cartCount > 0" class="badge">{{ cartCount }}</span>
        </button>

        <!-- Профиль / Авторизация -->
        <div v-if="authStore.isAuthenticated" class="profile-dropdown">
          <button class="action-btn profile-btn" @click="toggleProfileDropdown">
            <img :src="authStore?.user.avatar" :alt="authStore.user.username" class="avatar">
          </button>
          <div v-if="isProfileOpen" class="dropdown-menu">
            <router-link to="/profile" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
              Профиль
            </router-link>
            <router-link to="/orders" class="dropdown-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
              </svg>
              Мои заказы
            </router-link>
            <div v-if="authStore.user && authStore.user.group == 99">
              <router-link to="/admin" class="dropdown-item">
                Админ панель
              </router-link>
            </div>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item logout" @click="logout">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
              Выйти
            </button>
          </div>
        </div>

        <router-link v-else to="/login" class="login-btn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
            <polyline points="10 17 15 12 10 7"></polyline>
            <line x1="15" y1="12" x2="3" y2="12"></line>
          </svg>
          Войти
        </router-link>
      </div>

      <!-- Мобильное меню -->
      <button class="mobile-menu-btn" @click="toggleMobileMenu">
        <span class="menu-icon" :class="{ active: isMobileMenuOpen }">
          <span></span><span></span><span></span>
        </span>
      </button>
    </div>

    <!-- Мобильная навигация -->
    <transition name="mobile-menu">
      <div v-if="isMobileMenuOpen" class="mobile-nav">
        <router-link to="/" class="mobile-nav-link" @click="closeMobileMenu">
          <span>🏠</span> Главная
        </router-link>
        <router-link to="/catalog" class="mobile-nav-link" @click="closeMobileMenu">
          <span>📦</span> Каталог
        </router-link>
        <router-link to="/blog" class="mobile-nav-link" @click="closeMobileMenu">
          <span>📝</span> Блог
        </router-link>
        <router-link to="/about" class="mobile-nav-link" @click="closeMobileMenu">
          <span>ℹ️</span> О нас
        </router-link>
        <router-link to="/contacts" class="mobile-nav-link" @click="closeMobileMenu">
          <span>📞</span> Контакты
        </router-link>
        <div class="mobile-divider"></div>
        <router-link to="/favorites" class="mobile-nav-link" @click="closeMobileMenu">
          <span>❤️</span> Избранное
        </router-link>
        <div>
        <router-link to="/profile" class="mobile-nav-link" @click="closeMobileMenu">
          <span>👤</span> Профиль
        </router-link>
        </div>
      </div>
    </transition>

    <!-- Модальное окно поиска -->
    <transition name="modal">
      <div v-if="isSearchOpen" class="search-modal" @click.self="closeSearch">
        <div class="search-modal-content">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Поиск товаров..." 
            class="search-modal-input"
            autofocus
            @keyup.enter="search"
          >
          <button class="search-modal-close" @click="closeSearch">✕</button>
        </div>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore';

const authStore = useAuthStore();

const router = useRouter()

// Состояния
const isMobileMenuOpen = ref(false)
const isProfileOpen = ref(false)
const isSearchOpen = ref(false)
const searchQuery = ref('')

// Данные пользователя (заглушка)
const userAvatar = ref('https://i.pravatar.cc/150?img=3')
const cartCount = ref(0)
const favoritesCount = ref(0)

// Проверка активной ссылки
const isActive = (path) => 
{
  return router.path === path
}

// Методы
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
  if (isMobileMenuOpen.value) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
  document.body.style.overflow = ''
}

const toggleProfileDropdown = () => {
  isProfileOpen.value = !isProfileOpen.value
}

const openSearch = () => {
  isSearchOpen.value = true
  document.body.style.overflow = 'hidden'
}

const closeSearch = () => {
  isSearchOpen.value = false
  document.body.style.overflow = ''
}

const search = () => {
  if (searchQuery.value.trim()) {
    router.push(`/catalog?search=${encodeURIComponent(searchQuery.value)}`)
    closeSearch()
    searchQuery.value = ''
  }
}

const toggleCart = () => {
  // Открыть корзину
  console.log('Open cart')
}

const logout = async () => {
  await authStore.logout();
  router.push('/');
}

// Закрытие выпадающего меню при клике вне
document.addEventListener('click', (e) => {
  if (!e.target.closest('.profile-dropdown') && isProfileOpen.value) {
    isProfileOpen.value = false
  }
})
</script>

<style scoped>
.site-header {
  position: sticky;
  top: 0;
  z-index: 1000;
  background: rgba(10, 10, 15, 0.85);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 0;
}

.header-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 1rem 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 2rem;
}

/* Логотип */
.logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  font-size: 1.5rem;
  font-weight: 700;
}

.logo-icon {
  font-size: 1.8rem;
}

.logo-text {
  color: white;
}

.gradient {
  background: linear-gradient(135deg, #c084fc, #60a5fa);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Десктопная навигация */
.desktop-nav {
  display: flex;
  gap: 2rem;
  flex: 1;
  justify-content: center;
}

.nav-link {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s;
  position: relative;
}

.nav-link:hover {
  color: white;
}

.nav-link.active {
  color: #c084fc;
}

.nav-link.active::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, #c084fc, #60a5fa);
  border-radius: 2px;
}

/* Действия пользователя */
.user-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.action-btn {
  position: relative;
  background: rgba(255, 255, 255, 0.05);
  border: none;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: white;
  transition: all 0.2s;
}

.action-btn:hover {
  background: rgba(255, 255, 255, 0.15);
}

.badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: linear-gradient(135deg, #ef4444, #f97316);
  color: white;
  font-size: 0.7rem;
  font-weight: bold;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

/* Выпадающее меню профиля */
.profile-dropdown {
  position: relative;
}

.dropdown-menu {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  background: rgba(20, 20, 30, 0.95);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  min-width: 200px;
  padding: 0.5rem;
  animation: slideDown 0.2s ease;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1rem;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  border-radius: 12px;
  transition: all 0.2s;
}

.dropdown-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

.dropdown-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
  margin: 0.5rem 0;
}

.logout {
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  color: #f87171;
}

.logout:hover {
  background: rgba(248, 113, 113, 0.1);
  color: #f87171;
}

/* Кнопка входа */
.login-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  padding: 0.5rem 1.2rem;
  border-radius: 30px;
  color: white;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.2s;
}

.login-btn:hover {
  transform: scale(1.05);
  opacity: 0.9;
}

/* Мобильное меню */
.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
}

.menu-icon {
  display: flex;
  flex-direction: column;
  gap: 5px;
  width: 24px;
}

.menu-icon span {
  height: 2px;
  background: white;
  border-radius: 2px;
  transition: all 0.3s;
}

.menu-icon.active span:nth-child(1) {
  transform: rotate(45deg) translate(5px, 5px);
}

.menu-icon.active span:nth-child(2) {
  opacity: 0;
}

.menu-icon.active span:nth-child(3) {
  transform: rotate(-45deg) translate(5px, -5px);
}

.mobile-nav {
  display: none;
  position: fixed;
  top: 70px;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(10, 10, 15, 0.98);
  backdrop-filter: blur(12px);
  flex-direction: column;
  padding: 2rem;
  gap: 1rem;
  z-index: 999;
  overflow-y: auto;
}

.mobile-nav-link {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  color: white;
  text-decoration: none;
  font-size: 1.2rem;
  border-radius: 16px;
  transition: all 0.2s;
}

.mobile-nav-link:hover {
  background: rgba(255, 255, 255, 0.1);
}

.mobile-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
  margin: 0.5rem 0;
}

/* Модальное окно поиска */
.search-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(8px);
  z-index: 1100;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.search-modal-content {
  position: relative;
  width: 100%;
  max-width: 600px;
}

.search-modal-input {
  width: 100%;
  padding: 1.2rem 3rem 1.2rem 1.5rem;
  background: rgba(20, 20, 30, 0.95);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 60px;
  color: white;
  font-size: 1.1rem;
  outline: none;
  transition: all 0.2s;
}

.search-modal-input:focus {
  border-color: #c084fc;
  box-shadow: 0 0 20px rgba(192, 132, 252, 0.3);
}

.search-modal-close {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.6);
  font-size: 1.5rem;
  cursor: pointer;
  transition: all 0.2s;
}

.search-modal-close:hover {
  color: white;
}

/* Анимации */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition: all 0.3s;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.modal-enter-active,
.modal-leave-active {
  transition: all 0.3s;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

/* Адаптивность */
@media (max-width: 1024px) {
  .desktop-nav {
    gap: 1.5rem;
  }
  
  .header-container {
    gap: 1rem;
  }
}

@media (max-width: 768px) {
  .desktop-nav {
    display: none;
  }
  
  .mobile-menu-btn {
    display: block;
  }
  
  .mobile-nav {
    display: flex;
  }
  
  .action-btn:not(.profile-btn) {
    display: none;
  }
  
  .login-btn span {
    display: none;
  }
  
  .login-btn {
    padding: 0.5rem;
  }
  
  .header-container {
    padding: 0.8rem 1rem;
  }
  
  .logo-text {
    display: none;
  }
}
</style>