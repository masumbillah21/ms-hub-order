import { defineStore } from 'pinia';
import Axios from 'axios';

export const useHubStore = defineStore({
  id: 'hub',
  state: () => ({
    hubSettings: [],
  }),
  actions: {
    async fetchHubSettings() {
      try {
        const response = await Axios.get(mhoAdminLocalizer.apiUrl + '/mho/v1/hub-settings');
        this.hubSettings = response.data;
      } catch (error) {
        console.error('Error fetching orders:', error);
      }
    },
  },
});