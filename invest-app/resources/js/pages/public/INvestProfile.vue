<script setup>
import DrySeason from '@/assets/images/dry-season.webp'
import WetSeason from '@/assets/images/wet-season.webp'
import Climate from '@/assets/images/climate.webp'
import INMap from '@/assets/images/in-map.webp'
</script>

<template>
  <!-- Banner (full width) -->
  <div
    class="w-auto bg-[url('@/assets/images/header.png')] bg-cover bg-center flex flex-col items-center justify-center py-5"
    style="min-height: 150px; margin-left: calc(50% - 50vw);">
    <img src="@/assets/images/in-logo.webp" class="w-24 lg:w-36" alt="Ilocos Norte Seal" />
    <h1 class="font-leagueSpartan text-5xl pt-4 lg:text-8xl font-extrabold text-shadow-lg uppercase text-white">
      Ilocos Norte
    </h1>
  </div>

  <div class="p-5 md:px-14 lg:px-24 xl:px-36 min-h-screen">
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 lg:grid-rows-[auto]">

      <!-- Row 1: Capital, Land Area, Coastline -->
      <div
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
      </div>

      <!-- Row 2: Demography and Languages side by side -->
      <div class="col-span-1 sm:col-span-4 lg:col-span-6 bg-white rounded-lg inner-shadow p-6 flex flex-col">
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
      </div>

      <!-- Row 3: Dry Season, Wet Season, Climate -->
      <div
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
      </div>

      <!-- Row 4: 4 stats (2x2 grid) -->
      <div class="col-span-1 sm:col-span-4 lg:col-span-4 grid grid-cols-2 gap-4">
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
      </div>

      <!-- Row 5: Economic Indicators and Map -->
      <div
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
      </div>

      <!-- Map -->
      <div
        class="col-span-1 sm:col-span-4 lg:col-span-4 lg:row-span-2 bg-white rounded-lg inner-shadow flex items-center justify-center p-6">
        <img :src="INMap" alt="Ilocos Norte Map" class="w-full max-w-3xl object-contain" />
      </div>

      <!-- Forms -->
      <div
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
      </div>

    </div>
  </div>
</template>