<script setup>
import { onMounted } from 'vue'
import ArrowRight from '@/assets/icons/arrow-r.vue'
import { useSectorStore } from '@/stores/sector.js'

const sectorStore = useSectorStore();

onMounted(() => {
  sectorStore.fetchPublicSectors();
});

</script>

<template>
  <div
    class="w-auto bg-[url('/assets/images/header.png')] bg-cover bg-center flex flex-col items-center justify-center py-5"
    style="min-height: 150px; margin-left: calc(50% - 50vw);">
    <h1
      class="font-leagueSpartan text-5xl pt-4 lg:text-8xl font-extrabold text-shadow-lg uppercase text-white text-center">
      Priority Industries
    </h1>
  </div>

  <!-- Priority Industries Card -->
  <div class="p-5 px-5 md:px-14 lg:px-24 xl:px-36">
    <div class="mt-5 lg:mt-5">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

        <!-- Loading -->
        <div v-if="sectorStore.loadingFetchPublicSectors" class="col-span-full py-10 flex flex-col space-y-2 items-center justify-center">
          <img class="w-32" src="@/assets/loading.gif" alt="Loading">
          <span class="loading loading-spinner loading-xl"></span>
        </div>

        <!-- Error -->
        <div v-else-if="sectorStore.errorFetchPublicSectors" class="col-span-full text-center py-10 text-red-500">
          {{ error }}
        </div>

        <!-- Cards -->
        <div v-else v-for="sector in sectorStore.publicSectorsList" :key="sector.id"
          class="card bg-base-100 shadow-sm rounded-3xl border border-slate-200">
          <figure class="h-40 overflow-hidden">
            <img class="h-full w-full object-cover" src="@/assets/images/food-and-agriculture.webp" alt="Shoes" />
          </figure>
          <div class="card-body">
            <h2 class="text-3xl lg:text-2xl font-bold text-sky-800">
              {{ sector.name }}
            </h2>
            <p>
              {{ sector.description }}
            </p>
            <div class="card-actions justify-end mt-3">
              <router-link :to="`/sectors/${sector.id}`" custom v-slot="{ navigate }">
                <button @click="navigate" class="btn btn-primary rounded-full font-normal btn-sm">
                  View More
                  <ArrowRight />
                </button>
              </router-link>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

</template>