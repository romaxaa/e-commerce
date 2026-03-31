import { createRouter, createWebHistory } from 'vue-router'
import Mainpage from '../components/mainpage.vue';
import Catalog from '../components/catalog.vue';
import Login from '../components/auth/Login.vue'

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
      component: Login
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'notfound',
      redirect: '/'
    }
  ]
});

export default router