import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import ProductsView from "@/views/ProductsView.vue";
import CategoriesView from "@/views/CategoriesView.vue";
import UsersView from "@/views/UsersView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: DashboardView
    },

    {
      path: '/products',
      name: 'products',
      component: ProductsView
    },

    {
      path: '/categories',
      name: 'categories',
      component: CategoriesView
    },

    {
      path: '/users',
      name: 'users',
      component: UsersView
    },
  ]
})

export default router
