<script setup>
import { router } from '@inertiajs/vue3'
import { ref, computed, onMounted } from 'vue'

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  users: Array
})
const authId = page.props.auth.id // أو كيف ما عندك معرف المستخدم

const search = ref('')

const filteredUsers = computed(() => {
  if (!search.value) return props.users

  return props.users.filter(user =>
    user.name.toLowerCase().includes(search.value.toLowerCase())
  )
})

const openChat = (userId) => {
  router.visit(`/chat/open/${userId}`)
}

</script>

<template>
    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-100 flex items-center justify-center p-6">
            <div class="w-full max-w-3xl bg-white rounded-2xl shadow-lg p-6">

            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                💬 اختر المستخدم لبدء المحادثة
            </h1>

                <!-- 🔍 search -->
                <input
                v-model="search"
                type="text"
                placeholder="🔍 ابحث عن مستخدم..."
                class="w-full mb-6 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400"
                />

            <div v-if="filteredUsers.length" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div
                v-for="user in filteredUsers"
                :key="user.id"
                class="flex items-center justify-between bg-gray-50 hover:bg-indigo-50 border rounded-xl p-4 transition"
                >
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold">
                    {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <span class="font-medium text-gray-700">
                    {{ user.name }}
                    </span>
                </div>

                <button
                    @click="openChat(user.id)"
                    class="px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                >
                    تحدث
                </button>
                </div>
            </div>

            <div v-else class="text-center text-gray-500">
                لا يوجد مستخدمون
            </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
