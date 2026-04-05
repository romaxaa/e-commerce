<template>
  <div class="admin-categories">
    <div class="page-actions">
      <button class="create-btn glass-button" @click="openCreateModal">
        <span>+</span> Создать категорию
      </button>
    </div>

    <div class="categories-table glass-panel">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Иконка</th>
            <th>Название</th>
            <th>Товаров</th>
            <th>Статус</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="category in categories" :key="category.id">
            <td>#{{ category.id }}</td>
            <td><span class="category-icon">{{ category.icon }}</span></td>
            <td>{{ category.name }}</td>
            <td>{{ category.productCount }}</td>
            <td>
              <span class="status-badge" :class="category.active ? 'active' : 'inactive'">
                {{ category.active ? 'Активна' : 'Скрыта' }}
              </span>
            </td>
            <td class="actions">
              <button class="action-btn edit" @click="editCategory(category)">✏️</button>
              <button class="action-btn delete" @click="deleteCategory(category.id)">🗑️</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Модальное окно создания/редактирования категории -->
    <transition name="modal">
      <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
        <div class="modal-content glass-panel">
          <h3>{{ isEditing ? 'Редактировать категорию' : 'Новая категория' }}</h3>
          <form @submit.prevent="saveCategory" class="category-form">
            <div class="form-group">
              <label>Название категории</label>
              <input type="text" v-model="categoryForm.name" required class="glass-input">
            </div>
            <div class="form-group">
              <label>Иконка (emoji)</label>
              <input type="text" v-model="categoryForm.icon" placeholder="📱" maxlength="2" class="glass-input">
            </div>
            <div class="form-group">
              <label>Описание</label>
              <textarea v-model="categoryForm.description" rows="3" class="glass-input"></textarea>
            </div>
            <div class="form-group">
              <label class="checkbox-label">
                <input type="checkbox" v-model="categoryForm.active">
                <span>Активна</span>
              </label>
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
import { ref, onMounted } from 'vue'
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
});

const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const categoryForm = ref({
  name: '',
  icon: '📁',
  description: '',
  active: true
})

const categories = ref([
  { id: 1, name: 'Смартфоны', icon: '📱', productCount: 156, active: true },
  { id: 2, name: 'Ноутбуки', icon: '💻', productCount: 89, active: true },
  { id: 3, name: 'Наушники', icon: '🎧', productCount: 123, active: true },
  { id: 4, name: 'Часы', icon: '⌚', productCount: 67, active: true },
  { id: 5, name: 'Камеры', icon: '📷', productCount: 45, active: false }
])

const openCreateModal = () => {
  isEditing.value = false
  categoryForm.value = { name: '', icon: '📁', description: '', active: true }
  showModal.value = true
}

const editCategory = (category) => {
  isEditing.value = true
  editingId.value = category.id
  categoryForm.value = { ...category }
  showModal.value = true
}

const saveCategory = () => {
  if (isEditing.value) {
    const index = categories.value.findIndex(c => c.id === editingId.value)
    if (index !== -1) {
      categories.value[index] = { ...categoryForm.value, id: editingId.value, productCount: categories.value[index].productCount }
    }
  } else {
    const newCategory = {
      ...categoryForm.value,
      id: Date.now(),
      productCount: 0
    }
    categories.value.push(newCategory)
  }
  showModal.value = false
}

const deleteCategory = (id) => {
  if (confirm('Вы уверены, что хотите удалить эту категорию?')) {
    categories.value = categories.value.filter(c => c.id !== id)
  }
}
</script>

<style scoped>
.admin-categories {
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

.categories-table {
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

.category-icon {
  font-size: 1.5rem;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 30px;
  font-size: 0.75rem;
}

.status-badge.active {
  background: rgba(34, 197, 94, 0.2);
  color: #4ade80;
}

.status-badge.inactive {
  background: rgba(107, 114, 128, 0.2);
  color: #9ca3af;
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
  max-width: 500px;
  width: 90%;
  padding: 2rem;
  background: rgba(20, 20, 30, 0.95);
  border-radius: 32px;
}

.modal-content h3 {
  margin-bottom: 1.5rem;
}

.category-form {
  display: flex;
  flex-direction: column;
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
</style>