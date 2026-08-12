<!-- Checkout.vue - Полностью рабочая страница оформления заказа -->
<template>
  <div class="checkout-page">
    <div class="checkout-container">
      <h1 class="checkout-title">Оформление заказа</h1>

      <div class="checkout-layout">
        <!-- Левая колонка - форма -->
        <div class="checkout-form">
          <!-- Контактные данные -->
        <div v-if="authStore.user == null">
            <div class="form-section glass-panel">
                <div class="section-header">
                <h2>Контактные данные</h2>
                </div>
                
                <div class="form-row">
                <div class="form-group">
                    <label>Имя <span class="required">*</span></label>
                    <input type="text" v-model="orderData.name" placeholder="Иван" class="glass-input">
                </div>
                <div class="form-group">
                    <label>Фамилия <span class="required">*</span></label>
                    <input type="text" v-model="orderData.lastname" placeholder="Иванов" class="glass-input">
                </div>
                </div>
                
                <div class="form-row">
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" v-model="orderData.email" placeholder="ivan@example.com" class="glass-input">
                </div>
                <div class="form-group">
                    <label>Телефон <span class="required">*</span></label>
                    <input type="tel" v-model="orderData.phone" placeholder="+7 (999) 123-45-67" class="glass-input">
                </div>
                </div>
            </div>
        </div>

          <!-- Способ доставки -->
          <div class="form-section glass-panel">
            <h2>Способ доставки</h2>
            <div class="delivery-options">
              <label class="radio-card" :class="{ active: orderData.delivery === 'courier' }">
                <input type="radio" value="courier" v-model="orderData.delivery">
                <div class="radio-content">
                  <span class="radio-icon">🚚</span>
                  <div>
                    <div class="radio-title">Курьерская доставка</div>
                    <div class="radio-desc">Доставка на дом</div>
                  </div>
                  <span class="radio-price">от 300 ₽</span>
                </div>
              </label>

              <label class="radio-card" :class="{ active: orderData.delivery === 'pickup' }">
                <input type="radio" value="pickup" v-model="orderData.delivery">
                <div class="radio-content">
                  <span class="radio-icon">🏪</span>
                  <div>
                    <div class="radio-title">Пункт выдачи</div>
                    <div class="radio-desc">Лично в руки</div>
                  </div>
                  <span class="radio-price">Бесплатно</span>
                </div>
              </label>

              <label class="radio-card" :class="{ active: orderData.delivery === 'post' }">
                <input type="radio" value="post" v-model="orderData.delivery">
                <div class="radio-content">
                  <span class="radio-icon">📮</span>
                  <div>
                    <div class="radio-title">Почта России</div>
                    <div class="radio-desc">Доставка в регионы</div>
                  </div>
                  <span class="radio-price">от 350 ₽</span>
                </div>
              </label>
            </div>
          </div>

          <!-- Адрес доставки (не для самовывоза) -->
          <div v-if="orderData.delivery !== 'pickup'" class="form-section glass-panel">
            <div class="section-header">
              <h2>Адрес доставки</h2>
              <div class="address-actions">
                <button 
                  v-if="savedAddresses.length > 0" 
                  type="button" 
                  class="action-btn-link"
                  @click="showAddressSelector = !showAddressSelector"
                >
                  {{ showAddressSelector ? 'Скрыть' : 'Выбрать из сохраненных' }}
                </button>
                <button 
                  type="button" 
                  class="action-btn-link"
                  @click="showNewAddressForm = !showNewAddressForm"
                >
                  {{ showNewAddressForm ? 'Отмена' : '+ Новый адрес' }}
                </button>
              </div>
            </div>

            <!-- Выбор сохраненного адреса -->
            <div v-if="showAddressSelector && savedAddresses.length > 0" class="saved-addresses">
              <div 
                v-for="address in savedAddresses" 
                :key="address.id"
                class="address-card"
                :class="{ active: selectedAddressId === address.id }"
                @click="selectAddress(address)"
              >
                <div class="address-type">
                  <span class="type-badge">{{ address.type }}</span>
                  <span v-if="address.isDefault" class="default-badge">По умолчанию</span>
                </div>
                <div class="address-text">{{ address.fullAddress }}</div>
                <div class="address-phone">📞 {{ address.phone }}</div>
              </div>
            </div>

            <!-- Форма адреса -->
            <div v-if="showNewAddressForm || (savedAddresses.length === 0 && !selectedAddressId)" class="address-form">
              <div class="form-row">
                <div class="form-group">
                  <label>Город <span class="required">*</span></label>
                  <input type="text" v-model="addressForm.city" placeholder="Москва" class="glass-input">
                </div>
                <div class="form-group">
                  <label>Улица <span class="required">*</span></label>
                  <input type="text" v-model="addressForm.street" placeholder="ул. Тверская" class="glass-input">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Дом <span class="required">*</span></label>
                  <input type="text" v-model="addressForm.house" placeholder="15" class="glass-input">
                </div>
                <div class="form-group">
                  <label>Квартира</label>
                  <input type="text" v-model="addressForm.apartment" placeholder="45" class="glass-input">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Индекс</label>
                  <input type="text" v-model="addressForm.postalCode" placeholder="125009" class="glass-input">
                </div>
                <div class="form-group">
                  <label>Тип адреса</label>
                  <select v-model="addressForm.type" class="glass-input">
                    <option value="Дом">Дом</option>
                    <option value="Работа">Работа</option>
                    <option value="Другой">Другой</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label>Комментарий к заказу</label>
                <textarea v-model="orderData.comment" placeholder="Пожелания по доставке..." rows="2" class="glass-input"></textarea>
              </div>
              <div class="form-actions" v-if="savedAddresses.length > 0">
                <button type="button" class="btn-secondary" @click="cancelAddressForm">Отмена</button>
                <button type="button" class="btn-primary" @click="saveNewAddress">Сохранить адрес</button>
              </div>
            </div>

            <!-- Отображение выбранного адреса -->
            <div v-else-if="selectedAddressId && !showNewAddressForm" class="selected-address">
              <div class="selected-header">
                <span>✅ Адрес доставки</span>
                <button type="button" class="action-btn-link" @click="changeAddress">Изменить</button>
              </div>
              <div class="selected-details">
                <strong>{{ selectedAddress.fullAddress }}</strong>
                <div>📞 {{ selectedAddress.phone }}</div>
              </div>
            </div>
          </div>

          <!-- Информация о самовывозе -->
          <div v-else class="form-section glass-panel pickup-info">
            <div class="pickup-icon">🏪</div>
            <h3>Самовывоз</h3>
            <p>г. Москва, ул. Тверская, 15</p>
            <p>Ежедневно: 10:00 - 22:00</p>
          </div>

          <!-- Способ оплаты -->
          <div class="form-section glass-panel">
            <h2>Способ оплаты</h2>
            <div class="payment-options">
              <label class="radio-card" :class="{ active: orderData.payment === 'card' }">
                <input type="radio" value="card" v-model="orderData.payment">
                <div class="radio-content">
                  <span class="radio-icon">💳</span>
                  <div>
                    <div class="radio-title">Банковская карта</div>
                    <div class="radio-desc">Visa, Mastercard, МИР</div>
                  </div>
                </div>
              </label>

              <label class="radio-card" :class="{ active: orderData.payment === 'sbp' }">
                <input type="radio" value="sbp" v-model="orderData.payment">
                <div class="radio-content">
                  <span class="radio-icon">📱</span>
                  <div>
                    <div class="radio-title">СБП</div>
                    <div class="radio-desc">Оплата по QR-коду</div>
                  </div>
                </div>
              </label>

              <label class="radio-card" :class="{ active: orderData.payment === 'cash' }">
                <input type="radio" value="cash" v-model="orderData.payment">
                <div class="radio-content">
                  <span class="radio-icon">💰</span>
                  <div>
                    <div class="radio-title">Наличные</div>
                    <div class="radio-desc">При получении</div>
                  </div>
                </div>
              </label>
            </div>
          </div>

          <!-- Промокод -->
          <div class="form-section glass-panel">
            <h2>Промокод</h2>
            <div class="promo-code">
              <input type="text" v-model="promoCode" placeholder="Введите промокод" class="glass-input">
              <button class="apply-btn" @click="applyPromo">Применить</button>
            </div>
            <div v-if="discount > 0" class="discount-info">
              🎉 Скидка {{ discount }}% применена
            </div>
          </div>
        </div>

        <!-- Правая колонка - итоги -->
        <div class="checkout-summary">
          <div class="summary-card glass-panel">
            <h2>Ваш заказ</h2>

            <div class="cart-items">
              <div v-for="item in cartItems" :key="item.id" class="cart-item">
                <img :src="item.img" class="item-img">
                <div class="item-info">
                  <div class="item-name">{{ item.product_name }}</div>
                  <div class="item-quantity">x{{ item.count }}</div>
                </div>
                <div class="item-price">{{ formatPrice(item.product_price * item.count) }} ₽</div>
              </div>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row">
              <span>Товары ({{ totalItems }} шт.)</span>
              <span>{{ formatPrice(subtotal) }} ₽</span>
            </div>

            <div class="summary-row" v-if="discount > 0">
              <span>Скидка ({{ discount }}%)</span>
              <span class="discount">-{{ formatPrice(subtotal * discount / 100) }} ₽</span>
            </div>

            <div class="summary-row">
              <span>Доставка</span>
              <span>{{ deliveryCost === 0 ? 'Бесплатно' : formatPrice(deliveryCost) + ' ₽' }}</span>
            </div>

            <div class="summary-divider"></div>

            <div class="summary-row total">
              <span>Итого к оплате</span>
              <span>{{ formatPrice(total) }} ₽</span>
            </div>

            <button class="checkout-btn" @click="submitOrder" :disabled="loading">
              {{ loading ? 'Оформление...' : 'Оформить заказ →' }}
            </button>

            <p class="secure-info">🔒 Безопасная оплата</p>
          </div>
        </div>
      </div>
    </div>
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

// Адреса
const savedAddresses = ref([])
const cartItems = ref([])
const selectedAddressId = ref(null)
const selectedAddress = ref(null)
const showAddressSelector = ref(false)
const showNewAddressForm = ref(false)

const addressForm = ref({
  city: '',
  street: '',
  house: '',
  apartment: '',
  postalCode: '',
  type: 'Дом'
})

// Промокод
const promoCode = ref('')
const discount = ref(0)
const loading = ref(false)

// Корзина
const fetchCart = async () => {
  const result = await authStore.fetchCart();

  if(result.success)
  {
    cartItems.value = result.products;
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

// Вычисления
const totalItems = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + item.count, 0)
})

const subtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + (item.product_price * item.count), 0)
})

const deliveryCost = computed(() => {
  if (orderData.value.delivery === 'pickup') return 0
  if (orderData.value.delivery === 'courier') {
    return subtotal.value >= 5000 ? 0 : 300
  }
  return 350
})

const total = computed(() => {
  const discountAmount = subtotal.value * discount.value / 100
  return subtotal.value - discountAmount + deliveryCost.value
})

// Методы
const formatPrice = (price) => {
  return price.toLocaleString('ru-RU')
}

const loadUserData = async () => {
  if (authStore.user != null) 
  {
    savedAddresses.value = [
      {
        id: 1,
        type: 'Дом',
        city: 'Москва',
        street: 'ул. Тверская',
        house: '15',
        apartment: '45',
        postalCode: '125009',
        phone: '+7 (999) 123-45-67',
        isDefault: true,
        fullAddress: 'г. Москва, ул. Тверская, д. 15, кв. 45'
      },
      {
        id: 2,
        type: 'Работа',
        city: 'Москва',
        street: 'пр. Ленина',
        house: '10',
        apartment: '505',
        postalCode: '119019',
        phone: '+7 (999) 765-43-21',
        isDefault: false,
        fullAddress: 'г. Москва, пр. Ленина, д. 10, оф. 505'
      }
    ]
    
    const defaultAddress = savedAddresses.value.find(a => a.isDefault)
    if (defaultAddress) {
      selectAddress(defaultAddress)
    }
  }
}

const selectAddress = (address) => {
  selectedAddressId.value = address.id
  selectedAddress.value = address
  showAddressSelector.value = false
  showNewAddressForm.value = false
}

const saveNewAddress = () => {
  if (!addressForm.value.city || !addressForm.value.street || !addressForm.value.house) {
    alert('Заполните обязательные поля')
    return
  }
  
  const newAddress = {
    id: Date.now(),
    ...addressForm.value,
    phone: orderData.value.phone,
    isDefault: false,
    fullAddress: `${addressForm.value.city}, ${addressForm.value.street}, д. ${addressForm.value.house}${addressForm.value.apartment ? ', кв. ' + addressForm.value.apartment : ''}`
  }
  
  savedAddresses.value.push(newAddress)
  selectAddress(newAddress)
  cancelAddressForm()
}

const cancelAddressForm = () => {
  showNewAddressForm.value = false
  addressForm.value = {
    city: '',
    street: '',
    house: '',
    apartment: '',
    postalCode: '',
    type: 'Дом'
  }
}

const changeAddress = () => {
  selectedAddressId.value = null
  selectedAddress.value = null
  showAddressSelector.value = true
}

const applyPromo = () => {
  if (promoCode.value.toLowerCase() === 'welcome10') {
    discount.value = 10
    alert('Скидка 10% применена')
  } else {
    alert('Неверный промокод')
  }
}

const orderData = ref({
    name: '',
    lastname: '',
    email: '',
    phone: '',
    delivery: 'courier',
    payment: '',
    adress_id: 1
})

const submitOrder = async () => {

    if(authStore.user == null)
    {
        if (!orderData.value.name || !orderData.value.phone || !orderData.value.email) 
        {
            alerts.show('Заполните контактные данные', 'error');
            return
        }
    }
    
    if (orderData.value.delivery !== 'pickup' && !selectedAddressId.value && !addressForm.value.city) 
    {
        alerts.show('Укажите адрес доставки', 'error');
        return
    }

    if (!orderData.value.payment) 
    {
        alerts.show('Укажите способ оплаты', 'error');
        return
    }
    
    loading.value = true

    const result = await authStore.Checkout(orderData.value.delivery, orderData.value.payment, orderData.value.adress_id);

    if(result.success)
    {
        alerts.show('Заказ успешно оформлен!', 'success');
        loading.value = false
    }
    
}

onMounted(() => {
  loadUserData()
  fetchCart()
})
</script>

<style scoped>
.checkout-page {
  min-height: 100vh;
  padding: 2rem;
  background: linear-gradient(135deg, #0a0a0f, #0f0f1a);
}

.checkout-container {
  max-width: 1400px;
  margin: 0 auto;
}

.checkout-title {
  font-size: 2rem;
  margin-bottom: 2rem;
  color: white;
}

.checkout-layout {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 2rem;
}

/* Левая колонка */
.checkout-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-section {
  padding: 1.5rem;
  border-radius: 24px;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.2rem;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.form-section h2 {
  font-size: 1.2rem;
  font-weight: 600;
  color: white;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.7);
}

.required {
  color: #ef4444;
}

.glass-input {
  padding: 0.75rem 1rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  color: white;
  font-size: 0.9rem;
  outline: none;
}

.glass-input:focus {
  border-color: #c084fc;
}

textarea.glass-input {
  resize: vertical;
}

/* Радио карточки */
.delivery-options, .payment-options {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.radio-card {
  display: block;
  cursor: pointer;
}

.radio-card input {
  display: none;
}

.radio-content {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  transition: all 0.2s;
}

.radio-card.active .radio-content {
  background: rgba(139, 92, 246, 0.15);
  border-color: #c084fc;
}

.radio-icon {
  font-size: 1.8rem;
}

.radio-title {
  font-weight: 500;
  margin-bottom: 0.2rem;
  color: white;
}

.radio-desc {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.5);
}

.radio-price {
  margin-left: auto;
  font-weight: 600;
  color: #c084fc;
  font-size: 0.85rem;
}

/* Кнопки действий */
.address-actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn-link {
  background: none;
  border: none;
  color: #c084fc;
  font-size: 0.75rem;
  cursor: pointer;
  padding: 0.2rem 0.5rem;
}

.action-btn-link:hover {
  text-decoration: underline;
}

/* Сохраненные адреса */
.saved-addresses {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.address-card {
  padding: 0.8rem;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s;
}

.address-card:hover {
  background: rgba(139, 92, 246, 0.1);
}

.address-card.active {
  background: rgba(139, 92, 246, 0.15);
  border-color: #c084fc;
}

.address-type {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 0.3rem;
}

.type-badge {
  padding: 0.15rem 0.5rem;
  background: rgba(139, 92, 246, 0.2);
  border-radius: 30px;
  font-size: 0.65rem;
  color: #c084fc;
}

.default-badge {
  padding: 0.15rem 0.5rem;
  background: rgba(34, 197, 94, 0.2);
  border-radius: 30px;
  font-size: 0.65rem;
  color: #4ade80;
}

.address-text {
  font-size: 0.85rem;
  margin-bottom: 0.2rem;
  color: white;
}

.address-phone {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.5);
}

/* Форма адреса */
.address-form {
  margin-top: 0.5rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  margin-top: 1rem;
}

.btn-secondary {
  padding: 0.5rem 1rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  cursor: pointer;
}

.btn-primary {
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 30px;
  color: white;
  cursor: pointer;
}

/* Выбранный адрес */
.selected-address {
  padding: 0.8rem;
  background: rgba(139, 92, 246, 0.1);
  border: 1px solid rgba(139, 92, 246, 0.3);
  border-radius: 12px;
}

.selected-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.selected-details {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.8);
}

/* Самовывоз */
.pickup-info {
  text-align: center;
}

.pickup-icon {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
}

.pickup-info h3 {
  margin-bottom: 0.5rem;
}

.pickup-info p {
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 0.2rem;
}

/* Промокод */
.promo-code {
  display: flex;
  gap: 0.5rem;
}

.promo-code .glass-input {
  flex: 1;
}

.apply-btn {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 12px;
  color: white;
  cursor: pointer;
}

.discount-info {
  margin-top: 0.5rem;
  font-size: 0.8rem;
  color: #4ade80;
}

/* Правая колонка - итоги */
.checkout-summary {
  position: sticky;
  top: 100px;
  align-self: start;
}

.summary-card {
  padding: 1.5rem;
  border-radius: 24px;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.summary-card h2 {
  font-size: 1.2rem;
  margin-bottom: 1rem;
}

.cart-items {
  max-height: 300px;
  overflow-y: auto;
  margin-bottom: 1rem;
}

.cart-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
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

.summary-divider {
  height: 1px;
  background: rgba(255, 255, 255, 0.1);
  margin: 0.8rem 0;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.6rem;
  color: rgba(255, 255, 255, 0.7);
  font-size: 0.9rem;
}

.summary-row.total {
  font-size: 1.1rem;
  font-weight: 700;
  color: white;
  margin-top: 0.5rem;
}

.summary-row.total span:last-child {
  color: #c084fc;
}

.checkout-btn {
  width: 100%;
  padding: 0.8rem;
  background: linear-gradient(135deg, #22c55e, #16a34a);
  border: none;
  border-radius: 30px;
  color: white;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  margin-top: 1rem;
}

.checkout-btn:hover {
  opacity: 0.9;
}

.secure-info {
  text-align: center;
  margin-top: 0.8rem;
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.4);
}

/* Адаптивность */
@media (max-width: 1024px) {
  .checkout-layout {
    grid-template-columns: 1fr;
  }
  
  .checkout-summary {
    position: static;
  }
}

@media (max-width: 768px) {
  .checkout-page {
    padding: 1rem;
  }
  
  .checkout-title {
    font-size: 1.5rem;
    margin-bottom: 1rem;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .form-section {
    padding: 1rem;
  }
  
  .radio-content {
    flex-wrap: wrap;
  }
  
  .radio-price {
    margin-left: 0;
  }
  
  .section-header {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .address-actions {
    width: 100%;
    justify-content: space-between;
  }
  
  .promo-code {
    flex-direction: column;
  }
}
</style>