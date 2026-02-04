<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import Toast from '@/Components/Toast.vue'

const page = usePage()
const authId = page.props.auth.user.id

// المحادثات
const conversations = ref(page.props.conversations)
const toastMessage = ref('')
const showToast = (msg) => {
  toastMessage.value = msg
  setTimeout(() => toastMessage.value = '', 3000)
}

// فتح المحادثة
const openChat = (conversationId) => {
  router.visit(`/privatechat/conversation/${conversationId}`)
}

// تحديث آخر رسالة وعدد الرسائل الغير مقروءة
const updateConversation = (conversationId, message, unreadCount = 0) => {
  const conv = conversations.value.find(c => c.id === conversationId)
  if (conv) {
    conv.last_message = message
    conv.unread_count = unreadCount
    // نطلعها للفوق
    conversations.value = [
      conv,
      ...conversations.value.filter(c => c.id !== conv.id)
    ]
    showToast(`رسالة جديدة من ${conv.other_user.name}`)
  } else {
    // إذا المحادثة جديدة، نضيفها
    conversations.value = [
      {
        id: conversationId,
        other_user: message.user,
        last_message: message,
        unread_count: unreadCount
      },
      ...conversations.value
    ]
  }
}

const deleteConversation = async (id) => {
  if (!confirm('حذف المحادثة؟')) return

  await axios.delete(`/chatprivate/conversation/${id}`)

  conversations.value = conversations.value.filter(c => c.id !== id)
}

// Listen للقنوات
onMounted(() => {
  // 1️⃣ الرسائل الجديدة
  conversations.value.forEach(conv => {
    Echo.private(`conversation.${conv.id}`)
      .listen('.message.conversation', (e) => {
        // إذا الرسالة من المستخدم الآخر
        const isMine = e.message.user_id === authId
        updateConversation(conv.id, e.message, isMine ? conv.unread_count : (conv.unread_count + 1 || 1))
      })
  })

    Echo.private(`user.${authId}`)
    .listen('.new.conversation', (e) => {
      console.log('🔥 NEW CONVERSATION', e)

      const exists = conversations.value.find(
        c => c.id === e.conversation.id
      )

      if (!exists) {
        conversations.value.unshift({
          id: e.conversation.id,
          other_user: e.sender,      // ← الشخص اللي صيفط
          last_message: e.message,   // ← أول رسالة
          unread_count: 1
        })
      }
    })

})
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-3xl mx-auto p-6">

      <h1 class="text-2xl font-bold mb-6">💬 المحادثات</h1>

      <div v-if="conversations.length" class="space-y-3">

        <div
          v-for="conv in conversations"
          :key="conv.id"
          @click="openChat(conv.id)"
          class="flex justify-between items-center p-4 bg-white rounded-xl shadow hover:bg-gray-50 cursor-pointer"
        >
          <div>
            <p class="font-semibold">
              {{ conv.other_user?.name ?? 'مستخدم محذوف' }}
            </p>

            <p class="text-sm text-gray-500 truncate max-w-xs">
              {{ conv.last_message?.content ?? 'لا توجد رسائل بعد' }}
            </p>
          </div>

          <div class="flex flex-col items-end">
            <span class="text-xs text-gray-400">
              {{ conv.last_message
                ? new Date(conv.last_message.created_at).toLocaleTimeString()
                : ''
              }}
            </span>
            <button
            @click.stop="deleteConversation(conv.id)"
            class="text-red-500 text-xs"
            >
            حذف
            </button>

            <!-- badge الرسائل الغير مقروءة -->
            <span
              v-if="conv.unread_count && conv.unread_count > 0"
              class="mt-1 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"
            >
              {{ conv.unread_count }}
            </span>
          </div>
        </div>

      </div>

      <div v-else class="text-gray-500 text-center">
        لا توجد محادثات بعد
      </div>
        <Toast :message="toastMessage" />
    </div>
  </AuthenticatedLayout>
</template>
