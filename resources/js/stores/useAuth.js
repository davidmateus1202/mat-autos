import { defineStore } from 'pinia';
import axios from 'axios';
import router from '../router';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        errors: {},
        loading: false
    }),
    getters: {
        isAuthenticated: (state) => !!state.user,
    },
    actions: {
        async getUser() {
            this.loading = true;
            try {
                const response = await axios.get('/api/user');
                this.user = response.data;
            } catch (error) {
                this.user = null;
            } finally {
                this.loading = false;
            }
        },
        async login(credentials) {
            this.loading = true;
            this.errors = {};
            try {
                await axios.get('/sanctum/csrf-cookie');
                await axios.post('/login', credentials);
                await this.getUser();
                router.push({ name: 'dashboard' });
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                }
            } finally {
                this.loading = false;
            }
        },
        async register(userData) {
            this.loading = true;
            this.errors = {};
            try {
                await axios.get('/sanctum/csrf-cookie');
                await axios.post('/register', userData);
                await this.getUser();
                router.push({ name: 'dashboard' });
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                }
            } finally {
                this.loading = false;
            }
        },
        async logout() {
            try {
                await axios.post('/logout');
                this.user = null;
                router.push({ name: 'login' });
            } catch (error) {
                console.error('Logout failed', error);
            }
        }
    }
});
