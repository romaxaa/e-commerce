<!-- AdminUsers.vue - Управление пользователями (исправленная версия) -->
<template>
  <div class="admin-users">
    <div class="admin-container">
      <!-- Заголовок -->
      <div class="page-header">
        <h2>👥 Управление пользователями</h2>
        <button class="export-btn" @click="exportUsers">
          📥 Экспорт CSV
        </button>
      </div>

      <!-- Статистика -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">👥</div>
          <div class="stat-info">
            <div class="stat-value">{{ totalUsers }}</div>
            <div class="stat-label">Всего пользователей</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🆕</div>
          <div class="stat-info">
            <div class="stat-value">{{ newUsersThisMonth }}</div>
            <div class="stat-label">Новых за месяц</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">✅</div>
          <div class="stat-info">
            <div class="stat-value">{{ activeUsers }}</div>
            <div class="stat-label">Активных</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🔒</div>
          <div class="stat-info">
            <div class="stat-value">{{ blockedUsers }}</div>
            <div class="stat-label">Заблокированных</div>
          </div>
        </div>
      </div>

      <!-- Поиск и фильтры -->
      <div class="filters-bar">
        <div class="search-wrapper">
          <span class="search-icon">🔍</span>
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Поиск по имени, email или логину..." 
            class="search-input"
          >
        </div>
        <div class="filter-group">
          <select v-model="roleFilter" class="filter-select">
            <option value="all">Все роли</option>
            <option value="user">Пользователи</option>
            <option value="manager">Менеджеры</option>
            <option value="admin">Администраторы</option>
          </select>
        </div>
      </div>

      <!-- Таблица пользователей -->
      <div class="table-wrapper">
        <table class="users-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Пользователь</th>
              <th>Контакты</th>
              <th>Роль</th>
              <th>Последний вход</th>
              <th>Действия</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in paginatedUsers" :key="user.id">
              <td class="col-id">#{{ user.id }}</td>
              <td class="col-user">
                <div class="user-info">
                  <img :src="user.avatar" :alt="user.name" class="user-avatar">
                  <div>
                    <div class="user-name">{{ user.name }}</div>
                    <div class="user-login">@{{ user.username }}</div>
                  </div>
                </div>
              </td>
              <td class="col-contacts">
                <div class="user-email">{{ user.email }}</div>
                <div class="user-phone">{{ user.phone || '—' }}</div>
              </td>
              <td class="col-role">
                <span class="role-badge" :class="user.role">
                  {{ user.group === '99' ? 'Администратор' : user.group === 'Администатор' ? 'Менеджер' : 'Пользователь' }}
                </span>
              </td>
              <td class="col-date">{{ formatDate(user.lastjoin) }}</td>
              <td class="col-actions">
                <div class="action-buttons">
                  <button class="action-btn edit" @click="editUser(user)" title="Редактировать">✏️</button>
                  <button class="action-btn block" @click="toggleBlockUser(user)" :title="user.status === 'blocked' ? 'Разблокировать' : 'Заблокировать'">
                    {{ user.status === 'blocked' ? '🔓' : '🔒' }}
                  </button>
                  <button class="action-btn delete" @click="deleteUser(user)" title="Удалить">🗑️</button>
                </div>
              </td>
            </tr>
            <tr v-if="paginatedUsers.length === 0">
              <td colspan="7" class="empty-row">Пользователи не найдены</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Пагинация -->
      <div class="pagination" v-if="totalPages > 1">
        <button class="page-btn prev" :disabled="currentPage === 1" @click="currentPage--">
          ← Назад
        </button>
        <div class="page-numbers">
          <button 
            v-for="page in visiblePages" 
            :key="page"
            class="page-num"
            :class="{ active: currentPage === page }"
            @click="currentPage = page"
          >
            {{ page }}
          </button>
        </div>
        <button class="page-btn next" :disabled="currentPage === totalPages" @click="currentPage++">
          Вперед →
        </button>
      </div>
    </div>

    <!-- Модальное окно редактирования -->
    <transition name="modal">
      <div v-if="showEditModal" class="modal-overlay" @click.self="showEditModal = false">
        <div class="modal-content">
          <div class="modal-header">
            <h3>Редактирование пользователя</h3>
            <button class="modal-close" @click="showEditModal = false">✕</button>
          </div>
          <form @submit.prevent="saveUser" class="edit-form">
            <div class="form-row">
              <div class="form-group">
                <label>Имя</label>
                <input type="text" v-model="editingUser.name" class="form-input">
              </div>
              <div class="form-group">
                <label>Логин</label>
                <input type="text" v-model="editingUser.username" class="form-input">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Email</label>
                <input type="email" v-model="editingUser.email" class="form-input">
              </div>
              <div class="form-group">
                <label>Телефон</label>
                <input type="tel" v-model="editingUser.phone" class="form-input">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Роль</label>
                <select v-model="editingUser.group" class="form-select">
                  <option value="1">Пользователь</option>
                  <option value="99">Администратор</option>
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn-cancel" @click="showEditModal = false">Отмена</button>
              <button type="submit" class="btn-save">Сохранить изменения</button>
            </div>
          </form>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore';
import { useAlertStore } from '../../stores/alertStore'; 

const router = useRouter();
const authStore = useAuthStore();
const alerts = useAlertStore();
const users = ref([]);


// Данные
const searchQuery = ref('')
const roleFilter = ref('all')
const statusFilter = ref('all')
const currentPage = ref(1)
const usersPerPage = 10
const showEditModal = ref(false)
const editingUser = ref({})

onMounted(async () => {

    // Если данных в сторе еще нет, загружаем их один раз
    if (!authStore.user) 
    {
      await authStore.checkAuth();
    }

    // Если после проверки пользователя всё еще нет — на выход
    if (!authStore.user || authStore.user.group != 99)
    {
      router.push("/");
    }
    
    fetchUsers();
});

const fetchUsers = async () => {
    const result = await authStore.fetchUsers();

    if(result.success)
    {
        users.value = result.data;
    }
    else
    {
        console.log(result.error);
    }
};

// Вычисления
const usersList = computed(() => users.value || [])

const totalUsers = computed(() => usersList.value.length)
const newUsersThisMonth = computed(() => usersList.value.filter(u => u.createdAt >= '2024-03-01').length)
const activeUsers = computed(() => usersList.value.filter(u => u.status === 'active').length)
const blockedUsers = computed(() => usersList.value.filter(u => u.status === 'blocked').length)

const filteredUsers = computed(() => {
  let filtered = [...usersList.value]
  
  if (searchQuery.value && searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(u => 
      (u.name && u.name.toLowerCase().includes(query)) ||
      (u.email && u.email.toLowerCase().includes(query)) ||
      (u.login && u.login.toLowerCase().includes(query))
    )
  }
  
  if (roleFilter.value !== 'all') {
    filtered = filtered.filter(u => u.role === roleFilter.value)
  }
  
  if (statusFilter.value !== 'all') {
    filtered = filtered.filter(u => u.status === statusFilter.value)
  }
  
  return filtered
})

const totalPages = computed(() => Math.ceil(filteredUsers.value.length / usersPerPage))

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * usersPerPage
  return filteredUsers.value.slice(start, start + usersPerPage)
})

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  return pages
})

// Методы
const formatDate = (date) => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('ru-RU')
}

const editUser = (user) => {
  editingUser.value = { ...user }
  showEditModal.value = true
}

const saveUser = () => {
  const index = users.value.findIndex(u => u.id === editingUser.value.id)
  if (index !== -1) {
    users.value[index] = { ...editingUser.value }
  }
  showEditModal.value = false
}

const toggleBlockUser = (user) => {
  if (confirm(`Вы уверены, что хотите ${user.status === 'blocked' ? 'разблокировать' : 'заблокировать'} пользователя ${user.name}?`)) {
    user.status = user.status === 'blocked' ? 'active' : 'blocked'
  }
}

const deleteUser = async (user) => {
  if (confirm(`Вы уверены, что хотите удалить пользователя? Это действие нельзя отменить.`))
  {
    const result = await authStore.deleteUser(user.id);

    if(result.success)
    {
      alerts.show('Пользователь успешно удален', 'success');
      await fetchUsers();
    }
    else
    {
      console.log(result.error);
    }
  }
}

const exportUsers = () => {
  alert('Экспорт пользователей в CSV')
}
</script>

<style scoped>
.admin-users {
  min-height: 100vh;
  background: linear-gradient(135deg, #0a0a0f, #0f0f1a);
  padding: 2rem;
}

.admin-container {
  max-width: 1400px;
  margin: 0 auto;
}

/* Заголовок */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.page-header h2 {
  font-size: 1.8rem;
  font-weight: 600;
  background: linear-gradient(135deg, #fff, #c084fc);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.export-btn {
  padding: 0.6rem 1.2rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  cursor: pointer;
  transition: all 0.2s;
}

.export-btn:hover {
  background: rgba(139, 92, 246, 0.2);
  border-color: #c084fc;
}

/* Статистика */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.2rem;
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 20px;
}

.stat-icon {
  font-size: 2.2rem;
}

.stat-info {
  flex: 1;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: #c084fc;
  line-height: 1;
}

.stat-label {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.5);
  margin-top: 0.25rem;
}

/* Поиск и фильтры */
.filters-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.search-wrapper {
  position: relative;
  flex: 1;
  max-width: 350px;
}

.search-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  font-size: 0.9rem;
  opacity: 0.5;
}

.search-input {
  width: 100%;
  padding: 0.7rem 1rem 0.7rem 2.5rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  font-size: 0.9rem;
  outline: none;
  transition: all 0.2s;
}

.search-input:focus {
  border-color: #c084fc;
}

.filter-group {
  display: flex;
  gap: 0.8rem;
}

.filter-select {
  padding: 0.7rem 1.5rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  font-size: 0.9rem;
  cursor: pointer;
  outline: none;
}

.filter-select:focus {
  border-color: #c084fc;
}

/* Таблица */
.table-wrapper {
  background: rgba(20, 20, 30, 0.6);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 20px;
  overflow-x: auto;
  margin-bottom: 1.5rem;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
}

.users-table th,
.users-table td {
  padding: 1rem 1.2rem;
  text-align: left;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.users-table th {
  color: rgba(255, 255, 255, 0.6);
  font-weight: 500;
  font-size: 0.85rem;
  background: rgba(0, 0, 0, 0.2);
}

.users-table tbody tr:hover {
  background: rgba(255, 255, 255, 0.02);
}

.empty-row {
  text-align: center;
  color: rgba(255, 255, 255, 0.5);
  padding: 3rem !important;
}

/* Колонки */
.col-id {
  width: 60px;
  font-weight: 500;
}

.col-user {
  width: 220px;
}

.col-contacts {
  width: 220px;
}

.col-role {
  width: 130px;
}

.col-status {
  width: 110px;
}

.col-date {
  width: 110px;
}

.col-actions {
  width: 100px;
}

/* Информация о пользователе */
.user-info {
  display: flex;
  align-items: center;
  gap: 0.8rem;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.user-name {
  font-weight: 500;
  margin-bottom: 0.2rem;
}

.user-login {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.4);
}

.user-email {
  font-size: 0.85rem;
  margin-bottom: 0.2rem;
}

.user-phone {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.4);
}

/* Бейджи */
.role-badge {
  display: inline-block;
  padding: 0.25rem 0.8rem;
  border-radius: 30px;
  font-size: 0.75rem;
  font-weight: 500;
}

.role-badge.admin {
  background: rgba(139, 92, 246, 0.2);
  color: #c084fc;
}

.role-badge.manager {
  background: rgba(59, 130, 246, 0.2);
  color: #60a5fa;
}

.role-badge.user {
  background: rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.7);
}

.status-badge {
  display: inline-block;
  padding: 0.25rem 0.8rem;
  border-radius: 30px;
  font-size: 0.75rem;
  font-weight: 500;
}

.status-badge.active {
  background: rgba(34, 197, 94, 0.2);
  color: #4ade80;
}

.status-badge.blocked {
  background: rgba(239, 68, 68, 0.2);
  color: #f87171;
}

.status-badge.pending {
  background: rgba(245, 158, 11, 0.2);
  color: #fbbf24;
}

/* Кнопки действий */
.action-buttons {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  width: 32px;
  height: 32px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 1rem;
}

.action-btn.edit:hover {
  background: rgba(59, 130, 246, 0.3);
  border-color: #60a5fa;
}

.action-btn.block:hover {
  background: rgba(245, 158, 11, 0.3);
  border-color: #fbbf24;
}

.action-btn.delete:hover {
  background: rgba(239, 68, 68, 0.3);
  border-color: #f87171;
}

/* Пагинация */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
}

.page-btn {
  padding: 0.5rem 1.2rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  cursor: pointer;
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
  background: rgba(139, 92, 246, 0.2);
  border-color: #c084fc;
}

.page-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.page-numbers {
  display: flex;
  gap: 0.5rem;
}

.page-num {
  width: 36px;
  height: 36px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  color: white;
  cursor: pointer;
  transition: all 0.2s;
}

.page-num:hover {
  background: rgba(139, 92, 246, 0.2);
  border-color: #c084fc;
}

.page-num.active {
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border-color: #c084fc;
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
  width: 90%;
  max-width: 550px;
  background: rgba(20, 20, 30, 0.98);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 24px;
  overflow: hidden;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-header h3 {
  font-size: 1.2rem;
  font-weight: 600;
}

.modal-close {
  width: 32px;
  height: 32px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  color: white;
  cursor: pointer;
  font-size: 1.2rem;
}

.edit-form {
  padding: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-size: 0.8rem;
  color: rgba(255, 255, 255, 0.7);
}

.form-input, .form-select {
  padding: 0.7rem 1rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  color: white;
  font-size: 0.9rem;
  outline: none;
}

.form-input:focus, .form-select:focus {
  border-color: #c084fc;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.btn-cancel {
  padding: 0.6rem 1.2rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 30px;
  color: white;
  cursor: pointer;
}

.btn-save {
  padding: 0.6rem 1.5rem;
  background: linear-gradient(135deg, #9333ea, #3b82f6);
  border: none;
  border-radius: 30px;
  color: white;
  cursor: pointer;
}

.btn-save:hover {
  opacity: 0.9;
}

/* Анимация модального окна */
.modal-enter-active, .modal-leave-active {
  transition: opacity 0.3s;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

/* Адаптивность */
@media (max-width: 1200px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 1024px) {
  .admin-users {
    padding: 1.5rem;
  }
  
  .col-user {
    min-width: 180px;
  }
  
  .col-contacts {
    min-width: 180px;
  }
}

@media (max-width: 768px) {
  .admin-users {
    padding: 1rem;
  }
  
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }
  
  .filters-bar {
    flex-direction: column;
    align-items: stretch;
  }
  
  .search-wrapper {
    max-width: 100%;
  }
  
  .filter-group {
    flex-wrap: wrap;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .modal-content {
    width: 95%;
  }
}
</style>