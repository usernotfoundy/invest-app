<script setup>
import INvestVideo from "@/components/INvestVid.vue"

const Inabel1 = "/assets/images/inabel2.jpg"
const QualityOfLifeIcon = "/assets/icons/quality-of-life.svg"
const FinancialSupportIcon = "/assets/icons/financial-support.svg"
const HandshakeIcon = "/assets/icons/handshake.svg"
const LocationIcon = "/assets/icons/location.svg"
const MegaworldImage = "/assets/images/megaworld.jpg"
const ReadMoreIcon = "/assets/icons/read-more.svg"

</script>

<template>

  <div class="md:px-14 lg:px-24 xl:px-36 px-5">

    <!-- Mobile Layout -->
    <div class="lg:hidden">
      <div class="p-5 px-5">
        <div
          class="flex flex-col items-center justify-center text-center h-full col-span-2 lg:col-span-1 font-leagueSpartan">
          <span class="font-instrument font-bold text-lg leading-tight">DESTINATION</span>
          <h1 class="text-8xl font-extrabold text-sky-800">UP</h1>
          <h1 class="text-8xl font-extrabold text-sky-800 leading-14">NORTH</h1>
        </div>

        <div class="col-span-2 lg:col-span-1 mt-4">
          <INvestVideo />
        </div>
        <div class="col-span-2">
          <div>
            <p class="text-md text-gray-600 text-justify">
              <br />
              Recent reports show investors expanding beyond Metro Manila. Our provincial
              government&quot;s initiatives,
              like major infrastructure projects, aim to ease congestion in the capital.
            </p>
            <p class="text-md text-gray-600 text-justify">
              <br />
              Ilocos Norte, with its location, resources, and business-friendly environment, is poised to
              become a prime
              investment hub in the North.
            </p>
            <p class="text-md text-gray-600 text-justify">
              <br />
              It's set to draw investors looking beyond Metro Manila for opportunities.
            </p>
            <p class="text-md text-gray-600 text-justify">
              <br />
              Up North is the way forward.
            </p>
          </div>
          <div class="pt-5 flex justify-center">
            <!-- <router-link to="/sectors" custom v-slot="{ href, navigate }">
              <a :href="href" @click="navigate"
                :class="['btn btn-sm w-36 bg-sky-700 text-white rounded-md font-normal']">Get
                Started</a>
            </router-link> -->
          </div>
        </div>
      </div>

    </div>

    <!-- Desktop Layout -->
    <div class="hidden lg:flex h-lvh">
      <div class="grid grid-cols-2 gap-3 h-auto">
        <div class="col-span-1 flex flex-col justify-center h-full ">
          <div class="font-leagueSpartan">
            <span class="font-instrument font-bold text-lg leading-tight">DESTINATION</span>
            <h1 class="text-8xl xl:text-9xl font-extrabold text-sky-800 font-spartan">UP</h1>
            <h1 class="text-8xl xl:text-9xl font-extrabold text-sky-800 font-spartan leading-14">NORTH</h1>
          </div>
          <div>
            <div class="pr-16">
              <p class="text-md text-gray-600">
                <br />
                Recent reports show investors expanding beyond Metro Manila. Our provincial
                government&quot;s
                initiatives,
                like major infrastructure projects, aim to ease congestion in the capital.
              </p>
              <p class="text-md text-gray-600">
                <br />
                Ilocos Norte, with its location, resources, and business-friendly environment, is poised
                to become a
                prime
                investment hub in the North.
              </p>
              <p class="text-md text-gray-600">
                <br />
                It's set to draw investors looking beyond Metro Manila for opportunities.
              </p>

              <p class="text-md text-gray-600">
                <br />
                Up North is the way forward.
              </p>
            </div>
          </div>
        </div>
        <div class="col-span-1 flex justify-center items-center bg-repeat bg-top bg-auto"
          :style="`background-image: url(${Inabel1})`">
          <INvestVideo />
        </div>
      </div>
    </div>
  </div>

  <!-- Reason to Invest -->
  <div class="bg-sky-50 w-full">
    <div class="px-5 p-5 md:px-14 lg:px-24 xl:px-36">
      <div class="pt-5 font-leagueSpartan">
        <h1 class="text-3xl text-center font-extrabold text-gray-800 font-spartan">REASON TO</h1>
        <p class="text-center font-extrabold text-sky-800 text-8xl leading-20">INvest</p>
      </div>

      <div class="grid grid-cols-2 gap-3 mt-5 lg:px-40">

        <div class="card bg-base-100 h-72 shadow-md rounded-4xl p-3 col-span-2 lg:col-span-1">
          <div class="card-body text-center">
            <img class="mx-auto w-24" :src="QualityOfLifeIcon" alt="Healtchcare Icon">
            <h2 class="card-title uppercase justify-center text-2xl text-sky-800 font-bold font-leagueSpartan">
              quality of life
            </h2>
            <p class="text-center">Clean, healthy, and peaceful environment, robust tourism industry, and
              self-sufficient food supply
              makes Ilocos Norte a highly livable province, ideal for doing business and raising a family.
            </p>
          </div>
        </div>

        <div class="card bg-base-100 h-72 shadow-md rounded-4xl p-3 col-span-2 lg:col-span-1">
          <div class="card-body text-center">
            <img class="mx-auto w-24" :src="FinancialSupportIcon" alt="Financial Icon">
            <h2 class="card-title uppercase justify-center text-2xl text-sky-800 font-bold font-leagueSpartan">
              government
              support <br>&
              incentives</h2>
            <p class="text-center">All fresh investments enjoy a waiver on the Provincial Share of Real
              Property Tax
              (RPT).</p>
          </div>
        </div>

        <div class="card bg-base-100 h-72 shadow-md rounded-4xl p-3 col-span-2 lg:col-span-1">
          <div class="card-body text-center">
            <img class="mx-auto w-24" :src="HandshakeIcon" alt="Healtchcare Icon">
            <h2 class="card-title uppercase justify-center text-2xl text-sky-800 font-bold font-leagueSpartan">
              business
              friendly</h2>
            <p class="text-center">Reasonable cost in doing business-labor and accommodation,
              transportation, rental and
              lease, water and power supply.</p>
          </div>
        </div>

        <div class="card bg-base-100 h-72 shadow-md rounded-4xl p-3 col-span-2 lg:col-span-1">
          <div class="card-body text-center">
            <img class="mx-auto w-24" :src="LocationIcon" alt="Locaiton Icon">
            <h2 class="card-title uppercase justify-center text-2xl text-sky-800 font-bold font-leagueSpartan">
              strategic
              location</h2>
            <p class="text-center">Ilocos Norte is the northern gateway for North and South-East Asia
              economies, is
              accessible via land, air and sea.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Latest News -->
  <div class="px-5 p-5 md:px-14 lg:px-24 xl:px-36 mt-6">
    <h1 class="text-5xl font-bold uppercase text-slate-700 font-leagueSpartan">Latest News</h1>

    <div class="grid grid-cols-4 gap-5 py-5">
      <div class="card bg-base-100 shadow-sm col-span-4 lg:col-span-1">
        <figure>
          <img :src="MegaworldImage" alt="Shoes" />
        </figure>
        <div class="card-body">
          <h2 class="card-title text-slate-700 font-leagueSpartan">Megaworld to spend P15B for 84-hectare
            township in Ilocos
            Norte</h2>
          <p class="text-slate-400">Listed property developer Megaworld Corp. has allocated P15 billion to
            develop an
            84-hectare township in Ilocos Norte...</p>
          <div class="card-actions justify-end">
            <a href="#"
              class="bg-transparent border-none p-0 hover:bg-transparent hover:border-none flex items-center gap-1 focus:outline-none focus:ring-0">
              <span class="font-medium text-sky-800">Read more</span>
              <img :src="ReadMoreIcon" alt="" class="w-4" />
            </a>
          </div>
        </div>
      </div>

      <div class="card bg-base-100 shadow-sm col-span-4 lg:col-span-1">
        <figure>
          <img :src="MegaworldImage" alt="Shoes" />
        </figure>
        <div class="card-body">
          <h2 class="card-title text-slate-700 font-leagueSpartan">Megaworld to spend P15B for 84-hectare
            township in Ilocos
            Norte</h2>
          <p class="text-slate-400">Listed property developer Megaworld Corp. has allocated P15 billion to
            develop an
            84-hectare township in Ilocos Norte...</p>
          <div class="card-actions justify-end">
            <a href="#"
              class="bg-transparent border-none p-0 hover:bg-transparent hover:border-none flex items-center gap-1 focus:outline-none focus:ring-0">
              <span class="font-medium text-sky-800">Read more</span>
              <img :src="ReadMoreIcon" alt="" class="w-4" />
            </a>
          </div>
        </div>
      </div>

      <div class="card bg-base-100 shadow-sm col-span-4 lg:col-span-1">
        <figure>
          <img :src="MegaworldImage" alt="Shoes" />
        </figure>
        <div class="card-body">
          <h2 class="card-title text-slate-700 font-leagueSpartan">Megaworld to spend P15B for 84-hectare
            township in Ilocos
            Norte</h2>
          <p class="text-slate-400">Listed property developer Megaworld Corp. has allocated P15 billion to
            develop an
            84-hectare township in Ilocos Norte...</p>
          <div class="card-actions justify-end">
            <a href="#"
              class="bg-transparent border-none p-0 hover:bg-transparent hover:border-none flex items-center gap-1 focus:outline-none focus:ring-0">
              <span class="font-medium text-sky-800">Read more</span>
              <img :src="ReadMoreIcon" alt="" class="w-4" />
            </a>
          </div>
        </div>
      </div>

      <div class="card bg-base-100 shadow-sm col-span-4 lg:col-span-1">
        <figure>
          <img :src="MegaworldImage" alt="Shoes" />
        </figure>
        <div class="card-body">
          <h2 class="card-title text-slate-700 font-leagueSpartan">Megaworld to spend P15B for 84-hectare
            township in Ilocos
            Norte</h2>
          <p class="text-slate-400">Listed property developer Megaworld Corp. has allocated P15 billion to
            develop an
            84-hectare township in Ilocos Norte...</p>
          <div class="card-actions justify-end">
            <a href="#"
              class="bg-transparent border-none p-0 hover:bg-transparent hover:border-none flex items-center gap-1 focus:outline-none focus:ring-0">
              <span class="font-medium text-sky-800">Read more</span>
              <img :src="ReadMoreIcon" alt="" class="w-4" />
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>