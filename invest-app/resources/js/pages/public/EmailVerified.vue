<!-- resources/js/components/EmailVerifiedComponent.vue -->
<template>
  <div class="bg-[#F7F7F7] min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full">
      <!-- Success Card -->
      <div class="bg-white rounded-2xl shadow-md p-8 text-center transform transition-all duration-500" 
           :class="{ 'scale-105': mounted }">
        
        <!-- Success Icon -->
        <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
          <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>

        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-800 mb-4">
          Email Verified!
        </h1>

        <!-- Message -->
        <p class="text-gray-600 text-lg mb-8 leading-relaxed">
          Your email address has been successfully verified. You can now login to the system.
        </p>

        <!-- Action Buttons -->
        <div class="space-y-4">
          
          <button 
            @click="goLogin"
            class="w-full bg-gray-100 text-gray-700 font-medium py-3 px-6 rounded-lg 
                   hover:bg-gray-200 transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-gray-300">
            Go to Login
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import router from '../../router';

export default {
  name: 'EmailVerifiedComponent',
  data() {
    return {
      mounted: false
    }
  },
  mounted() {
    // Trigger animation after component mounts
    setTimeout(() => {
      this.mounted = true;
    }, 100);
  },
  methods: {
    goLogin() {
      // Redirect to home page
      router.push("/login");
    }
  }
}
</script>