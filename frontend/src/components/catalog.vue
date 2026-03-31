<template>
  <div class="catalog-page">
    <div class="container">
      <h1>Каталог товаров</h1>
      <p>Страница каталога</p>
      <router-link to="/">Вернуться на главную</router-link>
    </div>
  </div>
</template>

<script setup>
console.log('Catalog page loaded')
</script>

<style scoped>
.catalog-page {
  padding: 2rem;
  color: white;
  text-align: center;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

a {
  color: #c084fc;
  text-decoration: none;
  display: inline-block;
  margin-top: 1rem;
}
</style>