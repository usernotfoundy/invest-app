<script setup>
import { onMounted, computed } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useUserStore } from "@/stores/user.js";
import { UserPen, SquaresExclude, ChartColumnBig, ChevronsUpDownIcon, LockKeyhole, LogOut, Newspaper, NotepadText } from "lucide-vue-next";


// --------------------
// Setup Composables
// --------------------
const router = useRouter();
const route = useRoute();
const userStore = useUserStore();
const logout = () => userStore.logout();


// --------------------
// Lifecycle Hooks
// --------------------
onMounted(() => {
  if (userStore.token) {
    userStore.fetchUserProfile();
  }
});

// --------------------
// Utility Functions
// --------------------
function clearAllCookies() {
  const cookies = document.cookie.split(";");

  for (let cookie of cookies) {
    const eqPos = cookie.indexOf("=");
    const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/";
  }
}

const userInitial = computed(() => {
  return userStore.user?.name?.[0]?.toUpperCase() || ''
})

// --------------------
// Actions / Handlers
// --------------------


// --------------------
// Computed Properties
// --------------------
const isSectorsActive = computed(() =>
  route.path.startsWith('/admin/manage-sectors')
);
</script>

<template>
  <div class="bg-adminBG">
    <div class="drawer lg:drawer-open text-textColor-1 font-textFont">
      <input id="my-drawer-2" type="checkbox" class="drawer-toggle" />
      <div class="drawer-content">
        <div class="p-5 overflow-y-hidden">
          <RouterView />
        </div>
      </div>

      <div class="drawer-side text-textColor-1">
        <label for="my-drawer-2" aria-label="close sidebar" class="drawer-overlay"></label>
        <ul class="menu bg-white text-base-content min-h-full w-64 p-4 flex flex-col justify-between">

          <!-- Top Sidebar Content -->
          <div>
            <div class="flex justify-center mb-6">
              <img class="w-32" src="@/assets/images/logo.webp" alt="INvest Logo" />
            </div>

            <li class="font-mainFont-1 pt-2">
              <router-link to="/admin/manage-sectors" custom v-slot="{ navigate }">
                <button @click="navigate"
                  :class="['btn btn-ghost h-8 flex justify-start border-0 gap-2', isSectorsActive ? 'bg-primary text-white' : 'hover:bg-btnHover']">
                  <SquaresExclude size="16" stroke-width="2" class="relative top-[1px]" />
                  <span class="mt-1 font-medium">Manage Sectors</span>
                </button>
              </router-link>
            </li>

            <li class="font-mainFont-1 pt-2">
              <router-link to="/admin/manage-users" custom v-slot="{ isActive, navigate }">
                <button @click="navigate"
                  :class="['btn btn-ghost h-8 flex justify-start border-0 gap-2', isActive ? 'bg-primary text-white' : 'hover:bg-btnHover']">
                  <UserPen size="16" stroke-width="2" class="relative top-[1px]" />
                  <span class="mt-1 font-medium">Manage Users</span>
                </button>
              </router-link>
            </li>

            <li class="font-mainFont-1 pt-2">
              <details open class="group">
                <summary class="btn btn-ghost h-8 flex justify-start border-0 gap-2 hover:bg-btnHover bg-transparent">
                  <ChartColumnBig size="16" stroke-width="2" class="relative top-[1px]" />
                  <span class="mt-1 font-medium">Content Manager</span>
                </summary>
                <ul class="pt-2">
                  <li>
                    <router-link to="/admin/manage-in-profile" custom v-slot="{ isActive, navigate }">
                      <button @click="navigate"
                        :class="['btn btn-ghost h-8 flex justify-start border-0 gap-2', isActive ? 'bg-primary text-white' : 'hover:bg-btnHover']">
                        <ChartColumnBig size="16" stroke-width="2" class="relative top-[1px]" />
                        <span class="mt-1 font-medium">IN Profile</span>
                      </button>
                    </router-link>
                  </li>
                  <li>
                    <router-link to="/admin/manage-news" custom v-slot="{ isActive, navigate }">
                      <button @click="navigate"
                        :class="['btn btn-ghost h-8 flex justify-start border-0 gap-2', isActive ? 'bg-primary text-white' : 'hover:bg-btnHover']">
                        <Newspaper size="16" stroke-width="2" class="relative top-[1px]" />
                        <span class="mt-1 font-medium">News</span>
                      </button>
                    </router-link>
                  </li>
                  <li>
                    <router-link to="/admin/manage-forms" custom v-slot="{ isActive, navigate }">
                      <button @click="navigate"
                        :class="['btn btn-ghost h-8 flex justify-start border-0 gap-2', isActive ? 'bg-primary text-white' : 'hover:bg-btnHover']">
                        <NotepadText size="16" stroke-width="2" class="relative top-[1px]" />
                        <span class="mt-1 font-medium">Forms</span>
                      </button>
                    </router-link>
                  </li>
                </ul>
              </details>
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
            <div v-else-if="userStore.user" class="dropdown dropdown-top dropdown-center w-full">
              <div tabindex="0" role="button"
                class="btn bg-transparent w-full border-0.5 p-6 px-2 hover:bg-btnHover rounded-lg flex items-center gap-3 mt-2">
                <div class="flex items-center justify-center bg-brandSky-3 text-lg w-9 h-9 rounded-lg object-cover">{{ userInitial }}</div>
                <p class="font-medium text-xs">{{ userStore.user.name }}</p>
                <ChevronsUpDownIcon size="16" stroke-width="2" class="ml-auto" />
              </div>

              <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-lg z-1 w-full p-2 shadow-sm space-y-2 border border-slate-100">
                <div class="flex items-center gap-3">
                  <div class="flex items-center justify-center bg-brandSky-3 text-lg w-9 h-9 rounded-lg object-cover">{{ userInitial }}</div>
                  <div class="flex flex-col leading-tight">
                    <p class="font-medium text-xs">{{ userStore.user.name }}</p>
                    <span class="text-xs text-gray-500 capitalize">{{ userStore.user.role }}</span>
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
                  <button @click="logout"
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
