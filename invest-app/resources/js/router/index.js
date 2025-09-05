import { createRouter, createWebHistory } from "vue-router";
import { useUserStore } from "@/stores/user"; // adjust path if needed

// Layouts
import AdminLayout from "@/layouts/AdminLayout.vue";
import EncoderLayout from "@/layouts/EncoderLayout.vue";
import LoginLayout from "@/layouts/LoginLayout.vue";
import PublicLayout from "@/layouts/PublicLayout.vue";

// Views
import ManageUsersView from "@/pages/auth/admin/ManageUsers.vue";
import ManageSectorsView from "@/pages/auth/admin/ManageSectors.vue";
import ManageINProfileView from "@/pages/auth/admin/ManageINProfile.vue";
import ManageNewsView from "@/pages/auth/admin/ManageNews.vue";
import ManageFormsView from "@/pages/auth/admin/ManageForms.vue";
import ShowSector from "@/pages/auth/admin/ShowSector.vue";
import SectorManageTable from "@/pages/auth/admin/SectorManageTable.vue";

import Home from "@/pages/public/Home.vue";
import PriorityIndustries from "@/pages/public/PriorityIndustries.vue";
import LoginView from "@/pages/public/Login.vue";

const routes = [
    // Admin Routes
    {
        path: "/admin",
        component: AdminLayout,
        meta: { requiresAuth: true, roles: ["admin"] },
        children: [
            { path: "/admin", redirect: { name: "ManageUsersView" } },
            { path: "manage-users", name: "ManageUsersView", component: ManageUsersView },
            { path: "manage-sectors", name: "ManageSectorsView", component: ManageSectorsView },
            { path: "manage-in-profile", name: "ManageINProfileView", component: ManageINProfileView },
            { path: "manage-news", name: "ManageNewsView", component: ManageNewsView },
            { path: "manage-forms", name: "ManageForms", component: ManageFormsView },
            { path: "manage-sectors/:id", name: "ShowSector", component: ShowSector },
            { path: "manage-sectors/manage-table/:id", name: "SectorManageTable", component: SectorManageTable },
        ],
    },

    // Public Routes
    {
        path: "/",
        component: PublicLayout,
        children: [
            { path: "", name: "Home", component: Home },
            { path: "priority-industries", name: "PriorityIndustries", component: PriorityIndustries },
        ],
    },

    // Login Route
    {
        path: "/login",
        component: LoginLayout,
        children: [{ path: "", name: "LoginView", component: LoginView }],
    },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition;
    if (to.hash) {
      return { el: to.hash, top: 0, behavior: "smooth" };
    }
    return { top: 0 };
  },
});

// ✅ Navigation Guard
router.beforeEach(async (to, from, next) => {
    const userStore = useUserStore();

    // If the route needs auth
    if (to.matched.some((record) => record.meta.requiresAuth)) {
        // If no user yet, try fetching
        if (!userStore.user) {
            await userStore.fetchUserProfile();
        }

        // If still no user → redirect to login
        if (!userStore.user) {
            return next({ name: "LoginView" });
        }

        // Check role restriction
        if (to.meta.roles && !to.meta.roles.includes(userStore.user.role)) {
            // Redirect based on actual role
            if (userStore.user.role === "admin") return next({ path: "/admin" });
            if (userStore.user.role === "encoder") return next({ path: "/encoder" });
            return next("/"); // fallback
        }
    }

    // Prevent logged-in users from seeing login
    if (to.name === "LoginView" && userStore.user) {
        if (userStore.user.role === "admin") return next({ path: "/admin" });
        if (userStore.user.role === "encoder") return next({ path: "/encoder" });
        return next("/");
    }

    next();
});

export default router;