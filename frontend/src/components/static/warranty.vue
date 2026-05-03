<!-- Warranty.vue - Гарантия -->
<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Гарантия</h1>
      <p>Мы гарантируем качество и надежность каждого товара</p>
    </div>

    <div class="page-content glass-panel">
      <section class="content-section">
        <h2>✅ Стандартная гарантия</h2>
        <div class="warranty-cards">
          <div class="warranty-card">
            <span class="warranty-icon">📱</span>
            <div>
              <h3>Смартфоны и планшеты</h3>
              <p>Гарантия 12 месяцев</p>
            </div>
          </div>
          <div class="warranty-card">
            <span class="warranty-icon">💻</span>
            <div>
              <h3>Ноутбуки и компьютеры</h3>
              <p>Гарантия 12 месяцев</p>
            </div>
          </div>
          <div class="warranty-card">
            <span class="warranty-icon">🎧</span>
            <div>
              <h3>Аксессуары и наушники</h3>
              <p>Гарантия 6 месяцев</p>
            </div>
          </div>
          <div class="warranty-card">
            <span class="warranty-icon">⌚</span>
            <div>
              <h3>Умные часы</h3>
              <p>Гарантия 12 месяцев</p>
            </div>
          </div>
        </div>
      </section>

      <section class="content-section">
        <h2>📝 Условия гарантии</h2>
        <ul class="terms-list">
          <li>Гарантия распространяется на производственные дефекты</li>
          <li>Срок гарантии начинает исчисляться с момента передачи товара покупателю</li>
          <li>Для гарантийного обслуживания необходим чек и заполненный гарантийный талон</li>
          <li>При обнаружении дефекта обратитесь в нашу сервисную службу</li>
        </ul>
      </section>

      <section class="content-section">
        <h2>🚫 Что не покрывается гарантией</h2>
        <ul class="exclusion-list">
          <li>Механические повреждения (трещины, царапины, разбитые экраны)</li>
          <li>Попадание жидкости внутрь устройства</li>
          <li>Естественный износ элементов (аккумуляторы, ремни и т.д.)</li>
          <li>Несанкционированный ремонт или модификация</li>
          <li>Использование неоригинальных аксессуаров</li>
        </ul>
      </section>

      <section class="content-section info-box">
        <h3>🔧 Сервисные центры</h3>
        <p>Мы сотрудничаем с авторизованными сервисными центрами во всех регионах. Для получения адреса ближайшего сервисного центра обратитесь в поддержку.</p>
      </section>

      <section class="content-section">
        <h2>⭐ Расширенная гарантия</h2>
        <div class="extended-card">
          <div class="extended-info">
            <h3>GlassShop Care+</h3>
            <p>Дополнительная защита для вашей техники от случайных повреждений</p>
            <ul>
              <li>Увеличенный срок гарантии до 24 месяцев</li>
              <li>Защита от случайных поломок</li>
              <li>Замена при неремонтопригодности</li>
              <li>Приоритетное обслуживание</li>
            </ul>
          </div>
          <div class="extended-price">+ 2 990 ₽</div>
        </div>
      </section>

      <div class="contact-support">
        <p>Вопросы по гарантии уточняйте у консультанта или в поддержке: <a href="mailto:info@glassshop.ru">info@glassshop.ru</a></p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 2rem;
}

.page-header {
  text-align: center;
  margin-bottom: 2rem;
}

.page-header h1 {
  font-size: 2rem;
  margin-bottom: 0.5rem;
}

.page-header p {
  color: rgba(255, 255, 255, 0.6);
}

.page-content {
  padding: 2rem;
  border-radius: 32px;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
}

.content-section {
  margin-bottom: 2rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.content-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.content-section h2 {
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
}

.warranty-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1rem;
}

.warranty-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 16px;
}

.warranty-icon {
  font-size: 1.8rem;
}

.warranty-card h3 {
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.warranty-card p {
  font-size: 0.8rem;
  color: #22c55e;
}

.terms-list, .exclusion-list {
  margin: 0;
  padding-left: 1.2rem;
  color: rgba(255, 255, 255, 0.7);
}

.terms-list li, .exclusion-list li {
  margin-bottom: 0.5rem;
}

.info-box {
  background: rgba(139, 92, 246, 0.1);
  border-radius: 20px;
  padding: 1.5rem;
}

.info-box h3 {
  margin-bottom: 0.5rem;
}

.extended-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(59, 130, 246, 0.1));
  border-radius: 20px;
  border: 1px solid rgba(139, 92, 246, 0.3);
}

.extended-info h3 {
  font-size: 1.2rem;
  margin-bottom: 0.5rem;
  color: #c084fc;
}

.extended-info p {
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 0.5rem;
}

.extended-info ul {
  margin: 0;
  padding-left: 1.2rem;
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.5);
}

.extended-price {
  font-size: 1.5rem;
  font-weight: 700;
  color: #c084fc;
  white-space: nowrap;
  margin-left: 1rem;
}

.contact-support {
  margin-top: 2rem;
  text-align: center;
  padding-top: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.6);
}

.contact-support a {
  color: #c084fc;
  text-decoration: none;
}

@media (max-width: 768px) {
  .page-container {
    padding: 1rem;
  }
  
  .warranty-cards {
    grid-template-columns: 1fr;
  }
  
  .extended-card {
    flex-direction: column;
    text-align: center;
  }
  
  .extended-price {
    margin-left: 0;
    margin-top: 1rem;
  }
}
</style>