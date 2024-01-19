// resources/js/router.js

import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/Login.vue';

const routes = [
  // ... other routes
  {
    path: '/dashboard',
    name: 'dahsboard',
    // component: Login,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
