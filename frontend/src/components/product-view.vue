<!-- ProductDetail.vue - Страница просмотра товара -->
<template>
  <div v-if="product" class="product-detail-page">
    <div class="product-container">
      <!-- Хлебные крошки -->
      <div class="breadcrumbs">
        <router-link to="/">Главная</router-link>
        <span>/</span>
        <router-link to="/catalog">Каталог</router-link>
        <span>/</span>
        <router-link :to="`/catalog/${product.category}`">{{ product.category_name }}</router-link>
        <span>/</span>
        <span class="current">{{ product.name }}</span>
      </div>

      <!-- Основная информация о товаре -->
      <div class="product-main glass-panel">
        <div class="product-gallery">
          <div class="main-image">
            <img :src="product.img" :alt="product.name">
            <div class="badge new" v-if="product.isNew">Новинка</div>
          </div>
          <div class="thumbnail-list">
            <div 
              v-for="(img, index) in product.images" 
              :key="index"
              class="thumbnail"
              :class="{ active: currentImage === img }"
              @click="currentImage = img"
            >
              <img :src="img" :alt="product.name">
            </div>
          </div>
        </div>

        <div class="product-info">
          <h1 class="product-title">{{ product.name }}</h1>
          
          <div class="product-rating">
            <div class="stars" v-if="comments != null">
              <span v-for="i in 5" :key="i" class="star" :class="{ active: i <= averageRating  }">★</span>
            </div>
            <div v-if="count != null" class="flex gap-2">
              <span class="reviews-count">{{ count }} отзывов</span>
            </div>
          </div>

            <div class="product-price">
                <span class="current-price">{{ formatPrice(product.price) }} ₽</span>
                <span class="old-price" v-if="product.oldprice">{{ formatPrice(product.oldprice) }} ₽</span>      
                <span class="discount" v-if="product.oldprice && product.oldprice > product.price">💰 Экономия {{ formatPrice(product.oldprice - product.price) }} ₽</span>       
                <span class="damage" v-else-if="product.oldprice && product.oldprice < product.price">📈 Подорожание на {{ formatPrice(product.price - product.oldprice) }} ₽</span>
                <span class="no-change" v-else-if="product.oldprice && product.oldprice === product.price">⚖️ Цена не изменилась</span>
            </div>

          <div class="product-options">
            <div class="option-group" v-if="specifications.colors">
              <label>Цвет:</label>
              <div class="color-options">
                <button 
                  v-for="color in specifications.colors" 
                  :key="color.name"
                  class="color-btn"
                  :style="{ background: color.code }"
                  :class="{ active: selectedColor === color.name }"
                  @click="selectedColor = color.name"
                >
                  <span v-if="color.code === '#fff' || color.code === '#ffffff'" style="color: #000;"></span>
                </button>
              </div>
            </div>

            <div class="option-group" v-if="product.specifications">
              <label>Характеристики:</label>
              <div class="spec-list">
                <span v-for="spec in specifications.specifications" :key="spec" class="spec-tag">{{ spec }}</span>
              </div>
            </div>
          </div>

          <div class="product-actions">
            <div class="quantity-selector">
              <button @click="decrementQuantity" :disabled="quantity <= 1">-</button>
              <span>{{ quantity }}</span>
              <button @click="incrementQuantity" :disabled="quantity >= product.stock">+</button>
            </div>
            <button class="add-to-cart-btn" @click="addToCart">
              🛒 Добавить в корзину
            </button>
            <button class="favorite-btn" @click="toggleFavorite" :class="{ active: isFavorite }">
              {{ isFavorite ? '❤️' : '🤍' }}
            </button>
          </div>

          <div class="stock-info" :class="{ low: product.stock < 10 }">
            <span>✅ В наличии: {{ product.stock }} шт.</span>
            <span v-if="product.stock < 10" class="low-stock">Осталось мало!</span>
          </div>

          <div class="delivery-info">
            <div class="delivery-item">
              <span>🚚</span>
              <span>Бесплатная доставка от 5000 ₽</span>
            </div>
            <div class="delivery-item">
              <span>🔄</span>
              <span>Возврат в течение 14 дней</span>
            </div>
            <div class="delivery-item">
              <span>🔒</span>
              <span>Гарантия 12 месяцев</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Табы с описанием и характеристиками -->
      <div class="product-tabs glass-panel">
        <div class="tabs-header">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            class="tab-btn"
            :class="{ active: activeTab === tab.id }"
            @click="activeTab = tab.id"
          >
            {{ tab.label }}
          </button>
        </div>
        <div class="tab-content">
          <div v-if="activeTab === 'description'" class="description-content">
            <p>{{ product.subtitle }}</p>
          </div>
          <div v-if="activeTab === 'specs'" class="specs-content">
            <table class="specs-table">
              <tr v-for="(value, key) in specifications.fullSpecs" :key="key">
                <td class="spec-name">{{ key }}</td>
                <td class="spec-value">{{ value }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <!-- Блок с отзывами -->
      <div class="reviews-section glass-panel">
        <div class="reviews-header">
          <h2>Отзывы покупателей</h2>
          <button class="write-review-btn" @click="showReviewModal = true">
            ✍️ Написать отзыв
          </button>
        </div>

        <!-- Статистика отзывов -->
        <div v-if="comments != null && comments.length">
          <div class="reviews-stats">
            <div class="rating-summary">
              <div class="average-rating"> {{ averageRating }} </div>
              <div class="stars-big">
                <span v-for="i in 5" :key="i" class="star" :class="{ active: i <= Math.floor(averageRating) }">★</span>
              </div>
              <div class="total-reviews">{{ count }} отзывов</div>
            </div>
            <div class="rating-bars">
              <div v-for="star in [5,4,3,2,1]" :key="star" class="rating-bar-item">
                <span class="star-label">{{ star }} ★</span>
                <div class="bar-bg">
                  <div class="bar-fill" :style="{ width: getRatingPercent(star) + '%' }"></div>
                </div>
                <span class="bar-count">{{ getRatingCount(star) }}</span>
              </div>
            </div>
          </div>

          <!-- Список отзывов -->
          <div class="reviews-list">
            <div v-for="comment in comments" :key="comment.id" :value="comment.id" class="review-card">
              <div class="review-header">
                <div class="reviewer-info">
                  <img :src="comment.img" :alt="comment.name" class="reviewer-avatar">
                  <div class="reviewer-details">
                    <span class="reviewer-name">{{ comment.name }} {{ comment.surname }}</span>
                    <span class="review-date">{{ formatDateTime(comment.created_at) }}</span>
                  </div>
                </div>
                <div class="review-rating">
                  <span v-for="i in 5" :key="i" class="star small" :class="{ active: i <= comment.grade }">★</span>
                </div>
              </div>
              <div class="review-content">
                <p>{{ comment.comment }}</p>
              </div>
              <div class="review-footer">
                <button class="like-btn" @click="likeReview(review.id)">
                  👍 {{ comment.likes ?? 0 }}
                </button>
                <button class="reply-btn" @click="showReplyForm(review.id)">
                  💬 Ответить
                </button>
              </div>
              
              <!-- Ответы на отзыв -->
              <!--<div v-if="review.replies && review.replies.length" class="replies-list">
                <div v-for="reply in review.replies" :key="reply.id" class="reply-card">
                  <div class="reply-header">
                    <span class="reply-author">{{ reply.author }}</span>
                    <span class="reply-date">{{ reply.date }}</span>
                  </div>
                  <p class="reply-content">{{ reply.content }}</p>
                </div>
              </div>-->

              <!-- Форма ответа -->
              <div v-if="replyFormId === comment.id" class="reply-form">
                <textarea v-model="replyText" placeholder="Ваш ответ..." rows="2" class="glass-input"></textarea>
                <div class="reply-actions">
                  <button class="cancel-btn" @click="replyFormId = null">Отмена</button>
                  <button class="submit-btn" @click="submitReply(review.id)">Ответить</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Пагинация отзывов -->
          <div class="pagination" v-if="totalReviewPages > 1">
            <button class="page-btn" :disabled="currentReviewPage === 1" @click="currentReviewPage--">←</button>
            <span class="page-info">{{ currentReviewPage }} / {{ totalReviewPages }}</span>
            <button class="page-btn" :disabled="currentReviewPage === totalReviewPages" @click="currentReviewPage++">→</button>
          </div>
        </div>

        <div v-else>
          <span class="text-gray-400">Отзывов покупателей нет</span>
        </div>
      </div>

      <!-- Похожие товары -->
      <div class="similar-products" v-if="similarProducts.length">
        <h2>Похожие товары</h2>
        <div class="similar-grid">
          <div v-for="product in similarProducts" :key="product.id" class="similar-card glass-panel" @click="goToProduct(product.id)">
            <img :src="product.image" :alt="product.name">
            <h4>{{ product.name }}</h4>
            <div class="price">{{ formatPrice(product.price) }} ₽</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Модальное окно написания отзыва -->
    <transition name="modal">
      <div v-if="showReviewModal" class="modal-overlay" @click.self="showReviewModal = false">
        <div class="modal-content glass-panel">
          <h3>Написать отзыв</h3>
          <form @submit.prevent="submitReview" class="review-form">
            <div class="form-group">
              <label>Оценка</label>
              <div class="rating-input">
                <span 
                  v-for="i in 5" 
                  :key="i" 
                  class="rating-star"
                  :class="{ active: i <= newReview.rating }"
                  :value="i"
                  @click="newReview.rating = i"
                >★</span>
              </div>
            </div>
            <div class="form-group">
              <label>Отзыв</label>
              <textarea v-model="newReview.content" rows="5" required class="glass-input"></textarea>
            </div>
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="newReview.anonymous">
                <span>Анонимно</span>
              </label>
            </div>
            <div class="modal-actions">
              <button type="button" class="cancel-btn" @click="showReviewModal = false">Отмена</button>
              <button type="submit" class="submit-btn">Отправить отзыв</button>
            </div>
          </form>
        </div>
      </div>
    </transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import {  useRoute ,useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthStore } from '../stores/authStore'
import { useAlertStore } from '../stores/alertStore';


const alerts = useAlertStore();
const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const product = ref(null);
const specifications = ref(null);
const comments = ref(null);
const count = ref(null);

// Текущее изображение
const currentImage = ref('')
const quantity = ref(1)
const activeTab = ref('description')
const selectedColor = ref('')
const isFavorite = ref(false)
const showReviewModal = ref(false)
const replyFormId = ref(null)
const replyText = ref('')
const currentReviewPage = ref(1)
const reviewsPerPage = 5

const formatDateTime = (date) => {
  if (!date) return '—'
  const d = new Date(date)
  return d.toLocaleString('ru-RU')
}

const fetchProduct = async () => {
  const slug = route.params.slug; // Получаем slug из URL
  
  try 
  {
    const response = await axios.post('/api/json.php', { 
    type: 'get_product_by_slug', 
    slug: slug 
    });

    // 1. Проверяем response.data
    if (response.data && response.data.result === 'good') 
    {
      product.value = response.data.data; // 2. Присваиваем сам объект товара
      specifications.value = response.data.specifications;
      
      // 3. Важно: инициализируем зависимые данные сразу после загрузки
      if (product.value.images && product.value.images.length > 0) 
      {
        currentImage.value = product.value.images[0];
      }
      if (product.value.colors && product.value.colors.length > 0) 
      {
        selectedColor.value = product.value.colors[0].name;
      }

      await loadComments();
    } 
    else 
    {
      console.warn("Товар не найден или ошибка в БД");
      router.push('/notfound');
    }
  } 
  catch (error) 
  {
    console.error("Ошибка сети:", error);
  }
};

const loadComments = async () => {
  try
  {
    if (!product.value || !product.value.id)
    {
      console.warn('Нет ID товара, комментарии не загружены');
      return;
    }
    
    const result = await authStore.fetchComments(product.value.id);
    
    if (result.success) 
    {
      comments.value = result.data.comments;
      count.value = result.count;
      console.log('Комменты успешно загружены!');
    } 
  }
  catch (error) 
  {
    console.dir(error); 

    if (error.response) 
    {
      console.error('Данные ответа:', error.response.data);
    } else 
    {
      console.error('Текст ошибки:', error.message);
    }
  }
}

// Похожие товары
const similarProducts = ref([
  {
    id: 2,
    name: 'iPhone 15',
    price: 74990,
    image: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=200&h=200&fit=crop'
  },
  {
    id: 3,
    name: 'Samsung Galaxy S24',
    price: 79990,
    image: 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=200&h=200&fit=crop'
  }
])

// Новый отзыв
const newReview = ref({
  rating: 5,
  title: '',
  content: '',
  anonymous: false
})

// Табы
const tabs = ref([
  { id: 'description', label: 'Описание' },
  { id: 'specs', label: 'Характеристики' }
])

// Пагинация отзывов
const paginatedReviews = computed(() => {
  const start = (currentReviewPage.value - 1) * reviewsPerPage
  const end = start + reviewsPerPage
  return reviews.value.slice(start, end)
})

if(comments.value != null)
{
  const totalReviewPages = computed(() => Math.ceil(comments.value.length / reviewsPerPage))
}

// Процент отзывов по рейтингу
const getRatingCount = (star) => {
  if(comments.value != null)
  {
    return comments.value.filter(r => Math.floor(r.grade) === star).length
  }
}

const getRatingPercent = (star) => {
  if(comments.value != null)
  {
    return (getRatingCount(star) / comments.value.length) * 100
  }
}

const averageRating = computed(() => {
  if (!comments.value || comments.value.length === 0) return 0
  
  const sum = comments.value.reduce((total, comment) => {
    return total + Number(comment.grade)
  }, 0)
  return sum / comments.value.length
})

// Форматирование цены
const formatPrice = (price) => {
  return price.toLocaleString('ru-RU')
}

// Управление количеством
const incrementQuantity = () => {
  if (quantity.value < product.value.stock) quantity.value++
}

const decrementQuantity = () => {
  if (quantity.value > 1) quantity.value--
}

// Добавление в корзину
const addToCart = () => {
  console.log('Добавлено в корзину:', { ...product.value, quantity: quantity.value })
  alert('Товар добавлен в корзину!')
}

// Избранное
const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value
}

// Отправка отзыва
const submitReview = async () => {
  const newReviewObj = {
    rating: newReview.value.rating,
    content: newReview.value.content,
  }
  showReviewModal.value = false
  newReview.value = { rating: 5, title: '', content: '', anonymous: false }

  const result = await authStore.createComment(product.value.id, newReviewObj.rating, newReviewObj.content);

  if (result.success)  
  {
    alerts.show('Отзыв успешно создан!', 'success');
    await fetchProduct();
  }
  else
  {
    console.error(result.error);
  }
}

// Лайк отзыва
const likeReview = (reviewId) => {
  const review = reviews.value.find(r => r.id === reviewId)
  if (review) review.likes++
}

// Показать форму ответа
const showReplyForm = (reviewId) => {
  replyFormId.value = replyFormId.value === reviewId ? null : reviewId
  replyText.value = ''
}

// Отправить ответ
const submitReply = (reviewId) => {
  const review = reviews.value.find(r => r.id === reviewId)
  if (review && replyText.value.trim()) {
    if (!review.replies) review.replies = []
    review.replies.push({
      id: Date.now(),
      author: 'Admin',
      date: new Date().toLocaleDateString('ru-RU'),
      content: replyText.value
    })
    replyFormId.value = null
    replyText.value = ''
  }
}

// Переход к товару
const goToProduct = (id) => {
  router.push(`/product/${id}`)
}

onMounted(() => {
  //currentImage.value = product.value.images[0]
  //selectedColor.value = product.value.colors?.[0]?.name || ''
  fetchProduct();
})
</script>

<style scoped>
.product-detail-page {
  min-height: 100vh;
  padding: 2rem;
  background: linear-gradient(135deg, #0a0a0f, #0f0f1a);
}

.product-container {
  max-width: 1400px;
  margin: 0 auto;
}

/* Хлебные крошки */
.breadcrumbs {
  margin-bottom: 2rem;
  color: rgba(255, 255, 255, 0.5);
}

.breadcrumbs a {
  color: rgba(255, 255, 255, 0.6);
  text-decoration: none;
  transition: color 0.2s;
}

.breadcrumbs a:hover {
  color: #c084fc;
}

.breadcrumbs span {
  margin: 0 0.5rem;
}

.breadcrumbs .current {
  color: #c084fc;
}

/* Основная информация */
.product-main {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  padding: 2rem;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border-radius: 32px;
  margin-bottom: 2rem;
}

/* Галерея */
.product-gallery {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.main-image {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.05);
}

.main-image img {
  width: 100%;
  height: auto;
  object-fit: cover;
}

.badge {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: #ef4444;
  padding: 0.25rem 0.75rem;
  border-radius: 30px;
  font-size: 0.8rem;
  font-weight: bold;
}

.badge.new {
  left: auto;
  right: 1rem;
  background: #22c55e;
}

.thumbnail-list {
  display: flex;
  gap: 0.5rem;
}

.thumbnail {
  width: 80px;
  height: 80px;
  border-radius: 12px;
  overflow: hidden;
  cursor: pointer;
  opacity: 0.6;
  transition: all 0.2s;
  border: 2px solid transparent;
}

.thumbnail.active {
  opacity: 1;
  border-color: #c084fc;
}

.thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Информация о товаре */
.product-title {
  font-size: 2rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.stars {
  display: flex;
  gap: 0.25rem;
}

.star {
  color: rgba(255, 255, 255, 0.2);
  font-size: 1.2rem;
}

.star.active {
  color: #fbbf24;
}

.reviews-count, .sku {
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.85rem;
}

.product-price {
  margin-bottom: 1.5rem;
}

.current-price {
  font-size: 2rem;
  font-weight: 700;
  color: #c084fc;
}

.old-price {
  font-size: 1.2rem;
  color: rgba(255, 255, 255, 0.4);
  text-decoration: line-through;
  margin-left: 1rem;
}

.discount {
  display: block;
  font-size: 0.85rem;
  color: #22c55e;
  margin-top: 0.5rem;
}

.damage {
  display: block;
  font-size: 0.85rem;
  color: #c52222;
  margin-top: 0.5rem;
}

.no-change{
    display: block;
    font-size: 0.85rem;
    color: #fdfdfd;
    margin-top: 0.5rem;
}

/* Опции */
.product-options {
  margin-bottom: 1.5rem;
}

.option-group {
  margin-bottom: 1rem;
}

.option-group label {
  display: block;
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.7);
  margin-bottom: 0.5rem;
}

.color-options {
  display: flex;
  gap: 0.5rem;
}

.color-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 0.2);
  cursor: pointer;
  transition: all 0.2s;
}

.color-btn.active {
  border-color: #c084fc;
  transform: scale(1.1);
}

.spec-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.spec-tag {
  background: rgba(255, 255, 255, 0.05);
  padding: 0.25rem 0.75rem;
  border-radius: 30px;
  font-size: 0.8rem;
}

/* Действия */
.product-actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.quantity-selector {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 30px;
  padding: 0.25rem;
}

.quantity-selector button {
  width: 36px;
  height: 36px;
  border: none;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  color: white;
  font-size: 1.2rem;
  cursor: pointer;
}

.quantity-selector span {
  min-width: 40px;
  text-align: center;
}

.add-to-cart-btn {
  flex: 1;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 30px;
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.add-to-cart-btn:hover {
  transform: scale(1.02);
  opacity: 0.9;
}

.favorite-btn {
  width: 48px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  font-size: 1.3rem;
  cursor: pointer;
  transition: all 0.2s;
}

.favorite-btn.active {
  background: rgba(239, 68, 68, 0.2);
  border-color: #ef4444;
}

/* Информация о доставке */
.stock-info {
  margin-bottom: 1rem;
  color: #22c55e;
}

.stock-info.low {
  color: #f59e0b;
}

.low-stock {
  display: inline-block;
  margin-left: 0.5rem;
  padding: 0.25rem 0.5rem;
  background: rgba(245, 158, 11, 0.2);
  border-radius: 30px;
  font-size: 0.75rem;
}

.delivery-info {
  background: rgba(255, 255, 255, 0.03);
  border-radius: 16px;
  padding: 1rem;
}

.delivery-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.6);
}

/* Табы */
.product-tabs {
  padding: 1.5rem;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border-radius: 32px;
  margin-bottom: 2rem;
}

.tabs-header {
  display: flex;
  gap: 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  margin-bottom: 1.5rem;
}

.tab-btn {
  padding: 0.75rem 1.5rem;
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.6);
  cursor: pointer;
  font-size: 1rem;
  position: relative;
}

.tab-btn.active {
  color: #c084fc;
}

.tab-btn.active::after {
  content: '';
  position: absolute;
  bottom: -1px;
  left: 0;
  right: 0;
  height: 2px;
  background: #c084fc;
}

.tab-content {
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.6;
}

.specs-table {
  width: 100%;
  border-collapse: collapse;
}

.specs-table tr {
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.specs-table td {
  padding: 0.75rem;
}

.spec-name {
  width: 40%;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.7);
}

/* Отзывы */
.reviews-section {
  padding: 2rem;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border-radius: 32px;
  margin-bottom: 2rem;
}

.reviews-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.reviews-header h2 {
  font-size: 1.5rem;
}

.write-review-btn {
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 30px;
  color: white;
  cursor: pointer;
}

/* Статистика отзывов */
.reviews-stats {
  display: grid;
  grid-template-columns: 200px 1fr;
  gap: 2rem;
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.average-rating {
  font-size: 3rem;
  font-weight: 700;
  color: #c084fc;
}

.stars-big {
  display: flex;
  gap: 0.25rem;
  margin: 0.5rem 0;
}

.stars-big .star {
  font-size: 1.5rem;
}

.rating-bar-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 0.5rem;
}

.star-label {
  width: 45px;
  font-size: 0.85rem;
}

.bar-bg {
  flex: 1;
  height: 8px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
  overflow: hidden;
}

.bar-fill {
  height: 100%;
  background: linear-gradient(90deg, #fbbf24, #f59e0b);
  border-radius: 4px;
}

.bar-count {
  width: 35px;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.5);
}

/* Карточка отзыва */
.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.review-card {
  background: rgba(255, 255, 255, 0.03);
  border-radius: 20px;
  padding: 1.5rem;
}

.review-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.reviewer-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.reviewer-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  object-fit: cover;
}

.reviewer-name {
  font-weight: 600;
}

.review-date {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.5);
  padding-left: 15px;
}

.review-rating .star {
  font-size: 0.9rem;
}

.review-content h4 {
  font-size: 1.1rem;
  margin-bottom: 0.5rem;
}

.review-content p {
  color: rgba(255, 255, 255, 0.7);
  line-height: 1.5;
}

.review-footer {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.like-btn, .reply-btn {
  background: none;
  border: none;
  color: rgba(255, 255, 255, 0.5);
  cursor: pointer;
  transition: color 0.2s;
}

.like-btn:hover, .reply-btn:hover {
  color: #c084fc;
}

/* Ответы */
.replies-list {
  margin-top: 1rem;
  padding-left: 3rem;
}

.reply-card {
  background: rgba(255, 255, 255, 0.02);
  border-radius: 12px;
  padding: 0.75rem;
  margin-top: 0.5rem;
}

.reply-header {
  display: flex;
  gap: 1rem;
  margin-bottom: 0.5rem;
  font-size: 0.85rem;
}

.reply-author {
  font-weight: 600;
  color: #c084fc;
}

.reply-date {
  color: rgba(255, 255, 255, 0.4);
  font-size: 0.7rem;
}

.reply-content {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.7);
}

.reply-form {
  margin-top: 1rem;
  padding-left: 3rem;
}

.reply-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

/* Похожие товары */
.similar-products {
  margin-top: 2rem;
}

.similar-products h2 {
  margin-bottom: 1.5rem;
}

.similar-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1.5rem;
}

.similar-card {
  padding: 1rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
}

.similar-card:hover {
  transform: translateY(-4px);
}

.similar-card img {
  width: 100%;
  height: 150px;
  object-fit: contain;
  margin-bottom: 1rem;
}

.similar-card h4 {
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.similar-card .price {
  color: #c084fc;
  font-weight: 600;
}

/* Пагинация */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.page-btn {
  padding: 0.5rem 1rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  cursor: pointer;
}

.page-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
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
}

.modal-content h3 {
  margin-bottom: 1.5rem;
}

.review-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.rating-input {
  display: flex;
  gap: 0.5rem;
}

.rating-star {
  font-size: 1.5rem;
  color: rgba(255, 255, 255, 0.2);
  cursor: pointer;
}

.rating-star.active {
  color: #fbbf24;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1rem;
}

.cancel-btn, .submit-btn {
  padding: 0.5rem 1.5rem;
  border-radius: 30px;
  cursor: pointer;
}

.cancel-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: white;
}

.submit-btn {
  background: linear-gradient(135deg, #9333ea, #3b82f6);
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
  .product-main {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .reviews-stats {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .product-detail-page {
    padding: 1rem;
  }
  
  .product-title {
    font-size: 1.5rem;
  }
  
  .product-actions {
    flex-wrap: wrap;
  }
  
  .reviews-header {
    flex-direction: column;
    gap: 1rem;
  }
  
  .replies-list {
    padding-left: 1rem;
  }
  
  .reply-form {
    padding-left: 1rem;
  }
}
</style>