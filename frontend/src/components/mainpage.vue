<template>
  <div class="shop-page">
    <!-- Hero секция с поиском и баннером -->
    <section class="shop-hero glass-panel">
      <div class="hero-content">
        <span class="hero-badge">🔥 Осенняя распродажа</span>
        <h1>Премиальная<br><span class="gradient-text">электроника</span></h1>
        <p>Скидки до 50% на топовые модели. Успей купить по выгодной цене!</p>
        
        <!-- Поиск по магазину -->
        <div class="search-wrapper">
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Поиск товаров..." 
            class="glass-input search-input"
          >
          <span class="search-icon">🔍</span>
        </div>

        <!-- Категории -->
        <div class="categories">
          <button 
            v-for="category in categories" 
            :key="category.name"
            class="category-chip"
            :class="{ active: selectedCategory === category.name }"
            @click="selectedCategory = category.name"
          >
            {{ category.name }}
          </button>
          <button 
            v-if="selectedCategory"
            class="category-chip clear-chip"
            @click="selectedCategory = ''"
          >
            ✕ Сбросить
          </button>
        </div>
      </div>

      <!-- Hero карточка товара -->
      <div class="hero-card">
        <div class="hero-product">
          <img src="https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=400&h=400&fit=crop" alt="Featured product">
          <div class="hero-product-info">
            <h3>Sony WH-1000XM5</h3>
            <div class="price">
              <span class="current">24 990 ₽</span>
              <span class="old">34 990 ₽</span>
            </div>
            <button class="glass-button">Купить сейчас</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Статистика -->
    <div class="stats-grid">
      <div class="stat-card glass-panel">
        <span class="stat-value">500+</span>
        <span class="stat-label">Товаров</span>
      </div>
      <div class="stat-card glass-panel">
        <span class="stat-value">50k+</span>
        <span class="stat-label">Клиентов</span>
      </div>
      <div class="stat-card glass-panel">
        <span class="stat-value">24/7</span>
        <span class="stat-label">Поддержка</span>
      </div>
      <div class="stat-card glass-panel">
        <span class="stat-value">⭐ 4.9</span>
        <span class="stat-label">Рейтинг</span>
      </div>
    </div>

    <!-- Сетка товаров -->
    <div class="products-grid">
      <div v-for="product in filteredProducts" :key="product.id" class="product-card glass-panel">
        <!-- Изображение товара -->
        <div class="product-image">
          <img :src="product.image" :alt="product.name">
          <span class="product-discount" v-if="product.discount">-{{ product.discount }}%</span>
          <button class="favorite-btn" @click="toggleFavorite(product.id)">
            <span>{{ product.isFavorite ? '❤️' : '🤍' }}</span>
          </button>
        </div>

        <!-- Информация о товаре -->
        <div class="product-info">
          <div class="product-category">{{ product.category }}</div>
          <h3 class="product-title">{{ product.name }}</h3>
          
          <div class="product-rating">
            <span class="stars">⭐ {{ product.rating }}</span>
            <span class="reviews">({{ product.reviews }} отзывов)</span>
          </div>

          <div class="product-price">
            <span class="current-price">{{ formatPrice(product.price) }} ₽</span>
            <span class="old-price" v-if="product.oldPrice">{{ formatPrice(product.oldPrice) }} ₽</span>
          </div>

          <!-- Характеристики -->
          <div class="product-specs">
            <span v-for="spec in product.specs.slice(0, 2)" :key="spec" class="spec-tag">
              {{ spec }}
            </span>
          </div>

          <!-- Кнопки действий -->
          <div class="product-actions">
            <button class="cart-btn" @click="addToCart(product)">
              🛒 В корзину
            </button>
            <button class="quick-view-btn" @click="quickView(product)">
              👁️ Быстрый просмотр
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Пустое состояние -->
    <div v-if="filteredProducts.length === 0" class="empty-state glass-panel">
      <span class="empty-icon">🔍</span>
      <h3>Товары не найдены</h3>
      <p>Попробуйте изменить параметры поиска или выберите другую категорию</p>
      <button class="glass-button" @click="resetFilters">Сбросить фильтры</button>
    </div>

    <!-- Пагинация -->
    <div class="pagination" v-if="totalPages > 1">
      <button 
        class="pagination-btn" 
        :disabled="currentPage === 1"
        @click="currentPage--"
      >
        ← Назад
      </button>
      <span class="page-info">Страница {{ currentPage }} из {{ totalPages }}</span>
      <button 
        class="pagination-btn" 
        :disabled="currentPage === totalPages"
        @click="currentPage++"
      >
        Вперед →
      </button>
    </div>

    <!-- Баннер подписки -->
    <div class="newsletter-banner glass-panel">
      <div class="banner-content">
        <span class="banner-badge">🎁 Спецпредложение</span>
        <h3>Скидка 30% на первый заказ</h3>
        <p>Подпишитесь на рассылку и получите персональный промокод</p>
        <div class="newsletter-form">
          <input type="email" placeholder="Ваш email" class="glass-input">
          <button class="glass-button">Подписаться</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

// Поиск и фильтры
const searchQuery = ref('')
const selectedCategory = ref('')
const currentPage = ref(1)
const productsPerPage = 8

// Категории
const categories = ref([
  { name: 'Все товары', icon: '📱' },
  { name: 'Смартфоны', icon: '📱' },
  { name: 'Ноутбуки', icon: '💻' },
  { name: 'Наушники', icon: '🎧' },
  { name: 'Часы', icon: '⌚' },
  { name: 'Камеры', icon: '📷' },
  { name: 'Аксессуары', icon: '🎒' }
])

// Товары
const products = ref([
  {
    id: 1,
    name: 'iPhone 15 Pro',
    category: 'Смартфоны',
    price: 89990,
    oldPrice: 119990,
    discount: 25,
    rating: 4.9,
    reviews: 1247,
    isFavorite: false,
    specs: ['128GB', 'A17 Pro'],
    image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=300&h=300&fit=crop'
  },
  {
    id: 2,
    name: 'Samsung Galaxy S24',
    category: 'Смартфоны',
    price: 79990,
    oldPrice: 109990,
    discount: 27,
    rating: 4.8,
    reviews: 892,
    isFavorite: false,
    specs: ['256GB', 'AI Phone'],
    image: 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=300&h=300&fit=crop'
  },
  {
    id: 3,
    name: 'MacBook Air M3',
    category: 'Ноутбуки',
    price: 119990,
    oldPrice: 159990,
    discount: 25,
    rating: 4.9,
    reviews: 634,
    isFavorite: false,
    specs: ['8GB RAM', '256GB SSD'],
    image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=300&h=300&fit=crop'
  },
  {
    id: 4,
    name: 'AirPods Max',
    category: 'Наушники',
    price: 49990,
    oldPrice: 69990,
    discount: 28,
    rating: 4.7,
    reviews: 423,
    isFavorite: false,
    specs: ['White', 'Noise Cancelling'],
    image: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=300&h=300&fit=crop'
  },
  {
    id: 5,
    name: 'Apple Watch Series 9',
    category: 'Часы',
    price: 35990,
    oldPrice: 45990,
    discount: 22,
    rating: 4.8,
    reviews: 856,
    isFavorite: false,
    specs: ['41mm', 'GPS'],
    image: 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=300&h=300&fit=crop'
  },
  {
    id: 6,
    name: 'Sony WH-1000XM5',
    category: 'Наушники',
    price: 24990,
    oldPrice: 34990,
    discount: 28,
    rating: 4.9,
    reviews: 1123,
    isFavorite: false,
    specs: ['Black', 'Noise Cancelling'],
    image: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=300&h=300&fit=crop'
  },
  {
    id: 7,
    name: 'DJI Mini 4 Pro',
    category: 'Камеры',
    price: 69990,
    oldPrice: 89990,
    discount: 22,
    rating: 4.9,
    reviews: 423,
    isFavorite: false,
    specs: ['4K', 'Mini'],
    image: 'https://images.unsplash.com/photo-1506947411487-a56738267384?w=300&h=300&fit=crop'
  },
  {
    id: 8,
    name: 'GoPro Hero 12',
    category: 'Камеры',
    price: 39990,
    oldPrice: 49990,
    discount: 20,
    rating: 4.8,
    reviews: 356,
    isFavorite: false,
    specs: ['5K', 'Waterproof'],
    image: 'https://images.unsplash.com/photo-1524143986875-3b098d78b363?w=300&h=300&fit=crop'
  }
])

// Фильтрация товаров
const filteredProducts = computed(() => {
  let filtered = products.value

  // Поиск
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(product => 
      product.name.toLowerCase().includes(query) ||
      product.category.toLowerCase().includes(query)
    )
  }

  // Категория
  if (selectedCategory.value && selectedCategory.value !== 'Все товары') {
    filtered = filtered.filter(product => product.category === selectedCategory.value)
  }

  // Пагинация
  const start = (currentPage.value - 1) * productsPerPage
  const end = start + productsPerPage
  return filtered.slice(start, end)
})

// Общее количество страниц
const totalPages = computed(() => {
  let total = products.value
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    total = total.filter(product => 
      product.name.toLowerCase().includes(query) ||
      product.category.toLowerCase().includes(query)
    )
  }
  if (selectedCategory.value && selectedCategory.value !== 'Все товары') {
    total = total.filter(product => product.category === selectedCategory.value)
  }
  return Math.ceil(total.length / productsPerPage)
})

// Форматирование цены
const formatPrice = (price) => {
  return price.toLocaleString('ru-RU')
}

// Добавить в корзину
const addToCart = (product) => {
  console.log('Добавлено в корзину:', product)
  // Здесь будет логика добавления в корзину
}

// Быстрый просмотр
const quickView = (product) => {
  console.log('Быстрый просмотр:', product)
  // Здесь будет модальное окно с быстрым просмотром
}

// Избранное
const toggleFavorite = (productId) => {
  const product = products.value.find(p => p.id === productId)
  if (product) {
    product.isFavorite = !product.isFavorite
  }
}

// Сброс фильтров
const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  currentPage.value = 1
}
</script>

<style scoped>
.shop-page {
  display: flex;
  flex-direction: column;
  gap: 3rem;
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

/* Glass панели */
.glass-panel {
  background: rgba(20, 20, 30, 0.5);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 32px;
  transition: all 0.3s ease;
}

/* Hero секция */
.shop-hero {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  padding: 3rem;
  background: linear-gradient(135deg, rgba(30, 30, 40, 0.6), rgba(20, 20, 30, 0.4));
}

.hero-content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.hero-badge {
  display: inline-block;
  background: rgba(139, 92, 246, 0.2);
  border: 1px solid rgba(139, 92, 246, 0.3);
  border-radius: 30px;
  padding: 0.4rem 1rem;
  font-size: 0.85rem;
  color: #c084fc;
  width: fit-content;
}

.hero-content h1 {
  font-size: 3rem;
  font-weight: 700;
  line-height: 1.2;
}

.gradient-text {
  background: linear-gradient(135deg, #c084fc, #60a5fa);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.hero-content p {
  color: rgba(255, 255, 255, 0.7);
  font-size: 1.1rem;
  max-width: 400px;
}

.hero-card {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 24px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-product {
  text-align: center;
}

.hero-product img {
  width: 200px;
  height: 200px;
  object-fit: contain;
  margin-bottom: 1rem;
}

.hero-product h3 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
}

.hero-product .price {
  display: flex;
  gap: 0.8rem;
  justify-content: center;
  margin-bottom: 1rem;
}

.current {
  font-size: 1.3rem;
  font-weight: bold;
  color: #c084fc;
}

.old {
  text-decoration: line-through;
  color: rgba(255, 255, 255, 0.4);
  font-size: 0.9rem;
}

/* Статистика */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}

.stat-card {
  padding: 1.5rem;
  text-align: center;
}

.stat-value {
  display: block;
  font-size: 1.8rem;
  font-weight: bold;
  color: #c084fc;
  margin-bottom: 0.5rem;
}

.stat-label {
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.9rem;
}

/* Поиск */
.search-wrapper {
  position: relative;
  max-width: 400px;
}

.search-input {
  padding-right: 3rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  padding: 0.8rem 1rem;
  width: 100%;
  color: white;
}

.search-input:focus {
  outline: none;
  border-color: rgba(139, 92, 246, 0.5);
}

.search-icon {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: rgba(255, 255, 255, 0.4);
}

/* Категории */
.categories {
  display: flex;
  flex-wrap: wrap;
  gap: 0.8rem;
}

.category-chip {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  padding: 0.5rem 1.2rem;
  color: rgba(255, 255, 255, 0.7);
  cursor: pointer;
  transition: all 0.2s;
}

.category-chip:hover {
  background: rgba(255, 255, 255, 0.1);
}

.category-chip.active {
  background: rgba(139, 92, 246, 0.2);
  border-color: rgba(139, 92, 246, 0.4);
  color: white;
}

.clear-chip {
  background: rgba(239, 68, 68, 0.1);
  border-color: rgba(239, 68, 68, 0.3);
}

.clear-chip:hover {
  background: rgba(239, 68, 68, 0.2);
}

/* Сетка товаров */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 2rem;
}

.product-card {
  overflow: hidden;
  transition: all 0.3s;
}

.product-card:hover {
  transform: translateY(-4px);
  background: rgba(35, 35, 45, 0.7);
}

.product-image {
  position: relative;
  height: 250px;
  overflow: hidden;
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s;
}

.product-card:hover .product-image img {
  transform: scale(1.05);
}

.product-discount {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: #ef4444;
  border-radius: 30px;
  padding: 0.2rem 0.8rem;
  font-size: 0.8rem;
  font-weight: bold;
}

.favorite-btn {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: rgba(0, 0, 0, 0.5);
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  cursor: pointer;
  font-size: 1.2rem;
  backdrop-filter: blur(4px);
}

.product-info {
  padding: 1.5rem;
}

.product-category {
  color: rgba(139, 92, 246, 0.8);
  font-size: 0.85rem;
  margin-bottom: 0.5rem;
}

.product-title {
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.stars {
  color: #fbbf24;
  font-size: 0.9rem;
}

.reviews {
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.8rem;
}

.product-price {
  margin-bottom: 1rem;
}

.current-price {
  font-size: 1.4rem;
  font-weight: bold;
  color: #c084fc;
}

.old-price {
  text-decoration: line-through;
  color: rgba(255, 255, 255, 0.4);
  font-size: 0.9rem;
  margin-left: 0.5rem;
}

.product-specs {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-bottom: 1rem;
}

.spec-tag {
  background: rgba(255, 255, 255, 0.05);
  border-radius: 30px;
  padding: 0.2rem 0.6rem;
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.6);
}

.product-actions {
  display: flex;
  gap: 0.8rem;
}

.cart-btn, .quick-view-btn {
  flex: 1;
  padding: 0.6rem;
  border-radius: 30px;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.9rem;
}

.cart-btn {
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  color: white;
}

.cart-btn:hover {
  transform: scale(1.02);
}

.quick-view-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: white;
}

.quick-view-btn:hover {
  background: rgba(255, 255, 255, 0.1);
}

/* Пустое состояние */
.empty-state {
  text-align: center;
  padding: 4rem;
}

.empty-icon {
  font-size: 4rem;
  display: block;
  margin-bottom: 1rem;
}

.empty-state h3 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 1.5rem;
}

/* Пагинация */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 2rem;
}

.pagination-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  padding: 0.5rem 1.5rem;
  color: white;
  cursor: pointer;
  transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
  background: rgba(255, 255, 255, 0.1);
}

.pagination-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.page-info {
  color: rgba(255, 255, 255, 0.7);
}

/* Баннер подписки */
.newsletter-banner {
  padding: 3rem;
  text-align: center;
  background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(59, 130, 246, 0.1));
}

.banner-badge {
  display: inline-block;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  padding: 0.3rem 1rem;
  font-size: 0.85rem;
  margin-bottom: 1rem;
}

.newsletter-banner h3 {
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
}

.newsletter-banner p {
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 1.5rem;
}

.newsletter-form {
  display: flex;
  gap: 1rem;
  max-width: 500px;
  margin: 0 auto;
}

.newsletter-form input {
  flex: 1;
}

/* Адаптивность */
@media (max-width: 1024px) {
  .shop-hero {
    grid-template-columns: 1fr;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .products-grid {
    grid-template-columns: 1fr;
  }
  
  .newsletter-form {
    flex-direction: column;
  }
  
  .hero-content h1 {
    font-size: 2rem;
  }
  
  .shop-hero {
    padding: 1.5rem;
  }
}
</style>
