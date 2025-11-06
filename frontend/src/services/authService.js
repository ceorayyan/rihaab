import api from './api';

export const authService = {
    async login(email, password) {
        const response = await api.post('/login', { email, password });
        if (response.data.token) {
            localStorage.setItem('rihaab_token', response.data.token);
        }
        return response.data;
    },

    async register(name, email, password, password_confirmation) {
        const response = await api.post('/register', {
            name,
            email,
            password,
            password_confirmation
        });
        if (response.data.token) {
            localStorage.setItem('rihaab_token', response.data.token);
        }
        return response.data;
    },

    async logout() {
        await api.post('/logout');
        localStorage.removeItem('rihaab_token');
    },

    async getCurrentUser() {
        const response = await api.get('/user');
        return response.data;
    }
};