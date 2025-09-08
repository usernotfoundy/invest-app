<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { Upload, Download, Plus, X, Ellipsis, Grid2X2X, CheckCircle, UserX, UserPen, CircleAlert, Pen, Pencil, Trash } from 'lucide-vue-next';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle, Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { useTableStore } from '@/stores/tables.js'
import { useUserStore } from '@/stores/user.js'

const tableStore = useTableStore();
const userStore = useUserStore();

const selectedChildId = ref(null);
const childForms = ref({})
const showSuccessSubmit = ref(false)
const loadingSubmit = ref(false)
const errorSubmit = ref(null)
const isInputDataOpen = ref(false)
const isActionOpen = ref(false)
const isUploadModalOpen = ref(false);
const selectedFile = ref(null);

const onFileChange = (e) => {
  selectedFile.value = e.target.files[0]
}

const onDrop = (e) => {
  e.preventDefault()
  selectedFile.value = e.dataTransfer.files[0]
}

function closeInputDataModal() {
  isInputDataOpen.value = false

  Object.keys(childForms.value).forEach(childId => {
    Object.keys(childForms.value[childId]).forEach(field => {
      childForms.value[childId][field] = ""
    })
  })
}

function closeUploadModal() {
  isUploadModalOpen.value = false;
  selectedFile.value = null;
}

function openInputDataModal() {
  isInputDataOpen.value = true
}

function openUpdateModal() {
  isUploadModalOpen.value = true
}

function openActionModal() {
  isActionOpen.value = true
}

const submit = async (childId) => {
  loadingSubmit.value = true
  errorSubmit.value = null

  try {
    await tableStore.submitChild(childId, childForms.value)

    closeInputDataModal();
    showSuccessSubmit.value = true
    setTimeout(() => (showSuccessSubmit.value = false), 5000)

    await tableStore.fetchTableByAssignedSector()
  } catch (error) {
    errorSubmit.value = error.message || "Failed to submit data"
  } finally {
    loadingSubmit.value = false
  }
}

const selectedChildName = computed(() => {
  const child = tableStore.children.find(c => c.id === selectedChildId.value);
  return child ? child.name : "";
});

const handleDownload = async () => {
  if (selectedChildId.value && selectedChildName.value) {
    await tableStore.downloadTemplate(
      selectedChildId.value,
      selectedChildName.value
    );
  }
};

const handleUpload = async () => {
  if (!selectedFile.value) return;

  try {
    await tableStore.uploadTemplate(
      selectedFile.value,
      userStore.user.assigned_sector,
      selectedChildId.value
    );

    console.log("Upload success"); //To remove
    closeUploadModal();

    await tableStore.fetchTableByAssignedSector();

  } catch (err) {
    console.error("Upload failed", err);
  }
};


onMounted(async () => {
  try {
    if (!userStore.user) {
      await userStore.fetchUserProfile()
    }

    await tableStore.fetchTableByAssignedSector()

    if (tableStore.children.length > 0) {
      selectedChildId.value = tableStore.children[0].id;
    } else {
      selectedChildId.value = null;
    }
  } catch (error) {
    console.error("Failed to load data:", error);
    // The error will be stored in tableStore.errorFetchTable
  }
})

watch(
  () => tableStore.children,
  (children) => {
    childForms.value = {}
    children.forEach(child => {
      // Ensure each child has its own values object
      childForms.value[child.id] = {}

      child.data_template.forEach(field => {
        // Pre-fill with empty or existing values if present
        childForms.value[child.id][field] = child.data?.[field] ?? ""
      })
    })
  },
  { immediate: true }
)
</script>

<template>
  <div>
    <h1 class="font-bold text-3xl p-2">Manage Data</h1>
    <div class="flex flex-row space-x-3">

      <select v-if="tableStore.children.length > 0" v-model="selectedChildId"
        class="select rounded-lg w-auto focus:outline-none focus:ring-0">
        <option v-for="child in tableStore.children" :key="child.id" :value="child.id">
          {{ child.name }} Table
        </option>
      </select>


      <select v-else id="noTableFound" name="noTableFound" class="select rounded-lg w-auto">
        <option disabled selected>No Table Found</option>
      </select>

      <button @click="openUpdateModal()" class="btn btn-dash border-slate-500 hover:bg-transparent hover:border-dashed">
        <Upload :size="16" :stroke-width="2" />
        Upload
      </button>

      <button class="btn btn-dash hover:bg-transparent hover:border-dashed"
        :disabled="!selectedChildId" @click="handleDownload(selectedChildId, selectedChildName)">
        <Download :size="16" :stroke-width="2" />
        Template
      </button>

      <button class="btn btn-primary" :disabled="!selectedChildId" @click="openInputDataModal()">
        <Plus :size="16" :stroke-width="2" />
        Input Data
      </button>

    </div>

    <!-- Table -->
    <div v-if="tableStore.loadingFetchTable"
      class="col-span-full py-10 flex flex-col h-full space-y-2 items-center justify-center">
      <img class="w-24" src="@/assets/loading.gif" alt="Loading">
      <span class="loading loading-spinner loading-xl"></span>
    </div>

    <div v-else-if="tableStore.errorFetchTable" class="text-red-600">
      {{ tableStore.errorFetchTable }}
    </div>

    <div v-else class="mt-5">
      <div v-if="tableStore.children.length > 0" class="w-full">

        <!-- Show selected child's table -->
        <div v-for="child in tableStore.children" :key="child.id" class="mt-5">
          <div v-if="selectedChildId === child.id"
            class="overflow-x-auto bg-white rounded-xl shadow-sm border border-slate-200"
            style=" height: calc(100dvh - 150px);"> <!-- dynamic height, adjust 150px for headers/spacing -->

            <table class="table table-hover table-auto w-full">
              <thead class="sticky top-0 bg-base-100 z-10 text-textColor-1 uppercase">
                <tr>
                  <th v-for="field in child.data_template" :key="field" class="lowercase">{{ field }}</th>
                  <th class="flex items-center justify-center lowercase">Action</th>
                </tr>
              </thead>

              <tbody class="bg-white overflow-y-auto">
                <!-- If there’s existing data -->
                <tr v-for="row in child.data" :key="row.id" class="hover:bg-slate-100 transition-colors">
                  <td v-for="field in child.data_template" :key="field">
                    {{ row[field] ?? '-' }}
                  </td>

                  <!-- Actions Menu -->
                  <td class="flex items-center justify-center">
                    <div>
                      <Menu as="div" class="relative inline-block text-left">
                        <MenuButton class="cursor-pointer hover:bg-slate-200 rounded-full">
                          <Ellipsis />
                        </MenuButton>

                        <transition enter-active-class="transition duration-100 ease-out"
                          enter-from-class="transform scale-95 opacity-0"
                          enter-to-class="transform scale-100 opacity-100"
                          leave-active-class="transition duration-75 ease-in"
                          leave-from-class="transform scale-100 opacity-100"
                          leave-to-class="transform scale-95 opacity-0">
                          <MenuItems
                            class="absolute def-inner-shadow right-0 mt-2 p-3 w-40 origin-top-right divide-y divide-gray-100 rounded-md bg-white ring-1 z-[9999] ring-black/5 focus:outline-none">
                            <div class="flex flex-col">
                              <MenuItem v-slot="{ active }">
                              <button :data-id="row.id" :class="[
                                active ? 'bg-btnHover' : 'text-gray-900',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm cursor-pointer'
                              ]">
                                <Pencil size="16" class="mr-2" />
                                <span>Update</span>
                              </button>
                              </MenuItem>

                              <MenuItem v-slot="{ active }">
                              <button :data-id="row.id" :class="[
                                active ? 'bg-btnHover text-red-700' : 'text-red-700',
                                'group flex w-full items-center rounded-md px-2 py-2 text-sm cursor-pointer'
                              ]">
                                <Trash size="16" class="mr-2" />
                                <span>Delete</span>
                              </button>
                              </MenuItem>
                            </div>
                          </MenuItems>
                        </transition>
                      </Menu>
                    </div>
                  </td>
                </tr>

                <!-- If no rows yet -->
                <tr v-if="!child.data || child.data.length === 0">
                  <td :colspan="child.data_template.length + 1" class="text-left py-4 text-gray-500">
                    <div class="flex justify-start space-x-2 items-center">
                      <CircleAlert size="16" />
                      <p>No data yet</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div v-else class="flex items-center justify-center rounded-xl py-10 bg-white text-center ">
        <Grid2X2X class="mr-2" />
        No tables found for this sector.
      </div>
    </div>

  </div>

  <!-- Modals -->
  <div>

    <!-- Input Data Modal -->
    <TransitionRoot appear :show="isInputDataOpen" as="template">
      <Dialog as="div" @close="closeInputDataModal" class="relative z-10">
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
                class="w-2xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                <DialogTitle as="h3">

                  <div class="flex">
                    <h1 class="text-3xl font-bold leading-6 text-textColor-1 flex-1"> Input Data </h1>
                    <button type="button" @click="closeInputDataModal()" class="cursor-pointer">
                      <X size="20" />
                    </button>
                  </div>
                </DialogTitle>

                <div v-for="child in tableStore.children" :key="child.id" class="mt-5">
                  <div v-if="selectedChildId === child.id">
                    <form @submit.prevent="submit(child.id)" class="pt-3">
                      <div class="grid grid-cols-4 mt-2 space-y-3 space-x-3">
                        <fieldset v-for="field in child.data_template" :key="field" class="fieldset">
                          <legend class="fieldset-legend">{{ field }}</legend>
                          <input :id="`name-${field}`" :name="`name-${field}`" type="text" class="input"
                            v-model="childForms[child.id][field]" />
                        </fieldset>
                      </div>
                      <div class="mt-4 flex justify-end">
                        <button type="submit"
                          class="btn btn-primary w-1/3 inline-flex justify-center rounded-md border border-transparent px-4 py-2 text-sm font-medium"
                          :disabled="loadingSubmit">
                          <span v-if="loadingSubmit">Submitting...</span>
                          <span v-else>Submit</span>
                        </button>
                      </div>

                      <p v-if="errorSubmit" class="text-red-500 mt-2">{{ errorSubmit }}</p>
                    </form>
                  </div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Upload Modal -->
    <TransitionRoot appear :show="isUploadModalOpen" as="template">
      <Dialog as="div" @close="closeUploadModal" class="relative z-10">
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
                class="w-2xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                <DialogTitle as="h3">
                  <div class="flex">
                    <h1 class="text-3xl font-bold leading-6 text-textColor-1 flex-1">Upload File</h1>
                    <button type="button" @click="closeUploadModal()" class="cursor-pointer">
                      <X size="20" />
                    </button>
                  </div>
                </DialogTitle>

                <!-- Upload Content -->
                <div
                  class="mt-6 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg p-8 cursor-pointer hover:border-brandSky-4 transition"
                  @dragover.prevent @drop="onDrop">
                  <input id="file-upload" type="file" class="hidden" @change="onFileChange" />
                  <label for="file-upload" class="text-center cursor-pointer">
                    <p class="text-gray-600">
                      Drag a file here, or <span class="text-brandSky-6 underline">click to browse</span>
                    </p>
                  </label>
                </div>

                <div v-if="selectedFile" class="mt-3 text-sm text-gray-700">
                  Selected: <strong>{{ selectedFile.name }}</strong>
                </div>

                <!-- Action buttons -->
                <div class="mt-6 flex justify-end gap-3">
                  <div class="mt-6 flex justify-end gap-3">
                    <button class="btn btn-primary px-4 py-2 rounded-lg text-white" :disabled="!selectedFile"
                      @click="handleUpload">
                      Upload
                    </button>
                  </div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

  </div>

  <!-- Toast -->
  <div>
    <TransitionRoot appear :show="showSuccessSubmit" as="template">
      <div class="fixed bottom-5 right-5 z-50">
        <TransitionChild enter="transform ease-in duration-300 transition" enter-from="translate-y-2 opacity-0"
          enter-to="translate-y-0 opacity-100" leave="transform ease-in duration-200 transition"
          leave-from="translate-y-0 opacity-100" leave-to="translate-y-2 opacity-0">
          <div class="bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center space-x-2">
            <CheckCircle class="w-5 h-5" />
            <span>Table updated successfully!</span>
          </div>
        </TransitionChild>
      </div>
    </TransitionRoot>
  </div>
</template>