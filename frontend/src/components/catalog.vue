<!-- Catalog.vue - Страница каталога с поиском MeiliSearch -->
<template>
  <div class="catalog-page">
    <div class="catalog-container">
      <!-- Hero секция с поиском -->
      <section class="catalog-hero glass-panel">
        <h1>Каталог товаров</h1>
        <p>{{ totalProducts }} товаров в нашем ассортименте</p>
        
        <!-- Глобальный поиск -->
        <div class="global-search">
          <div class="search-wrapper">
            <input 
              type="text" 
              v-model="searchQuery" 
              @input="handleSearch"
              placeholder="Поиск товаров по названию, категории, бренду..."
              class="search-input glass-input"
              autocomplete="off"
            >
            <button class="search-btn" @click="handleSearch">
              🔍 Найти
            </button>
            <button v-if="searchQuery" class="clear-btn" @click="clearSearch">
              ✕
            </button>
          </div>
          
          <!-- Результаты поиска (подсказки) -->
          <div v-if="searchSuggestions.length && searchQuery" class="search-suggestions glass-panel">
            <div 
              v-for="suggestion in searchSuggestions" 
              :key="suggestion.id"
              class="suggestion-item"
              @click="selectSuggestion(suggestion)"
            >
              <img :src="suggestion.image" :alt="suggestion.name" class="suggestion-img">
              <div class="suggestion-info">
                <span class="suggestion-name">{{ highlightMatch(suggestion.name, searchQuery) }}</span>
                <span class="suggestion-price">{{ formatPrice(suggestion.price) }} ₽</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Фильтры и товары -->
      <div class="catalog-layout">
        <!-- Боковая панель фильтров -->
        <aside class="filters-sidebar glass-panel">
          <div class="filters-header">
            <h3>Фильтры</h3>
            <button class="reset-filters" @click="resetFilters">Сбросить все</button>
          </div>

          <!-- Поиск по категориям -->
          <div class="filter-group">
            <div class="filter-title" @click="toggleFilter('category')">
              <span>Категории</span>
              <span class="filter-arrow">{{ openFilters.category ? '▼' : '▶' }}</span>
            </div>
            <div v-show="openFilters.category" class="filter-options">
              <label v-for="cat in categories" :key="cat.name" class="filter-checkbox">
                <input type="checkbox" :value="cat.name" v-model="filters.categories">
                <span>{{ cat.name }} ({{ cat.count }})</span>
              </label>
            </div>
          </div>

          <!-- Цена -->
          <div class="filter-group">
            <div class="filter-title" @click="toggleFilter('price')">
              <span>Цена</span>
              <span class="filter-arrow">{{ openFilters.price ? '▼' : '▶' }}</span>
            </div>
            <div v-show="openFilters.price" class="filter-options price-range">
              <div class="price-inputs">
                <input type="number" v-model="filters.priceMin" placeholder="от" class="price-input glass-input">
                <span>-</span>
                <input type="number" v-model="filters.priceMax" placeholder="до" class="price-input glass-input">
              </div>
              <input 
                type="range" 
                v-model="filters.priceMin" 
                :min="minPrice" 
                :max="maxPrice" 
                class="price-slider"
              >
            </div>
          </div>

          <!-- Бренды -->
          <div class="filter-group">
            <div class="filter-title" @click="toggleFilter('brand')">
              <span>Бренды</span>
              <span class="filter-arrow">{{ openFilters.brand ? '▼' : '▶' }}</span>
            </div>
            <div v-show="openFilters.brand" class="filter-options">
              <label v-for="brand in brands" :key="brand.name" class="filter-checkbox">
                <input type="checkbox" :value="brand.name" v-model="filters.brands">
                <span>{{ brand.name }} ({{ brand.count }})</span>
              </label>
            </div>
          </div>

          <!-- Рейтинг -->
          <div class="filter-group">
            <div class="filter-title" @click="toggleFilter('rating')">
              <span>Рейтинг</span>
              <span class="filter-arrow">{{ openFilters.rating ? '▼' : '▶' }}</span>
            </div>
            <div v-show="openFilters.rating" class="filter-options rating-options">
              <label v-for="r in [5,4,3,2,1]" :key="r" class="filter-checkbox">
                <input type="radio" :value="r" v-model="filters.rating">
                <span>
                  <span v-for="i in r" class="star-small">★</span>
                  <span v-for="i in (5-r)" class="star-small empty">☆</span>
                  и выше
                </span>
              </label>
            </div>
          </div>

          <!-- Наличие -->
          <div class="filter-group">
            <div class="filter-title" @click="toggleFilter('stock')">
              <span>Наличие</span>
              <span class="filter-arrow">{{ openFilters.stock ? '▼' : '▶' }}</span>
            </div>
            <div v-show="openFilters.stock" class="filter-options">
              <label class="filter-checkbox">
                <input type="checkbox" v-model="filters.inStock">
                <span>Только в наличии</span>
              </label>
            </div>
          </div>
        </aside>

        <!-- Основной контент -->
        <main class="products-area">
          <!-- Сортировка -->
          <div class="sorting-bar glass-panel">
            <div class="sorting-left">
              <span>Найдено: {{ filteredProducts.length }} товаров</span>
              <span class="active-filters" v-if="activeFiltersCount">
                • {{ activeFiltersCount }} фильтра
              </span>
            </div>
            <div class="sorting-right">
              <label>Сортировать:</label>
              <select v-model="sortBy" class="sort-select glass-input">
                <option value="default">По умолчанию</option>
                <option value="price_asc">Цена: по возрастанию</option>
                <option value="price_desc">Цена: по убыванию</option>
                <option value="rating">По рейтингу</option>
                <option value="newest">Сначала новинки</option>
              </select>
              <div class="view-toggle">
                <button class="view-btn" :class="{ active: viewMode === 'grid' }" @click="viewMode = 'grid'">▦</button>
                <button class="view-btn" :class="{ active: viewMode === 'list' }" @click="viewMode = 'list'">☰</button>
              </div>
            </div>
          </div>

          <!-- Сетка товаров -->
          <div v-if="paginatedProducts.length" class="products-grid" :class="{ 'list-view': viewMode === 'list' }">
            <div 
              v-for="product in paginatedProducts" 
              :key="product.id" 
              class="product-card glass-panel"
              @click="goToProduct(product.id)"
            >
              <div class="product-image">
                <img :src="product.img" :alt="product.name">
                <div class="product-badges">
                  <span v-if="product.isNew" class="badge new">NEW</span>
                  <span v-if="product.discount" class="badge discount">-{{ product.discount }}%</span>
                </div>
                <button class="favorite-btn" @click.stop="toggleFavorite(product.id)">
                  {{ product.isFavorite ? '❤️' : '🤍' }}
                </button>
              </div>
              
              <div class="product-info">
                <div class="product-category">{{ product.category_name }}</div>
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
                
                <div class="product-actions">
                  <button class="cart-btn" @click.stop="addToCart(product)">
                    🛒 В корзину
                  </button>
                  <button class="quick-view-btn" @click.stop="">
                    👁️
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Пустое состояние -->
          <div v-else class="empty-state glass-panel">
            <span class="empty-icon">🔍</span>
            <h3>Товары не найдены</h3>
            <p>Попробуйте изменить параметры поиска или сбросить фильтры</p>
            <button class="reset-btn" @click="resetFilters">Сбросить фильтры</button>
          </div>

          <!-- Пагинация -->
          <div v-if="totalPages > 1" class="pagination">
            <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">←</button>
            <div class="page-numbers">
              <button 
                v-for="page in visiblePages" 
                :key="page"
                class="page-num"
                :class="{ active: currentPage === page }"
                @click="currentPage = page"
              >
                {{ page }}
              </button>
            </div>
            <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">→</button>
          </div>
        </main>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const authStore = useAuthStore();
const route = useRoute()
const router = useRouter()

const products = ref([]);

// Состояние
const searchQuery = ref('')
const searchSuggestions = ref([])
const viewMode = ref('grid')
const sortBy = ref('default')
const currentPage = ref(1)
const productsPerPage = 12

// Фильтры
const filters = ref({
  categories: [],
  brands: [],
  priceMin: 0,
  priceMax: 200000,
  rating: null,
  inStock: false
})

// Открытые группы фильтров
const openFilters = ref({
  category: true,
  price: true,
  brand: false,
  rating: false,
  stock: false
})

// Данные для фильтров
const categories = ref([
  { name: 'Смартфоны', count: 156 },
  { name: 'Ноутбуки', count: 89 },
  { name: 'Наушники', count: 123 },
  { name: 'Часы', count: 67 },
  { name: 'Камеры', count: 45 },
  { name: 'Аксессуары', count: 234 }
])

const brands = ref([
  { name: 'Apple', count: 89 },
  { name: 'Samsung', count: 67 },
  { name: 'Sony', count: 45 },
  { name: 'Xiaomi', count: 78 },
  { name: 'Google', count: 23 }
])

// Минимальная и максимальная цена
const minPrice = ref(0)
const maxPrice = ref(200000)

const fetchProduct = async () => {  
  try 
  {
    const result = await authStore.fetchProducts();

    // 1. Проверяем response.data
    if (result.success) 
    {
      products.value = result.data;
      console.log(products.value);
    } 
    else 
    {
      console.warn("error");
    }
  } 
  catch (error) 
  {
    console.error("Ошибка сети:", error);
  }
};

// Товары (моковые данные)
/*const products = ref([
  {
    id: 1,
    name: 'iPhone 15 Pro',
    category: 'Смартфоны',
    brand: 'Apple',
    price: 89990,
    oldprice: 119990,
    discount: 25,
    rating: 4.9,
    reviews: 1247,
    stock: 45,
    isNew: true,
    isFavorite: false,
    image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=300&h=300&fit=crop',
    description: 'Титан. Прочный и легкий. Новый корпус из титана авиационного класса.'
  },
  {
    id: 2,
    name: 'Samsung Galaxy S24',
    category: 'Смартфоны',
    brand: 'Samsung',
    price: 79990,
    oldprice: 109990,
    discount: 27,
    rating: 4.8,
    reviews: 892,
    stock: 32,
    isNew: true,
    isFavorite: false,
    image: 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=300&h=300&fit=crop',
    description: 'Искусственный интеллект нового поколения. Мощный процессор.'
  },
  {
    id: 3,
    name: 'MacBook Air M3',
    category: 'Ноутбуки',
    brand: 'Apple',
    price: 119990,
    oldprice: 159990,
    discount: 25,
    rating: 4.9,
    reviews: 634,
    stock: 23,
    isNew: true,
    isFavorite: false,
    image: 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=300&h=300&fit=crop',
    description: 'Сверхпортативный. Невероятно быстрый. Чип M3.'
  },
  {
    id: 4,
    name: 'Sony WH-1000XM5',
    category: 'Наушники',
    brand: 'Sony',
    price: 24990,
    oldprice: 34990,
    discount: 28,
    rating: 4.9,
    reviews: 1123,
    stock: 67,
    isNew: false,
    isFavorite: false,
    image: 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=300&h=300&fit=crop',
    description: 'Лучшее шумоподавление. Превосходное качество звука.'
  },
  {
    id: 5,
    name: 'Apple Watch Series 9',
    category: 'Часы',
    brand: 'Apple',
    price: 35990,
    oldprice: 45990,
    discount: 22,
    rating: 4.8,
    reviews: 856,
    stock: 89,
    isNew: true,
    isFavorite: false,
    image: 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=300&h=300&fit=crop',
    description: 'S9 SiP. Яркий дисплей. Новые жесты.'
  },
  {
    id: 6,
    name: 'DJI Mini 4 Pro',
    category: 'Камеры',
    brand: 'DJI',
    price: 69990,
    oldprice: 89990,
    discount: 22,
    rating: 4.9,
    reviews: 423,
    stock: 12,
    isNew: true,
    isFavorite: false,
    image: 'https://images.unsplash.com/photo-1506947411487-a56738267384?w=300&h=300&fit=crop',
    description: '4K HDR видео. Умные функции. Компактный размер.'
  },
  {
    id: 7,
    name: 'GoPro Hero 12',
    category: 'Камеры',
    brand: 'GoPro',
    price: 39990,
    oldprice: 49990,
    discount: 20,
    rating: 4.8,
    reviews: 356,
    stock: 0,
    isNew: true,
    isFavorite: false,
    image: 'https://images.unsplash.com/photo-1524143986875-3b098d78b363?w=300&h=300&fit=crop',
    description: 'Экшн-камера нового поколения. 5.3K видео.'
  },
  {
    id: 8,
    name: 'Xiaomi Pad 6',
    category: 'Аксессуары',
    brand: 'Xiaomi',
    price: 29990,
    oldprice: 39990,
    discount: 25,
    rating: 4.7,
    reviews: 234,
    stock: 56,
    isNew: false,
    isFavorite: false,
    image: 'https://images.unsplash.com/photo-1589739900243-4b52cd9dd104?w=300&h=300&fit=crop',
    description: '144Hz дисплей. Процессор Snapdragon 870.'
  }
])*/

// Количество активных фильтров
const activeFiltersCount = computed(() => {
  let count = 0
  if (filters.value.categories.length) count++
  if (filters.value.brands.length) count++
  if (filters.value.priceMin > minPrice.value || filters.value.priceMax < maxPrice.value) count++
  if (filters.value.rating) count++
  if (filters.value.inStock) count++
  if (searchQuery.value) count++
  return count
})

// Фильтрация товаров
const filteredProducts = computed(() => {
  let result = [...products.value]

  // Поиск
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(p => 
      p.name.toLowerCase().includes(query) ||
      p.category.toLowerCase().includes(query) ||
      p.brand.toLowerCase().includes(query)
    )
  }

  // Фильтр по категориям
  if (filters.value.categories.length) {
    result = result.filter(p => filters.value.categories.includes(p.category))
  }

  // Фильтр по брендам
  if (filters.value.brands.length) {
    result = result.filter(p => filters.value.brands.includes(p.brand))
  }

  // Фильтр по цене
  result = result.filter(p => 
    p.price >= (filters.value.priceMin || minPrice.value) && 
    p.price <= (filters.value.priceMax || maxPrice.value)
  )

  // Фильтр по рейтингу
  if (filters.value.rating) {
    result = result.filter(p => p.rating >= filters.value.rating)
  }

  // Фильтр по наличию
  if (filters.value.inStock) {
    result = result.filter(p => p.stock > 0)
  }

  // Сортировка
  switch (sortBy.value) {
    case 'price_asc':
      result.sort((a, b) => a.price - b.price)
      break
    case 'price_desc':
      result.sort((a, b) => b.price - a.price)
      break
    case 'rating':
      result.sort((a, b) => b.rating - a.rating)
      break
    case 'newest':
      result.sort((a, b) => (b.isNew ? 1 : 0) - (a.isNew ? 1 : 0))
      break
  }

  return result
})

// Общее количество товаров
const totalProducts = computed(() => filteredProducts.value.length)

// Пагинация
const totalPages = computed(() => Math.ceil(filteredProducts.value.length / productsPerPage))

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * productsPerPage
  const end = start + productsPerPage
  return filteredProducts.value.slice(start, end)
})

// Видимые страницы для пагинации
const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

// Поиск с подсказками
const handleSearch = async () => {
  currentPage.value = 1
  
  if (searchQuery.value.length > 1) {
    // Здесь будет запрос к MeiliSearch
    // Пока имитация
    searchSuggestions.value = product.value
      .filter(p => p.name.toLowerCase().includes(searchQuery.value.toLowerCase()))
      .slice(0, 5)
  } else {
    searchSuggestions.value = []
  }
}

const clearSearch = () => {
  searchQuery.value = ''
  searchSuggestions.value = []
}

const selectSuggestion = (product) => {
  goToProduct(product.id)
}

// Подсветка совпадений
const highlightMatch = (text, query) => {
  if (!query) return text
  const regex = new RegExp(`(${query})`, 'gi')
  return text.replace(regex, '<mark>$1</mark>')
}

// Фильтры
const toggleFilter = (filter) => {
  openFilters.value[filter] = !openFilters.value[filter]
}

const resetFilters = () => {
  filters.value = {
    categories: [],
    brands: [],
    priceMin: minPrice.value,
    priceMax: maxPrice.value,
    rating: null,
    inStock: false
  }
  searchQuery.value = ''
  sortBy.value = 'default'
  currentPage.value = 1
}

// Действия с товарами
const toggleFavorite = (id) => {
  const product = product.value.find(p => p.id === id)
  if (product) product.isFavorite = !product.isFavorite
}

const addToCart = (product) => {
  console.log('Добавлено в корзину:', product)
  alert(`${product.name} добавлен в корзину!`)
}

const buyNow = (product) => {
  console.log('Купить сейчас:', product)
  router.push('/checkout')
}

const goToProduct = (id) => {
  router.push(`/product/${id}`)
}

// Форматирование цены
const formatPrice = (price) => {
  return price?.toLocaleString('ru-RU') || 0
}

// Сброс страницы при изменении фильтров
watch([searchQuery, filters, sortBy], () => {
  currentPage.value = 1
})

// Инициализация из URL параметров
onMounted(() => {
  fetchProduct();
  if (route.query.search) 
  {
    searchQuery.value = route.query.search
    handleSearch()
  }
  if (route.query.category) 
  {
    filters.value.categories = [route.query.category]
  }
})
</script>

<style scoped>
.catalog-page {
  min-height: 100vh;
  padding: 2rem;
  background: linear-gradient(135deg, #0a0a0f, #0f0f1a);
}

.catalog-container {
  max-width: 1400px;
  margin: 0 auto;
}

/* Hero секция */
.catalog-hero {
  padding: 2rem;
  border-radius: 32px;
  margin-bottom: 2rem;
  text-align: center;
  background: linear-gradient(135deg, rgba(30, 30, 40, 0.8), rgba(20, 20, 30, 0.6));
}

.catalog-hero h1 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.catalog-hero p {
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 1.5rem;
}

/* Глобальный поиск */
.global-search {
  position: relative;
  max-width: 600px;
  margin: 0 auto;
}

.search-wrapper {
  display: flex;
  gap: 0.5rem;
  position: relative;
}

.search-input {
  flex: 1;
  padding: 0.75rem 2.5rem 0.75rem 1rem;
}

.clear-btn {
  position: absolute;
  right: 80px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.5);
  cursor: pointer;
  font-size: 1rem;
}

.search-btn {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 30px;
  color: white;
  cursor: pointer;
}

/* Подсказки поиска */
.search-suggestions {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  margin-top: 0.5rem;
  border-radius: 16px;
  overflow: hidden;
  z-index: 100;
}

.suggestion-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  cursor: pointer;
  transition: background 0.2s;
}

.suggestion-item:hover {
  background: rgba(255, 255, 255, 0.05);
}

.suggestion-img {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  object-fit: cover;
}

.suggestion-info {
  flex: 1;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.suggestion-name mark {
  background: rgba(139, 92, 246, 0.3);
  color: #c084fc;
}

.suggestion-price {
  color: #c084fc;
  font-weight: 600;
}

/* Layout каталога */
.catalog-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 1.5rem;
}

/* Фильтры */
.filters-sidebar {
  padding: 1.5rem;
  border-radius: 24px;
  height: fit-content;
  position: sticky;
  top: 100px;
}

.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.filters-header h3 {
  font-size: 1.2rem;
}

.reset-filters {
  background: none;
  border: none;
  color: #c084fc;
  cursor: pointer;
  font-size: 0.8rem;
}

.filter-group {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.filter-title {
  display: flex;
  justify-content: space-between;
  cursor: pointer;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.filter-arrow {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.5);
}

.filter-options {
  margin-top: 0.75rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-checkbox {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.7);
}

.filter-checkbox input {
  width: 16px;
  height: 16px;
  cursor: pointer;
  accent-color: #c084fc;
}

/* Цена */
.price-range {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.price-inputs {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.price-input {
  width: 100px;
  padding: 0.4rem 0.6rem;
  font-size: 0.85rem;
}

.price-slider {
  width: 100%;
  accent-color: #c084fc;
}

/* Звезды рейтинга в фильтрах */
.star-small {
  color: #fbbf24;
  font-size: 0.8rem;
}

.star-small.empty {
  color: rgba(255, 255, 255, 0.2);
}

/* Сортировка */
.sorting-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-radius: 24px;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.sorting-left {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.active-filters {
  color: #c084fc;
  font-size: 0.85rem;
}

.sorting-right {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.sort-select {
  padding: 0.4rem 1rem;
  font-size: 0.85rem;
  cursor: pointer;
}

.view-toggle {
  display: flex;
  gap: 0.25rem;
}

.view-btn {
  padding: 0.4rem 0.75rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  cursor: pointer;
  font-size: 1.2rem;
}

.view-btn.active {
  background: rgba(139, 92, 246, 0.3);
  border-color: #c084fc;
}

/* Сетка товаров */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.products-grid.list-view {
  display: flex;
  flex-direction: column;
}

.products-grid.list-view .product-card {
  display: flex;
  gap: 1.5rem;
}

.products-grid.list-view .product-image {
  width: 200px;
  flex-shrink: 0;
}

.product-card {
  border-radius: 24px;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.3s;
}

.product-card:hover {
  transform: translateY(-4px);
}

.product-image {
  position: relative;
  height: 220px;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.03);
}

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.3s;
}

.product-card:hover .product-image img {
  transform: scale(1.05);
}

.product-badges {
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

.favorite-btn {
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
}

.product-info {
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

.product-actions {
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

/* Пустое состояние */
.empty-state {
  text-align: center;
  padding: 4rem;
  border-radius: 32px;
}

.empty-icon {
  font-size: 4rem;
  display: block;
  margin-bottom: 1rem;
}

.empty-state h3 {
  margin-bottom: 0.5rem;
}

.empty-state p {
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 1.5rem;
}

.reset-btn {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 30px;
  color: white;
  cursor: pointer;
}

/* Пагинация */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 2rem;
}

.page-btn, .page-num {
  padding: 0.5rem 0.75rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  cursor: pointer;
  min-width: 36px;
}

.page-num.active {
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border-color: #c084fc;
}

.buy-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: white;
  cursor: pointer;
}

/* Адаптивность */
@media (max-width: 1024px) {
  .catalog-layout {
    grid-template-columns: 1fr;
  }
  
  .filters-sidebar {
    position: static;
  }
  
}

@media (max-width: 768px) {
  .catalog-page {
    padding: 1rem;
  }
  
  .sorting-bar {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .sorting-right {
    width: 100%;
    justify-content: space-between;
  }
  
  .products-grid {
    grid-template-columns: 1fr;
  }
  
  .products-grid.list-view .product-card {
    flex-direction: column;
  }
  
  .products-grid.list-view .product-image {
    width: 100%;
  }
}
</style>