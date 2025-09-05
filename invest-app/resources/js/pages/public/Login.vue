<script setup>
import { AlertTriangle } from 'lucide-vue-next'
import { useUserStore } from '@/stores/user.js';

const userStore = useUserStore();

const submit = () => {
  userStore.login(email.value, password.value);
};

</script>

<template>
  <div class="hidden lg:flex min-h-screen items-center justify-center px-4 bg-[#F7F7F7]">
    <div class="flex flex-col border-slate-100 shadow-sm border bg-base-100 rounded-xl w-1/3 overflow-hidden">
      <div class="p-12 space-y-6">
        <div class="flex items-center justify-center">
          <img src="@/assets/images/logo.webp" alt="Logo" class="h-36 object-cover" />
        </div>

        <form @submit.prevent="submit">
          <div class="flex flex-col justify-center items-center space-y-4">
            <h1 class="tracking-widest">ADMINISTRATOR</h1>

            <div class="w-full flex flex-col ">
              <label for="email" class="block font-medium text-gray-700 text-lg">Email</label>
              <label class="input w-full">
                <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                  <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none"
                    stroke="currentColor">
                    <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                  </g>
                </svg>
                <input type="email" name="email" id="email" required="" autocomplete="email" v-model="userStore.email"
                  placeholder="your.username@domain.com" />
              </label>
            </div>

            <div class="w-full flex flex-col ">
              <label for="email" class="block font-medium text-gray-700 text-lg">Password</label>
              <label class="input w-full">
                <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                  <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none"
                    stroke="currentColor">
                    <path
                      d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z">
                    </path>
                    <circle cx="16.5" cy="7.5" r=".5" fill="currentColor"></circle>
                  </g>
                </svg>
                <input type="password" name="passowrd" id="password" required="" autocomplete="password"
                  v-model="userStore.password" placeholder="•••••••••" />
              </label>

              <div class="w-full flex flex-col mt-2">
                <!-- <div class="form-control">
                  <label class="label cursor-pointer">
                    <input type="checkbox" class="checkbox checkbox-sm" />
                    <span class="label-text ml-2">Remember me</span>
                  </label>
                </div> -->
                <p v-if="userStore.loginError" class="text-red-500">
                  {{ userStore.loginError }}
                </p>
              </div>
            </div>

            <div class="w-full flex flex-col ">
              <button :disabled="userStore.loadingLogin" type="submit" class="btn btn-primary">
                <span v-if="userStore.loadingLogin" className="loading loading-dots loading-md"></span>
                <span v-else>Login</span>
              </button>
              <a class="mt-3 text-sky-600 text-center font-regular ">Forgot password?</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="relative min-h-screen flex flex-col lg:hidden">
    <!-- Logo at top -->
    <div class="absolute top-6 w-full flex justify-center">
      <img src="@/assets/images/logo.webp" class="w-40" alt="INvest Logo">
    </div>

    <!-- Centered error text -->
    <div class="flex flex-col flex-grow justify-center items-center text-center px-6">
      <AlertTriangle class="w-24 h-auto text-red-600 mb-4" />
      <h1 class="text-2xl font-bold text-red-600 uppercase">Desktop Only</h1>
      <p class="mt-2 text-sm text-gray-700 max-w-sm">
        This webpage is only available for desktop devices.<br />
        Please visit using a larger screen.
      </p>
    </div>
  </div>

</template>