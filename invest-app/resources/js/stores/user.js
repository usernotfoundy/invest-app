import { defineStore } from "pinia";
import axiosClient from "../axios.js";
import router from "../router";

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

    // caching timestamps
    lastFetchedUsers: null,
    lastFetchedProfile: null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.user,
    userCount: (state) => state.usersList.length,
  },

  actions: {
    async login(email, password) {
      this.loadingLogin = true;
      this.loginError = null;

      try {
        await axiosClient.get("/sanctum/csrf-cookie");

        await axiosClient.post("/login", { email, password });

        const response = await axiosClient.get("/user/profile");
        this.user = response.data;
        this.lastFetchedProfile = Date.now();

        sessionStorage.setItem(
          "profileCache",
          JSON.stringify({
            user: this.user,
            lastFetched: this.lastFetchedProfile,
          })
        );

        console.log("✅ Login successful, user profile fetched:", this.user);

        // 4. Redirect by role
        if (this.user?.role === "admin") {
          router.push("/admin");
        } else if (this.user?.role === "agency") {
          router.push("/encoder");
        } else {
          router.push("/");
        }
      } catch (err) {
        console.error("❌ Login error:", err);

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

        this.user = null;
        this.usersList = [];
        this.lastFetchedUsers = null;
        this.lastFetchedProfile = null;

        sessionStorage.clear();

        // Clear localStorage too if something is cached there
        // localStorage.clear();

        router.push({ name: "LoginView" });

        console.log("Logged out successfully, all cache cleared");
      } catch (err) {
        console.error("Logout error:", err.response || err);
      }
    },


    async createUser({ name, email, password, assignedSector, role }) {
      this.createUserError = null;

      try {
        const res = await axiosClient.post("/register", {
          name,
          email,
          password,
          assignedSector,
          role,
        });

        // force refresh user list after creating a user
        await this.fetchUsers(true);

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

    async deleteUser(id) {
      try {
        await axiosClient.delete(`/delete-user/${id}`);
        this.usersList = this.usersList.filter((user) => user.id !== id);
        return true;
      } catch (err) {
        console.error("Failed to delete user:", err.response || err);
        return false;
      }
    },

    async fetchUsers(force = false) {
      this.fetchUsersError = null;

      const cacheDuration = 5 * 60 * 1000; // 5 minutes
      const now = Date.now();

      if (
        !force &&
        this.usersList.length > 0 &&
        this.lastFetchedUsers &&
        now - this.lastFetchedUsers < cacheDuration
      ) {
        return this.usersList;
      }

      this.loadingFetchUsers = true;

      try {
        const response = await axiosClient.get("/get-users", {
          withCredentials: true,
        });

        if (Array.isArray(response.data)) {
          this.usersList = response.data.map((user) => ({
            id: user.id,
            name: user.name,
            email: user.email,
            role: user.role || "No role",
            assigned_sector: user.assigned_sector || "Unassigned",
            email_verified_at: user.email_verified_at || null,
          }));
          this.lastFetchedUsers = now;
        } else {
          console.error("Unexpected response:", response.data);
          this.fetchUsersError = "Invalid server response.";
        }

        return this.usersList;
      } catch (err) {
        console.error("Failed to fetch users:", err.response || err);
        this.fetchUsersError =
          err.response?.data?.message || "Failed to fetch users.";
      } finally {
        this.loadingFetchUsers = false;
      }
    },

    async fetchUserProfile(force = false) {
      this.fetchUserProfileError = null;

      const cacheDuration = 5 * 60 * 1000; // 5 minutes
      const now = Date.now();

      // ✅ Check sessionStorage for cached profile
      const cached = sessionStorage.getItem("profileCache");
      if (cached) {
        const { user, lastFetched } = JSON.parse(cached);

        if (!force && user && lastFetched && now - lastFetched < cacheDuration) {
          this.user = user;
          this.lastFetchedProfile = lastFetched;
          return this.user;
        }
      }

      this.loadingFetchUserProfile = true;

      try {
        const response = await axiosClient.get("/user/profile");

        this.user = response.data;
        this.lastFetchedProfile = now;

        // ✅ Save to sessionStorage
        sessionStorage.setItem(
          "profileCache",
          JSON.stringify({
            user: this.user,
            lastFetched: this.lastFetchedProfile,
          })
        );

        return this.user;
      } catch (err) {
        console.error("Fetch user profile error:", err.response || err);

        if (err.response && err.response.status === 401) {
          this.fetchUserProfileError = "Unauthorized. Please login again.";
          this.user = null;
          sessionStorage.removeItem("profileCache"); // ❌ clear stale cache

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
