<!-- OrderStatus.vue - Страница статуса заказа -->
<template>
  <div class="order-status-page">
    <div class="status-container">
      <!-- Анимация загрузки -->
      <div v-if="loading" class="loading-state glass-panel">
        <div class="spinner"></div>
        <h2>Обработка заказа...</h2>
        <p>Пожалуйста, подождите, мы проверяем статус вашего платежа</p>
      </div>

      <!-- Успешный статус -->
      <div v-else-if="orderStatus === 'success'" class="success-state glass-panel">
        <div class="success-icon">✓</div>
        <h1>Заказ успешно оформлен!</h1>
        <p class="order-number">Номер заказа: #{{ orderData.orderNumber }}</p>
        
        <div class="order-details">
          <div class="details-grid">
            <div class="detail-item">
              <span class="detail-label">Дата заказа</span>
              <span class="detail-value">{{ orderData.date }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Способ оплаты</span>
              <span class="detail-value">{{ orderData.paymentMethod }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Способ доставки</span>
              <span class="detail-value">{{ orderData.deliveryMethod }}</span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Сумма заказа</span>
              <span class="detail-value">{{ formatPrice(orderData.total) }} ₽</span>
            </div>
          </div>

          <div class="delivery-address" v-if="orderData.address">
            <h3>📍 Адрес доставки</h3>
            <p>{{ orderData.address }}</p>
          </div>

          <div class="order-items">
            <h3>🛍️ Состав заказа</h3>
            <div class="items-list">
              <div v-for="item in orderData.items" :key="item.id" class="order-item">
                <img :src="item.image" :alt="item.name" class="item-img">
                <div class="item-info">
                  <div class="item-name">{{ item.name }}</div>
                  <div class="item-quantity">x{{ item.quantity }}</div>
                </div>
                <div class="item-price">{{ formatPrice(item.price * item.quantity) }} ₽</div>
              </div>
            </div>
          </div>
        </div>

        <div class="action-buttons">
          <router-link to="/profile/orders" class="btn-secondary">
            📋 Мои заказы
          </router-link>
          <router-link to="/catalog" class="btn-primary">
            🛍️ Продолжить покупки
          </router-link>
        </div>
      </div>

      <!-- Ошибка -->
      <div v-else-if="orderStatus === 'error'" class="error-state glass-panel">
        <div class="error-icon">⚠️</div>
        <h1>Ошибка при оплате</h1>
        <p>{{ errorMessage || 'Произошла ошибка при обработке платежа. Пожалуйста, попробуйте снова.' }}</p>
        
        <div class="action-buttons">
          <button class="btn-secondary" @click="retryPayment">🔄 Попробовать снова</button>
          <router-link to="/cart" class="btn-primary">🛒 Вернуться в корзину</router-link>
        </div>
      </div>

      <!-- Статус ожидания -->
      <div v-else-if="orderStatus === 'pending'" class="pending-state glass-panel">
        <div class="pending-icon">⏳</div>
        <h1>Заказ обрабатывается</h1>
        <p>Ваш заказ принят, но платеж еще не подтвержден. Мы уведомим вас по email при изменении статуса.</p>
        
        <div class="action-buttons">
          <router-link to="/profile/orders" class="btn-secondary">
            📋 Мои заказы
          </router-link>
          <router-link to="/" class="btn-primary">
            🏠 На главную
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const loading = ref(true)
const orderStatus = ref('pending') // 'pending', 'success', 'error'
const errorMessage = ref('')
const orderData = ref({
  orderNumber: '',
  date: '',
  paymentMethod: '',
  deliveryMethod: '',
  total: 0,
  address: '',
  items: []
})

// Форматирование цены
const formatPrice = (price) => {
  return price.toLocaleString('ru-RU')
}

// Получение данных заказа
const fetchOrderStatus = async () => {
  loading.value = true
  
  // Получаем ID заказа из URL параметров
  const orderId = route.query.order_id
  const sessionId = route.query.session_id
  
  try {
    // Имитация API запроса
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    // В реальном проекте здесь будет запрос к вашему API
    // const response = await axios.get(`/api/orders/status?order_id=${orderId}&session_id=${sessionId}`)
    // orderStatus.value = response.data.status
    // orderData.value = response.data.order
    
    // Mock данные для демонстрации
    const mockSuccess = true // Измените на false для теста ошибки
    
    if (mockSuccess) {
      orderStatus.value = 'success'
      orderData.value = {
        orderNumber: Math.floor(Math.random() * 1000000),
        date: new Date().toLocaleDateString('ru-RU'),
        paymentMethod: 'Банковская карта',
        deliveryMethod: 'Курьерская доставка',
        total: 139970,
        address: 'г. Москва, ул. Тверская, д. 15, кв. 45',
        items: [
          {
            id: 1,
            name: 'iPhone 15 Pro',
            price: 89990,
            quantity: 1,
            image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=80&h=80&fit=crop'
          },
          {
            id: 4,
            name: 'Sony WH-1000XM5',
            price: 24990,
            quantity: 2,
            image: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=80&h=80&fit=crop'
          }
        ]
      }
    } else {
      orderStatus.value = 'error'
      errorMessage.value = 'Платеж не прошел. Пожалуйста, проверьте данные карты или попробуйте другой способ оплаты.'
    }
  } catch (error) {
    orderStatus.value = 'error'
    errorMessage.value = 'Не удалось получить статус заказа. Пожалуйста, проверьте соединение или обратитесь в поддержку.'
  } finally {
    loading.value = false
  }
}

// Повторить платеж
const retryPayment = () => {
  router.push('/cart')
}

onMounted(() => {
  fetchOrderStatus()
})
</script>

<style scoped>
.order-status-page {
  min-height: 100vh;
  padding: 2rem;
  background: linear-gradient(135deg, #0a0a0f, #0f0f1a);
  display: flex;
  align-items: center;
  justify-content: center;
}

.status-container {
  max-width: 800px;
  width: 100%;
  margin: 0 auto;
}

/* Общие стили для состояний */
.loading-state,
.success-state,
.error-state,
.pending-state {
  padding: 2.5rem;
  border-radius: 32px;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  text-align: center;
}

/* Спиннер загрузки */
.spinner {
  width: 60px;
  height: 60px;
  margin: 0 auto 1.5rem;
  border: 3px solid rgba(139, 92, 246, 0.2);
  border-top: 3px solid #c084fc;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Успешный статус */
.success-icon {
  width: 80px;
  height: 80px;
  margin: 0 auto 1.5rem;
  background: linear-gradient(135deg, #22c55e, #16a34a);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  color: white;
  animation: scaleIn 0.5s ease;
}

@keyframes scaleIn {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  80% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

h1 {
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
  color: white;
}

.order-number {
  font-size: 1.2rem;
  color: #c084fc;
  margin-bottom: 2rem;
}

/* Детали заказа */
.order-details {
  text-align: left;
  margin: 2rem 0;
}

.details-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.detail-label {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.5);
}

.detail-value {
  font-size: 0.9rem;
  font-weight: 500;
  color: white;
}

/* Адрес доставки */
.delivery-address {
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.delivery-address h3 {
  font-size: 1rem;
  margin-bottom: 0.5rem;
  color: rgba(255, 255, 255, 0.8);
}

.delivery-address p {
  color: rgba(255, 255, 255, 0.6);
  line-height: 1.5;
}

/* Товары в заказе */
.order-items h3 {
  font-size: 1rem;
  margin-bottom: 1rem;
  color: rgba(255, 255, 255, 0.8);
}

.items-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.order-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 12px;
}

.item-img {
  width: 50px;
  height: 50px;
  border-radius: 8px;
  object-fit: cover;
}

.item-info {
  flex: 1;
}

.item-name {
  font-size: 0.85rem;
  font-weight: 500;
  margin-bottom: 0.2rem;
  color: white;
}

.item-quantity {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.5);
}

.item-price {
  font-weight: 600;
  color: #c084fc;
  font-size: 0.85rem;
}

/* Кнопки действий */
.action-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 2rem;
}

.btn-primary,
.btn-secondary {
  padding: 0.75rem 1.5rem;
  border-radius: 30px;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.2s;
  cursor: pointer;
  display: inline-block;
}

.btn-primary {
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  color: white;
  border: none;
}

.btn-primary:hover {
  transform: scale(1.02);
  opacity: 0.9;
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: white;
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.1);
}

/* Ошибка */
.error-icon,
.pending-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.error-state h1 {
  color: #f87171;
}

.pending-state h1 {
  color: #fbbf24;
}

/* Адаптивность */
@media (max-width: 768px) {
  .order-status-page {
    padding: 1rem;
  }
  
  .loading-state,
  .success-state,
  .error-state,
  .pending-state {
    padding: 1.5rem;
  }
  
  h1 {
    font-size: 1.3rem;
  }
  
  .order-number {
    font-size: 0.9rem;
  }
  
  .details-grid {
    grid-template-columns: 1fr;
    gap: 0.8rem;
  }
  
  .action-buttons {
    flex-direction: column;
  }
  
  .btn-primary,
  .btn-secondary {
    text-align: center;
  }
  
  .success-icon {
    width: 60px;
    height: 60px;
    font-size: 2rem;
  }
}

@media (max-width: 480px) {
  .order-item {
    flex-wrap: wrap;
  }
  
  .item-price {
    margin-left: 60px;
  }
}
</style>