// stores/logs.js
import { defineStore } from "pinia";
import axios from "axios";

export const useLogStore = defineStore("logStore", {
  state: () => ({
    logs: [],
    fetchLogsLoading: false,
    fetchLogsError: null,
  }),

  actions: {
    async fetchLogs() {
      this.fetchLogsLoading = true;
      this.fetchLogsError = null;

      try {
        // ✅ Make sure this matches your Laravel API route
        const response = await axios.get("/logs");

        // ✅ If using Laravel's apiResource, it’s usually response.data
        this.logs = response.data;
      } catch (error) {
        console.error("Error fetching logs:", error);
        this.fetchLogsError = error.response?.data?.message || error.message;
      } finally {
        this.fetchLogsLoading = false;
      }
    },
  },
});