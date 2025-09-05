import { defineStore } from "pinia";
import axiosClient from "../axios.js"; // Your axios instance
import router from "../router"; // Import router if you want redirection inside store

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null,
    name: "",
    email: "",
    password: "",
    assignedSector: "",
    role: "",
    loadingLogin: false,
    loadingFetchUsers: false,
    loadingFetchUserProfile: false,
    loadingCreateUser: false,
    loginError: null,
    fetchUsersError: null,
    fetchUserProfileError: null,
    createUserError: null,
    usersList: [],
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    userCount: (state) => state.usersList.length,
  },

  actions: {
    async login(email, password) {
      this.loadingLogin = true;
      this.loginError = null;

      try {
        // 1. Ensure CSRF cookie is set
        await axiosClient.get("/sanctum/csrf-cookie");

        // 2. Perform login
        await axiosClient.post("/login", { email, password });

        // 3. Fetch user profile
        const response = await axiosClient.get("/user/profile");
        this.user = response.data;

        console.log("Login successful, user profile fetched:", this.user);

        // 4. Redirect (use imported router, not this.router)
        if (this.user?.role === "admin") {
          router.push("/admin");
        } else if (this.user?.role === "agency") {
          router.push("/encoder");
        } else {
          router.push("/");
        }
      } catch (err) {
        console.error("Login error:", err);

        if (err.response?.status === 422) {
          this.loginError = "Validation failed. Please check your inputs.";
        } else {
          this.loginError =
            err.response?.data?.message ||
            "Invalid email and password. Please try again";
        }
      } finally {
        this.loadingLogin = false;
        this.email = "";
        this.password = "";
      }
    },

    async logout() {
      try {
        await axiosClient.post("/logout", {}, { withCredentials: true });

        // Clear user from store
        this.user = null;

        // Redirect to login
        router.push({ name: "LoginView" });

        console.log("Logged out successfully");
      } catch (err) {
        console.error("Logout error:", err.response || err);
      }
    },


    async createUser({ name, email, password, assignedSector, role }) {
      this.createUserError = null;

      try {
        const res = await axiosClient.post("/api/register", {
          name,
          email,
          password,
          assignedSector,
          role,
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

    async deleteUser(id) {
      try {
        await axiosClient.delete(`/api/delete-user/${id}`, {
          headers: { Authorization: `Bearer ${this.token}` },
        });
        // Optionally remove the user from usersList here if you want instant UI update
        this.usersList = this.usersList.filter((user) => user.id !== id);
        return true;
      } catch (err) {
        console.error("Failed to delete user:", err.response || err);
        return false;
      }
    },

    async fetchUsers() {
      this.loadingFetchUsers = true;
      this.fetchUsersError = null;

      try {
        const response = await axiosClient.get("/get-users", {
          withCredentials: true, // since you’re using Sanctum
        });

        // ✅ Make sure response.data is an array
        if (Array.isArray(response.data)) {
          this.usersList = response.data.map((user) => ({
            id: user.id,
            name: user.name,
            email: user.email,
            role: user.role || "No role",
            assigned_sector: user.assigned_sector || "Unassigned",
          }));
        } else {
          console.error("Unexpected response:", response.data);
          this.fetchUsersError = "Invalid server response.";
        }
      } catch (err) {
        console.error("Failed to fetch users:", err.response || err);
        this.fetchUsersError =
          err.response?.data?.message || "Failed to fetch users.";
      } finally {
        this.loadingFetchUsers = false;
      }
    },


    async fetchUserProfile() {
      this.loadingFetchUserProfile = true;
      this.fetchUserProfileError = null;

      try {
        // Sanctum automatically authenticates via session cookie
        const response = await axiosClient.get("/user/profile");

        // Store user data in Pinia
        this.user = response.data;

      } catch (err) {
        console.error("Fetch user profile error:", err.response || err);

        if (err.response && err.response.status === 401) {
          this.fetchUserProfileError = "Unauthorized. Please login again.";
          this.user = null;

          // Redirect to login
          if (router.currentRoute.value.name !== "Login") {
            router.push({ name: "Login" });
          }
        } else {
          this.fetchUserProfileError =
            err.response?.data?.message || "Failed to fetch user data";
        }
      } finally {
        this.loadingFetchUserProfile = false;
      }
    },
  },
});
