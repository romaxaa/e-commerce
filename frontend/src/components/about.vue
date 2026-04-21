<template>
<div class="about-page">
    <div class="about-container">
        <!-- Hero секция -->
        <section class="about-hero glass-panel">
            <div class="hero-content">
                <span class="hero-badge">✨ О компании</span>
                <h1>Мы создаём<br><span class="gradient-text">технологии будущего</span></h1>
                <p>GlassShop — это больше, чем просто магазин электроники. Мы объединяем людей с передовыми технологиями, делая их доступными для каждого.</p>
                </div>
                <div class="hero-stats">
                <div class="stat">
                    <span class="stat-number">5+</span>
                    <span class="stat-label">лет на рынке</span>
                </div>
                <div class="stat">
                    <span class="stat-number">50k+</span>
                    <span class="stat-label">довольных клиентов</span>
                </div>
                <div class="stat">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">товаров в каталоге</span>
                </div>
                <div class="stat">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">поддержка</span>
                </div>
            </div>
        </section>

        <!-- Наша миссия -->
        <section class="mission-section glass-panel">
            <div class="mission-content">
            <div class="mission-icon">🎯</div>
            <h2>Наша миссия</h2>
            <p>Сделать современные технологии доступными и понятными для каждого, предоставляя только качественные товары с гарантией и лучшим сервисом.</p>
            </div>
            <div class="values-grid">
            <div class="value-card">
                <span class="value-icon">⚡</span>
                <h3>Качество</h3>
                <p>Только проверенные бренды и оригинальная продукция</p>
            </div>
            <div class="value-card">
                <span class="value-icon">🤝</span>
                <h3>Доверие</h3>
                <p>Честные цены и прозрачные условия сотрудничества</p>
            </div>
            <div class="value-card">
                <span class="value-icon">🚀</span>
                <h3>Инновации</h3>
                <p>Следим за трендами и предлагаем только новинки</p>
            </div>
            <div class="value-card">
                <span class="value-icon">💚</span>
                <h3>Забота</h3>
                <p>Индивидуальный подход к каждому клиенту</p>
            </div>
            </div>
        </section>

        <!-- Команда -->
        <section class="team-section">
            <h2 class="section-title">Наша команда</h2>
            <p class="section-subtitle">Профессионалы, которые любят своё дело</p>
            <div class="team-grid">
            <div v-for="member in team" :key="member.id" class="team-card glass-panel">
                <div class="member-photo">
                <img :src="member.avatar" :alt="member.name">
                <div class="social-links">
                    <a :href="member.social.linkedin" class="social-link">🔗</a>
                    <a :href="member.social.github" class="social-link">💻</a>
                </div>
                </div>
                <h3>{{ member.name }}</h3>
                <p class="member-role">{{ member.role }}</p>
                <p class="member-bio">{{ member.bio }}</p>
            </div>
            </div>
        </section>

        <!-- Карта и контакты -->
        <section class="contact-section">
            <div class="contact-info glass-panel">
                <h2>Свяжитесь с нами</h2>
                <div class="contact-details">
                    <div class="contact-item">
                    <span class="contact-icon">📍</span>
                    <div>
                        <h4>Адрес</h4>
                        <p>г. Москва, ул. Тверская, 15</p>
                    </div>
                    </div>
                    <div class="contact-item">
                    <span class="contact-icon">📞</span>
                    <div>
                        <h4>Телефон</h4>
                        <p>8 (800) 123-45-67</p>
                    </div>
                    </div>
                    <div class="contact-item">
                    <span class="contact-icon">✉️</span>
                    <div>
                        <h4>Email</h4>
                        <p>info@glassshop.ru</p>
                    </div>
                    </div>
                    <div class="contact-item">
                    <span class="contact-icon">⏰</span>
                    <div>
                        <h4>Режим работы</h4>
                        <p>Ежедневно: 10:00 - 22:00</p>
                    </div>
                    </div>
                </div>
                <div class="social-media">
                    <h4>Мы в соцсетях</h4>
                    <div class="social-icons">
                    <a href="#" class="social-icon">📘</a>
                    <a href="#" class="social-icon">📸</a>
                    <a href="#" class="social-icon">🎵</a>
                    <a href="#" class="social-icon">💬</a>
                    </div>
                </div>
            </div>
            
            <div class="map-container glass-panel">
                <div id="map" class="map"></div>
                <div class="map-fallback">
                    <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2244.356849262456!2d37.610319!3d55.757673!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46b54a5b2f5b3b7b%3A0x6c4f3b9e5c8a3c0!2z0KLQstC10YDRgdC60LDRjyDRg9C7LiwgMTUsINCc0L7RgdC60LLQsCwgMTI1MDA5!5e0!3m2!1sru!2sru!4v1700000000000!5m2!1sru!2sru" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </section>

        <!-- Отзывы клиентов -->
        <section class="testimonials-section">
            <h2 class="section-title">Что говорят наши клиенты</h2>
            <div class="testimonials-slider">
                <button class="slider-btn prev" @click="prevTestimonial">←</button>
                <div class="testimonials-container">
                    <div v-for="(testimonial, index) in visibleTestimonials" :key="testimonial.id" class="testimonial-card glass-panel">
                    <div class="testimonial-content">
                        <p class="testimonial-text">“{{ testimonial.text }}”</p>
                        <div class="testimonial-author">
                        <img :src="testimonial.avatar" :alt="testimonial.author">
                        <div>
                            <h4>{{ testimonial.author }}</h4>
                            <p class="author-role">{{ testimonial.role }}</p>
                        </div>
                        </div>
                        <div class="testimonial-rating">
                        <span v-for="i in 5" :key="i" class="star" :class="{ active: i <= testimonial.rating }">★</span>
                        </div>
                    </div>
                    </div>
                </div>
                <button class="slider-btn next" @click="nextTestimonial">→</button>
            </div>
        </section>

        <!-- Партнеры -->
        <section class="partners-section glass-panel">
            <h2>Наши партнеры</h2>
            <div class="partners-grid">
                <div v-for="partner in partners" :key="partner.name" class="partner-logo">
                    <span class="partner-icon">{{ partner.icon }}</span>
                    <span class="partner-name">{{ partner.name }}</span>
                </div>
            </div>
        </section>

        <!-- Преимущества -->
        <section class="advantages-section">
                <div class="advantage-item glass-panel">
                    <span class="advantage-icon">🚚</span>
                    <h3>Бесплатная доставка</h3>
                    <p>При заказе от 5000 ₽</p>
                </div>
                <div class="advantage-item glass-panel">
                    <span class="advantage-icon">🔄</span>
                    <h3>Возврат 14 дней</h3>
                    <p>Без проблем и вопросов</p>
                </div>
                <div class="advantage-item glass-panel">
                    <span class="advantage-icon">🔧</span>
                    <h3>Гарантия 12 месяцев</h3>
                    <p>На все товары</p>
                </div>
                <div class="advantage-item glass-panel">
                    <span class="advantage-icon">💳</span>
                    <h3>Удобная оплата</h3>
                    <p>Картой, наличными, SBP</p>
                </div>
        </section>
    </div>
</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Данные команды
const team = ref([
  {
    id: 1,
    name: 'Алексей Иванов',
    role: 'Основатель и CEO',
    bio: 'Энтузиаст технологий с 10-летним опытом в e-commerce',
    avatar: 'https://i.pravatar.cc/150?img=1',
    social: {
      linkedin: '#',
      github: '#'
    }
  },
  {
    id: 2,
    name: 'Мария Смирнова',
    role: 'Руководитель отдела маркетинга',
    bio: 'Специалист по digital-стратегиям и брендингу',
    avatar: 'https://i.pravatar.cc/150?img=2',
    social: {
      linkedin: '#',
      github: '#'
    }
  },
  {
    id: 3,
    name: 'Дмитрий Петров',
    role: 'Технический директор',
    bio: 'Эксперт в области IT и разработки',
    avatar: 'https://i.pravatar.cc/150?img=3',
    social: {
      linkedin: '#',
      github: '#'
    }
  },
  {
    id: 4,
    name: 'Елена Волкова',
    role: 'Руководитель отдела продаж',
    bio: 'Профессионал в области клиентского сервиса',
    avatar: 'https://i.pravatar.cc/150?img=4',
    social: {
      linkedin: '#',
      github: '#'
    }
  }
])

// Отзывы
const testimonials = ref([
  {
    id: 1,
    author: 'Иван Петров',
    role: 'Постоянный клиент',
    text: 'Отличный магазин! Большой выбор, быстрая доставка, приятные цены. Техника вся оригинальная, с гарантией. Рекомендую!',
    rating: 5,
    avatar: 'https://i.pravatar.cc/150?img=5'
  },
  {
    id: 2,
    author: 'Анна Смирнова',
    role: 'Клиент',
    text: 'Заказывала наушники, всё пришло быстро и в отличной упаковке. Консультанты вежливые, помогли с выбором. Спасибо!',
    rating: 5,
    avatar: 'https://i.pravatar.cc/150?img=6'
  },
  {
    id: 3,
    author: 'Михаил Козлов',
    role: 'IT-специалист',
    text: 'Заказываю здесь технику уже не первый раз. Всегда оригиналы, цены ниже чем у конкурентов. Доставка в срок.',
    rating: 4,
    avatar: 'https://i.pravatar.cc/150?img=7'
  },
  {
    id: 4,
    author: 'Екатерина Морозова',
    role: 'Дизайнер',
    text: 'Отличный сервис! Возврат оформили без проблем. Техника качественная, буду заказывать ещё.',
    rating: 5,
    avatar: 'https://i.pravatar.cc/150?img=8'
  }
])

// Партнеры
const partners = ref([
  { name: 'Apple', icon: '🍎' },
  { name: 'Samsung', icon: '📱' },
  { name: 'Sony', icon: '🎮' },
  { name: 'Xiaomi', icon: '📷' },
  { name: 'Google', icon: '🔍' },
  { name: 'Microsoft', icon: '💻' }
])

// Пагинация отзывов
const currentTestimonialIndex = ref(0)
const testimonialsPerPage = 3

const visibleTestimonials = computed(() => {
  const start = currentTestimonialIndex.value
  const end = start + testimonialsPerPage
  return testimonials.value.slice(start, end)
})

const nextTestimonial = () => {
  if (currentTestimonialIndex.value + testimonialsPerPage < testimonials.value.length) {
    currentTestimonialIndex.value += testimonialsPerPage
  } else {
    currentTestimonialIndex.value = 0
  }
}

const prevTestimonial = () => {
  if (currentTestimonialIndex.value - testimonialsPerPage >= 0) {
    currentTestimonialIndex.value -= testimonialsPerPage
  } else {
    currentTestimonialIndex.value = Math.max(0, testimonials.value.length - testimonialsPerPage)
  }
}

// Инициализация карты
onMounted(() => {
  // Карта уже подключена через iframe
  console.log('About page loaded')
})
</script>

<style scoped>
.about-page {
  min-height: 100vh;
  padding: 2rem;
  background: linear-gradient(135deg, #0a0a0f, #0f0f1a);
}

.about-container {
  max-width: 1400px;
  margin: 0 auto;
}

/* Glass панели */
.glass-panel {
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Hero секция */
.about-hero {
  padding: 3rem;
  border-radius: 32px;
  margin-bottom: 2rem;
  background: linear-gradient(135deg, rgba(30, 30, 40, 0.8), rgba(20, 20, 30, 0.6));
  text-align: center;
}

.hero-badge {
  display: inline-block;
  background: rgba(139, 92, 246, 0.2);
  border: 1px solid rgba(139, 92, 246, 0.3);
  border-radius: 30px;
  padding: 0.3rem 1rem;
  font-size: 0.85rem;
  color: #c084fc;
  margin-bottom: 1.5rem;
}

.about-hero h1 {
  font-size: 3rem;
  font-weight: 700;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.gradient-text {
  background: linear-gradient(135deg, #c084fc, #60a5fa);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.about-hero p {
  color: rgba(255, 255, 255, 0.7);
  font-size: 1.1rem;
  max-width: 600px;
  margin: 0 auto 2rem;
}

.hero-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.stat {
  text-align: center;
}

.stat-number {
  display: block;
  font-size: 2rem;
  font-weight: 700;
  color: #c084fc;
}

.stat-label {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.6);
}

/* Миссия */
.mission-section {
  padding: 3rem;
  border-radius: 32px;
  margin-bottom: 2rem;
  text-align: center;
}

.mission-icon {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.mission-section h2 {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.mission-section p {
  color: rgba(255, 255, 255, 0.7);
  max-width: 700px;
  margin: 0 auto 2rem;
  line-height: 1.6;
}

.values-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  margin-top: 2rem;
}

.value-card {
  padding: 1.5rem;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 24px;
  transition: all 0.3s;
}

.value-card:hover {
  background: rgba(255, 255, 255, 0.06);
  transform: translateY(-4px);
}

.value-icon {
  font-size: 2rem;
  display: block;
  margin-bottom: 1rem;
}

.value-card h3 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
}

.value-card p {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.5);
  margin: 0;
}

/* Команда */
.team-section {
  margin-bottom: 2rem;
}

.section-title {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.section-subtitle {
  text-align: center;
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 2rem;
}

.team-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1.5rem;
}

.team-card {
  padding: 1.5rem;
  border-radius: 24px;
  text-align: center;
  transition: all 0.3s;
}

.team-card:hover {
  transform: translateY(-4px);
}

.member-photo {
  position: relative;
  width: 150px;
  height: 150px;
  margin: 0 auto 1rem;
}

.member-photo img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid rgba(139, 92, 246, 0.5);
}

.social-links {
  position: absolute;
  bottom: 0;
  right: 0;
  display: flex;
  gap: 0.3rem;
}

.social-link {
  width: 30px;
  height: 30px;
  background: rgba(0, 0, 0, 0.6);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  color: white;
  font-size: 0.8rem;
  transition: all 0.2s;
}

.social-link:hover {
  background: #c084fc;
  transform: scale(1.1);
}

.team-card h3 {
  font-size: 1.2rem;
  margin-bottom: 0.25rem;
}

.member-role {
  color: #c084fc;
  font-size: 0.85rem;
  margin-bottom: 0.5rem;
}

.member-bio {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.6);
}

/* Контакты и карта */
.contact-section {
  display: grid;
  grid-template-columns: 1fr 1.5fr;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.contact-info {
  padding: 2rem;
  border-radius: 32px;
}

.contact-info h2 {
  margin-bottom: 1.5rem;
}

.contact-details {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.contact-item {
  display: flex;
  gap: 1rem;
}

.contact-icon {
  font-size: 1.5rem;
}

.contact-item h4 {
  margin-bottom: 0.25rem;
}

.contact-item p {
  color: rgba(255, 255, 255, 0.6);
  font-size: 0.9rem;
}

.social-media h4 {
  margin-bottom: 1rem;
}

.social-icons {
  display: flex;
  gap: 1rem;
}

.social-icon {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  color: white;
  font-size: 1.2rem;
  transition: all 0.2s;
}

.social-icon:hover {
  background: #c084fc;
  transform: scale(1.1);
}

.map-container {
  border-radius: 32px;
  overflow: hidden;
  height: 400px;
}

.map-fallback {
  width: 100%;
  height: 100%;
}

.map-fallback iframe {
  width: 100%;
  height: 100%;
}

/* Отзывы */
.testimonials-section {
  margin-bottom: 2rem;
}

.testimonials-slider {
  display: flex;
  align-items: center;
  gap: 1rem;
  position: relative;
}

.slider-btn {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  color: white;
  cursor: pointer;
  transition: all 0.2s;
}

.slider-btn:hover {
  background: rgba(139, 92, 246, 0.3);
  border-color: #c084fc;
}

.testimonials-container {
  flex: 1;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.testimonial-card {
  padding: 1.5rem;
  border-radius: 24px;
  transition: all 0.3s;
}

.testimonial-text {
  font-style: italic;
  color: rgba(255, 255, 255, 0.8);
  line-height: 1.6;
  margin-bottom: 1.5rem;
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.testimonial-author img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.testimonial-author h4 {
  margin-bottom: 0.25rem;
}

.author-role {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.5);
}

.testimonial-rating .star {
  color: rgba(255, 255, 255, 0.2);
  font-size: 0.9rem;
}

.testimonial-rating .star.active {
  color: #fbbf24;
}

/* Партнеры */
.partners-section {
  padding: 2rem;
  border-radius: 32px;
  margin-bottom: 2rem;
  text-align: center;
}

.partners-section h2 {
  margin-bottom: 2rem;
}

.partners-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 2rem;
}

.partner-logo {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  opacity: 0.6;
  transition: all 0.2s;
}

.partner-logo:hover {
  opacity: 1;
  transform: scale(1.05);
}

.partner-icon {
  font-size: 2.5rem;
}

.partner-name {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.7);
}

/* Преимущества */
.advantages-section {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}

.advantage-item {
  padding: 1.5rem;
  border-radius: 24px;
  text-align: center;
  transition: all 0.3s;
}

.advantage-item:hover {
  transform: translateY(-4px);
}

.advantage-icon {
  font-size: 2rem;
  display: block;
  margin-bottom: 1rem;
}

.advantage-item h3 {
  font-size: 1rem;
  margin-bottom: 0.5rem;
}

.advantage-item p {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.5);
}

/* Адаптивность */
@media (max-width: 1024px) {
  .hero-stats {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .values-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .contact-section {
    grid-template-columns: 1fr;
  }
  
  .testimonials-container {
    grid-template-columns: 1fr;
  }
  
  .advantages-section {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .about-page {
    padding: 1rem;
  }
  
  .about-hero {
    padding: 2rem;
  }
  
  .about-hero h1 {
    font-size: 2rem;
  }
  
  .hero-stats {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .values-grid {
    grid-template-columns: 1fr;
  }
  
  .team-grid {
    grid-template-columns: 1fr;
  }
  
  .advantages-section {
    grid-template-columns: 1fr;
  }
  
  .partners-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>