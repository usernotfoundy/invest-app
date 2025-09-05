<template>
  <div ref="videoContainer" class="card shadow-lg bg-base-100">
    <figure class="w-full">
      <video ref="videoRef"
      autoplay
      loop
      playsinline
      controls
      controlsList="nodownload noremoteplayback"
      disablePictureInPicture
      class="w-full h-auto rounded-lg">
        <source :src="INvestVid" type="video/mp4" />
        Your browser does not support the video tag.
      </video>
    </figure>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, nextTick } from 'vue'

const INvestVid = '/assets/videos/INvest.mp4'
const videoRef = ref(null)
const videoContainer = ref(null)

let observer

onMounted(async () => {
  await nextTick()

  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        const video = videoRef.value
        if (!video) return

        if (entry.isIntersecting) {
          if (video.paused) {
            video.play().catch((err) => {
              console.warn('Autoplay blocked:', err)
            })
          }
        } else {
          video.pause()
        }
      })
    },
    { threshold: 0.5 }
  )

  if (videoContainer.value) {
    observer.observe(videoContainer.value)

    // ✅ Check immediately if already visible
    const rect = videoContainer.value.getBoundingClientRect()
    const inView =
      rect.top < window.innerHeight * 0.5 &&
      rect.bottom > window.innerHeight * 0.5

    if (inView && videoRef.value?.paused) {
      videoRef.value.play().catch((err) => {
        console.warn('Autoplay blocked:', err)
      })
    }
  }
})

onBeforeUnmount(() => {
  if (videoContainer.value && observer) {
    observer.unobserve(videoContainer.value)
  }
})
</script>