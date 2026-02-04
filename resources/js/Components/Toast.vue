<script setup>
defineProps({
  message: {
    type: String,
    default: ''
  }
})
</script>

<template>
  <transition name="fade">
    <div
      v-if="message"
      class="fixed top-5 right-5 z-50 bg-black text-white px-4 py-2 rounded-lg shadow-lg"
    >
      {{ message }}
    </div>
  </transition>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 1s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
