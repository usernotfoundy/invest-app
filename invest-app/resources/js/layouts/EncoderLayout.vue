<script setup>
import { onMounted, computed, onBeforeUnmount } from "vue";
import { useRouter } from "vue-router";
import { useUserStore } from "@/stores/user.js";
import { FileSpreadsheet, ChevronsUpDownIcon, LockKeyhole, LogOut } from 'lucide-vue-next';

// --------------------
// Setup Composables
// --------------------
const router = useRouter();
const userStore = useUserStore();

// --------------------
// Computed Properties
// --------------------
const userInitial = computed(() => 
  userStore.user?.name?.[0]?.toUpperCase() || 'U'
);

const userDisplayName = computed(() => 
  userStore.user?.name || 'Unknown User'
);

const userRole = computed(() => 
  userStore.user?.role || 'user'
);

const isUserLoaded = computed(() => 
  !userStore.loadingFetchUserProfile && !userStore.fetchUserProfileError && userStore.user
);

// --------------------
// Actions / Handlers
// --------------------
const handleLogout = async () => {
  try {
    await userStore.logout();
    router.push('/login');
  } catch (error) {
    console.error('Logout failed:', error);
  }
};

const handleChangePassword = () => {
  // Navigate to change password page
  router.push('/change-password');
};

// --------------------
// Lifecycle Hooks
// --------------------
onMounted(async () => {
  // Only fetch if we have a token and no user data
  if (userStore.token && !userStore.user) {
    try {
      await userStore.fetchUserProfile();
    } catch (error) {
      console.error('Failed to fetch user profile:', error);
    }
  }
});

// Cleanup on unmount
onBeforeUnmount(() => {
  // Any cleanup if needed
});
</script>

<template>
  <div class="bg-adminBG">
    <div class="drawer lg:drawer-open text-textColor-1 font-textFont">
      <input id="my-drawer-2" type="checkbox" class="drawer-toggle" />
      <div class="drawer-content">
        <!-- Page content here -->
        <div class="p-5 overflow-y-hidden">
          <RouterView />
        </div>

      </div>
      <div class="drawer-side text-textColor-1">
        <label for="my-drawer-2" aria-label="close sidebar" class="drawer-overlay"></label>
        <ul class="menu bg-white text-base-content min-h-full w-64 p-4 flex flex-col justify-between">

          <!-- Top content -->
          <div>
            <div class="flex justify-center mb-6">
              <img class="w-32" src="@/assets/images/logo.webp" alt="INvest Logo">
            </div>
            <li class="font-mainFont-1 pt-2">
              <router-link to="/encoder" class="btn btn-ghost h-8 flex justify-start border-0 gap-2"
                active-class="bg-primary text-white">
                <FileSpreadsheet :size="16" :stroke-width="2" class="relative top-[1px]" />
                <span class="mt-1 font-medium">Manage Data</span>
              </router-link>
            </li>
          </div>

          <!-- Bottom User Content -->
          <div class="w-full">

            <!-- Loading -->
            <div v-if="userStore.loadingFetchUserProfile" class="text-center py-6">
              Loading user...
            </div>

            <!-- Error -->
            <div v-else-if="userStore.fetchUserProfileError" class="text-red-500 text-center py-2">
              {{ userStore.fetchUserProfileError }}
            </div>

            <!-- User Dropdown -->
            <div v-else-if="isUserLoaded" class="dropdown dropdown-top dropdown-center w-full">
              <div tabindex="0" role="button"
                class="btn bg-transparent w-full border-0.5 p-6 px-2 hover:bg-btnHover rounded-lg flex items-center gap-3 mt-2">
                <div class="flex items-center justify-center bg-brandSky-3 text-lg w-9 h-9 rounded-lg object-cover">{{
                  userInitial }}</div>
                <p class="font-medium text-md">{{ userDisplayName }}</p>
                <ChevronsUpDownIcon size="16" stroke-width="2" class="ml-auto" />
              </div>

              <ul tabindex="0"
                class="dropdown-content menu bg-base-100 rounded-lg z-1 w-full p-2 shadow-sm space-y-2 border border-slate-100">
                <div class="flex items-center gap-3 mx-3">
                  <div class="flex flex-col leading-tight">
                    <p class="font-medium text-md">{{ userDisplayName }}</p>
                    <span class="text-xs text-gray-500 capitalize">{{ userRole }}</span>
                  </div>
                </div>

                <li>
                  <router-link to="#"
                    class="btn btn-ghost flex h-8 justify-start gap-2 border-0 hover:border-0 hover:shadow-sm hover:bg-btnHover">
                    <LockKeyhole size="16" stroke-width="2" class="relative top-[1px]" />
                    <span class="mt-1 font-medium">Change Password</span>
                  </router-link>
                </li>

                <li>
                  <button @click="handleLogout"
                    class="btn btn-ghost flex h-8 justify-start gap-2 border-0 hover:border-0 hover:shadow-sm hover:bg-btnHover">
                    <LogOut size="16" stroke-width="2" color="#b91c1c" class="relative top-[1px]" />
                    <span class="mt-1 font-medium text-red-800">Logout</span>
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </ul>
      </div>
    </div>
  </div>
</template>