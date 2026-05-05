<!-- Favorites.vue - Страница избранных товаров -->
<template>
  <div class="favorites-page">
    <div class="favorites-container">
      <div class="favorites-header">
        <h1 class="favorites-title">Избранное</h1>
        <p class="favorites-subtitle">Товары, которые вам понравились</p>
      </div>

      <!-- Пустое избранное -->
      <div v-if="favoritesItems.length === 0" class="empty-favorites glass-panel">
        <div class="empty-icon">❤️</div>
        <h2>В избранном пока пусто</h2>
        <p>Добавляйте товары в избранное, чтобы не потерять понравившиеся позиции</p>
        <router-link to="/catalog" class="continue-shopping-btn">
          🛍️ Перейти в каталог
        </router-link>
      </div>

      <!-- Сетка избранных товаров -->
      <div v-else class="favorites-content">
        <div class="favorites-grid">
          <div v-for="product in favoritesItems" :key="product.id" class="favorite-card glass-panel">
            <div class="card-image">
              <img :src="product.image" :alt="product.name">
              <div class="card-badges">
                <span v-if="product.isNew" class="badge new">NEW</span>
                <span v-if="product.discount" class="badge discount">-{{ product.discount }}%</span>
              </div>
              <button class="remove-favorite" @click="removeFromFavorites(product.id)" title="Удалить из избранного">
                ❤️
              </button>
            </div>
            
            <div class="card-info">
              <div class="product-category">{{ product.category }}</div>
              <h3 class="product-title">{{ product.name }}</h3>
              
              <div class="product-rating">
                <span class="stars">
                  <span v-for="i in 5" :key="i" class="star" :class="{ active: i <= product.rating }">★</span>
                </span>
                <span class="reviews">({{ product.reviews }})</span>
              </div>
              
              <div class="product-price">
                <span class="current-price">{{ formatPrice(product.price) }} ₽</span>
                <span v-if="product.oldprice" class="old-price">{{ formatPrice(product.oldprice) }} ₽</span>
              </div>
              
              <div class="product-stock" :class="{ low: product.stock < 10 }">
                {{ product.stock > 0 ? `✅ В наличии ${product.stock} шт.` : '❌ Нет в наличии' }}
              </div>
              
              <div class="card-actions">
                <button class="cart-btn" @click="addToCart(product)" :disabled="product.stock === 0">
                  🛒 В корзину
                </button>
                <button class="quick-view-btn" @click="quickView(product)">
                  👁️
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Кнопка очистить всё -->
        <div class="clear-all" v-if="favoritesItems.length > 0">
          <button class="clear-all-btn" @click="clearAllFavorites">
            🗑️ Очистить всё избранное
          </button>
        </div>
      </div>

      <!-- Рекомендации на основе избранного -->
      <div class="recommendations-section" v-if="recommendedProducts.length && favoritesItems.length > 0">
        <h2 class="recommendations-title">Вам может понравиться</h2>
        <div class="recommendations-grid">
          <div v-for="product in recommendedProducts.slice(0, 4)" :key="product.id" class="rec-card glass-panel" @click="addToFavorites(product)">
            <img :src="product.image" :alt="product.name">
            <div class="rec-info">
              <h4>{{ product.name }}</h4>
              <div class="price">{{ formatPrice(product.price) }} ₽</div>
            </div>
            <button class="add-favorites-btn">❤️</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно очистки -->
    <transition name="modal">
      <div v-if="showClearModal" class="modal-overlay" @click.self="showClearModal = false">
        <div class="modal-content glass-panel">
          <h3>Очистить избранное?</h3>
          <p>Вы уверены, что хотите удалить все товары из избранного? Это действие нельзя отменить.</p>
          <div class="modal-actions">
            <button class="cancel-btn" @click="showClearModal = false">Отмена</button>
            <button class="confirm-btn" @click="confirmClearAll">Удалить всё</button>
          </div>
        </div>
      </div>
    </transition>

    <!-- Модальное окно быстрого просмотра -->
    <transition name="modal">
      <div v-if="quickViewProduct" class="modal-overlay" @click.self="quickViewProduct = null">
        <div class="modal-content glass-panel modal-large">
          <button class="modal-close" @click="quickViewProduct = null">✕</button>
          <div class="quick-view">
            <img :src="quickViewProduct.image" :alt="quickViewProduct.name" class="quick-view-img">
            <div class="quick-view-info">
              <h2>{{ quickViewProduct.name }}</h2>
              <div class="product-category">{{ quickViewProduct.category }}</div>
              <div class="product-rating">
                <span class="stars">
                  <span v-for="i in 5" :key="i" class="star" :class="{ active: i <= quickViewProduct.rating }">★</span>
                </span>
                <span>({{ quickViewProduct.reviews }} отзывов)</span>
              </div>
              <div class="product-price">
                <span class="current-price">{{ formatPrice(quickViewProduct.price) }} ₽</span>
                <span v-if="quickViewProduct.oldprice" class="old-price">{{ formatPrice(quickViewProduct.oldprice) }} ₽</span>
              </div>
              <p class="quick-view-desc">{{ quickViewProduct.description }}</p>
              <div class="product-stock">{{ quickViewProduct.stock > 0 ? `✅ В наличии ${quickViewProduct.stock} шт.` : '❌ Нет в наличии' }}</div>
              <div class="quick-view-actions">
                <button class="cart-btn large" @click="addToCart(quickViewProduct)">🛒 Добавить в корзину</button>
                <button class="favorite-btn large" @click="toggleFavoriteInModal" :class="{ active: isFavoriteInModal }">
                  {{ isFavoriteInModal ? '❤️ В избранном' : '🤍 В избранное' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// Состояние
const favoritesItems = ref([])
const recommendedProducts = ref([])
const showClearModal = ref(false)
const quickViewProduct = ref(null)
const isFavoriteInModal = ref(false)

// Данные всех товаров (для рекомендаций)
const allProducts = ref([
  {
    id: 1,
    name: 'iPhone 15 Pro',
    category: 'Смартфоны',
    price: 89990,
    oldprice: 119990,
    discount: 25,
    rating: 4.9,
    reviews: 1247,
    stock: 45,
    isNew: true,
    image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=300&h=300&fit=crop',
    description: 'Титан. Прочный и легкий. Новый корпус из титана авиационного класса.'
  },
  {
    id: 2,
    name: 'Samsung Galaxy S24',
    category: 'Смартфоны',
    price: 79990,
    oldprice: 109990,
    discount: 27,
    rating: 4.8,
    reviews: 892,
    stock: 32,
    isNew: true,
    image: 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=300&h=300&fit=crop',
    description: 'Искусственный интеллект нового поколения. Мощный процессор.'
  },
  {
    id: 3,
    name: 'MacBook Air M3',
    category: 'Ноутбуки',
    price: 119990,
    oldprice: 159990,
    discount: 25,
    rating: 4.9,
    reviews: 634,
    stock: 23,
    isNew: true,
    image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=300&h=300&fit=crop',
    description: 'Сверхпортативный. Невероятно быстрый. Чип M3.'
  },
  {
    id: 4,
    name: 'Sony WH-1000XM5',
    category: 'Наушники',
    price: 24990,
    oldprice: 34990,
    discount: 28,
    rating: 4.9,
    reviews: 1123,
    stock: 67,
    isNew: false,
    image: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=300&h=300&fit=crop',
    description: 'Лучшее шумоподавление. Превосходное качество звука.'
  },
  {
    id: 5,
    name: 'Apple Watch Series 9',
    category: 'Часы',
    price: 35990,
    oldprice: 45990,
    discount: 22,
    rating: 4.8,
    reviews: 856,
    stock: 89,
    isNew: true,
    image: 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=300&h=300&fit=crop',
    description: 'S9 SiP. Яркий дисплей. Новые жесты.'
  },
  {
    id: 6,
    name: 'DJI Mini 4 Pro',
    category: 'Камеры',
    price: 69990,
    oldprice: 89990,
    discount: 22,
    rating: 4.9,
    reviews: 423,
    stock: 12,
    isNew: true,
    image: 'https://images.unsplash.com/photo-1506947411487-a56738267384?w=300&h=300&fit=crop',
    description: '4K HDR видео. Умные функции. Компактный размер.'
  }
])

// Форматирование цены
const formatPrice = (price) => {
  return price.toLocaleString('ru-RU')
}

// Добавление в корзину
const addToCart = (product) => {
  // Получаем существующую корзину
  const cart = JSON.parse(localStorage.getItem('cart') || '[]')
  const existingItem = cart.find(item => item.id === product.id)
  
  if (existingItem) {
    existingItem.quantity++
  } else {
    cart.push({ ...product, quantity: 1 })
  }
  
  localStorage.setItem('cart', JSON.stringify(cart))
  alert(`${product.name} добавлен в корзину!`)
}

// Удаление из избранного
const removeFromFavorites = (id) => {
  favoritesItems.value = favoritesItems.value.filter(item => item.id !== id)
  saveFavorites()
}

// Очистка всего избранного
const clearAllFavorites = () => {
  showClearModal.value = true
}

const confirmClearAll = () => {
  favoritesItems.value = []
  saveFavorites()
  showClearModal.value = false
}

// Добавление в избранное (из рекомендаций)
const addToFavorites = (product) => {
  const exists = favoritesItems.value.some(item => item.id === product.id)
  if (!exists) {
    favoritesItems.value.push(product)
    saveFavorites()
    alert(`${product.name} добавлен в избранное!`)
  }
}

// Быстрый просмотр
const quickView = (product) => {
  quickViewProduct.value = product
  isFavoriteInModal.value = favoritesItems.value.some(item => item.id === product.id)
}

const toggleFavoriteInModal = () => {
  if (isFavoriteInModal.value) {
    favoritesItems.value = favoritesItems.value.filter(item => item.id !== quickViewProduct.value.id)
    isFavoriteInModal.value = false
  } else {
    favoritesItems.value.push(quickViewProduct.value)
    isFavoriteInModal.value = true
  }
  saveFavorites()
}

// Сохранение избранного в localStorage
const saveFavorites = () => {
  localStorage.setItem('favorites', JSON.stringify(favoritesItems.value))
  updateRecommendations()
}

// Загрузка избранного из localStorage
const loadFavorites = () => {
  const savedFavorites = localStorage.getItem('favorites')
  if (savedFavorites) {
    favoritesItems.value = JSON.parse(savedFavorites)
  } else {
    // Моковые данные для примера
    favoritesItems.value = [
      {
        id: 1,
        name: 'iPhone 15 Pro',
        category: 'Смартфоны',
        price: 89990,
        oldprice: 119990,
        discount: 25,
        rating: 4.9,
        reviews: 1247,
        stock: 45,
        isNew: true,
        image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=300&h=300&fit=crop',
        description: 'Титан. Прочный и легкий.'
      },
      {
        id: 4,
        name: 'Sony WH-1000XM5',
        category: 'Наушники',
        price: 24990,
        oldprice: 34990,
        discount: 28,
        rating: 4.9,
        reviews: 1123,
        stock: 67,
        isNew: false,
        image: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=300&h=300&fit=crop',
        description: 'Лучшее шумоподавление.'
      }
    ]
  }
  updateRecommendations()
}

// Обновление рекомендаций (исключаем уже избранные)
const updateRecommendations = () => {
  const favoriteIds = favoritesItems.value.map(item => item.id)
  recommendedProducts.value = allProducts.value.filter(
    product => !favoriteIds.includes(product.id)
  )
}

// Синхронизация с другими вкладками
window.addEventListener('storage', (e) => {
  if (e.key === 'favorites') {
    loadFavorites()
  }
})

onMounted(() => {
  loadFavorites()
})
</script>

<style scoped>
.favorites-page {
  min-height: 80vh;
  padding: 2rem;
  background: linear-gradient(135deg, #0a0a0f, #0f0f1a);
}

.favorites-container {
  max-width: 1400px;
  margin: 0 auto;
}

.favorites-header {
  margin-bottom: 2rem;
}

.favorites-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.favorites-subtitle {
  color: rgba(255, 255, 255, 0.6);
}

/* Пустое избранное */
.empty-favorites {
  text-align: center;
  padding: 4rem;
  border-radius: 32px;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
}

.empty-icon {
  font-size: 5rem;
  margin-bottom: 1rem;
}

.empty-favorites h2 {
  margin-bottom: 0.5rem;
}

.empty-favorites p {
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

/* Сетка избранного */
.favorites-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.favorite-card {
  border-radius: 24px;
  overflow: hidden;
  transition: all 0.3s;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
}

.favorite-card:hover {
  transform: translateY(-4px);
}

.card-image {
  position: relative;
  height: 220px;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.03);
}

.card-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.3s;
}

.favorite-card:hover .card-image img {
  transform: scale(1.05);
}

.card-badges {
  position: absolute;
  top: 0.5rem;
  left: 0.5rem;
  display: flex;
  gap: 0.5rem;
}

.badge {
  padding: 0.2rem 0.6rem;
  border-radius: 30px;
  font-size: 0.7rem;
  font-weight: bold;
}

.badge.new {
  background: #22c55e;
  color: white;
}

.badge.discount {
  background: #ef4444;
  color: white;
}

.remove-favorite {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: rgba(0, 0, 0, 0.5);
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  cursor: pointer;
  font-size: 1.2rem;
  backdrop-filter: blur(4px);
  transition: all 0.2s;
}

.remove-favorite:hover {
  transform: scale(1.1);
}

.card-info {
  padding: 1rem;
}

.product-category {
  font-size: 0.7rem;
  color: #c084fc;
  margin-bottom: 0.25rem;
}

.product-title {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
}

.star {
  color: rgba(255, 255, 255, 0.2);
  font-size: 0.8rem;
}

.star.active {
  color: #fbbf24;
}

.reviews {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.5);
}

.product-price {
  margin-bottom: 0.5rem;
}

.current-price {
  font-size: 1.2rem;
  font-weight: 700;
  color: #c084fc;
}

.old-price {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.4);
  text-decoration: line-through;
  margin-left: 0.5rem;
}

.product-stock {
  font-size: 0.7rem;
  color: #22c55e;
  margin-bottom: 0.75rem;
}

.product-stock.low {
  color: #f59e0b;
}

.card-actions {
  display: flex;
  gap: 0.5rem;
}

.cart-btn {
  flex: 1;
  padding: 0.5rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 30px;
  color: white;
  font-size: 0.8rem;
  cursor: pointer;
}

.cart-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.quick-view-btn {
  padding: 0.5rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  cursor: pointer;
}

/* Кнопка очистить всё */
.clear-all {
  display: flex;
  justify-content: center;
  margin-bottom: 3rem;
}

.clear-all-btn {
  padding: 0.75rem 2rem;
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 30px;
  color: #f87171;
  cursor: pointer;
  transition: all 0.2s;
}

.clear-all-btn:hover {
  background: rgba(239, 68, 68, 0.3);
}

/* Рекомендации */
.recommendations-section {
  margin-top: 2rem;
}

.recommendations-title {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
}

.recommendations-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 1.5rem;
}

.rec-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 20px;
  cursor: pointer;
  transition: all 0.2s;
}

.rec-card:hover {
  transform: translateY(-4px);
}

.rec-card img {
  width: 70px;
  height: 70px;
  border-radius: 12px;
  object-fit: cover;
}

.rec-info {
  flex: 1;
}

.rec-info h4 {
  font-size: 0.9rem;
  margin-bottom: 0.25rem;
}

.rec-info .price {
  color: #c084fc;
  font-weight: 600;
  font-size: 0.85rem;
}

.add-favorites-btn {
  width: 32px;
  height: 32px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.2s;
}

.add-favorites-btn:hover {
  background: rgba(239, 68, 68, 0.2);
  transform: scale(1.1);
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
  max-width: 500px;
  width: 90%;
  padding: 2rem;
  background: rgba(20, 20, 30, 0.95);
  border-radius: 32px;
  text-align: center;
}

.modal-large {
  max-width: 800px;
  text-align: left;
}

.modal-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
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

/* Быстрый просмотр */
.quick-view {
  display: flex;
  gap: 2rem;
}

.quick-view-img {
  width: 250px;
  height: 250px;
  object-fit: contain;
  border-radius: 16px;
}

.quick-view-info {
  flex: 1;
}

.quick-view-info h2 {
  margin-bottom: 0.25rem;
}

.quick-view-info .product-category {
  margin-bottom: 0.5rem;
}

.quick-view-desc {
  color: rgba(255, 255, 255, 0.7);
  margin: 1rem 0;
  line-height: 1.5;
}

.quick-view-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.cart-btn.large, .favorite-btn.large {
  padding: 0.75rem 1.5rem;
  border-radius: 30px;
  font-size: 1rem;
}

.favorite-btn.large {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  cursor: pointer;
}

.favorite-btn.large.active {
  background: rgba(239, 68, 68, 0.2);
  border-color: #ef4444;
  color: #ef4444;
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
  .quick-view {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
}

@media (max-width: 768px) {
  .favorites-page {
    padding: 1rem;
  }
  
  .favorites-title {
    font-size: 1.5rem;
  }
  
  .favorites-grid {
    grid-template-columns: 1fr;
  }
  
  .recommendations-grid {
    grid-template-columns: 1fr;
  }
  
  .quick-view-img {
    width: 200px;
    height: 200px;
  }
  
  .quick-view-actions {
    flex-direction: column;
  }
}
</style>