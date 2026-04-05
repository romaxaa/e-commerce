<!-- Admin.vue - Главная страница админ панели -->
<template>
  <div class="admin-page">
    <div class="admin-container">
      <!-- Боковая панель -->
      <aside class="admin-sidebar glass-panel">
        <div class="sidebar-header">
          <div class="logo">
            <span class="logo-icon">🛡️</span>
            <span class="logo-text">Admin<span class="gradient">Panel</span></span>
          </div>
        </div>

        <nav class="admin-nav">
          <router-link to="/admin" class="nav-item" :class="{ active: $route.path === '/admin' }">
            <span class="nav-icon">📊</span>
            <span class="nav-label">Дашборд</span>
          </router-link>
          <router-link to="/manage-category" class="nav-item" :class="{ active: $route.path === '/manage-category' }">
            <span class="nav-icon">📁</span>
            <span class="nav-label">Категории</span>
          </router-link>
          <router-link to="/manage-product" class="nav-item" :class="{ active: $route.path === '/manage-product' }">
            <span class="nav-icon">📦</span>
            <span class="nav-label">Товары</span>
          </router-link>
          <router-link to="/admin/orders" class="nav-item" :class="{ active: $route.path === '/admin/orders' }">
            <span class="nav-icon">🚚</span>
            <span class="nav-label">Заказы</span>
          </router-link>
          <router-link to="/admin/users" class="nav-item" :class="{ active: $route.path === '/admin/users' }">
            <span class="nav-icon">👥</span>
            <span class="nav-label">Пользователи</span>
          </router-link>
          <router-link to="/admin/reviews" class="nav-item" :class="{ active: $route.path === '/admin/reviews' }">
            <span class="nav-icon">⭐</span>
            <span class="nav-label">Отзывы</span>
          </router-link>
          <router-link to="/admin/settings" class="nav-item" :class="{ active: $route.path === '/admin/settings' }">
            <span class="nav-icon">⚙️</span>
            <span class="nav-label">Настройки</span>
          </router-link>
        </nav>

        <div class="sidebar-footer">
          <router-link to="/" class="back-to-shop">
            <span>← Вернуться в магазин</span>
          </router-link>
        </div>
      </aside>

      <!-- Основной контент -->
      <main class="admin-content">
        <div class="content-header">
          <h1>{{ pageTitle }}</h1>
          <div class="admin-actions">
            <div class="admin-search">
              <input type="text" placeholder="Поиск..." class="search-input glass-input">
            </div>
            <div class="admin-profile">
              <img :src="admin.avatar" alt="Admin" class="admin-avatar">
              <div class="admin-info">
                <span class="admin-name">{{ admin.name }}</span>
                <span class="admin-role">Администратор</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Дашборд -->
        <div v-if="$route.path === '/admin'" class="dashboard">
          <!-- Статистика -->
          <div class="stats-grid">
            <div class="stat-card glass-panel">
              <div class="stat-icon">💰</div>
              <div class="stat-info">
                <span class="stat-value">{{ formatPrice(stats.totalRevenue) }}</span>
                <span class="stat-label">Общий доход</span>
              </div>
              <div class="stat-change positive">+12.5%</div>
            </div>

            <div class="stat-card glass-panel">
              <div class="stat-icon">📦</div>
              <div class="stat-info">
                <span class="stat-value">{{ stats.totalOrders }}</span>
                <span class="stat-label">Заказов</span>
              </div>
              <div class="stat-change positive">+8.2%</div>
            </div>

            <div class="stat-card glass-panel">
              <div class="stat-icon">👥</div>
              <div class="stat-info">
                <span class="stat-value">{{ stats.totalUsers }}</span>
                <span class="stat-label">Пользователей</span>
              </div>
              <div class="stat-change positive">+15.3%</div>
            </div>

            <div class="stat-card glass-panel">
              <div class="stat-icon">⭐</div>
              <div class="stat-info">
                <span class="stat-value">{{ stats.averageRating }}</span>
                <span class="stat-label">Средний рейтинг</span>
              </div>
              <div class="stat-change positive">+0.8</div>
            </div>
          </div>

          <!-- График продаж -->
          <div class="chart-section glass-panel">
            <div class="section-header">
              <h2>Продажи за последние 7 дней</h2>
              <select class="chart-select glass-input">
                <option>Эта неделя</option>
                <option>Прошлая неделя</option>
                <option>Этот месяц</option>
              </select>
            </div>
            <div class="chart-container">
              <div class="bar-chart">
                <div v-for="(day, index) in salesData" :key="index" class="bar-wrapper">
                  <div class="bar" :style="{ height: (day.value / maxSales) * 200 + 'px' }"></div>
                  <span class="bar-label">{{ day.label }}</span>
                  <span class="bar-value">{{ formatPrice(day.value) }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="two-columns">
            <!-- Последние заказы -->
            <div class="recent-orders glass-panel">
              <div class="section-header">
                <h2>Последние заказы</h2>
                <router-link to="/admin/orders" class="view-all">Все заказы →</router-link>
              </div>
              <div class="orders-list">
                <div v-for="order in recentOrders" :key="order.id" class="order-row">
                  <div class="order-info">
                    <span class="order-id">#{{ order.id }}</span>
                    <span class="order-customer">{{ order.customer }}</span>
                  </div>
                  <div class="order-amount">{{ formatPrice(order.amount) }} ₽</div>
                  <div class="order-status" :class="order.statusClass">{{ order.status }}</div>
                </div>
              </div>
            </div>

            <!-- Популярные товары -->
            <div class="popular-products glass-panel">
              <div class="section-header">
                <h2>Популярные товары</h2>
                <router-link to="/admin/products" class="view-all">Все товары →</router-link>
              </div>
              <div class="products-list">
                <div v-for="product in popularProducts" :key="product.id" class="product-row">
                  <img :src="product.image" :alt="product.name" class="product-thumb">
                  <div class="product-info">
                    <span class="product-name">{{ product.name }}</span>
                    <span class="product-sales">{{ product.sales }} продаж</span>
                  </div>
                  <div class="product-revenue">{{ formatPrice(product.revenue) }} ₽</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Дочерние компоненты будут рендериться здесь -->
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/authStore';

const authStore = useAuthStore();

onMounted(async () => 
{
    // Если данных в сторе еще нет, загружаем их один раз
    if (!authStore.user) 
    {
      await authStore.checkAuth();
    }

    // Если после проверки пользователя всё еще нет — на выход
    if (!authStore.user || !authStore.user.group == 99)
    {
      router.push("/");
    }
});

const route = useRoute()

// Заголовок страницы
const pageTitle = computed(() => {
  const titles = {
    '/admin': 'Дашборд',
    '/admin/categories': 'Управление категориями',
    '/admin/products': 'Управление товарами',
    '/admin/orders': 'Управление заказами',
    '/admin/users': 'Управление пользователями',
    '/admin/reviews': 'Управление отзывами',
    '/admin/settings': 'Настройки магазина'
  }
  return titles[route.path] || 'Админ панель'
})

// Данные админа
const admin = ref({
  name: 'Алексей Иванов',
  avatar: 'https://i.pravatar.cc/150?img=3',
  role: 'admin'
})

// Статистика
const stats = ref({
  totalRevenue: 1250000,
  totalOrders: 1247,
  totalUsers: 5689,
  averageRating: 4.8
})

// Данные для графика
const salesData = ref([
  { label: 'Пн', value: 125000 },
  { label: 'Вт', value: 98000 },
  { label: 'Ср', value: 142000 },
  { label: 'Чт', value: 167000 },
  { label: 'Пт', value: 189000 },
  { label: 'Сб', value: 210000 },
  { label: 'Вс', value: 198000 }
])

const maxSales = computed(() => Math.max(...salesData.value.map(d => d.value)))

// Последние заказы
const recentOrders = ref([
  { id: '12345', customer: 'Иван Петров', amount: 89990, status: 'Доставлен', statusClass: 'delivered' },
  { id: '12344', customer: 'Мария Сидорова', amount: 24990, status: 'В пути', statusClass: 'shipping' },
  { id: '12343', customer: 'Алексей Смирнов', amount: 119990, status: 'Обработка', statusClass: 'processing' },
  { id: '12342', customer: 'Елена Волкова', amount: 35990, status: 'Оплачен', statusClass: 'paid' },
  { id: '12341', customer: 'Дмитрий Козлов', amount: 49990, status: 'Доставлен', statusClass: 'delivered' }
])

// Популярные товары
const popularProducts = ref([
  { id: 1, name: 'iPhone 15 Pro', image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=50&h=50&fit=crop', sales: 234, revenue: 20979000 },
  { id: 2, name: 'MacBook Air M3', image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=50&h=50&fit=crop', sales: 156, revenue: 18718440 },
  { id: 3, name: 'Sony WH-1000XM5', image: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=50&h=50&fit=crop', sales: 189, revenue: 4723110 }
])

// Форматирование цены
const formatPrice = (price) => {
  return price.toLocaleString('ru-RU')
}
</script>

<style scoped>
.admin-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0a0f, #0f0f1a);
}

.admin-container {
  display: flex;
  min-height: 100vh;
}

/* Боковая панель */
.admin-sidebar {
  width: 280px;
  background: rgba(15, 15, 25, 0.95);
  backdrop-filter: blur(12px);
  border-right: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
  height: 100vh;
}

.sidebar-header {
  padding: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.3rem;
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

/* Навигация */
.admin-nav {
  flex: 1;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem 1rem;
  border-radius: 16px;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  transition: all 0.2s;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.05);
  color: white;
}

.nav-item.active {
  background: linear-gradient(135deg, rgba(147, 51, 234, 0.2), rgba(59, 130, 246, 0.2));
  color: #c084fc;
}

.nav-icon {
  font-size: 1.2rem;
}

.nav-label {
  font-size: 0.95rem;
  font-weight: 500;
}

/* Футер сайдбара */
.sidebar-footer {
  padding: 1.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.back-to-shop {
  color: rgba(255, 255, 255, 0.6);
  text-decoration: none;
  font-size: 0.9rem;
  display: block;
  text-align: center;
}

.back-to-shop:hover {
  color: white;
}

/* Основной контент */
.admin-content {
  flex: 1;
  padding: 2rem;
  overflow-x: auto;
}

.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.content-header h1 {
  font-size: 1.8rem;
  font-weight: 600;
  background: linear-gradient(135deg, #fff, #c084fc);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.admin-actions {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.admin-search {
  position: relative;
}

.search-input {
  padding: 0.5rem 1rem 0.5rem 2.5rem;
  width: 250px;
}

.search-icon {
  position: absolute;
  left: 0.8rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 0.9rem;
  opacity: 0.5;
}

.admin-profile {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.admin-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.admin-info {
  display: flex;
  flex-direction: column;
}

.admin-name {
  font-size: 0.9rem;
  font-weight: 500;
}

.admin-role {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.5);
}

/* Статистика */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  padding: 1.5rem;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border-radius: 24px;
  display: flex;
  align-items: center;
  gap: 1rem;
  position: relative;
}

.stat-icon {
  font-size: 2.5rem;
}

.stat-info {
  flex: 1;
}

.stat-value {
  display: block;
  font-size: 1.8rem;
  font-weight: 700;
  color: #c084fc;
}

.stat-label {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.6);
}

.stat-change {
  font-size: 0.75rem;
  padding: 0.2rem 0.5rem;
  border-radius: 30px;
}

.stat-change.positive {
  background: rgba(34, 197, 94, 0.2);
  color: #4ade80;
}

.stat-change.negative {
  background: rgba(239, 68, 68, 0.2);
  color: #f87171;
}

/* График */
.chart-section {
  padding: 1.5rem;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border-radius: 24px;
  margin-bottom: 2rem;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.section-header h2 {
  font-size: 1.2rem;
  font-weight: 600;
}

.view-all {
  color: #c084fc;
  text-decoration: none;
  font-size: 0.85rem;
}

.chart-select {
  padding: 0.4rem 1rem;
  font-size: 0.85rem;
}

.chart-container {
  padding: 1rem 0;
}

.bar-chart {
  display: flex;
  justify-content: space-around;
  align-items: flex-end;
  height: 250px;
  gap: 1rem;
}

.bar-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}

.bar {
  width: 100%;
  max-width: 60px;
  background: linear-gradient(180deg, #c084fc, #3b82f6);
  border-radius: 8px 8px 4px 4px;
  transition: height 0.3s;
  min-height: 4px;
}

.bar-label {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.6);
}

.bar-value {
  font-size: 0.7rem;
  color: #c084fc;
}

/* Две колонки */
.two-columns {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.recent-orders, .popular-products {
  padding: 1.5rem;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border-radius: 24px;
}

/* Список заказов */
.orders-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.order-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 12px;
}

.order-info {
  display: flex;
  flex-direction: column;
}

.order-id {
  font-weight: 600;
  font-size: 0.85rem;
}

.order-customer {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.5);
}

.order-amount {
  font-weight: 500;
  color: #c084fc;
}

.order-status {
  padding: 0.2rem 0.6rem;
  border-radius: 30px;
  font-size: 0.7rem;
}

.order-status.delivered {
  background: rgba(34, 197, 94, 0.2);
  color: #4ade80;
}

.order-status.shipping {
  background: rgba(245, 158, 11, 0.2);
  color: #fbbf24;
}

.order-status.processing {
  background: rgba(59, 130, 246, 0.2);
  color: #60a5fa;
}

.order-status.paid {
  background: rgba(139, 92, 246, 0.2);
  color: #c084fc;
}

/* Список товаров */
.products-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.product-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 12px;
}

.product-thumb {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  object-fit: cover;
}

.product-info {
  flex: 1;
}

.product-name {
  display: block;
  font-size: 0.9rem;
  font-weight: 500;
}

.product-sales {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.5);
}

.product-revenue {
  font-weight: 600;
  color: #c084fc;
  font-size: 0.85rem;
}

/* Glass компоненты */
.glass-panel {
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.glass-input {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  padding: 0.5rem 1rem;
  color: white;
  outline: none;
}

.glass-input:focus {
  border-color: #c084fc;
}

/* Адаптивность */
@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .two-columns {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .admin-container {
    flex-direction: column;
  }
  
  .admin-sidebar {
    width: 100%;
    height: auto;
    position: relative;
  }
  
  .admin-nav {
    flex-direction: row;
    flex-wrap: wrap;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .content-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .admin-actions {
    width: 100%;
    justify-content: space-between;
  }
  
  .bar-chart {
    gap: 0.5rem;
  }
  
  .bar-label {
    font-size: 0.7rem;
  }
}
</style>