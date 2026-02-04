<script setup>
import { ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import axios from 'axios'
import { onMounted, onBeforeUnmount } from 'vue'

const page = usePage()

const messages = ref(page.props.messages)
const body = ref('')

const send = async () => {
  if (!body.value.trim()) return

  const res = await axios.post('/privatechat/send', {
    conversation_id: page.props.conversation.id,
    content: body.value
  })

  messages.value.push(res.data)
  body.value = ''
}

// onMounted(() => {
//   Echo.private(`conversation.${page.props.conversation.id}`)
//     .listen('MessageConversation', (e) => {
//       messages.value.push(e.message)
//     })
// })

onBeforeUnmount(() => {
  Echo.leave(`conversation.${page.props.conversation.id}`)
})
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-3xl mx-auto p-6">

      <h2 class="text-xl font-bold mb-4">
        💬 {{ page.props.otherUser.name }}
      </h2>

      <!-- messages -->
      <div class="border rounded-lg p-4 h-96 overflow-y-auto mb-4 bg-gray-50">
        <div
          v-for="msg in messages"
          :key="msg.id"
          :class="msg.user_id === page.props.auth.user.id ? 'text-right' : 'text-left'"
          class="mb-2"
        >
          <span
            class="inline-block px-3 py-2 rounded-lg"
            :class="msg.user_id === page.props.auth.user.id
              ? 'bg-indigo-600 text-white'
              : 'bg-white border'"
          >
            {{ msg.content }}
          </span>
        </div>
      </div>

      <!-- input -->
      <div class="flex gap-2">
        <input
          v-model="body"
          @keyup.enter="send"
          placeholder="اكتب رسالة..."
          class="flex-1 border rounded-lg px-4 py-2"
        />
        <button
          @click="send"
          class="bg-indigo-600 text-white px-4 py-2 rounded-lg"
        >
          إرسال
        </button>
      </div>

    </div>
  </AuthenticatedLayout>
</template>
