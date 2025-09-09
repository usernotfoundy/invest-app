// stores/sector.js
import { defineStore } from "pinia";
import axiosClient from "@/axios.js";

export const useSectorStore = defineStore("sector", {
  state: () => ({
    loadingFetchSectors: false,
    loadingFetchPublicSectors: false,
    loadingCreateSector: false,
    errorFetchSectors: null,
    errorFetchPublicSectors: null,
    errorCreateSector: null,

    name: "",
    description: "",

    sectorsList: [],
    publicSectorsList: [],

    // Cache timestamps
    lastFetchedSectors: null,
    lastFetchedPublicSectors: null,
  }),

  actions: {
    // ✅ Private helper for saving cache
    _saveCache(key, data) {
      sessionStorage.setItem(
        key,
        JSON.stringify({
          data,
          timestamp: Date.now(),
        })
      );
    },

    // ✅ Private helper for loading cache
    _loadCache(key, maxAgeMs = 5 * 60 * 1000) {
      // default: 5 minutes cache
      const cache = sessionStorage.getItem(key);
      if (!cache) return null;

      try {
        const parsed = JSON.parse(cache);
        const isExpired = Date.now() - parsed.timestamp > maxAgeMs;
        return isExpired ? null : parsed.data;
      } catch {
        return null;
      }
    },

    async fetchSectors(force = false) {
      this.loadingFetchSectors = true;
      this.errorFetchSectors = null;

      try {
        // ✅ Try cached data first
        if (!force) {
          const cached = this._loadCache("sectorsCache", 10 * 60 * 1000); // 10 min
          if (cached) {
            this.sectorsList = cached;
            this.loadingFetchSectors = false;
            return;
          }
        }

        const response = await axiosClient.get("/get-sectors", {
          withCredentials: true,
        });

        this.sectorsList = response.data.map((sector) => ({
          id: sector.id,
          name: sector.name,
          description: sector.description,
        }));

        this.lastFetchedSectors = Date.now();
        this._saveCache("sectorsCache", this.sectorsList);
      } catch (err) {
        console.error("Error fetching sectors:", err);
        this.errorFetchSectors =
          err.response?.data?.message || "Failed to fetch sectors";
      } finally {
        this.loadingFetchSectors = false;
      }
    },

    async fetchPublicSectors(force = false) {
      this.loadingFetchPublicSectors = true;
      this.errorFetchPublicSectors = null;

      try {
        // ✅ Try cached data first
        if (!force) {
          const cached = this._loadCache("publicSectorsCache", 30 * 60 * 1000); // 30 min
          if (cached) {
            this.publicSectorsList = cached;
            this.loadingFetchPublicSectors = false;
            return;
          }
        }

        const response = await axiosClient.get("/public/get-sectors");
        const sectors = Array.isArray(response.data)
          ? response.data
          : response.data.data || [];

        this.publicSectorsList = sectors.map((sector) => ({
          id: sector.id,
          name: sector.name,
          description: sector.description,
        }));

        this.lastFetchedPublicSectors = Date.now();
        this._saveCache("publicSectorsCache", this.publicSectorsList);
      } catch (err) {
        console.error("Error fetching public sectors:", err);
        this.errorFetchPublicSectors =
          err.response?.data?.message || "Failed to fetch public sectors";
      } finally {
        this.loadingFetchPublicSectors = false;
      }
    },

    async fetchSectorById(id, force = false) {
      if (!id) throw new Error("No sector id provided");

      this.loading = true;
      this.error = null;

      try {
        // ✅ Use cache per ID
        if (!force) {
          const cached = this._loadCache(`sector_${id}`, 30 * 60 * 1000);
          if (cached) {
            this.sector = cached;
            this.loading = false;
            return this.sector;
          }
        }

        const { data } = await axiosClient.get(`/show-sector/${id}`);
        this.sector = data?.sector ?? data?.data ?? data;

        this._saveCache(`sector_${id}`, this.sector);

        return this.sector;
      } catch (err) {
        console.error("fetchSectorById failed:", err);
        this.error =
          err.response?.data?.message || err.message || "Failed to load sector";
        throw err;
      } finally {
        this.loading = false;
      }
    },

    async createSector({ name, description }) {
      this.error = null;

      try {
        const res = await axiosClient.post("/create-sector", {
          name,
          description,
        });

        // ✅ Clear cache after create
        sessionStorage.removeItem("sectorsCache");
        return res.data;
      } catch (err) {
        if (err.response?.status === 422) {
          this.createUserError = err.response.data.errors;
        } else {
          this.createUserError = { general: ["Something went wrong."] };
        }
        throw err;
      }
    },

    async updateSector(id, payload) {
      this.updateLoading = true;
      this.updateError = null;

      try {
        const { data } = await axiosClient.put(`/update-sector/${id}`, payload);

        const updatedSector = data.sector ?? data;
        this.sector = updatedSector;

        // ✅ Update cache
        sessionStorage.setItem(
          `sector_${id}`,
          JSON.stringify({ data: updatedSector, timestamp: Date.now() })
        );
        sessionStorage.removeItem("sectorsCache"); // invalidate list cache

        return updatedSector;
      } catch (error) {
        console.error("Update sector error:", error);

        if (error.response?.data?.errors) {
          this.updateError = error.response.data.errors;
        } else {
          this.updateError = { general: [error.message || "Update failed"] };
        }

        throw error;
      } finally {
        this.updateLoading = false;
      }
    },
  },
});