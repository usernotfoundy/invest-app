import { defineStore } from "pinia";
import axiosClient from "@/axios";
import { useUserStore } from "@/stores/user";

export const useTableStore = defineStore("table", {

  state: () => ({
    currentInput: "",
    data_template: [],
    loading: false,
    sectorName: null,
    children: [],
    loadingFetchTable: false,
    errorFetchTable: null,
    error: null,
    table: null,
    uploading: false,
    uploadError: null,
    uploadSuccess: false,
  }),

  actions: {
    addInput() {
      if (this.currentInput.trim() !== "") {
        this.data_template.push(this.currentInput);
        this.currentInput = "";
      }
    },

    removeInput(index) {
      this.data_template.splice(index, 1);
    },

    // Create Sector Child Table
    async submitTable({ sectorId, name }) {
      this.error = null;

      try {
        const res = await axiosClient.post("/create-sector-child", {
          sector_id: sectorId,
          name,
          data: null,
          data_template: this.data_template,
        });

        this.data_template = [];
        this.currentInput = "";

        return res.data;
      } catch (err) {
        if (err.response?.status === 422) {
          this.error = err.response.data.errors; // Laravel validation
        } else {
          this.error = { general: ["Something went wrong."] };
        }
        throw err;
      }
    },

    async fetchTableById(id) {
      this.loadingFetchTable = true;
      this.errorFetchTable = null;

      try {
        if (!id) throw new Error("No table id provided");

        const { data } = await axiosClient.get(`/sector-children/${id}`);

        this.sectorName = data?.sector_name ?? null;
        this.children = data?.children ?? [];

        // return children with data_template included
        return {
          sectorName: this.sectorName,
          children: this.children,
        };
      } catch (err) {
        console.error("fetchTableById failed:", err);
        this.errorFetchTable =
          err.response?.data?.message || err.message || "Failed to load table";
        throw err;
      } finally {
        this.loadingFetchTable = false;
      }
    },

    async fetchTableByAssignedSector() {
      this.loadingFetchTable = true;
      this.errorFetchTable = null;

      try {
        const userStore = useUserStore();

        // ⏳ If profile is not loaded yet, fetch it first
        if (!userStore.user) {
          await userStore.fetchUserProfile();
        }

        const assignedSectorId = userStore.user?.assigned_sector;
        if (!assignedSectorId) throw new Error("No assigned sector for user");

        const { data } = await axiosClient.get(
          `/encoding/view-children/${assignedSectorId}`
        );

        this.sectorName = data?.sector_name ?? null;
        this.children = data?.children ?? [];

        return {
          sectorName: this.sectorName,
          children: this.children,
        };
      } catch (err) {
        console.error("fetchTableByAssignedSector failed:", err);
        this.errorFetchTable =
          err.response?.data?.message || err.message || "Failed to load table";
        throw err;
      } finally {
        this.loadingFetchTable = false;
      }
    },

    async submitChild(childId, childForms) {
      const child = this.children.find((c) => c.id === childId);
      if (!child) return;

      const payload = {
        sector_id: child.sector_id,
        child_id: child.id,
        data: { ...childForms[childId] },
      };

      try {
        const res = await axiosClient.post("/encoding/data-input", payload);

        // Reset form after submit
        Object.keys(childForms[childId]).forEach((field) => {
          childForms[childId][field] = "";
        });

        return res.data;
      } catch (err) {
        console.error("Submit failed:", err.response?.data || err.message);
        throw err;
      }
    },

    async downloadTemplate(childId, childName) {
      try {
        const response = await axiosClient.get(
          `/encoding/download-template/${childId}`,
          {
            responseType: "blob",
          }
        );

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;

        // sanitize childName for filename (replace spaces with _)
        const safeName = childName.replace(/\s+/g, "_");
        link.setAttribute(
          "download",
          `${safeName}_table_template.xlsx`.toLowerCase()
        );

        document.body.appendChild(link);
        link.click();
        link.remove();

        return true;
      } catch (err) {
        console.error("Download template failed:", err.response || err);
        return false;
      }
    },

    async uploadTemplate(file, sectorId, childId) {
      try {
        const formData = new FormData();

        // match backend keys exactly
        formData.append("file_upload", file);
        formData.append("sector_id", sectorId);
        formData.append("child_id", childId);

        const response = await axiosClient.post(
          "/encoding/upload-template",
          formData,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );

        return response.data;
      } catch (err) {
        console.error("Upload failed:", err.response?.data || err.message);
        throw err;
      }
    },
  },
});
