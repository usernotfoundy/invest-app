<script setup>
import { PlusCircle, ChartNoAxesCombined, X, ALargeSmall, AtSign, Lock, SquareMousePointer, Ellipsis, UserX, UserPen, CheckCircle, ShieldUser, CircleAlert, TriangleAlert } from 'lucide-vue-next';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle, Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { UserGroupIcon } from '@heroicons/vue/16/solid';
import { ref, onMounted } from 'vue'
import { useUserStore } from '@/stores/user.js';
import { useSectorStore } from '@/stores/sector.js';
import { useLogStore } from '@/stores/logs.js';

const userStore = useUserStore();
const sectorStore = useSectorStore();
const logStore = useLogStore();

const name = ref("");
const password = ref("");
const email = ref("");
const assignedSector = ref("");
const role = ref("");
const confirmDeleteText = ref("");

const showSuccess = ref(false);
const showDeleteSuccess = ref(false);
const loadingCreateUser = ref(false);
const createUserError = ref(null);
const activeModal = ref(null);
const selectedUser = ref(null);

onMounted(() => {
  userStore.fetchUsers();
  sectorStore.fetchSectors();
  logStore.fetchLogs();
});

const createUser = async () => {
  loadingCreateUser.value = true;
  createUserError.value = null;

  try {
    await userStore.createUser({
      name: name.value,
      email: email.value,
      password: password.value,
      assignedSector: assignedSector.value,
      role: role.value,
    });

    closeModal();
    showSuccess.value = true
    setTimeout(() => {
      showSuccess.value = false
    }, 5000);

    await userStore.fetchUsers();

  } catch (err) {
    console.error(err);
    createUserError.value = err.message;
  } finally {
    loadingCreateUser.value = false;
  }
};

const submitDelete = async () => {
  if (!selectedUser.value) return;

  const success = await userStore.deleteUser(selectedUser.value.id);

  if (success) {
    closeModal();
    showDeleteSuccess.value = true;
    setTimeout(() => {
      showDeleteSuccess.value = false;
    }, 5000);
    await userStore.fetchUsers(); // Refresh user list
  }
};

function closeModal() {
  if (activeModal.value === 'delete') {
    confirmDeleteText.value = "";
    selectedUser.value = null;
  } else if (activeModal.value === 'create') {
    name.value = "";
    email.value = "";
    password.value = "";
    assignedSector.value = "";
    role.value = "";
    userStore.createUserError = {};
  } else if (activeModal.value === 'update') {
    // Add any fields you want to clear for update modal
    selectedUser.value = null;
    // Optionally clear update-specific refs here
  }
  activeModal.value = null;
}

function openDeleteConfirm(user) {
  selectedUser.value = user
  activeModal.value = 'delete'
}
function openUpdateUser(user) {
  selectedUser.value = user
  activeModal.value = 'update'
}
function openModal() {
  activeModal.value = 'create'
}
</script>

<template>
  <div class="grid grid-cols-3 gap-2">

    <div class="grid grid-cols-2 gap-2 col-span-2">
      <!-- Page Title -->
      <div class="col-span-3 flex py-5 items-center">
        <h1 class="font-bold text-5xl">Manage Users</h1>
      </div>

      <!-- Admin -->
      <div class="col-span-1 shadow-sm rounded-lg bg-white p-3">
        <div class="grid grid-cols-2 h-full">
          <div class="flex flex-col justify-center items-center">
            <h1 class="font-bold text-5xl text-textColor-1">26</h1>
            <h2 class="text-xs">Admin</h2>
          </div>
          <div class="flex justify-center items-center">
            <UserGroupIcon class="size-16" />
          </div>
        </div>

      </div>

      <!-- Encoder -->
      <div class="col-span-1 shadow-sm rounded-lg bg-white p-3">
        <div class="grid grid-cols-2 h-full">
          <div class="flex flex-col justify-center items-center">
            <h1 class="font-bold text-5xl text-textColor-1">26</h1>
            <h2 class="text-xs">Encoder</h2>
          </div>
          <div class="flex justify-center items-center">
            <UserGroupIcon class="size-16" />
          </div>
        </div>
      </div>

      <!-- Today's Activity -->
      <div class="col-span-1 shadow-sm rounded-lg bg-white p-3">
        <div class="grid grid-cols-2 h-full">
          <div class="flex flex-col items-center justify-center">
            <h1 class="font-bold text-5xl text-textColor-1">151</h1>
            <h2 class="text-xs">Today's Activiy</h2>
          </div>
          <div class="flex justify-center items-center">
            <ChartNoAxesCombined class="size-16" />
          </div>
        </div>
      </div>

      <!-- Search Bar and Create User Button -->
      <div class="col-span-3 pt-3 flex">
        <div class="flex-1">
          <label class="input">
            <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
              </g>
            </svg>
            <input id="search" type="search" class="grow" placeholder="Search" />
          </label>
        </div>

        <!-- Create User Button -->
        <button type="button" class="btn btn-primary" @click="openModal">
          <PlusCircle size="20" /> Create User
        </button>

      </div>

      <!-- Main Table -->
      <div class="col-span-3 shadow-sm rounded-lg">
        <div class="overflow-auto rounded-lg bg-white"
          style="height: calc(100dvh - 290px); max-height: calc(100dvh - 290px);">
          <table class="table table-hover table-auto max-h-64 overflow-y-auto w-full">
            <thead class="sticky top-0 bg-base-100 z-10 text-textColor-1 uppercase">
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Assigned Sector</th>
                <th class="flex items-center justify-center">Action</th>
              </tr>
            </thead>

            <tbody class="bg-white overflow-y-auto">
              <!-- Loading Message -->
              <tr v-if="userStore.loadingFetchUser">
                <td colspan="6" class="text-center py-20 text-gray-500 text-lg">
                  Loading users...
                </td>
              </tr>

              <!-- Actual Users -->
              <tr v-else v-for="user in userStore.usersList" :key="user.id"
                class="hover:bg-slate-100 transition-colors">
                <th>{{ user.id }}</th>
                <td>{{ user.name }}</td>
                <td>{{ user.email }}</td>
                <td>{{ user.role }}</td>
                <td>{{ user.assigned_sector }}</td>
                <td class="flex items-center justify-center">
                  <div>
                    <Menu as="div" class="relative inline-block text-left">
                      <MenuButton class="cursor-pointer hover:bg-slate-200 rounded-full">
                        <Ellipsis />
                      </MenuButton>
                      <transition enter-active-class="transition duration-100 ease-out"
                        enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                        leave-active-class="transition duration-75 ease-in"
                        leave-from-class="transform scale-100 opacity-100"
                        leave-to-class="transform scale-95 opacity-0">
                        <MenuItems
                          class="absolute def-inner-shadow right-0 mt-2 p-3 w-40 origin-top-right divide-y divide-gray-100 rounded-md bg-white ring-1 z-50 ring-black/5 focus:outline-none">
                          <div class="flex flex-col">
                            <MenuItem v-slot="{ active }">
                            <button
                              :class="[active ? 'bg-btnHover' : 'text-gray-900', 'group flex w-full items-center rounded-md px-2 py-2 text-sm cursor-pointer']"
                              :key="user.id" @click="openUpdateUser(user)">
                              <UserPen size="16" class="mr-2" />
                              <span>Update User</span>
                            </button>
                            </MenuItem>

                            <MenuItem v-slot="{ active }">
                            <button
                              :class="[active ? 'bg-btnHover text-red-700' : 'text-red-700', 'group flex w-full items-center rounded-md px-2 py-2 text-sm cursor-pointer']"
                              :key="user.id" @click="openDeleteConfirm(user)">
                              <UserX size="16" class="mr-2" />
                              <span>Delete User</span>
                            </button>
                            </MenuItem>
                          </div>
                        </MenuItems>
                      </transition>
                    </Menu>
                  </div>
                </td>
              </tr>

              <!-- No Users -->
              <tr v-if="!userStore.loadingFetchUsers && !userStore.usersList.length">
                <td colspan="6" class="text-center py-4">No users found.</td>
              </tr>

              <!-- Error Message -->
              <tr v-if="userStore.fetchUsersError">
                <td colspan="6" class="text-center py-4 text-red-500">
                  {{ userStore.fetchUsersError }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>


    <!-- Recent Activites -->
    <div class="col-span-1 shadow-sm rounded-lg bg-white p-5 space-y-3">
      <h1 class="font-bold text-2xl">Recent Activities</h1>

      <div class="overflow-x-auto">
        <table class="table table-xs table-pin-rows w-full">
          <thead class="font-bold text-textColor-1">
            <tr>
              <td>ID</td>
              <td>Log</td>
              <td>User</td>
              <td>Performed At</td>
            </tr>
          </thead>
          <tbody>
            <!-- Loading -->
            <tr v-if="logStore.fetchLogsLoading">
              <td colspan="4" class="text-center py-20 text-gray-500 text-lg">
                Loading logs...
              </td>
            </tr>

            <!-- Error -->
            <tr v-else-if="logStore.fetchLogsError">
              <td colspan="4" class="text-center py-20 text-red-500 text-lg">
                Error: {{ logStore.fetchLogsError }}
              </td>
            </tr>

            <!-- Logs -->
            <tr v-else v-for="log in [...logStore.logs].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))"
              :key="log.id" class="hover:bg-slate-100 transition-colors">
              <td>{{ log.id }}</td>
              <td>{{ log.description }}</td>
              <td>{{ log.causer?.name || "Unknown" }}</td>
              <td>{{ new Date(log.created_at).toLocaleString() }}</td>
            </tr>

            <!-- No Logs -->
            <tr v-if="
              !logStore.fetchLogsLoading &&
              !logStore.fetchLogsError &&
              logStore.logs.length === 0
            ">
              <td colspan="4" class="text-center py-4">No logs found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>



    <!-- Modals -->

    <!-- Create User Modal -->
    <div>
      <TransitionRoot appear :show="activeModal === 'create'" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-10">
          <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100"
            leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
            <div class="fixed inset-0 bg-black/25" />
          </TransitionChild>

          <div class="fixed inset-0 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
              <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100"
                leave-to="opacity-0 scale-95">

                <DialogPanel
                  class="w-auto transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                  <DialogTitle as="h3">
                    <div class="flex">
                      <h1 class="text-3xl font-bold leading-6 text-textColor-1 flex-1"> Create User</h1>
                      <button type="button" @click="closeModal()" class="cursor-pointer">
                        <X size="20" />
                      </button>
                    </div>
                  </DialogTitle>

                  <form @submit.prevent="createUser" class="pt-3">
                    <!-- Name -->
                    <div class="mt-2 flex flex-col space-y-3">
                      <label class="input w-96" for="name">
                        <ALargeSmall class="size-4" color="#949494" />
                        <input class="grow" id="name" type="text" v-model="name" autocomplete="true"
                          placeholder="Enter full name" required />
                      </label>

                      <!-- Email -->

                      <div>
                        <label class="input w-96" for="email">
                          <AtSign class="size-4" color="#949494" />
                          <input class="grow" id="email" type="email" v-model="email" autocomplete="true"
                            placeholder="Enter email address" required />
                        </label>

                        <p v-if="userStore.createUserError?.email" class="flex text-xs text-red-700 mt-1">
                          <CircleAlert class="mr-2" size="14" />
                          {{ userStore.createUserError.email[0] }}
                        </p>
                      </div>

                      <!-- Password -->
                      <div>
                        <label class="input w-96" for="password">
                          <Lock class="size-4" color="#949494" />
                          <input name="password" id="password" type="password" v-model="password" autocomplete="off"
                            placeholder="Enter password" required />
                        </label>
                        <p v-if="userStore.createUserError?.password" class="flex text-xs text-red-700 mt-1">
                          <CircleAlert class="mr-2" size="14" />
                          {{ userStore.createUserError.password[0] }}
                        </p>
                      </div>

                      <!-- Role -->
                      <label class="input w-96">
                        <ShieldUser class="size-4" color="#949494" />
                        <select id="role" name="role" class="w-full focus:outline-none" v-model="role" required>
                          <option disabled value="">Select Role</option>
                          <option value="admin"> Admin </option>
                          <option value="agency"> Agency </option>
                        </select>
                      </label>

                      <!-- Assigned Sector -->
                      <label v-if="role === 'agency'" class="input w-95" for="assigned_sector">
                        <SquareMousePointer class="size-4" color="#949494" />
                        <select id="assigned_sector" name="assignedSector" class="w-full focus:outline-none"
                          v-model="assignedSector" required>
                          <option disabled value="">Select Sector</option>
                          <option v-for="sector in sectorStore.sectorsList" :key="sector.id" :value="sector.id">
                            {{ sector.name }}
                          </option>
                        </select>
                      </label>

                      <!-- Submit Button -->
                      <button class="btn btn-primary" type="submit" :disabled="loadingCreateUser">
                        {{ loadingCreateUser ? "Creating..." : "Create User" }}
                      </button>
                    </div>

                  </form>

                </DialogPanel>
              </TransitionChild>
            </div>
          </div>
        </Dialog>
      </TransitionRoot>
    </div>

    <!-- Confirm User Delete Modal -->
    <div>
      <TransitionRoot appear :show="activeModal === 'delete'" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-10">
          <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100"
            leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
            <div class="fixed inset-0 bg-black/25" />
          </TransitionChild>

          <div class="fixed inset-0 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
              <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100"
                leave-to="opacity-0 scale-95">

                <DialogPanel
                  class="w-xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                  <DialogTitle as="h3">
                    <div class="flex flex-col justify-center items-center space-y-3">
                      <div class="p-5 bg-red-100 rounded-full">
                        <TriangleAlert size="40" color="#991b1b" />
                      </div>
                      <h1 class="text-lg font-bold leading-6 text-red-800 flex-1"> Are you sure you want to delete this
                        user?</h1>
                    </div>
                  </DialogTitle>

                  <form @submit.prevent="submitDelete" class="pt-3">
                    <div class="mt-2">
                      <p class="text-sm text-gray-500">
                        Deleting this user cannot be undone. Type <span class="font-bold">confirm</span> to delete.
                      </p>
                      <input id="confirmDeleteText" name="confirmDeleteText" v-model="confirmDeleteText" type="text"
                        placeholder="Type confirm" class="mt-2 w-full border rounded px-2 py-1" />
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4 flex justify-end space-x-2">
                      <button @click="closeModal()" class="px-4 py-2 cursor-pointer bg-gray-200 rounded">
                        Cancel
                      </button>
                      <button type="submit" :disabled="confirmDeleteText !== 'confirm'"
                        class="px-4 py-2 bg-red-600 text-white rounded cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                        Delete
                      </button>
                    </div>

                  </form>

                </DialogPanel>
              </TransitionChild>
            </div>
          </div>
        </Dialog>
      </TransitionRoot>
    </div>


    <!-- Toast -->
    <TransitionRoot appear :show="showSuccess" as="template">
      <div class="fixed bottom-5 right-5 z-50">
        <TransitionChild enter="transform ease-in duration-300 transition" enter-from="translate-y-2 opacity-0"
          enter-to="translate-y-0 opacity-100" leave="transform ease-in duration-200 transition"
          leave-from="translate-y-0 opacity-100" leave-to="translate-y-2 opacity-0">
          <div class="bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center space-x-2">
            <CheckCircle class="w-5 h-5" />
            <span>User added successfully!</span>
          </div>
        </TransitionChild>
      </div>
    </TransitionRoot>

    <TransitionRoot appear :show="showDeleteSuccess" as="template">
      <div class="fixed bottom-5 right-5 z-50">
        <TransitionChild enter="transform ease-in duration-300 transition" enter-from="translate-y-2 opacity-0"
          enter-to="translate-y-0 opacity-100" leave="transform ease-in duration-200 transition"
          leave-from="translate-y-0 opacity-100" leave-to="translate-y-2 opacity-0">
          <div class="bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center space-x-2">
            <CheckCircle class="w-5 h-5" />
            <span>User deleted successfully!</span>
          </div>
        </TransitionChild>
      </div>
    </TransitionRoot>

  </div>

</template>