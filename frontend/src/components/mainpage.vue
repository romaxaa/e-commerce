<!-- LandingPage.vue -->
<template>
  <div class="landing-page">
    <!-- Hero Section с эффектом стекла -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
      <!-- Анимированный фон -->
      <div class="absolute inset-0 bg-gradient-to-br from-purple-900/20 via-black to-blue-900/20"></div>
      
      <!-- Плавающие стеклянные сферы -->
      <div class="absolute top-20 left-10 w-72 h-72 bg-purple-500/30 rounded-full blur-3xl animate-float"></div>
      <div class="absolute bottom-20 right-10 w-96 h-96 bg-blue-500/30 rounded-full blur-3xl animate-float-delayed"></div>
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-r from-purple-600/10 to-blue-600/10 rounded-full blur-3xl"></div>
      
      <div class="container mx-auto px-4 relative z-10">
        <div class="text-center max-w-4xl mx-auto">
          <!-- Анимированный заголовок -->
          <div class="animate-fade-up">
            <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold mb-6 bg-gradient-to-r from-white via-purple-300 to-blue-300 bg-clip-text text-transparent">
              Будущее
              <span class="block text-4xl md:text-6xl mt-2">уже здесь</span>
            </h1>
          </div>
          
          <!-- Описание с эффектом стекла -->
          <div class="animate-fade-up animation-delay-200">
            <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-2xl mx-auto backdrop-blur-sm">
              Откройте для себя коллекцию премиальных товаров, 
              созданных для тех, кто ценит качество и стиль
            </p>
          </div>
          
          <!-- CTA Кнопки -->
          <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-up animation-delay-400">
            <button @click="scrollToCatalog" class="glass-button px-8 py-4 rounded-full text-lg font-semibold group">
              <span>Исследовать коллекцию</span>
              <ArrowRightIcon class="inline-block ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" />
            </button>
            <button class="glass-button-outline px-8 py-4 rounded-full text-lg font-semibold">
              Смотреть видео
            </button>
          </div>
        </div>
      </div>
      
      <!-- Scroll индикатор -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <div class="w-6 h-10 border-2 border-gray-400 rounded-full flex justify-center">
          <div class="w-1 h-2 bg-gray-400 rounded-full mt-2 animate-scroll"></div>
        </div>
      </div>
    </section>
    
    <!-- Категории с эффектом стекла -->
    <section class="py-20 bg-black/40 backdrop-blur-sm">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
          Исследуйте категории
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="(category, index) in categories" :key="index" 
               class="glass-card group cursor-pointer"
               :style="{ animationDelay: `${index * 100}ms` }"
               @click="navigateToCategory(category)">
            <div class="relative overflow-hidden rounded-2xl">
              <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent z-10"></div>
              <img :src="category.image" :alt="category.name" 
                   class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700" />
              <div class="absolute bottom-0 left-0 right-0 p-6 z-20">
                <h3 class="text-2xl font-bold mb-2">{{ category.name }}</h3>
                <p class="text-gray-300 text-sm">{{ category.count }} товаров</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Популярные товары -->
    <section class="py-20">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-12">
          <h2 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
            Популярные товары
          </h2>
          <button @click="scrollToCatalog" class="text-purple-400 hover:text-purple-300 transition-colors flex items-center gap-2">
            Все товары
            <ArrowRightIcon class="w-4 h-4" />
          </button>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="(product, index) in popularProducts" :key="product.id"
               class="glass-card group cursor-pointer animate-fade-up"
               :style="{ animationDelay: `${index * 100}ms` }"
               @click="navigateToProduct(product.id)">
            <div class="relative overflow-hidden rounded-2xl mb-4">
              <img :src="product.image" :alt="product.name" 
                   class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700" />
              <div class="absolute top-4 right-4 glass-effect px-3 py-1 rounded-full text-sm">
                -{{ product.discount }}%
              </div>
            </div>
            <h3 class="text-lg font-semibold mb-2">{{ product.name }}</h3>
            <div class="flex items-center gap-2 mb-3">
              <span class="text-2xl font-bold text-purple-400">{{ formatPrice(product.price) }} ₽</span>
              <span class="text-gray-400 line-through text-sm">{{ formatPrice(product.oldPrice) }} ₽</span>
            </div>
            <div class="flex items-center gap-1 mb-4">
              <StarIcon v-for="star in 5" :key="star" class="w-4 h-4" 
                        :class="star <= product.rating ? 'text-yellow-400 fill-current' : 'text-gray-600'" />
              <span class="text-gray-400 text-sm ml-2">({{ product.reviews }})</span>
            </div>
            <button @click.stop="addToCart(product)" 
                    class="w-full glass-button py-3 rounded-xl font-semibold group-hover:bg-purple-600 transition-all">
              В корзину
            </button>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Преимущества -->
    <section class="py-20 bg-black/40 backdrop-blur-sm">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="(feature, index) in features" :key="index"
               class="glass-card text-center p-8 animate-fade-up"
               :style="{ animationDelay: `${index * 100}ms` }">
            <div class="w-16 h-16 bg-gradient-to-br from-purple-500/20 to-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
              <component :is="feature.icon" class="w-8 h-8 text-purple-400" />
            </div>
            <h3 class="text-xl font-semibold mb-2">{{ feature.title }}</h3>
            <p class="text-gray-400">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>
    
    <!-- CTA Баннер -->
    <section class="py-20">
      <div class="container mx-auto px-4">
        <div class="glass-card-gradient relative overflow-hidden p-12 rounded-3xl">
          <div class="relative z-10 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
              Готовы к обновлению?
            </h2>
            <p class="text-gray-300 mb-8 max-w-2xl mx-auto">
              Подпишитесь на рассылку и получайте первыми информацию о новинках и специальных предложениях
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center max-w-md mx-auto">
              <input type="email" placeholder="Ваш email" 
                     class="glass-input flex-1 px-6 py-3 rounded-full outline-none">
              <button class="glass-button px-8 py-3 rounded-full font-semibold">
                Подписаться
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { ArrowRightIcon, StarIcon } from '@heroicons/vue/24/outline'

const router = useRouter()

// Категории
const categories = ref([
  { name: 'Электроника', count: 245, image: 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=500&h=500&fit=crop' },
  { name: 'Одежда', count: 532, image: 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=500&h=500&fit=crop' },
  { name: 'Аксессуары', count: 189, image: 'https://images.unsplash.com/photo-1523170335258-f5ed11844a49?w=500&h=500&fit=crop' },
  { name: 'Для дома', count: 367, image: 'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=500&h=500&fit=crop' }
])

// Популярные товары
const popularProducts = ref([
  {
    id: 1,
    name: 'Premium Wireless Headphones',
    price: 24990,
    oldPrice: 39990,
    discount: 37,
    rating: 4.8,
    reviews: 128,
    image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop'
  },
  {
    id: 2,
    name: 'Smart Watch Ultra',
    price: 35990,
    oldPrice: 49990,
    discount: 28,
    rating: 4.9,
    reviews: 256,
    image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop'
  },
  {
    id: 3,
    name: 'Minimalist Backpack',
    price: 8990,
    oldPrice: 12990,
    discount: 30,
    rating: 4.7,
    reviews: 89,
    image: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&h=400&fit=crop'
  },
  {
    id: 4,
    name: 'Wireless Speaker',
    price: 15990,
    oldPrice: 24990,
    discount: 36,
    rating: 4.8,
    reviews: 167,
    image: 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=400&h=400&fit=crop'
  }
])

// Преимущества
const features = ref([
  { title: 'Бесплатная доставка', description: 'При заказе от 5000 ₽', icon: 'TruckIcon' },
  { title: 'Гарантия качества', description: '12 месяцев на все товары', icon: 'ShieldCheckIcon' },
  { title: 'Поддержка 24/7', description: 'Всегда на связи', icon: 'HeadsetIcon' }
])

const formatPrice = (price) => {
  return price.toLocaleString('ru-RU')
}

const scrollToCatalog = () => {
  // Будет реализовано позже
  console.log('Scroll to catalog')
}

const navigateToCategory = (category) => {
  router.push(`/catalog?category=${category.name}`)
}

const navigateToProduct = (productId) => {
  router.push(`/product/${productId}`)
}

const addToCart = (product) => {
  console.log('Added to cart:', product)
}
</script>

<style scoped>
@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(5deg); }
}

@keyframes float-delayed {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-30px) rotate(-5deg); }
}

@keyframes scroll {
  0% { transform: translateY(0px); opacity: 1; }
  100% { transform: translateY(15px); opacity: 0; }
}

@keyframes fade-up {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-float {
  animation: float 8s ease-in-out infinite;
}

.animate-float-delayed {
  animation: float-delayed 10s ease-in-out infinite;
}

.animate-scroll {
  animation: scroll 2s ease-in-out infinite;
}

.animate-fade-up {
  animation: fade-up 0.8s ease-out forwards;
  opacity: 0;
}

.animation-delay-200 {
  animation-delay: 0.2s;
}

.animation-delay-400 {
  animation-delay: 0.4s;
}

/* Glassmorphism стили */
.glass-card {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  border-radius: 1.5rem;
  padding: 1.5rem;
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
}

.glass-card:hover {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 255, 255, 0.2);
  transform: translateY(-5px);
}

.glass-button {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  transition: all 0.3s ease;
}

.glass-button:hover {
  background: rgba(139, 92, 246, 0.6);
  border-color: rgba(139, 92, 246, 0.8);
  transform: scale(1.05);
}

.glass-button-outline {
  background: transparent;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  transition: all 0.3s ease;
}

.glass-button-outline:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.5);
  transform: scale(1.05);
}

.glass-input {
  background: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
}

.glass-input:focus {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(139, 92, 246, 0.5);
  outline: none;
}

.glass-card-gradient {
  background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(59, 130, 246, 0.1));
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.glass-effect {
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(8px);
}
</style>