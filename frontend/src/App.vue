<template>
  <div class="app">
    <router-view />
  </div>
</template>

<script setup>
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  background: #0a0a0a;
  color: white;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  overflow-x: hidden;
}

.app {
  min-height: 100vh;
}
</style>