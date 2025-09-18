<template>
  <!-- Main container with responsive padding and background -->
  <div class="p-5">
    <!-- Outer grid for the entire component, centered on larger screens -->
    <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 items-start">

      <!-- Major Locators Section -->
      <div class="md:col-span-1 lg:col-span-1">
        <h1 class="text-center text-2xl font-bold text-brandSky-8 uppercase mb-4">Major Locators</h1>
        <div class="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 justify-items-center">
          <div v-for="(locator, index) in groupedLocators.major_locator" :key="index"
            class="w-full h-auto flex justify-center items-center">
            <img class="w-24 h-auto" :src="locator.url" :alt="locator.name" />
          </div>
        </div>
      </div>

      <!-- Indicative Locators Section -->
      <div class="md:col-span-1 lg:col-span-2">
        <h1 class="text-center text-2xl font-bold text-brandSky-8 uppercase mb-4">Indicative Locators</h1>
        <div class="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-3 gap-4 justify-items-center">
          <div v-for="(locator, index) in groupedLocators.indicative_locator" :key="index"
            class="w-full h-auto flex justify-center items-center">
            <img class="w-24 h-auto" :src="locator.url" :alt="locator.name" />
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const locators = ref([]);

onMounted(async () => {
  try {
    const res = await fetch("/locators.json");
    locators.value = await res.json();
  } catch (error) {
    console.error("Failed to load data:", error);
  }
});

const groupedLocators = computed(() => {
  const groups = {
    major_locator: [],
    indicative_locator: []
  };

  locators.value.forEach(locator => {
    if (locator.category === 'major_locator') {
      groups.major_locator.push(locator);
    } else if (locator.category === 'indicative_locator') {
      groups.indicative_locator.push(locator);
    }
  });

  return groups;
});
</script>