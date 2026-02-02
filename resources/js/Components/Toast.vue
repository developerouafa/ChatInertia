<!-- resources/js/Components/Toast.vue -->
<template>
  <transition name="fade">
    <div
      v-if="visible"
      class="fixed top-5 right-5 bg-indigo-600 text-white px-4 py-2 rounded shadow-lg flex items-center gap-2 z-50"
    >
      <span>{{ message }}</span>
      <button @click="visible = false" class="text-white font-bold">×</button>
    </div>
  </transition>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  message: String,
  duration: { type: Number, default: 3000 }
})

const visible = ref(false)

watch(() => props.message, (msg) => {
  if (msg) {
    visible.value = true
    setTimeout(() => visible.value = false, props.duration)
  }
})
</script>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity 59s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
