<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

// Create reactive variables to store the data
const crimeEfficiency = ref(null);
const povertyIncidence = ref(null);
const literacyRate = ref(null);
const yearRange = ref('');

// A function to fetch and process data from a single endpoint
const fetchData = async (url) => {
  try {
    const res = await axios.get(url);
    return res.data;
  } catch (err) {
    console.error(`Failed to load data from ${url}`, err);
    return null;
  }
};

onMounted(async () => {
  // Fetch all three APIs concurrently using Promise.all
  const [crimeRes, povertyRes, literacyRes] = await Promise.all([
    fetchData('/api/inprofile/crimeEfficiency'),
    fetchData('/api/inprofile/povertyIncidence'),
    fetchData('/api/inprofile/literacyRate'),
  ]);

  // Process Crime Efficiency data to get the latest year's value
  if (crimeRes && crimeRes.data && crimeRes.data.length > 0) {
    const latestCrimeData = crimeRes.data.reduce((latest, current) =>
      current.year > latest.year ? current : latest,
    );
    crimeEfficiency.value = { value: latestCrimeData.rate.toFixed(0), label: 'Crime Efficiency', unit: '%' };
  }

  // Process Poverty Incidence data to get the latest year's value
  if (povertyRes && povertyRes.data && povertyRes.data.length > 0) {
    const latestPovertyData = povertyRes.data.reduce((latest, current) =>
      current.year > latest.year ? current : latest,
    );
    povertyIncidence.value = { value: latestPovertyData.estimates.toFixed(1), label: 'Poverty Incidence', unit: '%' };
  }

  // Process Literacy Rate data (already only one entry, so it's the latest)
  if (literacyRes && literacyRes.data && literacyRes.data.length > 0) {
    const data = literacyRes.data[0];
    const average = (data['simple literacy rate'] + data['functional literacy rate']) / 2;
    literacyRate.value = { value: average.toFixed(1), label: 'Literacy Rate', unit: '%' };
  }

  // Determine the year range from the available data (no changes needed here)
  const allYears = [
    ...(crimeRes?.data.map(item => item.year) || []),
    ...(povertyRes?.data.map(item => item.year) || []),
    ...(literacyRes?.data.map(item => item.year) || []),
  ].filter(Boolean).sort();

  if (allYears.length > 0) {
    const minYear = allYears[0];
    const maxYear = allYears[allYears.length - 1];
    yearRange.value = `Data from ${minYear} - ${maxYear}`;
  }
});
</script>

<template>
  <div class="flex flex-col md:flex-row justify-around lg:flex-col text-center w-full">
    <div v-if="crimeEfficiency" class="flex flex-col items-center justify-center p-4">
      <h1 class="font-bold text-3xl text-brandSky-8">{{ crimeEfficiency.value }}{{ crimeEfficiency.unit }}</h1>
      <p class="text-gray-600">{{ crimeEfficiency.label }}</p>
    </div>

    <div v-if="povertyIncidence" class="flex flex-col items-center justify-center p-4">
      <h1 class="font-bold text-3xl text-brandSky-8">{{ povertyIncidence.value }}{{ povertyIncidence.unit }}</h1>
      <p class="text-gray-600">{{ povertyIncidence.label }}</p>
    </div>

    <div v-if="literacyRate" class="flex flex-col items-center justify-center p-4">
      <h1 class="font-bold text-3xl text-brandSky-8">{{ literacyRate.value }}{{ literacyRate.unit }}</h1>
      <p class="text-gray-600">{{ literacyRate.label }}</p>
    </div>
  </div>
  <div class="mt-4 text-center text-sm text-gray-500">
    {{ yearRange }}
  </div>
</template>