import { createRouter, createWebHistory } from 'vue-router'
import Mainpage from '../components/mainpage.vue';
import Catalog from '../components/catalog.vue';
import Login from '../components/auth/Login.vue';
import Profile from '../components/profile.vue';
import Admin from '../components/admin.vue';
import { useAuthStore } from '../stores/authStore';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: Mainpage
    },
    {
      path: '/catalog',
      name: 'catalog',
      component: Catalog
    },
    {
      path: '/login',
      name: 'login',
      component: Login,
      meta: { guestOnly: true }
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'notfound',
      redirect: '/'
    },
    {
      path: '/profile',
      name: 'profile',
      component: Profile,
      meta: { guestOnly: false }
    },
    {
      path: '/admin',
      name: 'admin',
      component: Admin,
      meta: {guestOnly: false}
    }
  ]
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // 1. Всегда проверяем авторизацию ПЕРЕД любым переходом, 
  // если Pinia еще не знает статус пользователя (например, после F5)
  if (!authStore.isLoaded) 
  {
    await authStore.checkAuth();
  }

  const isAuthenticated = !!authStore.user;

  // 2. Если страница ТОЛЬКО для авторизованных, а юзер — аноним
  if (to.meta.requiresAuth && !isAuthenticated) {
    return next('/login');
  }

  // 3. Если страница ТОЛЬКО для гостей (логин/рега), а юзер УЖЕ вошел
  if (to.meta.guestOnly && isAuthenticated) {
    return next('/profile'); // Отфутболиваем его в профиль
  }

  // 4. В остальных случаях — пропускаем
  next();
});

export default router