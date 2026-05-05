<!-- Profile.vue -->
<template>
  <div class="profile-page">
    <div class="profile-container">
      <!-- Боковая навигация -->
      <aside class="profile-sidebar glass-panel">
        <div class="user-avatar-section">
          <div class="avatar-wrapper">
            <img :src="profileForm?.avatar" :alt="profileForm?.username" class="user-avatar">
            <label for="avatar-upload" class="avatar-edit-btn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                <circle cx="12" cy="13" r="4"/>
              </svg>
            </label>
            <input type="file" id="avatar-upload" accept="image/*" style="display: none" @change="uploadAvatar">
          </div>
          <h3 class="user-name">{{ profileForm.username }}</h3>
          <p class="user-email">{{ profileForm.email }}</p>
          <!--<div class="user-status" :class="user.isVerified ? 'verified' : 'unverified'">
            {{ user.isVerified ? '✓ Подтвержден' : '⚠ Не подтвержден' }}
          </div>-->
        </div>

        <nav class="profile-nav">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            class="nav-item"
            :class="{ active: activeTab === tab.id }"
            @click="activeTab = tab.id"
          >
            <span class="nav-icon">{{ tab.icon }}</span>
            <span class="nav-label">{{ tab.label }}</span>
          </button>
        </nav>

        <div class="profile-stats">
          <div class="stat-item">
            <span class="stat-value">{{ stats.orders }}</span>
            <span class="stat-label">Заказов</span>
          </div>
          <div class="stat-item">
            <span class="stat-value">{{ stats.favorites }}</span>
            <span class="stat-label">Избранное</span>
          </div>
          <div class="stat-item">
            <span class="stat-value">{{ stats.reviews }}</span>
            <span class="stat-label">Отзывов</span>
          </div>
        </div>
      </aside>

      <!-- Основной контент -->
      <main class="profile-content glass-panel">
        <!-- Личные данные -->
        <div v-if="activeTab === 'profile'" class="tab-content">
          <h2 class="content-title">Личные данные</h2>
          
          <form @submit.prevent="updateProfile" class="profile-form">
            <div class="form-row">
              <div class="form-group">
                <label>Имя</label>
                <input type="text" v-model="profileForm.name" class="glass-input">
              </div>
              <div class="form-group">
                <label>Фамилия</label>
                <input type="text" v-model="profileForm.lastname" class="glass-input">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Email</label>
                <input type="email" v-model="profileForm.email" class="glass-input" disabled>
              </div>
              <div class="form-group">
                <label>Телефон</label>
                <input type="tel" v-model="profileForm.phone" placeholder="+7 (999) 123-45-67" class="glass-input">
              </div>
            </div>

            <div class="form-group">
              <label>Дата рождения</label>
              <input type="date" v-model="profileForm.birthday" class="glass-input">
            </div>

            <div class="form-group">
              <label>О себе</label>
              <textarea v-model="profileForm.bio" rows="4" placeholder="Расскажите о себе..." class="glass-input"></textarea>
            </div>

            <button type="submit" class="save-btn" :disabled="saving">
              <span v-if="!saving">Сохранить изменения</span>
              <span v-else class="loader"></span>
            </button>
          </form>
        </div>

        <!-- Адреса доставки -->
        <div v-if="activeTab === 'addresses'" class="tab-content">
          <div class="content-header">
            <h2 class="content-title">Адреса доставки</h2>
            <button class="add-btn" @click="showAddressModal = true">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
              </svg>
              Добавить адрес
            </button>
          </div>

          <div v-if="addresses != null" class="addresses-list">
            <div v-for="address in addresses" :key="address.id" class="address-card">
              <div class="address-info">
                <div class="address-type">
                  <span class="type-badge">{{ address.type }}</span>
                  <span v-if="address.isDefault" class="default-badge">По умолчанию</span>
                </div>
                <p class="address-text">{{ address.street }}, {{ address.city }}, {{ address.postalCode }}</p>
                <p class="address-phone">{{ address.phone }}</p>
              </div>
              <div class="address-actions">
                <button class="icon-btn" @click="setDefaultAddress(address.id)" title="Сделать основным">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                  </svg>
                </button>
                <button class="icon-btn edit" @click="editAddress(address)">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M20 14.66V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h5.34"/>
                    <polygon points="18 2 22 6 12 16 8 16 8 12 18 2"/>
                  </svg>
                </button>
                <button class="icon-btn delete" @click="deleteAddress(address.id)">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Настройки -->
        <div v-if="activeTab === 'settings'" class="tab-content">
          <h2 class="content-title">Настройки аккаунта</h2>
          
          <div class="settings-section">
            <h3>Безопасность</h3>
            <div class="setting-item">
              <div class="setting-info">
                <span>Изменить пароль</span>
                <p class="setting-desc">Обновите пароль для защиты аккаунта</p>
              </div>
              <button class="setting-btn" @click="showPasswordModal = true">Изменить</button>
            </div>
          </div>

          <div class="settings-section">
            <h3>Уведомления</h3>
            <div class="setting-item">
              <div class="setting-info">
                <span>Email рассылка</span>
                <p class="setting-desc">Получать новости и специальные предложения</p>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" v-model="profileForm.visible" @input="updateProfile">
                <span class="toggle-slider"></span>
              </label>
            </div>
          
          </div>

          <div class="settings-section">
            <h3>Конфиденциальность</h3>
            <div class="setting-item">
              <div class="setting-info">
                <span>Показывать профиль другим пользователям</span>
                <p class="setting-desc">Ваш профиль будет виден в отзывах и комментариях</p>
              </div>
              <label class="toggle-switch">
                <input type="checkbox" v-model="profileForm.notifications" @input="updateProfile">
                <span class="toggle-slider"></span>
              </label>
            </div>
          </div>
        </div>

        <!-- История заказов -->
        <div v-if="activeTab === 'orders'" class="tab-content">
          <h2 class="content-title">История заказов</h2>
          
          <div v-if="orders.length === 0" class="empty-state">
            <span class="empty-icon">📦</span>
            <p>У вас пока нет заказов</p>
            <router-link to="/catalog" class="shop-now-btn">Начать покупки</router-link>
          </div>
          
          <div v-else class="orders-list">
            <div v-for="order in orders" :key="order.id" class="order-card">
              <div class="order-header">
                <div class="order-info">
                  <span class="order-id">Заказ #{{ order.id }}</span>
                  <span class="order-date">{{ order.date }}</span>
                </div>
                <div class="order-status" :class="order.statusClass">
                  {{ order.status }}
                </div>
              </div>
              
              <div class="order-items">
                <div v-for="item in order.items.slice(0, 2)" :key="item.id" class="order-item">
                  <img :src="item.image" :alt="item.name" class="item-image">
                  <div class="item-details">
                    <span class="item-name">{{ item.name }}</span>
                    <span class="item-quantity">x{{ item.quantity }}</span>
                  </div>
                  <span class="item-price">{{ formatPrice(item.price * item.quantity) }} ₽</span>
                </div>
                <div v-if="order.items.length > 2" class="more-items">
                  + еще {{ order.items.length - 2 }} товара
                </div>
              </div>
              
              <div class="order-footer">
                <span class="order-total">Итого: {{ formatPrice(order.total) }} ₽</span>
                <button class="repeat-order-btn" @click="repeatOrder(order)">Повторить заказ</button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- Модальное окно добавления адреса -->
    <transition name="modal">
      <div v-if="showAddressModal" class="modal-overlay" @click.self="showAddressModal = false">
        <div class="modal-content glass-panel">
          <h3>{{ editingAddress ? 'Редактировать адрес' : 'Новый адрес' }}</h3>
          <form @submit.prevent="saveAddress" class="address-form">
            <select v-model="addressForm.type" class="glass-input">
              <option value="">Тип</option>
              <option value="Работа">Работа</option>
              <option value="Дом">Дом</option>
              <option value="Другой">Другой</option>
            </select>
            <select v-model="addressForm.city" class="glass-input">
              <option v-for="city in cities" :value="city.id">{{ city.name }}</option>
            </select>
            <input type="text" v-model="addressForm.street" placeholder="Улица, дом" class="glass-input">
            <input type="text" v-model="addressForm.office" placeholder="Квартира, офис" class="glass-input">
            <input type="text" v-model="addressForm.postalCode" placeholder="Индекс" class="glass-input">
            <label class="checkbox-label">
              <input type="checkbox" v-model="addressForm.isDefault">
              <span>Сделать адресом по умолчанию</span>
            </label>
            <div class="modal-actions">
              <button type="button" class="cancel-btn" @click="showAddressModal = false">Отмена</button>
              <button type="submit" class="save-btn">Сохранить</button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- Модальное окно смены пароля -->
    <transition name="modal">
      <div v-if="showPasswordModal" class="modal-overlay" @click.self="showPasswordModal = false">
        <div class="modal-content glass-panel">
          <h3>Изменить пароль</h3>
          <form @submit.prevent="changePassword" class="password-form">
            <input type="password" v-model="passwordForm.current" placeholder="Текущий пароль" class="glass-input">
            <input type="password" v-model="passwordForm.new" placeholder="Новый пароль" class="glass-input">
            <input type="password" v-model="passwordForm.confirm" placeholder="Подтвердите пароль" class="glass-input">
            <div class="modal-actions">
              <button type="button" class="cancel-btn" @click="showPasswordModal = false">Отмена</button>
              <button type="submit" class="save-btn">Изменить</button>
            </div>
          </form>
        </div>
      </div>
    </transition>

    <!-- Модальное окно удаления аккаунта -->
    <transition name="modal">
      <div v-if="showDeleteModal" class="modal-overlay" @click.self="showDeleteModal = false">
        <div class="modal-content glass-panel danger-modal">
          <h3>Удаление аккаунта</h3>
          <p>Вы уверены, что хотите удалить свой аккаунт? Это действие необратимо.</p>
          <input type="text" v-model="deleteConfirm" placeholder="Введите DELETE для подтверждения" class="glass-input">
          <div class="modal-actions">
            <button type="button" class="cancel-btn" @click="showDeleteModal = false">Отмена</button>
            <button type="button" class="danger-btn" @click="deleteAccount" :disabled="deleteConfirm !== 'DELETE'">Удалить</button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore';
import { useAlertStore } from '../stores/alertStore'; 

const authStore = useAuthStore();
const alerts = useAlertStore();
const router = useRouter();

const cities = ref(null);
const regions = ref(null);

// Активная вкладка
const activeTab = ref('profile')

const fetchCityies = async () => {
  const result = await authStore.fetchCityies();

  if (result.success) 
  {
    cities.value = result.cities;
    regions.value = result.regions;
  } 
  else 
  {
    console.error('Ошибка загрузки:', result.error);
  }
};

const updateProfile = async () => {
  const result = await authStore.update_user_info(authStore.user.id, profileForm.name, profileForm.surname, profileForm.email, profileForm.phone, profileForm.birthday, profileForm.bio, profileForm.notifications, profileForm.visible);

  if(!result.success)
  {
    console.log(result.error);
  }
  else
  {
    alerts.show('Данные успешно изменены!', 'success');
  }
}

const saveAddress = async () => {
  const result = await authStore.saveAddress(authStore.user.id, addressForm.type, addressForm.street, addressForm.city, addressForm.postalCode, addressForm.office, addressForm.isDefault);

  if(result.success)
  {
    console.log('good');
  }
  else
  {
    console.log(result.error);
  }
}

const changePassword = async () => {
  const result = await authStore.editPassword(passwordForm.current, passwordForm.new, passwordForm.confirm);
  if (passwordForm.new !== passwordForm.confirm) 
  {
    alerts.show('Пароли не совпадают')
    return
  }
  if (passwordForm.new.length < 6)
  {
    alerts.show('Пароль должен содержать минимум 6 символов', 'bad');
    return
  }
  if(result.success)
  {
    showPasswordModal.value = false;
    alerts.show('Данные успешно изменены!', 'success');
  }
  else
  {
    console.log(result.error);
  }
}

// Табы навигации
const tabs = ref([
  { id: 'profile', icon: '👤', label: 'Личные данные' },
  { id: 'addresses', icon: '📍', label: 'Адреса доставки' },
  { id: 'orders', icon: '📦', label: 'История заказов' },
  { id: 'settings', icon: '⚙️', label: 'Настройки' }
])

// Форма пароля
const passwordForm = reactive({
  current: '',
  new: '',
  confirm: ''
})

// Статистика
const stats = ref({
  orders: 12,
  favorites: 8,
  reviews: 5
})

// Заказы
const orders = ref([
  {
    id: '12345',
    date: '15 марта 2024',
    status: 'Доставлен',
    statusClass: 'delivered',
    total: 89990,
    items: [
      { id: 1, name: 'iPhone 15 Pro', price: 89990, quantity: 1, image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=100&h=100&fit=crop' }
    ]
  },
  {
    id: '12344',
    date: '10 марта 2024',
    status: 'В пути',
    statusClass: 'shipping',
    total: 24990,
    items: [
      { id: 2, name: 'Sony WH-1000XM5', price: 24990, quantity: 1, image: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=100&h=100&fit=crop' }
    ]
  }
])

// Состояния модальных окон
const showAddressModal = ref(false)
const showPasswordModal = ref(false)
const showDeleteModal = ref(false)
const saving = ref(false)
const editingAddress = ref(null)

// Форма адреса
const addressForm = reactive({
  type: 'Дом',
  street: '',
  city: '',
  postalCode: '',
  isDefault: false
})

// Подтверждение удаления
const deleteConfirm = ref('')

// Форматирование цены
const formatPrice = (price) => {
  return price.toLocaleString('ru-RU')
}

// Форма профиля
const profileForm = reactive({
    username: authStore.user?.username,
    name: authStore.user?.name,
    lastname: authStore.user?.surname,
    email: authStore.user?.email,
    phone: authStore.user?.phone,
    birthday: authStore.user?.birthdays,
    bio: authStore.user?.bio,
    avatar: authStore.user?.avatar,
    visible: authStore.user?.visible,
    notifications: authStore.user?.visible
})

// Настройки
const settings = ref({
  emailNotifications: true,
  smsNotifications: true,
  publicProfile: true
})

// Загрузка аватара
const uploadAvatar = (event) => {
  const file = event.target.files[0]
  if (file) {
    const reader = new FileReader()
    reader.onload = (e) => {
      user.value.avatar = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

// Сохранение адреса
/*const saveAddress = () => {
  if (editingAddress.value) {
    // Редактирование
    const index = addresses.value.findIndex(a => a.id === editingAddress.value.id)
    if (index !== -1) {
      addresses.value[index] = { ...addressForm, id: editingAddress.value.id }
    }
  } else {
    // Новый адрес
    const newAddress = {
      ...addressForm,
      id: Date.now()
    }
    addresses.value.push(newAddress)
  }
  
  // Если адрес по умолчанию, снимаем с других
  if (addressForm.isDefault) {
    addresses.value.forEach(addr => {
      if (addr.id !== (editingAddress.value?.id || newAddress.id)) {
        addr.isDefault = false
      }
    })
  }
  
  showAddressModal.value = false
  resetAddressForm()
}

// Редактирование адреса
const editAddress = (address) => {
  editingAddress.value = address
  Object.assign(addressForm, address)
  showAddressModal.value = true
}

// Удаление адреса
const deleteAddress = (id) => {
  addresses.value = addresses.value.filter(a => a.id !== id)
}*/

// Установка адреса по умолчанию
const setDefaultAddress = (id) => {
  addresses.value.forEach(addr => {
    addr.isDefault = addr.id === id
  })
}

// Сброс формы адреса
const resetAddressForm = () => {
  addressForm.type = 'Дом'
  addressForm.street = ''
  addressForm.city = ''
  addressForm.postalCode = ''
  addressForm.phone = ''
  addressForm.office = ''
  addressForm.isDefault = false
  editingAddress.value = null
}

// Удаление аккаунта
const deleteAccount = () => {
  if (deleteConfirm.value === 'DELETE') {
    alert('Аккаунт удален')
    router.push('/')
  }
}

// Повторить заказ
const repeatOrder = (order) => {
  console.log('Повторить заказ:', order)
  router.push('/catalog')
}

onMounted(async () => 
{
    // Если данных в сторе еще нет, загружаем их один раз
    if (!authStore.user) 
    {
      await authStore.checkAuth();
    }

    // Если после проверки пользователя всё еще нет — на выход
    if (!authStore.user)
    {
      router.push("/login");
    }

    fetchCityies();
});
</script>

<style scoped>
.profile-page {
  min-height: 80vh;
  padding: 2rem;
  background: linear-gradient(135deg, rgba(0,0,0,0.9), rgba(10,10,20,0.95));
}

.profile-container {
  max-width: 1400px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 2rem;
}

/* Боковая панель */
.profile-sidebar {
  padding: 2rem;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border-radius: 32px;
  height: fit-content;
  position: sticky;
  top: 100px;
}

.user-avatar-section {
  text-align: center;
  margin-bottom: 2rem;
}

.avatar-wrapper {
  position: relative;
  display: inline-block;
}

.user-avatar {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid rgba(139, 92, 246, 0.5);
}

.avatar-edit-btn {
  position: absolute;
  bottom: 5px;
  right: 5px;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.2s;
}

.avatar-edit-btn:hover {
  transform: scale(1.1);
}

.user-name {
  font-size: 1.3rem;
  font-weight: 600;
  margin-top: 1rem;
  margin-bottom: 0.25rem;
}

.user-email {
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.85rem;
  margin-bottom: 0.5rem;
}

.user-status {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 30px;
  font-size: 0.75rem;
}

.user-status.verified {
  background: rgba(34, 197, 94, 0.2);
  color: #4ade80;
}

.user-status.unverified {
  background: rgba(245, 158, 11, 0.2);
  color: #fbbf24;
}

/* Навигация */
.profile-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 2rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem 1rem;
  background: transparent;
  border: none;
  border-radius: 16px;
  color: rgba(255, 255, 255, 0.7);
  cursor: pointer;
  transition: all 0.2s;
  text-align: left;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.05);
  color: white;
}

.nav-item.active {
  background: linear-gradient(135deg, rgba(147, 51, 234, 0.2), rgba(59, 130, 246, 0.2));
  color: #c084fc;
  border-left: 2px solid #c084fc;
}

.nav-icon {
  font-size: 1.2rem;
}

.nav-label {
  font-size: 0.95rem;
  font-weight: 500;
}

/* Статистика */
.profile-stats {
  display: flex;
  justify-content: space-around;
  padding-top: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.stat-item {
  text-align: center;
}

.stat-value {
  display: block;
  font-size: 1.5rem;
  font-weight: bold;
  color: #c084fc;
}

.stat-label {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.5);
}

/* Основной контент */
.profile-content {
  padding: 2rem;
  background: rgba(20, 20, 30, 0.5);
  backdrop-filter: blur(12px);
  border-radius: 32px;
}

.content-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
}

.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
}

/* Формы */
.profile-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
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

.glass-input {
  padding: 0.75rem 1rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  color: white;
  font-size: 0.9rem;
  outline: none;
  transition: all 0.2s;
}

.glass-input:focus {
  border-color: #c084fc;
}

textarea.glass-input {
  resize: vertical;
  font-family: inherit;
}

/* Кнопки */
.save-btn, .add-btn {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 30px;
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.save-btn:hover, .add-btn:hover {
  transform: scale(1.02);
  opacity: 0.9;
}

.add-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
}

/* Адреса */
.addresses-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.address-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.05);
}

.address-type {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.type-badge {
  background: rgba(139, 92, 246, 0.2);
  padding: 0.2rem 0.6rem;
  border-radius: 30px;
  font-size: 0.75rem;
  color: #c084fc;
}

.default-badge {
  background: rgba(34, 197, 94, 0.2);
  padding: 0.2rem 0.6rem;
  border-radius: 30px;
  font-size: 0.75rem;
  color: #4ade80;
}

.address-text {
  margin-bottom: 0.25rem;
  font-size: 0.9rem;
}

.address-phone {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.5);
}

.address-actions {
  display: flex;
  gap: 0.5rem;
}

.icon-btn {
  background: rgba(255, 255, 255, 0.05);
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.icon-btn:hover {
  background: rgba(255, 255, 255, 0.1);
}

.icon-btn.delete:hover {
  background: rgba(239, 68, 68, 0.2);
  color: #f87171;
}

/* Настройки */
.settings-section {
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.settings-section h3 {
  font-size: 1.1rem;
  margin-bottom: 1rem;
  color: rgba(255, 255, 255, 0.9);
}

.setting-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 0;
}

.setting-info span {
  font-weight: 500;
}

.setting-desc {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.5);
  margin-top: 0.25rem;
}

/* Toggle Switch */
.toggle-switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 26px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255, 255, 255, 0.2);
  border-radius: 34px;
  transition: 0.3s;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  border-radius: 50%;
  transition: 0.3s;
}

input:checked + .toggle-slider {
  background: linear-gradient(135deg, #9333ea, #3b82f6);
}

input:checked + .toggle-slider:before {
  transform: translateX(24px);
}

.danger-section {
  border-bottom: none;
}

.danger-btn {
  padding: 0.5rem 1rem;
  background: rgba(239, 68, 68, 0.2);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 30px;
  color: #f87171;
  cursor: pointer;
  transition: all 0.2s;
}

.danger-btn:hover {
  background: rgba(239, 68, 68, 0.3);
}

/* Заказы */
.orders-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.order-card {
  background: rgba(255, 255, 255, 0.03);
  border-radius: 20px;
  padding: 1rem;
  border: 1px solid rgba(255, 255, 255, 0.05);
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.order-id {
  font-weight: 600;
}

.order-date {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.5);
  margin-left: 1rem;
}

.order-status {
  padding: 0.25rem 0.75rem;
  border-radius: 30px;
  font-size: 0.75rem;
}

.order-status.delivered {
  background: rgba(34, 197, 94, 0.2);
  color: #4ade80;
}

.order-status.shipping {
  background: rgba(245, 158, 11, 0.2);
  color: #fbbf24;
}

.order-items {
  margin-bottom: 1rem;
}

.order-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.5rem 0;
}

.item-image {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  object-fit: cover;
}

.item-details {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.item-name {
  font-size: 0.9rem;
}

.item-quantity {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.5);
}

.item-price {
  font-weight: 600;
}

.more-items {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.5);
  padding-top: 0.5rem;
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 0.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.order-total {
  font-weight: 600;
  color: #c084fc;
}

.repeat-order-btn {
  padding: 0.5rem 1rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  cursor: pointer;
  transition: all 0.2s;
}

.repeat-order-btn:hover {
  background: rgba(255, 255, 255, 0.1);
}

/* Модальные окна */
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
  max-width: 500px;
  width: 90%;
  padding: 2rem;
  background: rgba(20, 20, 30, 0.95);
  border-radius: 32px;
}

.modal-content h3 {
  margin-bottom: 1.5rem;
  font-size: 1.3rem;
}

.address-form, .password-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1rem;
}

.cancel-btn {
  padding: 0.5rem 1rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  cursor: pointer;
}

.empty-state {
  text-align: center;
  padding: 3rem;
}

.empty-icon {
  font-size: 4rem;
  display: block;
  margin-bottom: 1rem;
}

.shop-now-btn {
  display: inline-block;
  margin-top: 1rem;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border-radius: 30px;
  color: white;
  text-decoration: none;
}

/* Анимации */
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

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

/* Адаптивность */
@media (max-width: 1024px) {
  .profile-container {
    grid-template-columns: 1fr;
  }
  
  .profile-sidebar {
    position: static;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .profile-page {
    padding: 1rem;
  }
  
  .address-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .address-actions {
    align-self: flex-end;
  }
  
  .order-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .order-footer {
    flex-direction: column;
    gap: 0.5rem;
    align-items: flex-start;
  }
}
</style>