<!-- Privacy.vue - Политика конфиденциальности -->
<template>
  <div class="page-container">
    <div class="page-header">
      <h1>Политика конфиденциальности</h1>
      <p>Последнее обновление: 1 января 2024 г.</p>
    </div>

    <div class="page-content glass-panel">
      <section class="content-section">
        <h2>1. Общие положения</h2>
        <p>Настоящая Политика конфиденциальности описывает, как GlassShop собирает, использует и защищает информацию, которую вы предоставляете при использовании нашего сайта.</p>
      </section>

      <section class="content-section">
        <h2>2. Какие данные мы собираем</h2>
        <ul>
          <li>Имя, фамилия и контактная информация (email, телефон)</li>
          <li>Адрес доставки</li>
          <li>История заказов</li>
          <li>Данные об использовании сайта (файлы cookie, IP-адрес)</li>
          <li>Отзывы и комментарии</li>
        </ul>
      </section>

      <section class="content-section">
        <h2>3. Как мы используем ваши данные</h2>
        <ul>
          <li>Для обработки и доставки ваших заказов</li>
          <li>Для связи с вами по вопросам заказов</li>
          <li>Для улучшения работы сайта и сервиса</li>
          <li>Для отправки информационных рассылок (с вашего согласия)</li>
          <li>Для персонализации вашего опыта на сайте</li>
        </ul>
      </section>

      <section class="content-section">
        <h2>4. Защита данных</h2>
        <p>Мы принимаем все необходимые меры для защиты ваших персональных данных от несанкционированного доступа, изменения, раскрытия или уничтожения. Передача данных осуществляется по защищенным каналам SSL.</p>
      </section>

      <section class="content-section">
        <h2>5. Передача данных третьим лицам</h2>
        <p>Мы не продаем, не обмениваем и не передаем ваши персональные данные третьим лицам, за исключением:</p>
        <ul>
          <li>Служб доставки (для выполнения заказов)</li>
          <li>Платежных систем (для обработки платежей)</li>
          <li>По требованию закона</li>
        </ul>
      </section>

      <section class="content-section">
        <h2>6. Файлы cookie</h2>
        <p>Мы используем файлы cookie для улучшения работы сайта, запоминания ваших предпочтений и анализа трафика. Вы можете отключить файлы cookie в настройках браузера.</p>
      </section>

      <section class="content-section">
        <h2>7. Ваши права</h2>
        <ul>
          <li>Право на доступ к вашим данным</li>
          <li>Право на исправление неточных данных</li>
          <li>Право на удаление данных (забытие)</li>
          <li>Право на ограничение обработки</li>
          <li>Право на переносимость данных</li>
        </ul>
      </section>

      <section class="content-section">
        <h2>8. Контакты</h2>
        <p>По вопросам обработки персональных данных вы можете связаться с нами:</p>
        <ul>
          <li>Email: <a href="mailto:info@glassshop.ru">info@glassshop.ru</a></li>
          <li>Телефон: 8 (800) 123-45-67</li>
          <li>Адрес: г. Москва, ул. Тверская, 15</li>
        </ul>
      </section>

      <div class="last-updated">
        <p>© 2026 GlassShop. Все права защищены.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.page-container {
  max-width: 900px;
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
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.85rem;
}

.page-content {
  padding: 2rem;
  border-radius: 32px;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
}

.content-section {
  margin-bottom: 1.5rem;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.content-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.content-section h2 {
  font-size: 1.2rem;
  margin-bottom: 1rem;
  color: #c084fc;
}

.content-section p {
  color: rgba(255, 255, 255, 0.7);
  line-height: 1.6;
  margin-bottom: 0.5rem;
}

.content-section ul {
  margin: 0;
  padding-left: 1.2rem;
  color: rgba(255, 255, 255, 0.7);
}

.content-section li {
  margin-bottom: 0.5rem;
  line-height: 1.5;
}

.content-section a {
  color: #c084fc;
  text-decoration: none;
}

.last-updated {
  margin-top: 2rem;
  padding-top: 1rem;
  text-align: center;
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.4);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

@media (max-width: 768px) {
  .page-container {
    padding: 1rem;
  }
  
  .page-content {
    padding: 1.5rem;
  }
}
</style>