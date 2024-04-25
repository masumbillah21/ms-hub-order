import { defineStore } from 'pinia';
import Axios from 'axios';

export const useStoreStore = defineStore({
  id: 'store',
  state: () => ({
    storeSettings: [],
  }),
  actions: {
    async fetchStoreSettings() {
      try {
        const response = await Axios.get(mhoAdminLocalizer.apiUrl + '/mho/v1/store-settings');
        this.storeSettings = response.data;
      } catch (error) {
        console.error('Error fetching orders:', error);
      }
    },
  },
});