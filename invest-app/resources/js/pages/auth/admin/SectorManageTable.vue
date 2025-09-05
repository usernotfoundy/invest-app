<script setup>
import { ref, onMounted, watch } from "vue";
import { useRouter, useRoute } from 'vue-router'
import { useSectorStore } from "@/stores/sector.js";
import { useTableStore } from "@/stores/tables.js";
import { ALargeSmall, ArrowLeft, Columns3, Grid2X2Plus, X, Plus, CircleSlash, CheckCircle } from 'lucide-vue-next'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle } from '@headlessui/vue'

const route = useRoute();
const router = useRouter();
const sectorStore = useSectorStore();
const tableStore = useTableStore();

const isOpen = ref(false)
const name = ref("");
const showSuccess = ref(false);

function openModal() {
  isOpen.value = true
}

function closeModal() {
  isOpen.value = false;
}

onMounted(() => {
  if (route.params.id) {
    sectorStore.fetchSectorById(route.params.id);
    tableStore.fetchTableById(route.params.id);
  }
});

const goPreviousPage = () => {
  router.back()
}

// if you can navigate between sector ids without leaving the page
watch(() => route.params.id, (id) => {
  if (id) sectorStore.fetchSectorById(id);
  if (id) tableStore.fetchTableById(id);
});

const createTable = async () => {
  if (!name.value) return; // simple validation

  const success = await tableStore.submitTable({
    sectorId: route.params.id,
    name: name.value,
  });

  if (success) {
    closeModal();

    // ✅ show success alert/toast
    showSuccess.value = true;
    setTimeout(() => {
      showSuccess.value = false;
    }, 5000);

    // optional: refresh list of tables if you have one
    await tableStore.fetchTableById(route.params.id);

    // reset form
    name.value = "";
    tableStore.data_template = [];
  }
};
</script>

<template>
  <!-- Upper Content (Buttons) -->
  <div class="flex ">
    <div class="flex-1">
      <button @click="goPreviousPage"
        class="flex transition-transform duration-200 hover:scale-105 items-center space-y-3 rounded-lg p-2 cursor-pointer">
        <ArrowLeft size="16" class="mr-5" />
        Back
      </button>
    </div>

    <div class="flex justify-end">
      <button class="btn btn-primary rounded-lg" @click="openModal">
        <Grid2X2Plus size="20" /> Create Table
      </button>
    </div>
  </div>

  <div v-if="tableStore.loadingFetchTable"
    class="col-span-full py-10 flex flex-col h-full space-y-2 items-center justify-center">
    <img class="w-24" src="@/assets/loading.gif" alt="Loading">
    <span class="loading loading-spinner loading-xl"></span>
  </div>

  <div v-else-if="tableStore.errorFetchTable" class="text-red-600">{{ tableStore.errorFetchTable }}</div>

  <!-- Main Content (Table) -->
  <div v-else class="p-10 bg-white w-full rounded-lg shadow-sm mt-5 max-h-xl overflow-y-auto">
    <div class="mb-5">
      <h1 class="font-bold text-3xl">Configure Fields</h1>
      <p>Hide or unhide fields here.</p>
    </div>

    <div v-if="tableStore.children.length > 0" class="w-full">
      <fieldset v-for="child in tableStore.children" :key="child.id"
        class="fieldset border-base-300 rounded-box border p-4 w-full mt-10">
        <legend class="fieldset-legend text-lg">{{ child.name }}</legend>

        <!-- Loop through data_template -->
        <div v-if="child.data_template && child.data_template.length > 0" class="grid grid-cols-3 gap-4">
          <div v-for="field in child.data_template" :key="field"
            class="card card-border rounded-xl border-slate-300 w-full">
            <div class="card-body">
              <div class="flex items-center justify-between">
                <h1 class="text-lg font-semibold">{{ field }}</h1>
                <input type="checkbox" checked class="toggle" />
              </div>
            </div>
          </div>
          <div>
            <button class="btn btn-ghost border-0 hover:bg-transparent mt-5">
              <Plus size="16" /> Add Column
            </button>
          </div>
        </div>


        <div v-else class="text-gray-400 italic mt-2">No template fields</div>
      </fieldset>

    </div>

    <div v-else class="text-gray-400 italic">No tables available.</div>
  </div>


  <!-- Modals -->
  <div>
    <TransitionRoot appear :show="isOpen" as="template">
      <Dialog as="div" @close="() => { }" class="relative z-10">
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
                class="w-lg transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                <DialogTitle as="h3">
                  <div class="flex">
                    <h1 class="text-3xl font-bold leading-6 text-textColor-1 flex-1"> Create Table </h1>
                    <button type="button" @click="closeModal()" class="cursor-pointer">
                      <X size="20" />
                    </button>
                  </div>
                </DialogTitle>

                <!-- Form -->
                <form @submit.prevent="createTable" class="pt-3">
                  <div class="mt-2 flex flex-col space-y-3">
                    <label class="input w-full" for="name">
                      <ALargeSmall class="size-4" color="#949494" />
                      <input class="grow" id="name" type="text" v-model="name" placeholder="Table Name" required />
                    </label>
                  </div>

                  <div class="mt-2 flex space-y-3 space-x-3">

                    <div class="flex-1">
                      <label class="input w-full" for="currentInput">
                        <Columns3 class="size-4" color="#949494" />
                        <input class="grow" id="currentInput" type="text" v-model="tableStore.currentInput"
                          placeholder="Column Name" />
                      </label>
                    </div>

                    <div class="tooltip" data-tip="Add Column">
                      <button type="button" @click="tableStore.addInput"
                        class="btn btn-primary rounded-lg text-white px-4 py-2"
                        :disabled="!tableStore.currentInput.trim()">
                        <Plus size="16" />
                      </button>
                    </div>

                  </div>

                  <!-- Badges -->
                  <div v-if="tableStore.data_template.length > 0" class="flex flex-wrap gap-2 mt-4">
                    <div v-for="(badge, index) in tableStore.data_template" :key="index"
                      class="badge badge-primary flex items-center gap-2">
                      {{ badge }}
                      <div class="tooltip" data-tip="Remove Column">
                        <button type="button" class="cursor-pointer rounded-full p-1"
                          @click="tableStore.removeInput(index)">
                          <X size="12" stroke-width="3" color="#FFFFFF" />
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- Empty state -->
                  <div class="flex items-center" v-else>
                    <CircleSlash size="16" color="#94a3b8" />
                    <p class="text-slate-400 pl-3"> No columns to add</p>
                  </div>

                  <div class="mt-4 flex justify-end">
                    <button type="submit"
                      class="btn btn-primary w-1/3 inline-flex justify-center rounded-md border border-transparent px-4 py-2 text-sm font-medium"
                      @click="submit">
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

  <!-- Toast -->
  <TransitionRoot appear :show="showSuccess" as="template">
    <div class="fixed bottom-5 right-5 z-50">
      <TransitionChild enter="transform ease-in duration-300 transition" enter-from="translate-y-2 opacity-0"
        enter-to="translate-y-0 opacity-100" leave="transform ease-in duration-200 transition"
        leave-from="translate-y-0 opacity-100" leave-to="translate-y-2 opacity-0">
        <div class="bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center space-x-2">
          <CheckCircle class="w-5 h-5" />
          <span>Table created successfully!</span>
        </div>
      </TransitionChild>
    </div>
  </TransitionRoot>
</template>