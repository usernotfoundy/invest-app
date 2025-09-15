<script setup>
import { ref, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useSectorStore } from "@/stores/sector";
import { useTableStore } from "@/stores/tables";
import { Cog, Ellipsis, ArrowLeft, Grid2X2X, Pencil, X, ALargeSmall, CircleAlert, CheckCircle } from "lucide-vue-next";
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle, Menu } from '@headlessui/vue'

const route = useRoute();
const router = useRouter();
const sectorStore = useSectorStore();
const tableStore = useTableStore();

const selectedChildId = ref(null);
const updateModal = ref(false);
const name = ref("");
const description = ref("");
const showSuccessUpdate = ref(false);
const loadingUpdateSector = ref(false);
const errorUpdateSector = ref(null);
const copied = ref(false);

const goPreviousPage = () => {
  router.back()
}

function openModal() {
  updateModal.value = true;
}

function closeModal() {
  updateModal.value = false;
  name.value = "";
  description.value = "";
}

function copyCode() {
  const code = document.getElementById("installCmd").innerText;
  navigator.clipboard.writeText(code).then(() => {
    copied.value = true;
    setTimeout(() => (copied.value = false), 1500);
  });
}

onMounted(async () => {
  if (route.params.id) {
    await sectorStore.fetchSectorById(route.params.id);
    await tableStore.fetchTableById(route.params.id);
  }

  if (tableStore.children.length > 0) {
    selectedChildId.value = tableStore.children[0].id;
  }
});

// if you can navigate between sector ids without leaving the page
watch(
  () => route.params.id,
  async (id) => {
    if (id) {
      await sectorStore.fetchSectorById(id);
      await tableStore.fetchTableById(id);

      if (tableStore.children.length > 0) {
        selectedChildId.value = tableStore.children[0].id;
      } else {
        selectedChildId.value = null;
      }
    }
  }
);

// Watch modal -> prefill form values when opened
watch(
  () => updateModal.value,
  (isOpen) => {
    if (isOpen && sectorStore.sector) {
      name.value = sectorStore.sector.name || "";
      description.value = sectorStore.sector.description || "";
    }
  }
);

// Submit handler
const submit = async () => {
  loadingUpdateSector.value = true;
  errorUpdateSector.value = null;

  try {
    const updated = await sectorStore.updateSector(sectorStore.sector.id, {
      name: name.value,
      description: description.value,
    });

    // also update local refs so modal is synced
    name.value = updated.name;
    description.value = updated.description;

    closeModal();
    showSuccessUpdate.value = true;
    setTimeout(() => (showSuccessUpdate.value = false), 5000);

    await sectorStore.fetchSectors(); // refresh list
  } catch (error) {
    errorUpdateSector.value = sectorStore.updateError;
  } finally {
    loadingUpdateSector.value = false;
  }
};
</script>

<template>



  <!-- Loading -->
  <div v-if="sectorStore.loading"
    class="col-span-full py-10 flex flex-col h-full space-y-2 items-center justify-center">
    <img class="w-24" src="@/assets/loading.gif" alt="Loading">
    <span class="loading loading-spinner loading-xl"></span>
  </div>

  <!-- Error Message -->
  <div v-else-if="sectorStore.error" class="text-red-600">{{ sectorStore.error }}</div>

  <!-- Main Content -->
  <div v-else-if="sectorStore.sector">

    <div class="grid grid-cols-2 h-36">
      <!-- Left side -->
      <div class="h-24 tool items-center space-x-5 py-5">
        <button @click="goPreviousPage"
          class="flex transition-transform duration-200 hover:scale-105 items-center space-y-3 p-2 cursor-pointer">
          <ArrowLeft size="16" class="mr-5" />
          Back
        </button>
        <div class="flex items-center space-x-5">
          <h1 class="text-4xl font-bold">{{ sectorStore.sector.name }}</h1>
          <div class="items-center justify-center tooltip tooltip-primary" data-tip="Update Sector">
            <button class="btn btn-sm rounded-full btn-primary" @click="openModal">
              <Pencil size="16" />
            </button>
          </div>
        </div>
      </div>

      <!-- Right side: Code Mockup -->
      <div class="flex justify-end">
        <div class="relative font-mono text-sm text-slate-800 w-fit max-w-xl">
          <!-- Code -->
          <div class="space-y-4 p-5 rounded-lg bg-white">
            <div class="flex flex-col">
              <span class="text-slate-500">Get data URL</span>
              <code id="copyGetData">http://localhost:8000/api/get-data/1</code>
            </div>
            <div class="flex flex-col">
              <span class="text-slate-500">Power BI URL</span>
              <code id="copyPowerBI">http://localhost:8000/api/get-data/1</code>
            </div>
          </div>

          <!-- Copy Button -->
          <!--
    <button @click="copyCode"
      class="absolute top-2 right-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs px-3 py-1 rounded-md transition">
      {{ copied ? 'Copied!' : 'Copy Get Data' }}
    </button>
    -->
        </div>
      </div>


    </div>

    <div class="space-y-3 pt-3">

      <div class="flex w-full">
        <!-- Dropdown to choose child -->
        <div v-if="tableStore.children.length > 0" class="flex-1">
          <select v-model="selectedChildId" class="select select-sm select-bordered">
            <option v-for="child in tableStore.children" :key="child.id" :value="child.id">
              {{ child.name }} Table
            </option>
          </select>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end">
          <router-link :to="`/admin/manage-sectors/manage-table/${sectorStore.sector.id}`"
            class="btn btn-warning rounded-lg font-normal">
            <Cog size="16" />
            Configure Table
          </router-link>
        </div>
      </div>

      <div v-if="tableStore.loadingFetchTable"
        class="col-span-full py-10 flex flex-col h-full space-y-2 items-center justify-center">
        <img class="w-24" src="@/assets/loading.gif" alt="Loading">
        <span class="loading loading-spinner loading-xl"></span>
      </div>

      <div v-else-if="tableStore.errorFetchTable" class="text-red-600">
        {{ tableStore.errorFetchTable }}
      </div>

      <!-- Table Section -->
      <div v-else>
        <div v-if="tableStore.children.length > 0" class="w-full">

          <!-- Show selected child's table -->
          <div v-for="child in tableStore.children" :key="child.id" class="mt-5">
            <div v-if="selectedChildId === child.id"
              class="overflow-x-auto bg-white rounded-xl shadow-sm border border-slate-200"
              style="max-height: calc(100dvh - 250px);">

              <table class="table table-auto w-full min-w-max">
                <thead class="sticky top-0 bg-base-100 z-10 text-textColor-1 lowercase">
                  <tr>
                    <th v-for="field in child.data_template" :key="field" class="px-3 py-2 text-left">{{ field }}</th>
                  </tr>
                </thead>

                <tbody class="bg-white">
                  <tr v-for="(row, idx) in child.data" :key="idx" class="hover:bg-slate-100 transition-colors">
                    <td v-for="field in child.data_template" :key="field" class="px-3 py-2">
                      {{ row[field] ?? '-' }}
                    </td>
                  </tr>

                  <!-- If no rows yet -->
                  <tr v-if="!child.data || child.data.length === 0">
                    <td :colspan="child.data_template.length" class="text-center py-4 text-gray-500">
                      No data yet
                    </td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>

        <div v-else class="flex items-center justify-center rounded-xl py-10 bg-white text-center">
          <Grid2X2X class="mr-2" />
          No child tables found for this sector.
        </div>
      </div>

    </div>
  </div>

  <!-- Modals -->
  <div>
    <TransitionRoot appear :show="updateModal" as="template">
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
                    <h1 class="text-3xl font-bold leading-6 text-textColor-1 flex-1"> Update Sector </h1>
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
                      class="textarea w-full h-40" placeholder="Description"></textarea>

                  </div>

                  <div class="mt-4 flex justify-end">
                    <button type="submit"
                      class="btn btn-primary w-1/3 inline-flex justify-center rounded-md border border-transparent px-4 py-2 text-sm font-medium"
                      @click="submit">
                      Update
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
  <TransitionRoot appear :show="showSuccessUpdate" as="template">
    <div class="fixed bottom-5 right-5 z-50">
      <TransitionChild enter="transform ease-in duration-300 transition" enter-from="translate-y-2 opacity-0"
        enter-to="translate-y-0 opacity-100" leave="transform ease-in duration-200 transition"
        leave-from="translate-y-0 opacity-100" leave-to="translate-y-2 opacity-0">
        <div class="bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center space-x-2">
          <CheckCircle class="w-5 h-5" />
          <span>Sector updated successfully!</span>
        </div>
      </TransitionChild>
    </div>
  </TransitionRoot>

  <!-- <TransitionRoot appear :show="showDeleteSuccess" as="template">
    <div class="fixed bottom-5 right-5 z-50">
      <TransitionChild enter="transform ease-in duration-300 transition" enter-from="translate-y-2 opacity-0"
        enter-to="translate-y-0 opacity-100" leave="transform ease-in duration-200 transition"
        leave-from="translate-y-0 opacity-100" leave-to="translate-y-2 opacity-0">
        <div class="bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center space-x-2">
          <CheckCircle class="w-5 h-5" />
          <span>Sector updated successfully!</span>
        </div>
      </TransitionChild>
    </div>
  </TransitionRoot> -->
</template>
