<!-- Returns.vue - Возврат товара -->
<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Возврат товара</h1>
      <p>Как вернуть товар и получить возврат средств</p>
    </div>

    <div class="page-content glass-panel">
      <section class="content-section">
        <h2>📋 Условия возврата</h2>
        <div class="info-grid">
          <div class="info-card">
            <span class="info-icon">⏰</span>
            <h3>14 дней</h3>
            <p>Срок возврата качественного товара</p>
          </div>
          <div class="info-card">
            <span class="info-icon">📦</span>
            <h3>Товарный вид</h3>
            <p>Товар должен быть в оригинальной упаковке</p>
          </div>
          <div class="info-card">
            <span class="info-icon">🧾</span>
            <h3>Чек</h3>
            <p>Наличие документа о покупке</p>
          </div>
          <div class="info-card">
            <span class="info-icon">🔧</span>
            <h3>Комплектация</h3>
            <p>Полная комплектация товара</p>
          </div>
        </div>
      </section>

      <section class="content-section">
        <h2>❌ Товары, не подлежащие возврату</h2>
        <ul class="exclusion-list">
          <li>Наушники и беруши (по гигиеническим причинам)</li>
          <li>Программное обеспечение в лицензионных носителях</li>
          <li>Товары с нарушенной упаковкой</li>
          <li>Технически сложные товары с признаками использования</li>
        </ul>
      </section>

      <section class="content-section">
        <h2>🔄 Как оформить возврат</h2>
        <div class="steps">
          <div class="step">
            <div class="step-number">1</div>
            <div>
              <h3>Заполните заявление</h3>
              <p><a class="hover:text-blue-300" href="./elements/word.pdf" download="word.pdf">Скачайте форму</a> на сайте или получите у менеджера</p>
            </div>
          </div>
          <div class="step">
            <div class="step-number">2</div>
            <div>
              <h3>Упакуйте товар</h3>
              <p>Сохраните заводскую упаковку и все аксессуары</p>
            </div>
          </div>
          <div class="step">
            <div class="step-number">3</div>
            <div>
              <h3>Отправьте товар</h3>
              <p>Передайте курьеру или в пункт выдачи</p>
            </div>
          </div>
          <div class="step">
            <div class="step-number">4</div>
            <div>
              <h3>Получите деньги</h3>
              <p>Средства вернутся в течение 10 дней</p>
            </div>
          </div>
        </div>
      </section>

      <section class="content-section info-box">
        <h3>💰 Способы возврата денег</h3>
        <ul>
          <li>На карту (в течение 3-5 рабочих дней)</li>
          <li>Наличными в пункте выдачи</li>
          <li>На банковский счет (до 10 рабочих дней)</li>
        </ul>
      </section>

      <div class="contact-support">
        <p>Вопросы по возврату: <a href="mailto:info@glassshop.ru">info@glassshop.ru</a> или по телефону 8 (800) 123-45-67</p>
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

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.info-card {
  text-align: center;
  padding: 1.5rem;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 20px;
}

.info-icon {
  font-size: 2rem;
  display: block;
  margin-bottom: 0.5rem;
}

.info-card h3 {
  margin-bottom: 0.25rem;
}

.info-card p {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.5);
}

.exclusion-list {
  margin: 0;
  padding-left: 1.2rem;
  color: rgba(255, 255, 255, 0.7);
}

.exclusion-list li {
  margin-bottom: 0.5rem;
}

.steps {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.step {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.step-number {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  flex-shrink: 0;
}

.step h3 {
  margin-bottom: 0.25rem;
}

.step p {
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.85rem;
}

.info-box {
  background: rgba(139, 92, 246, 0.1);
  border-radius: 20px;
  padding: 1.5rem;
}

.info-box h3 {
  margin-bottom: 1rem;
}

.info-box ul {
  margin: 0;
  padding-left: 1.2rem;
  color: rgba(255, 255, 255, 0.7);
}

.info-box li {
  margin-bottom: 0.5rem;
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
  
  .info-grid {
    grid-template-columns: 1fr;
  }
  
  .step {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
}
</style>