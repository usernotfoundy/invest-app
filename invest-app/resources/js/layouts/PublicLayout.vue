<script setup>
import { ref, computed } from "vue"
import { useRoute } from 'vue-router'
import BurgerIcon from '@/components/BurgerIcon.vue'

// --------------------
// Setup Composables
// --------------------
const route = useRoute();

// --------------------
// State
// --------------------
const open = ref(false)

// --------------------
// Icons and Images
// --------------------
const InvestLogo = "/assets/images/logo.webp"
const FacebookIcon = "/assets/icons/facebook-icon.svg"
const INLogo = "/assets/images/in-logo.webp"
const BagongPilipipinasLogo = "/assets/images/bagong-pilipinas-logo.webp"
const LocationIcon = "/assets/icons/location-1.svg"
const TelephoneIcon = "/assets/icons/telephone.svg"
const EmailIcon = "/assets/icons/email.svg"
const WebIcon = "/assets/icons/facebook-icon-1.svg"

// --------------------
// Computed Properties
// --------------------
const isSectorsActive = computed(() =>
  route.path.startsWith('/sectors')
);
</script>

<template>
  <div class="sticky top-0 z-50 navbar bg-base-100 shadow-sm px-5 md:px-14 lg:px-24 xl:px-36">

    <!-- Logo -->
    <div class="flex-1">
      <img :src="InvestLogo" alt="INvest Logo" class="w-36">
    </div>

    <ul class="hidden lg:flex px-10 flex-auto justify-end space-x-4 items-center">
      <li>
        <RouterLink to="/" custom v-slot="{ isExactActive, navigate }">
          <button @click="navigate" :class="['btn rounded-2xl h-8 hover:rounded-2xl btn-ghost',
            isExactActive ? 'btn-active btn-primary' : 'btn-ghost'
          ]">Home</button>
        </RouterLink>
      </li>

      <li>
        <!-- <RouterLink to="/invest-profile" custom v-slot="{ isActive, navigate }">
          <button @click="navigate" :class="['btn rounded-2xl h-8 hover:rounded-2xl btn-ghost',
            isActive ? 'btn-active btn-primary' : 'btn-ghost'
          ]">INvest Profile</button>
        </RouterLink> -->
      </li>

      <li>
        <!-- <RouterLink to="/sectors" custom v-slot="{ navigate }">
          <button @click="navigate" :class="['btn rounded-2xl h-8 hover:rounded-2xl btn-ghost',
            isSectorsActive ? 'btn-active btn-primary' : 'btn-ghost'
          ]">Priority Industries</button>
        </RouterLink> -->
      </li>

      <li>
        <!-- <RouterLink to="/about" custom v-slot="{ isActive, navigate }" exact-active-class="">
          <button @click="navigate" :class="['btn rounded-2xl h-8 hover:rounded-2xl btn-ghost',
            isActive ? 'btn-active btn-primary' : 'btn-ghost'
          ]">About</button>
        </RouterLink> -->
      </li>
    </ul>


    <div class="hidden lg:flex">
      <a href="https://www.facebook.com/investilocos" @click="navigate"><img :src="FacebookIcon" alt="Facebook Icon"
          class="w-10"></a>
    </div>

    <!-- Dropdown -->
    <div class="flex-none lg:hidden">

      <details class="dropdown dropdown-end" @toggle="open = $event.target.open">
        <summary class="btn btn-ghost bg-transparent border-0 m-1">
          <BurgerIcon />
        </summary>

        <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
          <ul v-if="open"
            class="menu dropdown-content bg-base-100 rounded-box z-1 w-52 p-2 shadow-lg border border-slate-200 inner-shadow">
            <li>
              <!-- <RouterLink to="/" custom v-slot="{isExactActive, navigate}" class="btn w-auto rounded-2xl h-8 btn-ghost">
                <button @click="navigate" :class="['btn w-auto rounded-2xl h-8 btn-ghost', isExactActive ? 'btn-active btn-primary' : 'btn-ghost ']">Home</button>
              </RouterLink> -->
            </li>

            <li>
              <!-- <RouterLink to="/invest-profile" custom v-slot="{isActive, navigate}">
                <button @click="navigate" :class="['btn w-auto btn-ghost rounded-2xl h-8', isActive ? 'btn-active btn-primary' : 'btn-ghost']">IN Profile</button>
              </RouterLink> -->
            </li>

            <li>
              <!-- <RouterLink to="/sectors" custom v-slot="{ navigate }">
                <button @click="navigate" :class="['btn w-auto rounded-2xl h-8', isSectorsActive ? 'btn-active btn-primary' : 'btn-ghost']">Priority Industries</button>
              </RouterLink> -->
            </li>

            <li>
              <!-- <RouterLink to="/about" custom v-slot="{isActive, navigate}">
                <button @click="navigate" :class="['btn w-auto btn-ghost rounded-2xl h-8', isActive ? 'btn-active btn-primary' : 'btn-ghost']">About</button>
              </RouterLink> -->
            </li>

            <li class="flex justify-center mt-5">
              <img :src="InvestLogo" alt="INvest Logo" class="w-24" />
            </li>
          </ul>

        </transition>
      </details>

    </div>
  </div>
  <main>
    <router-view />
  </main>
  <div class="bg-slate-100 w-full">
    <div class="p-10 py-20 md:px-14 lg:px-24 xl:px-36">
      <div class="grid grid-cols-2">

        <!-- Footer  -->
        <div class="col-span-2 lg:col-span-1">
          <div class="items-center flex">
            <img :src="INLogo" alt="Ilocos Norte Logo" class="w-20" />
            <img :src="BagongPilipipinasLogo" alt="Bagong Pilipinas Logo" class="w-28" />
          </div>
          <h1 class="text-2xl font-medium">Ilocos Norte Trade and Investment Promotions Office</h1>
          <div class="flex flex-col justify-center space-y-3 text-sm text-gray-700 pt-5">
            <div class="flex items-start gap-2">
              <img :src="LocationIcon" alt="Location" class="w-4 mt-1" />
              <span>Ilocos Norte Provincial Capitol, Rizal St., Laoag City, Philippines 2900</span>
            </div>
            <div class="flex items-start gap-2">
              <img :src="TelephoneIcon" alt="Phone" class="w-4 mt-1" />
              <span>+63 (77) 123-4567</span>
            </div>
            <div class="flex items-start gap-2">
              <img :src="EmailIcon" alt="Email" class="w-4 mt-1" />
              <span>invest@ilocosnorte.gov.ph</span>
            </div>
            <div class="flex items-start gap-2">
              <img :src="WebIcon" alt="Website" class="w-4 mt-1" />
              <span>www.ilocosnorte.gov.ph</span>
            </div>
          </div>
        </div>

        <!-- Get in touch with us -->
        <div class="mt-20 col-span-2 lg:col-span-1 lg:mt-0">
          <h1 class="text-2xl text-center font-medium leading-1">Get IN touch with us</h1>
          <div class="flex flex-col justify-center pt-5">
            <form class="flex flex-col space-y-3" action="">
              <input type="text" placeholder="Full Name" class="input w-full" />
              <input type="email" placeholder="Email" class="input w-full" />
              <input type="text" placeholder="Subject" class="input w-full" />
              <textarea class="textarea w-full" placeholder="Message"></textarea>
              <button class="btn bg-sky-800 text-white">Submit</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="bg-sky-800 w-full">
    <div class="flex flex-col py-2 md:px-14 lg:px-24 xl:px-36 text-center text-xs text-slate-100">
      <span>© Provincial Government of Ilocos Norte</span>
      <span>Powered by the PGIN - Information Technology Office <br class="lg:hidden" />Software Engineering
        Section</span>
    </div>
  </div>
</template>