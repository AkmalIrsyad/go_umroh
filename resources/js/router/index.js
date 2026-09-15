import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

// Customer routes
const customerRoutes = [
    { path: '/', component: () => import('../pages/customer/HomePage.vue'), name: 'home' },
    { path: '/packages', component: () => import('../pages/customer/PackageListPage.vue'), name: 'packages' },
    { path: '/packages/:id', component: () => import('../pages/customer/PackageDetailPage.vue'), name: 'package.detail' },
    { path: '/booking/:id', component: () => import('../pages/customer/BookingPage.vue'), name: 'booking', meta: { requiresAuth: true } },
    { path: '/orders', component: () => import('../pages/customer/MyOrdersPage.vue'), name: 'my.orders', meta: { requiresAuth: true } },
    { path: '/orders/:id', component: () => import('../pages/customer/OrderDetailPage.vue'), name: 'order.detail', meta: { requiresAuth: true } },
    { path: '/profile', component: () => import('../pages/customer/ProfilePage.vue'), name: 'profile', meta: { requiresAuth: true } },
    { path: '/faq', component: () => import('../pages/customer/FaqPage.vue'), name: 'faq' },
    { path: '/manasik', component: () => import('../pages/customer/ManasikPage.vue'), name: 'manasik' },
    { path: '/login', component: () => import('../pages/auth/LoginPage.vue'), name: 'login' },
    { path: '/register', component: () => import('../pages/auth/RegisterPage.vue'), name: 'register' },
];

// Admin routes
const adminRoutes = [
    {
        path: '/admin',
        component: () => import('../layouts/AdminLayout.vue'),
        meta: { requiresAuth: true, role: 'admin' },
        children: [
            { path: '', redirect: '/admin/dashboard' },
            { path: 'dashboard', component: () => import('../pages/admin/DashboardPage.vue'), name: 'admin.dashboard' },
            { path: 'users', component: () => import('../pages/admin/UsersPage.vue'), name: 'admin.users' },
            { path: 'packages', component: () => import('../pages/admin/PackagesPage.vue'), name: 'admin.packages' },
            { path: 'packages/create', component: () => import('../pages/admin/PackageFormPage.vue'), name: 'admin.packages.create' },
            { path: 'packages/:id/edit', component: () => import('../pages/admin/PackageFormPage.vue'), name: 'admin.packages.edit' },
            { path: 'orders', component: () => import('../pages/admin/OrdersPage.vue'), name: 'admin.orders' },
            { path: 'orders/:id', component: () => import('../pages/admin/OrderDetailPage.vue'), name: 'admin.orders.detail' },
            { path: 'participants', component: () => import('../pages/admin/ParticipantsPage.vue'), name: 'admin.participants' },
            { path: 'payments', component: () => import('../pages/admin/PaymentsPage.vue'), name: 'admin.payments' },
            { path: 'banks', component: () => import('../pages/admin/BanksPage.vue'), name: 'admin.banks' },
            { path: 'airlines', component: () => import('../pages/admin/AirlinesPage.vue'), name: 'admin.airlines' },
            { path: 'hotels', component: () => import('../pages/admin/HotelsPage.vue'), name: 'admin.hotels' },
            { path: 'routes', component: () => import('../pages/admin/RoutesPage.vue'), name: 'admin.routes' },
            { path: 'faqs', component: () => import('../pages/admin/FaqsPage.vue'), name: 'admin.faqs' },
            { path: 'manasiks', component: () => import('../pages/admin/ManasikPage.vue'), name: 'admin.manasiks' },
            { path: 'sliders', component: () => import('../pages/admin/SlidersPage.vue'), name: 'admin.sliders' },
            { path: 'settings', component: () => import('../pages/admin/SettingsPage.vue'), name: 'admin.settings' },
            { path: 'reports', component: () => import('../pages/admin/ReportsPage.vue'), name: 'admin.reports' },
        ],
    },
];

// Merchant routes
const merchantRoutes = [
    {
        path: '/merchant',
        component: () => import('../layouts/AdminLayout.vue'),
        meta: { requiresAuth: true, role: 'merchants' },
        children: [
            { path: '', redirect: '/merchant/dashboard' },
            { path: 'dashboard', component: () => import('../pages/merchant/DashboardPage.vue'), name: 'merchant.dashboard' },
            { path: 'packages', component: () => import('../pages/merchant/PackagesPage.vue'), name: 'merchant.packages' },
            { path: 'packages/create', component: () => import('../pages/merchant/PackageFormPage.vue'), name: 'merchant.packages.create' },
            { path: 'packages/:id/edit', component: () => import('../pages/merchant/PackageFormPage.vue'), name: 'merchant.packages.edit' },
            { path: 'orders', component: () => import('../pages/merchant/OrdersPage.vue'), name: 'merchant.orders' },
            { path: 'reports', component: () => import('../pages/merchant/ReportsPage.vue'), name: 'merchant.reports' },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes: [...customerRoutes, ...adminRoutes, ...merchantRoutes],
    scrollBehavior() {
        return { top: 0 };
    },
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next({ name: 'login', query: { redirect: to.fullPath } });
        return;
    }

    if (to.meta.role && !authStore.hasRole(to.meta.role)) {
        next({ name: 'home' });
        return;
    }

    next();
});

export default router;