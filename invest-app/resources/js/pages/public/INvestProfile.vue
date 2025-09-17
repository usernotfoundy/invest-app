<script setup>

import { ref, onMounted } from 'vue'
import { ZoomImg } from "vue3-zoomer";
import { ArrowRightLeft } from 'lucide-vue-next';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue';
import PopulationChart from '@/components/charts/PopulationChart.vue'
import AverageFamilyIncomeChart from '@/components/charts/AverageFamilyIncomeChart.vue'
import PurchasingPowerChart from '@/components/charts/PurchasingPower.vue';
import AverageFamilyExpenditure from '@/components/charts/AveFamilyExpenditureChart.vue'
import INProfileKPI from '@/components/KPI.vue'
// import DrySeason from '@/assets/images/dry-season.webp'
// import WetSeason from '@/assets/images/wet-season.webp'
// import Climate from '@/assets/images/climate.webp'
// import INMap from '@/assets/images/in-map.webp'

const FloodMap2019 = "/assets/images/maps/flood_map_2019.png"
const MunicipalModal = ref(false);
const municipalities = ref([]);
const hazardMaps = ref([]);

const selectedMunicipality = ref(null)
const selectedMap = ref(null)

function openMunicipalModal() {
  MunicipalModal.value = true;
}

const selectMunicipality = (municipality, closeFn) => {
  selectedMunicipality.value = municipality;
  closeFn()
}

onMounted(async () => {
  try {
    // Fetch municipalities
    const res1 = await fetch("/municipalities.json")
    municipalities.value = await res1.json()

    // Default select Laoag City
    selectedMunicipality.value = municipalities.value.find(
      (m) => m.name.toLowerCase() === "laoag city"
    )

    // Fetch hazard maps
    const res2 = await fetch("/maps.json")
    hazardMaps.value = await res2.json()

    // ✅ Default select the Flood map
    selectedMap.value =
      hazardMaps.value.find((m) =>
        m.name.toLowerCase().includes("flood")
      ) || hazardMaps.value[0] // fallback to first if not found
  } catch (err) {
    console.error("Failed to load data:", err)
  }
})

</script>

<template>
  <!-- Banner (full width) -->
  <div
    class="w-auto bg-[url('@/assets/images/header.png')] bg-cover bg-center flex flex-col items-center justify-center py-5"
    style="min-height: 150px; margin-left: calc(50% - 50vw);">
    <img src="@/assets/images/in-logo.webp" class="w-18 lg:w-24" alt="Ilocos Norte Seal" />
    <h1 class="font-leagueSpartan text-3xl pt-2 lg:text-7xl font-extrabold text-shadow-lg uppercase text-white">
      Ilocos Norte
    </h1>
  </div>

  <div class="p-5 md:px-14 lg:px-24 xl:px-36 min-h-screen">
    <div class="grid grid-cols-4 gap-2 lg:grid-rows-[auto]">

      <!-- Row 1: Capital, Land Area, Coastline -->
      <div
        class="relative col-span-4 sm:col-span-4 lg:col-span-2 rounded-lg bg-slate-100 flex flex-col items-center p-5 justify-center transition">
        <!-- Rotating Arrow Button -->
        <!-- <button @click="openMunicipalModal"
          class="absolute top-4 right-4 p-2 bg-brandSky-5 text-white rounded-full shadow hover:bg-brandSky-3 transition-transform transform hover:rotate-90">
          <ArrowRightLeft Right class="w-3 h-3" />
        </button> -->
        <Popover v-slot="{ open, close }">
          <PopoverButton :class="open ? 'text-white' : 'text-white/90'"
            class="absolute top-4 right-4 p-2 bg-brandSky-5 text-white rounded-full shadow hover:bg-brandSky-6 transition-transform transform outline-none">
            <ArrowRightLeft Right class="w-3 h-3" />
          </PopoverButton>

          <transition enter-active-class="transition duration-200 ease-out" enter-from-class="translate-y-1 opacity-0"
            enter-to-class="translate-y-0 opacity-100" leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-1 opacity-0">
            <PopoverPanel
              class="absolute left-1/2 z-10 mt-3 w-screen max-w-sm -translate-x-1/2 transform px-4 sm:px-0 lg:max-w-3xl">
              <div class="overflow-hidden rounded-lg shadow-lg ring-1 ring-black/5">
                <div class="relative grid gap-8 bg-white p-7 h-72 overflow-y-auto">

                  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                    <div v-for="(municipality, index) in municipalities" :key="index"
                      @click="selectMunicipality(municipality, close)"
                      class="flex flex-col justify-center items-center p-2 rounded-lg transition">
                      <img class="w-12 mb-2" :src="municipality.seal" :alt="municipality.name + ' Seal'" />
                      <p class="text-gray-800 font-medium">{{ municipality.name }}</p>
                    </div>
                  </div>

                </div>
              </div>
            </PopoverPanel>
          </transition>
        </Popover>

        <!-- Card Content -->
        <img :src="selectedMunicipality ? selectedMunicipality.seal : ''"
          :alt="selectedMunicipality ? selectedMunicipality.name + ' Seal' : 'No Seal'" class="w-16 mb-3" />
        <h1 class="text-3xl font-bold text-sky-800 leading-5 uppercase">{{ selectedMunicipality ?
          selectedMunicipality.name : '-' }}</h1>
        <h2 class="text-lg lg:text-xl text-gray-700 mt-1 uppercase leading-tight"> {{ selectedMunicipality ?
          selectedMunicipality.type : '-' }} </h2>
      </div>

      <div
        class="col-span-2 sm:col-span-2 lg:col-span-1 rounded-lg bg-slate-100 flex flex-col items-center p-10 justify-center">
        <h1 class="text-3xl lg:text-5xl font-extrabold text-sky-800">
          {{ selectedMunicipality ? selectedMunicipality.district : '-' }}
        </h1>
        <h2 class="text-sm lg:text-lg text-gray-700 mb-1 uppercase leading-tight">District</h2>
      </div>

      <div
        class="col-span-2 sm:col-span-2 lg:col-span-1 rounded-lg bg-slate-100 flex flex-col items-center p-10 justify-center">
        <h1 class="text-3xl lg:text-5xl font-extrabold text-sky-800">
          {{ selectedMunicipality ? selectedMunicipality.barangays : '-' }}
        </h1>
        <h2 class="text-sm lg:text-xl text-gray-700 mb-1 uppercase leading-tight">Barangays</h2>
      </div>

      <!-- Charts -->
      <div
        class="col-span-4 sm:col-span-4 row-span-auto lg:col-span-4 content-center h-auto rounded-lg w-full bg-slate-100 p-5">
        <h1 class="text-center text-2xl font-bold text-brandSky-8 pb-3 uppercase">Economic Indicators</h1>
        <div class="grid grid-cols-2 justify-center w-full">
          <div class="lg:p-5 pb-10 col-span-2 lg:col-span-1">
            <PopulationChart class="lg:p-5" />
          </div>
          <div class="lg:p-5 pb-10 col-span-2 lg:col-span-1">
            <PurchasingPowerChart class="lg:p-5" />
          </div>
          <div class="lg:p-5 pb-10 col-span-2 lg:col-span-1">
            <AverageFamilyIncomeChart class="lg:p-5" />
          </div>
          <div class="lg:p-5 pb-10 col-span-2 lg:col-span-1">
            <AverageFamilyExpenditure class="lg:p-5" />
          </div>
        </div>
      </div>

      <div
        class="col-span-4 sm:col-span-4 lg:col-span-1 h-full rounded-lg bg-slate-100 flex flex-col justify-evenly p-5">
        <INProfileKPI />
      </div>


      <div class="col-span-4 sm:col-span-4 lg:col-span-3 rounded-lg bg-slate-100 flex flex-col lg:flex-row p-2">

        <select v-model="selectedMap"
          class="select select-bordered select-sm lg:hidden w-full mb-2 bg-white text-gray-800">
          <option v-for="(map, index) in hazardMaps" :key="index" :value="map"
            class="bg-white text-gray-800 hover:bg-slate-100">
            {{ map.name }}
          </option>
        </select>

        <div class="gap-5 hidden lg:flex flex-col mb-2 content-start p-5">
          <h1 class="font-bold text-2xl text-brandSky-8">Hazard Map</h1>
          <div v-for="(map, index) in hazardMaps" :key="index" class="flex items-center">
            <input type="radio" :id="'radio-' + index" :value="map" v-model="selectedMap"
              class="radio radio-sm text-gray-600" />
            <label :for="'radio-' + index" class="ml-1 text-sm font-medium text-gray-800 cursor-pointer">
              {{ map.name }}
            </label>
          </div>
        </div>

        <div class="flex flex-1 justify-center items-center lg:ml-2">
          <ZoomImg v-if="selectedMap" :src="selectedMap.file" :alt="selectedMap.name" zoom-type="move" :step="2"
            :show-zoom-btns="true" trigger="click" :zoom-scale="10" class="w-100 h-auto object-contain" />
        </div>
      </div>

      <!-- Row 1: Capital, Land Area, Coastline -->
      <!-- <div
        class="col-span-1 sm:col-span-4 lg:col-span-4 rounded-lg bg-white inner-shadow flex flex-col items-center p-10 justify-center">
        <img src="@/assets/images/laoag-city.webp" alt="Capital Icon" class="w-16 mb-3" />
        <h1 class="text-3xl font-bold text-sky-800 leading-5">LAOAG CITY</h1>
        <h2 class="text-lg lg:text-2xl text-gray-700 mt-1 leading-tight">CAPITAL</h2>
      </div>

      <div
        class="col-span-1 sm:col-span-4 lg:col-span-4 bg-white rounded-lg inner-shadow flex flex-col items-center justify-center p-8">
        <h2 class="text-lg lg:text-2xl text-gray-700 mb-1 leading-tight">LAND AREA</h2>
        <h1 class="text-3xl lg:text-5xl font-extrabold text-sky-800">
          3,622.91 <span class="text-base">km.<sup>2</sup></span>
        </h1>
      </div>

      <div
        class="col-span-1 sm:col-span-4 lg:col-span-4 bg-white rounded-lg inner-shadow flex flex-col items-center justify-center p-8">
        <h2 class="text-lg lg:text-2xl text-gray-700 mb-1 leading-tight">COASTLINE</h2>
        <h1 class="text-3xl lg:text-5xl font-extrabold text-sky-800">
          155.37 <span class="text-base">kms.</span>
        </h1>
      </div> -->

      <!-- Row 2: Demography and Languages side by side -->
      <!-- <div class="col-span-1 sm:col-span-4 lg:col-span-6 bg-white rounded-lg inner-shadow p-6 flex flex-col">
        <h1 class="text-3xl font-extrabold text-sky-800 text-center uppercase mb-4">Demography</h1>
        <div class="flex flex-col sm:flex-row sm:justify-between gap-6">
          <p class="text-sm sm:text-base">
            <span class="font-semibold">Population:</span> 609,588 (2020)
          </p>
          <div class="text-sm sm:text-base">
            <p class="font-semibold mb-2">Population Breakdown by age:</p>
            <ul class="list-disc list-inside space-y-1">
              <li>20% - 10 & Below</li>
              <li>16% - 11-19</li>
              <li>24% - 20-34</li>
              <li>32% - 36-64</li>
              <li>8% - 65 & above</li>
            </ul>
          </div>
        </div>
      </div>

      <div
        class="col-span-1 sm:col-span-4 lg:col-span-6 grid grid-cols-2 grid-rows-2 gap-0 rounded-lg overflow-hidden inner-shadow"
        style="padding: 0;">
        <div class="flex items-center justify-center bg-indigo-100 rounded-tl-lg">
          <h2 class="uppercase font-bold text-2xl text-indigo-800 text-center p-4">
            Languages
          </h2>
        </div>
        <div class="bg-red-100 flex flex-col items-center justify-center p-6 rounded-tr-lg">
          <h3 class="text-red-800 font-extrabold uppercase text-2xl mb-1">Ilocano</h3>
          <span class=" text-md font-medium">Kablaaw!</span>
          <span class="italic text-sm">káb-la-aw</span>
        </div>
        <div class="bg-sky-100 flex flex-col items-center justify-center p-6 rounded-bl-lg">
          <h3 class="text-sky-800 font-extrabold uppercase text-2xl mb-1">Filipino</h3>
          <span class=" text-md font-medium">Kumusta!</span>
          <span class="italic text-sm">koo-mús-ta</span>
        </div>
        <div class="bg-amber-100 flex flex-col items-center justify-center p-6 rounded-br-lg">
          <h3 class="text-amber-800 font-extrabold uppercase text-2xl mb-1">English</h3>
          <span class=" text-md font-medium">Hello!</span>
          <span class="italic text-sm">hə-ˈlō</span>
        </div>
      </div> -->

      <!-- Row 3: Dry Season, Wet Season, Climate -->
      <!-- <div
        class="col-span-1 sm:col-span-4 lg:col-span-4 relative rounded-lg overflow-hidden inner-shadow flex flex-col justify-center bg-white p-6">
        <div :style="`background-image: url(${DrySeason})`" class="absolute inset-0 bg-center bg-cover opacity-30">
        </div>
        <div class="relative z-10">
          <p class="text-sm mb-1">DRY SEASONS</p>
          <h2 class="font-bold text-sky-800 text-2xl md:text-3xl leading-tight">
            NOVEMBER TO <br />APRIL
          </h2>
        </div>
      </div>

      <div
        class="col-span-1 sm:col-span-4 lg:col-span-4 relative rounded-lg overflow-hidden inner-shadow flex flex-col justify-center bg-white p-6">
        <div :style="`background-image: url(${WetSeason})`" class="absolute inset-0 bg-center bg-cover opacity-30">
        </div>
        <div class="relative z-10">
          <p class="text-sm mb-1">WET SEASONS</p>
          <h2 class="font-bold text-sky-800 text-2xl md:text-3xl leading-tight">
            MAY TO <br />OCTOBER
          </h2>
        </div>
      </div>

      <div
        class="col-span-1 sm:col-span-4 lg:col-span-4 relative rounded-lg overflow-hidden inner-shadow flex flex-col justify-center items-center bg-white p-6 text-center">
        <div :style="`background-image: url(${Climate})`" class="absolute inset-0 bg-center bg-cover opacity-30"></div>
        <div class="relative z-10">
          <p class="text-xs md:text-lg mb-1">CLIMATE</p>
          <h2 class="font-bold text-sky-800 text-5xl">23°C - 30°C</h2>
          <p class="text-xs md:text-md mt-1">AVERAGE TEMPERATURE</p>
        </div>
      </div> -->

      <!-- Row 4: 4 stats (2x2 grid) -->
      <!-- <div class="col-span-1 sm:col-span-4 lg:col-span-4 grid grid-cols-2 gap-4">
        <div class="bg-white rounded-lg inner-shadow flex flex-col items-center justify-center p-6">
          <h1 class="text-4xl font-bold text-sky-800">2</h1>
          <p class="text-xs text-center mt-1 uppercase">CONGRESSIONAL DISTRICTS</p>
        </div>
        <div class="bg-white rounded-lg inner-shadow flex flex-col items-center justify-center p-6">
          <h1 class="text-4xl font-bold text-sky-800">21</h1>
          <p class="text-xs text-center mt-1 uppercase">MUNICIPALITIES</p>
        </div>
        <div class="bg-white rounded-lg inner-shadow flex flex-col items-center justify-center p-6">
          <h1 class="text-4xl font-bold text-sky-800">2</h1>
          <p class="text-xs text-center mt-1 uppercase">CITIES</p>
        </div>
        <div class="bg-white rounded-lg inner-shadow flex flex-col items-center justify-center p-6">
          <h1 class="text-4xl font-bold text-sky-800">559</h1>
          <p class="text-xs text-center mt-1 uppercase">BARANGAYS</p>
        </div>
      </div> -->

      <!-- Row 5: Economic Indicators and Map -->
      <!-- <div
        class="col-span-1 sm:col-span-4 lg:col-span-4 bg-white rounded-lg inner-shadow p-6 flex flex-col justify-center">
        <h2 class="text-xl font-bold text-sky-800 mb-4">ECONOMIC INDICATORS</h2>
        <p class="text-xs mb-4 leading-relaxed">
          Employment Rate: 98.9%<br />
          Purchasing Power of Peso: 0.91<br />
          Consumer Price Index: 119.8
        </p>
        <p class="text-xs leading-relaxed">
          Average Annual Family<br />
          Income: Php 334,980<br />
          Expenditure: 215,770
        </p>
      </div> -->

      <!-- Map -->
      <!-- <div
        class="col-span-1 sm:col-span-4 lg:col-span-4 lg:row-span-2 bg-white rounded-lg inner-shadow flex items-center justify-center p-6">
        <img :src="INMap" alt="Ilocos Norte Map" class="w-full max-w-3xl object-contain" />
      </div> -->

      <!-- Forms -->
      <!-- <div
        class="col-span-1 sm:col-span-4 lg:col-span-8 bg-white rounded-lg inner-shadow p-6 flex flex-col justify-center">
        <h1 class="text-2xl font-bold mb-4">Forms</h1>
        <ul class="space-y-3">
          <li>
            <a href="#" class="inline-block px-4 py-2 bg-sky-600 text-white rounded hover:bg-sky-700 transition"
              download>
              Download Application Form (PDF)
            </a>
          </li>
          <li>
            <a href="#" class="inline-block px-4 py-2 bg-sky-600 text-white rounded hover:bg-sky-700 transition"
              download>
              Download Permit Form (PDF)
            </a>
          </li>
          <li>
            <a href="#" class="inline-block px-4 py-2 bg-sky-600 text-white rounded hover:bg-sky-700 transition"
              download>
              Download Report Form (PDF)
            </a>
          </li>
        </ul>
      </div> -->

    </div>

  </div>
</template>