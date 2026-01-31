<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'

const page = usePage()

// filters
const search = ref(page.props.filters?.search ?? "")
const per_page = ref(page.props.filters?.per_page ?? 5)

// البحث بـ throttle
let timeout = null
const searchUsers = () => {
    clearTimeout(timeout)

    timeout = setTimeout(() => {
        const form = useForm({
            search: search.value,
            per_page: per_page.value,
        })

        form.get(route('users.index'), {
            preserveState: true,
            replace: true,
        })
    }, 400)
}

// Reset
const resetSearch = () => {
    search.value = ""
    searchUsers()
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Users Table</h1>

            <!-- Filters -->
            <div class="flex gap-2 mb-4">
                <input
                    v-model="search"
                    @keyup="searchUsers"
                    placeholder="بحث بالاسم أو الإيميل"
                    class="border px-3 py-2 flex-1"
                />
                <select v-model="per_page" @change="searchUsers" class="border px-3 py-2">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
                <button v-if="search" @click="resetSearch" class="border px-3 py-2">
                    Reset
                </button>
            </div>

            <!-- Table -->
            <table class="w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border p-2">ID</th>
                        <th class="border p-2">Name</th>
                        <th class="border p-2">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in page.props.users.data" :key="user.id">
                        <td class="border p-2">{{ user.id }}</td>
                        <td class="border p-2">{{ user.name }}</td>
                        <td class="border p-2">{{ user.email }}</td>
                    </tr>
                    <tr v-if="!page.props.users.data.length">
                        <td colspan="3" class="text-center p-4 text-gray-500">
                            No users found
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination مباشر -->
            <div class="flex gap-2 mt-4">
                <button
                    v-for="link in page.props.users.links"
                    :key="link.label"
                    v-html="link.label"
                    :disabled="!link.url"
                    @click="link.url && router.get(link.url, {}, { preserveState: true })"
                    class="px-3 py-1 border rounded"
                    :class="{ 'bg-gray-300': link.active, 'cursor-not-allowed opacity-50': !link.url }"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
