<template>
  <div class="admin-products">
    <div class="page-actions">
      <button class="create-btn glass-button" @click="openCreateModal">
        <span>+</span> Добавить товар
      </button>
    </div>

    <div class="products-table glass-panel">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Изображение</th>
            <th>Название</th>
            <th>Категория</th>
            <th>Цена</th>
            <th>Остаток</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>#{{ product.id }}</td>
            <td><img :src="product.img" class="product-thumb"></td>
            <td>{{ product.name }}</td>
            <td>{{ product.category_name }}</td>
            <td>{{ formatPrice(product.price) }} ₽</td>
            <td>{{ product.stock }} шт.</td>
            <td class="actions">
              <button class="action-btn edit" @click="editProduct(product)">✏️</button>
              <button class="action-btn delete" @click="deleteProduct(product.id)">🗑️</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Модальное окно товара -->
    <transition name="modal">
      <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
        <div class="modal-content glass-panel modal-large">
          <h3>{{ isEditing ? 'Редактировать товар' : 'Новый товар' }}</h3>
          <form @submit.prevent="saveProduct" class="product-form">
            <div class="form-row">
              <div class="form-group">
                <label>Название товара</label>
                <input type="text" v-model="productForm.name" required class="glass-input">
              </div>
              <div class="form-group">
                <label>Категория</label>
                <select v-model="productForm.category" class="glass-input">
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Цена (₽)</label>
                <input type="number" v-model="productForm.price" required class="glass-input">
              </div>
              <div class="form-group">
                <label>Старая цена (₽)</label>
                <input type="number" v-model="productForm.oldPrice" class="glass-input">
              </div>
              <div class="form-group">
                <label>Остаток</label>
                <input type="number" v-model="productForm.stock" required class="glass-input">
              </div>
            </div>

            <div class="form-group">
              <label>Описание</label>
              <textarea v-model="productForm.description" rows="3" class="glass-input"></textarea>
            </div>

            <div class="form-group">
              <label>URL изображения</label>
              <input type="url" v-model="productForm.image" class="glass-input">
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="productForm.isNew">
                  <span>Новинка</span>
                </label>
              </div>
              <div class="form-group">
                <label class="checkbox-label">
                  <input type="checkbox" v-model="productForm.isPopular">
                  <span>Популярный</span>
                </label>
              </div>
            </div>

            <div class="modal-actions">
              <button type="button" class="cancel-btn" @click="showModal = false">Отмена</button>
              <button type="submit" class="save-btn">Сохранить</button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../../stores/authStore';

const authStore = useAuthStore();

onMounted(async () => 
{
    // Если данных в сторе еще нет, загружаем их один раз
    if (!authStore.user) 
    {
      await authStore.checkAuth();
    }

    // Если после проверки пользователя всё еще нет — на выход
    if (!authStore.user || !authStore.user.group == 99)
    {
      router.push("/");
    }

    loadProducts();
    loadCategories();
});

const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

//const categories = ref(['Смартфоны', 'Ноутбуки', 'Наушники', 'Часы', 'Камеры', 'Аксессуары'])

const productForm = ref
({
  name: '',
  category_id: null,
  price: 0,
  oldPrice: null,
  stock: 0,
  description: '',
  image: '',
  isNew: false,
  isPopular: false
})

const products = computed(() => authStore.products);
const categories = computed(() => authStore.categories)
//const productsLoading = computed(() => authStore.productsLoading);
//const productsError = computed(() => authStore.productsError);

const loadCategories = async () => {
  const result = await authStore.fetchCategories();
  
  if (result.success) 
  {
    console.log('категории загружены');
  }
  else 
  {
    console.error('Ошибка загрузки:', result.error);
  }
};

// Метод для загрузки продуктов
const loadProducts = async () => {
  const result = await authStore.fetchProducts();
  
  if (result.success) 
  {
    console.log('Товары загружены');
  }
  else 
  {
    console.error('Ошибка загрузки:', result.error);
  }
};

const formatPrice = (price) => {
  return price.toLocaleString('ru-RU')
}

const openCreateModal = () => {
  isEditing.value = false
  productForm.value = { name: '', category_id: null, price: null, oldPrice: null, stock: null, description: '', image: '', isNew: false, isPopular: false }
  showModal.value = true
}

const editProduct = (product) => {
  isEditing.value = true
  editingId.value = product.id
  productForm.value = { ...product }
  showModal.value = true
}

const saveProduct = async () => {
  const responce = await authStore.createProduct(productForm.value.name, productForm.value.category_id, productForm.value.price, productForm.value.oldPrice, productForm.value.stock, productForm.value.description, productForm.value.image, productForm.value.isNew, productForm.value.isPopular);
  if (result.success)  
  {
    showModal.value = false;
    console.log(data);
    await loadProducts();
  }
  else
  {
    console.error(result.error);
  }
}

const deleteProduct = async (id) => {
  if (confirm('Вы уверены, что хотите удалить этот товар?')) 
  {
    const responce = await authStore.deleteProduct(id);
    if (result.success)  
    {
      showModal.value = false;
      await loadProducts();
    }
    else
    {
      console.error(result.error);
    }
  }
}

</script>

<style scoped>
.admin-products {
  padding: 1rem 0;
}

.page-actions {
  margin-bottom: 1.5rem;
  display: flex;
  justify-content: flex-end;
}

.create-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 30px;
  color: white;
  cursor: pointer;
}

.products-table {
  overflow-x: auto;
  padding: 1rem;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

th {
  color: rgba(255, 255, 255, 0.6);
  font-weight: 500;
}

.product-thumb {
  width: 50px;
  height: 50px;
  border-radius: 8px;
  object-fit: cover;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  background: rgba(255, 255, 255, 0.05);
  border: none;
  border-radius: 8px;
  padding: 0.4rem 0.6rem;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn.edit:hover {
  background: rgba(59, 130, 246, 0.3);
}

.action-btn.delete:hover {
  background: rgba(239, 68, 68, 0.3);
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
  max-width: 700px;
  width: 90%;
  padding: 2rem;
  background: rgba(20, 20, 30, 0.95);
  border-radius: 32px;
}

.modal-large {
  max-width: 800px;
}

.modal-content h3 {
  margin-bottom: 1.5rem;
}

.product-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.7);
}

.checkbox-label {
  flex-direction: row;
  align-items: center;
  cursor: pointer;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1rem;
}

.cancel-btn, .save-btn {
  padding: 0.5rem 1.5rem;
  border-radius: 30px;
  cursor: pointer;
}

.cancel-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: white;
}

.save-btn {
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  color: white;
}

.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>