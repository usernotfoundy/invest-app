<script setup>
import { ref, onMounted } from 'vue'
import { useSectorStore } from '@/stores/sector.js'
import { PlusCircle, X, ALargeSmall, CheckCircle, Nut, CircleAlert } from 'lucide-vue-next'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle, Menu } from '@headlessui/vue'

const sectorStore = useSectorStore();
const name = ref("");
const description = ref("");
const showSuccess = ref(false)
const loadingCreateSector = ref(false);
const errorCreateSector = ref(null);

onMounted(() => {
  sectorStore.fetchSectors();
});

const submit = async () => {
  loadingCreateSector.value = true;
  errorCreateSector.value = null;

  try {
    await sectorStore.createSector({
      name: name.value,
      description: description.value,
    });

    closeModal();
    showSuccess.value = true;
    setTimeout(() => {
      showSuccess.value = false;
    }, 5000);

    await sectorStore.fetchSectors();
  } catch (error) {
    console.error(error);
    errorCreateSector.value = error.message;
  } finally {
    loadingCreateSector.value = false;
  }
};


const isOpen = ref(false)

function closeModal() {
  isOpen.value = false;
  name.value = "";
  description.value = "";
}

function openModal() {
  isOpen.value = true
}
</script>

<template>

  <TransitionRoot appear :show="showSuccess" as="template">
    <div class="fixed bottom-5 right-5 z-50">
      <TransitionChild enter="transform ease-in duration-300 transition" enter-from="translate-y-2 opacity-0"
        enter-to="translate-y-0 opacity-100" leave="transform ease-in duration-200 transition"
        leave-from="translate-y-0 opacity-100" leave-to="translate-y-2 opacity-0">
        <div class="bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center space-x-2">
          <CheckCircle class="w-5 h-5" />
          <span>Sector added successfully!</span>
        </div>
      </TransitionChild>
    </div>
  </TransitionRoot>
  <!-- Page Title -->
  <div class="flex items-center py-3">
    <h1 class="font-bold text-5xl flex-1">Manage Sectors</h1>
  </div>

  <!-- Search Bar -->
  <div class="flex">
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

    <!-- Add Sector Button -->
    <div class="flex justify-end">
      <button class="btn btn-primary" @click="openModal">
        <PlusCircle size="20" /> Add Sector
      </button>
    </div>
  </div>

  <div v-if="sectorStore.loadingFetchSectors" class="flex flex-col h-full items-center justify-center"> <span
      class="loading loading-dots loading-md"></span>Loading</div>

  <div v-if="sectorStore.errorFetchSectors" class="text-red-500">{{ sectorStore.errorFetchSectors }}</div>

  <div v-if="sectorStore.sectorsList.length === 0" class=" bg-white mt-5 rounded-lg w-full p-5">
    <div class="flex justify-center text-center space-x-2 items-center">
      <CircleAlert size="16" />
      <p>No sectors yet</p>
    </div>
  </div>

  <!-- Sector Cards -->
  <div v-else class="grid grid-cols-3 gap-4 pt-5">
    <router-link v-for="sector in sectorStore.sectorsList" :key="sector.id" :to="`/admin/manage-sectors/${sector.id}`"
      class="block">
      <div
        class="card bg-base-100 shadow-sm border border-brandSky-8 rounded-xl transition-transform duration-200 hover:scale-105 hover:z-50 hover:border-brandSky-11">
        <div class="card-body flex flex-row items-center">
          <div class="flex flex-1 flex-col">
            <h1 class="text-xl font-bold">{{ sector.name }}</h1>
          </div>
          <div class="w-16 h-16 flex items-center justify-center bg-slate-200 rounded-xl">
            <Nut size="32" />
          </div>
        </div>
      </div>
    </router-link>
  </div>

  <div>
    <TransitionRoot appear :show="isOpen" as="template">
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
                    <h1 class="text-3xl font-bold leading-6 text-textColor-1 flex-1"> Add Sector </h1>
                    <button type="button" @click="closeModal()" class="cursor-pointer">
                      <X size="20" />
                    </button>
                  </div>
                </DialogTitle>

                <form @submit.prevent="submit" class="pt-3">
                  <div class="mt-2 flex flex-col space-y-3">
                    <label class="input w-96">
                      <ALargeSmall class="size-4" color="#949494" />
                      <input type="text" name="name" id="name" required autocomplete="name" v-model="name" class="grow"
                        placeholder="Sector Name" />
                    </label>

                    <p v-if="sectorStore.createSectorError?.name" class="flex text-xs text-red-700 mt-1">
                      <CircleAlert class="mr-2" size="14" />
                      {{ sectorStore.createSectorError.name[0] }}
                    </p>

                    <textarea id="sectorDescription" name="sectorDescription" required v-model="description"
                      class="textarea w-full" placeholder="Description"></textarea>

                  </div>

                  <div class="mt-4 flex justify-end">
                    <button type="submit"
                      class="btn btn-primary w-1/3 inline-flex justify-center rounded-md border border-transparent px-4 py-2 text-sm font-medium">
                      Submit
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
</template>