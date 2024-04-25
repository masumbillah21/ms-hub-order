import { defineStore } from 'pinia';
import Axios from 'axios';

export const useOrderStore = defineStore({
  id: 'order',
  state: () => ({
    orders: [],
  }),
  actions: {
    async fetchOrders() {
      try {
        const response = await Axios.get(mhoAdminLocalizer.apiUrl + '/mho/v1/order-list');
        this.orders = response.data;
      } catch (error) {
        console.error('Error fetching orders:', error);
      }
    },
  },
});