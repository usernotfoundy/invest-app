import { defineStore } from "pinia";
import axiosClient from '@/axios.js';

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
  }),

  actions: {
    async fetchSectors() {
      this.loadingFetchSectors = true;
      this.errorFetchSectors = null;
      try {
        const response = await axiosClient.get("/get-sectors", {
          withCredentials: true,
        });

        this.sectorsList = response.data.map((sector) => ({
          id: sector.id,
          name: sector.name,
          description: sector.description,
        }));
      } catch (err) {
        console.error("Error fetching sectors:", err);
        this.errorFetchSectors =
          err.response?.data?.message || "Failed to fetch sectors";
      } finally {
        this.loadingFetchSectors = false;
      }
    },


    async fetchPublicSectors() {
      this.loadingFetchPublicSectors = true;
      this.errorFetchPublicSectors = null;

      try {
        const response = await axiosClient.get("/public/get-sectors");

        const sectors = Array.isArray(response.data)
          ? response.data
          : response.data.data || [];

        this.publicSectorsList = sectors.map((sector) => ({
          id: sector.id,
          name: sector.name,
          description: sector.description,
        }));
      } catch (err) {
        console.error("Error fetching public sectors:", err);
        this.errorFetchPublicSectors =
          err.response?.data?.message || "Failed to fetch public sectors";
      } finally {
        this.loadingFetchPublicSectors = false;
      }
    },


    async fetchSectorById(id) {
      this.loading = true;
      this.error = null;

      try {
        if (!id) throw new Error("No sector id provided");

        const { data } = await axiosClient.get(`/show-sector/${id}`);

        // Handle different API shapes: {sector}, {data}, or raw object
        this.sector = data?.sector ?? data?.data ?? data;
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

        return res.data;
      } catch (err) {
        if (err.response?.status === 422) {
          // Laravel validation errors
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
        const { data } = await axiosClient.put(
          `/update-sector/${id}`,
          payload
        );

        // handle response shape
        const updatedSector = data.sector ?? data;

        this.sector = updatedSector; // update local state
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

// stores/sector.js
// import { defineStore } from "pinia";
// import axiosClient from "@/axios.js";

// export const useSectorStore = defineStore("sector", {
//   state: () => ({
//     publicSectorsList: [],
//     loadingFetchPublicSectors: false,
//     errorFetchPublicSectors: null,
//   }),

//   actions: {
//     async fetchPublicSectors() {
//       this.loadingFetchPublicSectors = true;
//       this.errorFetchPublicSectors = null;

//       try {
//         const response = await axiosClient.get("/public/get-sectors");

//         const sectors = Array.isArray(response.data)
//           ? response.data
//           : response.data.data || [];

//         this.publicSectorsList = sectors.map((sector) => ({
//           id: sector.id,
//           name: sector.name,
//           description: sector.description,
//         }));
//       } catch (err) {
//         console.error("Error fetching public sectors:", err);
//         this.errorFetchPublicSectors =
//           err.response?.data?.message || "Failed to fetch public sectors";
//       } finally {
//         this.loadingFetchPublicSectors = false;
//       }
//     },
//   },
// });
