import api from './api';

export const postService = {
    async getPosts(page = 1) {
        const response = await api.get(`/posts?page=${page}`);
        return response.data;
    },

    async createPost(postData) {
        const response = await api.post('/posts', postData);
        return response.data;
    },

    async likePost(postId) {
        const response = await api.post(`/posts/${postId}/like`);
        return response.data;
    },

    async commentOnPost(postId, comment) {
        const response = await api.post(`/posts/${postId}/comment`, { comment });
        return response.data;
    }
};