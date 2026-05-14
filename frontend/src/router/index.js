import { createRouter, createWebHistory } from 'vue-router'
import Mainpage from '../components/mainpage.vue';
import Catalog from '../components/catalog.vue';
import Login from '../components/auth/Login.vue';
import Profile from '../components/profile.vue';
import Admin from '../components/admin.vue';
import Manageproduct from '../components/admin/manage-product.vue';
import ManageCategory from '../components/admin/manage-category.vue';
import ProductView from '../components/product-view.vue';
import notfound from '../components/notfound.vue';
import about from '../components/about.vue';
import cart from '../components/cart.vue';
import favorites from '../components/favorites.vue';
import delivery from '../components/static/delivery.vue';
import returns from '../components/static/returns.vue';
import warranty from '../components/static/warranty.vue';
import privacy from '../components/static/privacy.vue';
import terms from '../components/static/terms.vue';
import manageUsers from '../components/admin/manage-users.vue';
import checkout from '../components/checkout.vue';
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
      component: notfound,
      meta: { guestOnly: false }
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
    },
    {
      path: '/manage-product',
      name: 'manage-product',
      component: Manageproduct,
      meta: {guestOnly: false}
    },
    {
      path: '/manage-users',
      name: 'manage-users',
      component: manageUsers,
      meta: {guestOnly:false}
    },
    {
      path: '/manage-category',
      name: 'manage-category',
      component: ManageCategory,
      meta: {guestOnly: false}
    },
    {
      path: '/product/:slug',
      name: 'product-view',
      component: ProductView,
      meta: {guestOnly: false},
      props: true
    },
    {
      path: '/about',
      name: 'about',
      component: about,
      meta: {guestOnly: false}
    },
    {
      path: '/cart',
      name: 'cart',
      component: cart,
      meta: {guestOnly: false}
    },
    {
      path: '/favorites',
      name: 'favorites',
      component: favorites,
      meta: {guestOnly: false}
    },
    {
      path: '/delivery',
      name: 'delivery',
      component: delivery,
      meta: {guestOnly: false}
    },
    {
      path: '/returns',
      name: 'returns',
      component: returns,
      meta: {guestOnly: false}
    },
    {
      path: '/warranty',
      name: 'warranty',
      component: warranty,
      meta: {guestOnly: false}
    },
    {
      path: '/privacy',
      name: 'privacy',
      component: privacy,
      meta: {guestOnly: false}
    },
    {
      path: '/terms',
      name: 'terms',
      component: terms,
      meta: {guestOnly: false}
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: checkout,
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