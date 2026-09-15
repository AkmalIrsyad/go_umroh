import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';

export const useAuthStore = defineStore('auth', () => {
    const user = ref(null);
    const token = ref(null);

    const isAuthenticated = computed(() => !!token.value && !!user.value);

    function hasRole(role) {
        if (!user.value) return false;
        const roles = user.value.roles || [];
        return roles.includes(role) || roles.includes('admin');
    }

    function initFromStorage() {
        const savedToken = localStorage.getItem('go_umrah_token');
        const savedUser = localStorage.getItem('go_umrah_user');
        if (savedToken && savedUser) {
            token.value = savedToken;
            try {
                user.value = JSON.parse(savedUser);
            } catch {
                logout();
            }
        }
    }

    async function login(credentials) {
        const { data } = await axios.post('/api/v1/login', credentials);
        token.value = data.data.token;
        user.value = data.data.user;
        localStorage.setItem('go_umrah_token', token.value);
        localStorage.setItem('go_umrah_user', JSON.stringify(user.value));
        return data.data.user;
    }

    async function register(payload) {
        const { data } = await axios.post('/api/v1/register', payload);
        return data;
    }

    async function logout() {
        try {
            await axios.post('/api/v1/logout');
        } catch {
            // ignore
        }
        token.value = null;
        user.value = null;
        localStorage.removeItem('go_umrah_token');
        localStorage.removeItem('go_umrah_user');
    }

    async function fetchMe() {
        const { data } = await axios.get('/api/v1/me');
        user.value = data.data;
        localStorage.setItem('go_umrah_user', JSON.stringify(user.value));
    }

    return { user, token, isAuthenticated, hasRole, initFromStorage, login, register, logout, fetchMe };
});