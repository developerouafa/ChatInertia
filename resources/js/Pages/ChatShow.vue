<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage()
const conversation = page.props.conversation
const messages = ref(page.props.messages)
const otherUser = page.props.otherUser
const authId = page.props.auth.user.id

const body = ref('')

const send = async () => {
  if (!body.value) return

  await axios.post('/chatprivate/send', {
    conversation_id: conversation.id,
    content: body.value
  })

  body.value = ''
}

const editMessage = async (msg) => {
  const newContent = prompt('عدل الرسالة', msg.content)
  if (!newContent) return

  await axios.put(`/chatprivate/message/${msg.id}`, {
    content: newContent
  })
}

// Listen للرسائل الجديدة
// تأكد من عدم التكرار (optional)
onMounted(() => {
  Echo.private(`conversation.${conversation.id}`)
    .listen('.message.conversation', (e) => {
        // 1
        // messages.value.push(e.message)
        // 2
      // Check if message already exists
      if (!messages.value.some(m => m.id === e.message.id)) {
        messages.value.push(e.message)
      }
      body.value = ''
    })
    .listen('.message.updated', (e) => {
    const index = messages.value.findIndex(m => m.id === e.message.id)
    if (index !== -1) {
        messages.value[index] = {
        ...messages.value[index],
        content: e.message.content,
        edited_at: e.message.edited_at
        }
    }
    })
})


onBeforeUnmount(() => {
  Echo.leave(`conversation.${conversation.id}`)
})
</script>

<template>
  <AuthenticatedLayout>
    <div class="text-xl font-bold mb-4 max-w-3xl mx-auto p-6 flex flex-col h-[80vh]">
      <h2 class="text-xl font-bold mb-4">
        💬 {{ otherUser.name }}
      </h2>

      <div class="border rounded-lg p-4 h-96 overflow-y-auto mb-4 bg-gray-50">
        <div
          v-for="msg in messages"
          :key="msg.id"
          :class="msg.user_id === authId
            ? 'text-right'
            : 'text-left'"
            class="mb-2"
        >
          <span class="inline-block px-3 py-2 rounded-lg"
            :class="msg.user_id === authId
              ? 'bg-indigo-600 text-white'
              : 'bg-white border'">
            {{ msg.content }}
          </span>
          <div v-if="msg.user_id === authId">
            <button @click="editMessage(msg)">✏️</button>
          </div>
        </div>
      </div>

      <div class="flex gap-2">
        <input v-model="body" @keyup.enter="send" placeholder="اكتب رسالة..." class="flex-1 border rounded-lg px-3 py-2" />
        <button @click="send" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">إرسال</button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
