<script setup>
import { BriefcaseIcon, TagIcon, BanknotesIcon } from '@heroicons/vue/16/solid';
import { PhilippinePesoIcon } from 'lucide-vue-next';
</script>

<template>
  <!-- Page Title -->
  <div class="flex items-center py-5">
    <h1 class="font-bold text-5xl flex-1">Manage IN Profile</h1>
  </div>

  <div class="grid grid-cols-7 h-fit gap-2 grid-row-[auto]">
    <div class="col-span-2 shadow-sm rounded-lg bg-white p-5">
      <h1 class="text-3xl font-bold pb-5">Total Population</h1>
      <div class="flex">
        <ul class="flex-1 space-y-5">
          <li>Capital</li>
          <li>Landarea</li>
          <li>Coastline</li>
          <li>Language</li>
        </ul>

        <ul class="font-bold space-y-5">
          <li>Laoag City</li>
          <li>3,622.91 km2</li>
          <li>155.37 kms</li>
          <li>Ilocano , English, Filipino</li>
        </ul>
      </div>
    </div>

    <div class="col-span-2 shadow-sm rounded-lg bg-white p-5">
      <h1 class="text-3xl font-bold pb-5">Political Subdivision</h1>
      <div class="flex w-full">
        <ul class="font-bold w-1/4 space-y-5">
          <li>2</li>
          <li>2</li>
          <li>21</li>
          <li>559</li>
        </ul>
        <ul class="w-3/4 space-y-5">
          <li>Congressional Districts</li>
          <li>Cities</li>
          <li>Municipalities</li>
          <li>Barangays</li>
        </ul>
      </div>
    </div>

    <div class="col-span-3 row-span-2 p-5 bg-white rounded-lg shadow-sm">
      <div class="flex flex-col space-y-3">
        <h1 class="text-3xl font-bold">Total Population</h1>
        <h1 class="font-bold text-6xl">609,588</h1>
        <p>( PSA 2020 )</p>
      </div>

      <div class="pt-5">
        <h1 class="text-xl font-bold">Population breakdown by age</h1>
      </div>
    </div>


    <div class="col-span-4 p-5 bg-white rounded-lg shadow-sm">
      <h1 class="text-3xl font-bold pb-5">Economic Indicators</h1>

      <div class="grid grid-cols-2 gap-8">
        <div class="space-y-6">
          <div class=" flex items-center">
            <BriefcaseIcon class="size-8" />
            <p class="flex-1 mx-2">Employment Rate:</p>
            <span class="font-bold">98.8%</span>
          </div>
          <div class=" flex items-center">
            <PhilippinePesoIcon class="size-8" />
            <p class="flex-1 mx-2">Purchasing Power of Peso:</p>
            <span class="font-bold">0.91</span>
          </div>
          <div class=" flex items-center">
            <TagIcon class="size-8" />
            <p class="flex-1 mx-2">Consumer Price Index:</p>
            <span class="font-bold">119.8</span>
          </div>
        </div>

        <div class="space-y-6">
          <div class=" flex items-center">
            <BanknotesIcon class="size-8" />
            <p class="flex-1 text-sm mx-2">Average Annual Family Income:</p>
            <span class="font-bold">98.8%</span>
          </div>
          <div class=" flex items-center">
            <BanknotesIcon class="size-8" />
            <p class="flex-1 text-sm mx-2">Average Annual Family Expenditure:</p>
            <span class="font-bold">98.8%</span>
          </div>
        </div>
      </div>
    </div>
  </div>


</template>