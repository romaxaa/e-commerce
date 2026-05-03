<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Доставка и оплата</h1>
      <p>Удобные способы получения и оплаты ваших заказов</p>
    </div>

    <div class="page-content glass-panel">
      <!-- Доставка -->
      <section class="content-section">
        <h2>🚚 Способы доставки</h2>
        
        <div class="delivery-methods">
          <div class="method-card">
            <div class="method-icon">📦</div>
            <div class="method-info">
              <h3>Курьерская доставка</h3>
              <p>Доставка по Москве в день заказа, по России от 2 до 5 дней</p>
              <ul>
                <li>Бесплатно при заказе от 5000 ₽</li>
                <li>Стоимость: 300 ₽ (Москва), 500 ₽ (область)</li>
                <li>Доставка осуществляется с 10:00 до 22:00</li>
              </ul>
            </div>
          </div>

          <div class="method-card">
            <div class="method-icon">🏪</div>
            <div class="method-info">
              <h3>Пункты выдачи заказов</h3>
              <p>Более 500 пунктов выдачи по всей России</p>
              <ul>
                <li>Бесплатно при заказе от 3000 ₽</li>
                <li>Стоимость: 200 ₽</li>
                <li>Срок хранения заказа: 7 дней</li>
              </ul>
            </div>
          </div>

          <div class="method-card">
            <div class="method-icon">📮</div>
            <div class="method-info">
              <h3>Почта России</h3>
              <p>Доставка в отдаленные регионы</p>
              <ul>
                <li>Стоимость рассчитывается индивидуально</li>
                <li>Срок доставки: 5-14 дней</li>
                <li>Отслеживание по трек-номеру</li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <!-- Оплата -->
      <section class="content-section">
        <h2>💳 Способы оплаты</h2>
        
        <div class="payment-methods">
          <div class="payment-card">
            <span class="payment-icon">💳</span>
            <div>
              <h3>Банковские карты</h3>
              <p>Visa, Mastercard, МИР</p>
            </div>
          </div>
          <div class="payment-card">
            <span class="payment-icon">📱</span>
            <div>
              <h3>Система быстрых платежей (СБП)</h3>
              <p>Оплата по QR-коду</p>
            </div>
          </div>
          <div class="payment-card">
            <span class="payment-icon">🏦</span>
            <div>
              <h3>Наличные</h3>
              <p>При получении курьеру или в пункте выдачи</p>
            </div>
          </div>
          <div class="payment-card">
            <span class="payment-icon">📱</span>
            <div>
              <h3>Apple Pay / Google Pay</h3>
              <p>Быстрая оплата через смартфон</p>
            </div>
          </div>
          <div class="payment-card">
            <span class="payment-icon">💰</span>
            <div>
              <h3>Оплата частями</h3>
              <p>Долями или в рассрочку от банков-партнеров</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Важная информация -->
      <section class="content-section info-box">
        <h3>ℹ️ Важная информация</h3>
        <p>После оформления заказа вы получите подтверждение на email. За час до доставки курьер свяжется с вами для подтверждения. При получении проверьте комплектацию и внешний вид товара. Оплата наличными возможна только при получении в Москве</p>
      </section>

      <div class="contact-support">
        <p>Остались вопросы? Напишите нам на <a href="mailto:delivery@glassshop.ru">info@glassshop.ru</a></p>
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

.delivery-methods, .payment-methods {
  display: grid;
  gap: 1.5rem;
}

.method-card {
  display: flex;
  gap: 1.5rem;
  padding: 1.5rem;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 20px;
}

.method-icon {
  font-size: 2.5rem;
}

.method-info h3 {
  margin-bottom: 0.5rem;
}

.method-info p {
  color: rgba(255, 255, 255, 0.6);
  margin-bottom: 0.5rem;
}

.method-info ul {
  margin: 0;
  padding-left: 1.2rem;
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.85rem;
}

.method-info li {
  margin-bottom: 0.25rem;
}

.payment-methods {
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
}

.payment-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 16px;
}

.payment-icon {
  font-size: 1.8rem;
}

.payment-card h3 {
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.payment-card p {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.5);
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
  
  .method-card {
    flex-direction: column;
    text-align: center;
  }
  
  .method-icon {
    font-size: 2rem;
  }
  
  .payment-methods {
    grid-template-columns: 1fr;
  }
}
</style>