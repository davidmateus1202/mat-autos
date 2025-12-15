import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/useAuth';

// Lazy load components
const Login = () => import('../pages/Auth/Login.vue');
const Dashboard = () => import('../pages/Dashboards/Index.vue');
const CarsIndex = () => import('../pages/Cars/Index.vue');
const CarsShow = () => import('../pages/Cars/Show.vue');
const FinanceIndex = () => import('../pages/Finance/Index.vue');
const Profile = () => import('../pages/Profile/Index.vue');
const NotFound = () => import('../pages/NotFound.vue');

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true, layout: 'none' }
    },
    {
        path: '/',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/cars',
        name: 'cars.index',
        component: CarsIndex,
        meta: { requiresAuth: true }
    },
    {
        path: '/cars/:id',
        name: 'cars.show',
        component: CarsShow,
        meta: { requiresAuth: true }
    },
    {
        path: '/finance',
        name: 'finance.index',
        component: FinanceIndex,
        meta: { requiresAuth: true }
    },
    {
        path: '/perfil',
        name: 'profile',
        component: Profile,
        meta: { requiresAuth: true }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: NotFound
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();
    
    if (!authStore.user && to.meta.requiresAuth) {
        await authStore.getUser();
    }

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'login' });
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next({ name: 'dashboard' });
    } else {
        next();
    }
});

export default router;
