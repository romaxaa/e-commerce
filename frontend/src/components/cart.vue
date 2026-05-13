<!-- Cart.vue - Страница корзины -->
<template>
  <div class="cart-page">
    <div class="cart-container">
      <h1 class="cart-title">Корзина</h1>
      
      <!-- Пустая корзина -->
      <div v-if="cartProducts.length === 0" class="empty-cart glass-panel">
        <div class="empty-cart-icon">🛒</div>
        <h2>Ваша корзина пуста</h2>
        <p>Похоже, вы еще не добавили ни одного товара в корзину</p>
        <router-link to="/catalog" class="continue-shopping-btn">
          🛍️ Перейти в каталог
        </router-link>
      </div>

      <!-- Корзина с товарами -->
      <div v-else class="cart-content">
        <div class="cart-items-section">
          <!-- Список товаров -->
          <div class="cart-items glass-panel">
            <div class="cart-header">
              <div class="header-product">Товар</div>
              <div class="header-price">Цена</div>
              <div class="header-quantity">Количество</div>
              <div class="header-total">Итого</div>
              <div class="header-actions"></div>
            </div>

            <div v-for="item in cartProducts" :key="item.id" class="cart-item">
              <div class="item-product">
                <img :src="item.img" :alt="item.name" class="item-image">
                <div class="item-info">
                  <h3>{{ item.product_name }}</h3>
                  <p class="item-category">{{ item.category_name }}</p>
                  <div class="item-actions-mobile">
                    <div class="mobile-quantity">
                      <button @click="decrementQuantity(item)" :disabled="item.quantity <= 1">-</button>
                      <span>{{ item.quantity }}</span>
                      <button @click="incrementQuantity(item)" :disabled="item.quantity >= item.stock">+</button>
                    </div>
                    <button class="remove-mobile" @click="removeItem(item.product_id)">🗑️</button>
                  </div>
                </div>
              </div>
              <div class="item-price">{{ formatPrice(item.product_price) }} ₽</div>
              <div class="item-quantity">
                <button @click="decrementQuantity(item)" :disabled="item.count <= 1">-</button>
                <span>{{ item.count }}</span>
                <button @click="incrementQuantity(item)" :disabled="item.count >= 100 /*item.stock*/">+</button>
              </div>
              <div class="item-total">{{ formatPrice(item.product_price * item.count) }} ₽</div>
              <div class="item-actions">
                <button class="remove-btn" @click="removeItem(item.product_id)" title="Удалить">
                  🗑️
                </button>
              </div>
            </div>
          </div>

          <!-- Рекомендации -->
          <div class="recommendations" v-if="recommendedProducts.length">
            <h3>Вам также может понравиться</h3>
            <div class="recommendations-grid">
              <div v-for="product in recommendedProducts.slice(0, 4)" :key="product.id" class="rec-card glass-panel" @click="addToCart(product)">
                <img :src="product.image" :alt="product.name">
                <h4>{{ product.name }}</h4>
                <div class="price">{{ formatPrice(product.price) }} ₽</div>
                <button class="add-cart-btn">+ В корзину</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Сводка заказа -->
        <div class="cart-summary-section">
          <div class="cart-summary glass-panel">
            <h3>Сводка заказа</h3>
            
            <div class="summary-row">
              <span>Товары ({{ totalItems }} шт.)</span>
              <span>{{ formatPrice(subtotal) }} ₽</span>
            </div>
            
            <div class="summary-row">
              <span>Скидка</span>
              <span class="discount">-{{ formatPrice(discount) }} ₽</span>
            </div>
            
            <div class="summary-row">
              <span>Доставка</span>
              <span v-if="subtotal >= freeShippingThreshold">Бесплатно</span>
              <span v-else>{{ formatPrice(shippingCost) }} ₽</span>
            </div>
            
            <div class="summary-divider"></div>
            
            <div class="summary-row total">
              <span>Итого</span>
              <span>{{ formatPrice(total) }} ₽</span>
            </div>
            
            <div class="free-shipping-bar" v-if="subtotal < freeShippingThreshold">
              <div class="shipping-progress" :style="{ width: shippingProgress + '%' }"></div>
              <p>Добавьте товаров на {{ formatPrice(freeShippingThreshold - subtotal) }} ₽ для бесплатной доставки</p>
            </div>
            
            <div class="promo-code">
              <input 
                type="text" 
                v-model="promoCode" 
                placeholder="Промокод" 
                class="promo-input glass-input"
              >
              <button class="apply-btn" @click="applyPromo">Применить</button>
            </div>
            
            <button class="checkout-btn" @click="checkout">
              Оформить заказ → 
            </button>
            
            <router-link to="/catalog" class="continue-link">
              ← Продолжить покупки
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно удаления -->
    <transition name="modal">
      <div v-if="showRemoveModal" class="modal-overlay" @click.self="showRemoveModal = false">
        <div class="modal-content glass-panel">
          <h3>Удалить товар?</h3>
          <p>Вы уверены, что хотите удалить товар из корзины?</p>
          <div class="modal-actions">
            <button class="cancel-btn" @click="showRemoveModal = false">Отмена</button>
            <button class="confirm-btn" @click="confirmRemove">Удалить</button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore';
import { useAlertStore } from '../stores/alertStore';

const authStore = useAuthStore();
const alerts = useAlertStore();

const router = useRouter()

// Состояние
const cartItems = ref([])
const promoCode = ref('')
const appliedPromo = ref(null)
const showRemoveModal = ref(false)
const cartProducts = ref([])
const removeId = ref();

// Настройки
const freeShippingThreshold = 5000
const shippingCost = 500

// Вычисляемые значения
const totalItems = computed(() => {
  return cartProducts.value.reduce((sum, item) => sum + item.quantity, 0)
})

const subtotal = computed(() => {
  return cartProducts.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
})

const discount = computed(() => {
  if (appliedPromo.value) {
    return subtotal.value * 0.1 // 10% скидка
  }
  return 0
})

const shippingProgress = computed(() => {
  return (subtotal.value / freeShippingThreshold) * 100
})

const total = computed(() => {
  const shipping = subtotal.value >= freeShippingThreshold ? 0 : shippingCost
  return subtotal.value - discount.value + shipping
})

const fetchCart = async () => {
  const result = await authStore.fetchCart();

  if(result.success)
  {
    cartProducts.value = result.products;
  }
  
  if(result.empty)
  {
    alerts.show('у вас пустая корзина!', 'warning');
  }

  if(!result.success)
  {
    console.log(result.error);
  }

};

// Рекомендуемые товары (моковые данные)
const recommendedProducts = ref([
  {
    id: 101,
    name: 'Чехол для iPhone 15 Pro',
    price: 2990,
    image: 'https://images.unsplash.com/photo-1585336261022-680e9ce1c5b1?w=150&h=150&fit=crop'
  },
  {
    id: 102,
    name: 'Зарядное устройство 65W',
    price: 3990,
    image: 'https://images.unsplash.com/photo-1583863788434-e58a36330cd0?w=150&h=150&fit=crop'
  },
  {
    id: 103,
    name: 'Наушники True Wireless',
    price: 5990,
    image: 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=150&h=150&fit=crop'
  },
  {
    id: 104,
    name: 'Защитное стекло',
    price: 990,
    image: 'https://images.unsplash.com/photo-1592890288564-76628ab30d3c?w=150&h=150&fit=crop'
  }
])

// Форматирование цены
const formatPrice = (price) => {
  return price.toLocaleString('ru-RU')
}

// Управление количеством
const incrementQuantity = (item) => {
  if (item.quantity < item.stock) {
    item.quantity++
    saveCart()
  }
}

const decrementQuantity = (item) => {
  if (item.quantity > 1) {
    item.quantity--
    saveCart()
  }
}

// Удаление товара
const removeItem = async (product_id) => {
  removeId.value = product_id
  showRemoveModal.value = true
}

const confirmRemove = async () => {
  const result = await authStore.removeCart(removeId.value);

  if(result.success)
  {
    await fetchCart();
    showRemoveModal.value = false
    alerts.show('Товар успешно удален!', 'success');
  }

}

// Добавление товара в корзину
const addToCart = (product) => {
  const existingItem = cartItems.value.find(item => item.id === product.id)
  
  if (existingItem) 
  {
    existingItem.quantity++
  } else 
  {
    cartItems.value.push({
      ...product,
      quantity: 1
    })
  }
  
  saveCart()
}

// Применение промокода
const applyPromo = () => {
  if (promoCode.value.toLowerCase() === 'welcome10') {
    appliedPromo.value = { code: 'welcome10', discount: 10 }
    alert('Промокод применен! Скидка 10%')
  } else if (promoCode.value.toLowerCase() === 'freeShipping') {
    // Бесплатная доставка уже учитывается
    alert('Промокод применен! Бесплатная доставка')
  } else {
    alert('Неверный промокод')
  }
  promoCode.value = ''
}

// Оформление заказа
const checkout = () => {
  router.push('/checkout')
}

// Сохранение корзины в localStorage
const saveCart = () => {
  localStorage.setItem('cart', JSON.stringify(cartItems.value))
}

// Загрузка корзины из localStorage
const loadCart = () => {
  const savedCart = localStorage.getItem('cart')
  if (savedCart) 
  {
    cartItems.value = JSON.parse(savedCart)
  } 
  else 
  {
    // Моковые данные для примера
    cartItems.value = [
      {
        id: 1,
        name: 'iPhone 15 Pro',
        category: 'Смартфоны',
        price: 89990,
        quantity: 1,
        stock: 45,
        image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=150&h=150&fit=crop'
      },
      {
        id: 4,
        name: 'Sony WH-1000XM5',
        category: 'Наушники',
        price: 24990,
        quantity: 2,
        stock: 67,
        image: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=150&h=150&fit=crop'
      }
    ]
  }
}

onMounted(() => {
  loadCart();
  fetchCart();
})
</script>

<style scoped>
.cart-page {
  min-height: 80vh;
  padding: 2rem;
  background: linear-gradient(135deg, #0a0a0f, #0f0f1a);
}

.cart-container {
  max-width: 1400px;
  margin: 0 auto;
}

.cart-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 2rem;
}

/* Пустая корзина */
.empty-cart {
  text-align: center;
  padding: 4rem;
  border-radius: 32px;
}

.empty-cart-icon {
  font-size: 5rem;
  margin-bottom: 1rem;
}

.empty-cart h2 {
  margin-bottom: 0.5rem;
}

.empty-cart p {
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 2rem;
}

.continue-shopping-btn {
  display: inline-block;
  padding: 0.75rem 2rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border-radius: 30px;
  color: white;
  text-decoration: none;
  transition: all 0.2s;
}

.continue-shopping-btn:hover {
  transform: scale(1.02);
  opacity: 0.9;
}

/* Содержимое корзины */
.cart-content {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 2rem;
}

/* Секция товаров */
.cart-items-section {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.cart-items {
  padding: 1.5rem;
  border-radius: 32px;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
}

.cart-header {
  display: grid;
  grid-template-columns: 3fr 1fr 1.5fr 1fr 0.5fr;
  gap: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.85rem;
}

.cart-item {
  display: grid;
  grid-template-columns: 3fr 1fr 1.5fr 1fr 0.5fr;
  gap: 1rem;
  align-items: center;
  padding: 1rem 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.cart-item:last-child {
  border-bottom: none;
}

.item-product {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.item-image {
  width: 80px;
  height: 80px;
  border-radius: 12px;
  object-fit: cover;
}

.item-info h3 {
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.item-category {
  font-size: 0.75rem;
  color: #c084fc;
}

.item-price, .item-total {
  color: rgba(255, 255, 255, 0.9);
  font-weight: 500;
}

.item-quantity {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.item-quantity button {
  width: 28px;
  height: 28px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  color: white;
  cursor: pointer;
}

.item-quantity button:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.item-quantity span {
  min-width: 30px;
  text-align: center;
}

.remove-btn {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  opacity: 0.5;
  transition: opacity 0.2s;
}

.remove-btn:hover {
  opacity: 1;
}

/* Мобильные действия (скрыты на десктопе) */
.item-actions-mobile {
  display: none;
}

/* Рекомендации */
.recommendations h3 {
  font-size: 1.2rem;
  margin-bottom: 1rem;
}

.recommendations-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

.rec-card {
  padding: 1rem;
  border-radius: 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
}

.rec-card:hover {
  transform: translateY(-4px);
}

.rec-card img {
  width: 100%;
  height: 100px;
  object-fit: contain;
  margin-bottom: 0.5rem;
}

.rec-card h4 {
  font-size: 0.85rem;
  margin-bottom: 0.25rem;
}

.rec-card .price {
  color: #c084fc;
  font-weight: 600;
  font-size: 0.85rem;
  margin-bottom: 0.5rem;
}

.add-cart-btn {
  width: 100%;
  padding: 0.3rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  font-size: 0.7rem;
  cursor: pointer;
}

/* Сводка заказа */
.cart-summary {
  padding: 1.5rem;
  border-radius: 32px;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  position: sticky;
  top: 100px;
}

.cart-summary h3 {
  font-size: 1.2rem;
  margin-bottom: 1.5rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
  color: rgba(255, 255, 255, 0.7);
}

.summary-row .discount {
  color: #22c55e;
}

.summary-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
  margin: 1rem 0;
}

.summary-row.total {
  font-size: 1.2rem;
  font-weight: 700;
  color: white;
}

.summary-row.total span:last-child {
  color: #c084fc;
}

/* Прогресс бесплатной доставки */
.free-shipping-bar {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 30px;
  padding: 0.75rem;
  margin: 1rem 0;
  position: relative;
  overflow: hidden;
}

.shipping-progress {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  background: linear-gradient(90deg, #22c55e, #4ade80);
  opacity: 0.2;
  transition: width 0.3s;
}

.free-shipping-bar p {
  position: relative;
  font-size: 0.8rem;
  text-align: center;
  margin: 0;
}

/* Промокод */
.promo-code {
  display: flex;
  gap: 0.5rem;
  margin: 1rem 0;
}

.promo-input {
  flex: 1;
  padding: 0.5rem 1rem;
  font-size: 0.85rem;
}

.apply-btn {
  padding: 0.5rem 1rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  cursor: pointer;
}

/* Кнопка оформления */
.checkout-btn {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 30px;
  color: white;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  margin-bottom: 1rem;
}

.checkout-btn:hover {
  transform: scale(1.02);
  opacity: 0.9;
}

.continue-link {
  display: block;
  text-align: center;
  color: rgba(255, 255, 255, 0.5);
  text-decoration: none;
  font-size: 0.85rem;
}

.continue-link:hover {
  color: #c084fc;
}

/* Модальное окно */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal-content {
  max-width: 400px;
  width: 90%;
  padding: 2rem;
  background: rgba(20, 20, 30, 0.95);
  border-radius: 32px;
  text-align: center;
}

.modal-content h3 {
  margin-bottom: 1rem;
}

.modal-content p {
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 1.5rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
}

.cancel-btn, .confirm-btn {
  padding: 0.5rem 1.5rem;
  border-radius: 30px;
  cursor: pointer;
}

.cancel-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: white;
}

.confirm-btn {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  border: none;
  color: white;
}

/* Анимации */
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

/* Адаптивность */
@media (max-width: 1024px) {
  .cart-content {
    grid-template-columns: 1fr;
  }
  
  .cart-summary {
    position: static;
  }
  
  .recommendations-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .cart-page {
    padding: 1rem;
  }
  
  .cart-title {
    font-size: 1.5rem;
    margin-bottom: 1rem;
  }
  
  .cart-header {
    display: none;
  }
  
  .cart-item {
    grid-template-columns: 1fr;
    gap: 0.75rem;
  }
  
  .item-product {
    flex-direction: row;
  }
  
  .item-price, .item-quantity, .item-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .item-price::before {
    content: "Цена:";
    color: rgba(255, 255, 255, 0.5);
  }
  
  .item-total::before {
    content: "Итого:";
    color: rgba(255, 255, 255, 0.5);
  }
  
  .item-actions {
    display: none;
  }
  
  .item-actions-mobile {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 0.5rem;
  }
  
  .mobile-quantity {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .mobile-quantity button {
    width: 28px;
    height: 28px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: white;
    cursor: pointer;
  }
  
  .remove-mobile {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
  }
  
  .recommendations-grid {
    grid-template-columns: 1fr;
  }
  
  .rec-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    text-align: left;
  }
  
  .rec-card img {
    width: 60px;
    height: 60px;
  }
  
  .rec-card h4 {
    flex: 1;
  }
  
  .add-cart-btn {
    width: auto;
    padding: 0.3rem 1rem;
  }
}
</style>