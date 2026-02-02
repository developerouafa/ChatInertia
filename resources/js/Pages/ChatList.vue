<!-- <script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import Toast from '@/Components/Toast.vue'

const page = usePage()
const conversations = ref(page.props.conversations)

const toastMessage = ref('')
const showToast = (msg) => {
  toastMessage.value = msg
  setTimeout(() => toastMessage.value = '', 3000)
}

const openChat = (conversationId) => {
  router.visit(`/privatechat/conversation/${conversationId}`)
}

const updateLastMessage = (conversationId, message) => {
  const conv = conversations.value.find(c => c.id === conversationId)
  if (conv) {
    conv.last_message = message
    conversations.value = [
      conv,
      ...conversations.value.filter(c => c.id !== conv.id)
    ]
    showToast(`رسالة جديدة من ${conv.other_user.name}`)
  }
}

onMounted(() => {
  conversations.value.forEach((conv) => {
    Echo.private(`conversation.${conv.id}`)
      .listen('.message.conversation', (e) => {
        updateLastMessage(conv.id, e.message)
      })
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
            <div class="flex items-center gap-3">
                <div>
                <p class="font-semibold">
                    {{ conv.other_user?.name ?? 'مستخدم محذوف' }}
                </p>

                <p class="text-sm text-gray-500 truncate max-w-xs">
                    {{ conv.last_message?.content ?? 'لا توجد رسائل بعد' }}
                </p>
                </div>

                <span v-if="conv.unread_count > 0" class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                {{ conv.unread_count }}
                </span>
            </div>

            <span class="text-xs text-gray-400">
                {{ conv.last_message
                ? new Date(conv.last_message.created_at).toLocaleTimeString()
                : ''
                }}
            </span>
            </div>

      </div>

      <div v-else class="text-gray-500 text-center">لا توجد محادثات بعد</div>

      <Toast :message="toastMessage" />
    </div>
  </AuthenticatedLayout>
</template> -->


<!-- <script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const page = usePage()
const conversations = ref(page.props.conversations)

// فتح المحادثة
const openChat = (conversationId) => {
  router.visit(`/privatechat/conversation/${conversationId}`)
}

// تحديث آخر رسالة + badge
const updateLastMessage = (conversationId, message) => {
  const conv = conversations.value.find(c => c.id === conversationId)
  if (conv) {
    conv.last_message = message
    // زيادة unread_count إذا كان من الآخر
    if (message.user_id !== page.props.auth.user.id) {
      conv.unread_count = (conv.unread_count || 0) + 1
    }

    // نرفع المحادثة لفوق
    conversations.value = [
      conv,
      ...conversations.value.filter(c => c.id !== conv.id)
    ]
  }
}

// عند التركيب: نستمع لكل محادثة
onMounted(() => {
  conversations.value.forEach((conv) => {
    Echo.private(`conversation.${conv.id}`)
      .listen('.message.conversation', (e) => {
        updateLastMessage(conv.id, e.message)
      })
  })
})
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-3xl mx-auto p-6">

      <h1 class="text-2xl font-bold mb-6">💬 المحادثات</h1>

      <div v-if="conversations.length" class="space-y-3">
        <div v-for="conv in conversations" :key="conv.id" @click="openChat(conv.id)"
        class="flex justify-between items-center p-4 bg-white rounded-xl shadow hover:bg-gray-50 cursor-pointer">
            <div>
                <p class="font-semibold">{{ conv.other_user?.name ?? 'مستخدم محذوف' }}</p>
                <p class="text-sm text-gray-500 truncate max-w-xs">
                {{ conv.last_message?.content ?? 'لا توجد رسائل بعد' }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400">
                {{ conv.last_message
                    ? new Date(conv.last_message.created_at).toLocaleTimeString()
                    : ''
                }}
                </span>

                <span v-if="conv.unread_count > 0" class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                {{ conv.unread_count }}
                </span>
            </div>
        </div>
      </div>

      <div v-else class="text-gray-500 text-center">
        لا توجد محادثات بعد
      </div>

    </div>
  </AuthenticatedLayout>
</template> -->

<!--
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { router, usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'

const page = usePage()
const authId = page.props.auth.user.id

const conversations = ref(page.props.conversations)

// فتح المحادثة وتصفير الـ unread badge
const openChat = async (conversationId) => {
  const conv = conversations.value.find(c => c.id === conversationId)
  if (conv) conv.unread_count = 0

  await axios.post('/chatprivate/mark-as-read', { conversation_id: conversationId })

  router.visit(`/privatechat/conversation/${conversationId}`)
}

// تحديث آخر رسالة + unread badge
const updateLastMessage = (conversationId, message) => {
  let conv = conversations.value.find(c => c.id === conversationId)

  if (conv) {
    conv.last_message = message
    if (message.user_id !== authId) conv.unread_count = (conv.unread_count || 0) + 1

    // نرفع المحادثة للفوق
    conversations.value = [
      conv,
      ...conversations.value.filter(c => c.id !== conv.id)
    ]
  } else {
    // محادثة جديدة لأول مرة
    conv = {
      id: message.conversation_id,
      other_user: message.user, // يجب أن يكون محمل من backend
      last_message: message,
      unread_count: message.user_id !== authId ? 1 : 0
    }
    conversations.value = [conv, ...conversations.value]
  }
}

// onMounted(() => {
//   // Listen لكل المحادثات الحالية
//   conversations.value.forEach((conv) => {
//     Echo.private(`conversation.${conv.id}`)
//       .listen('.message.conversation', (e) => {
//         updateLastMessage(conv.id, e.message)
//       })
//   })

//   // Listen لأي محادثة جديدة خاصة بالمستخدم
//   Echo.private(`user.${authId}`)
//     .listen('.message.conversation', (e) => {
//       updateLastMessage(e.message.conversation_id, e.message)
//     })
// })

onMounted(() => {
  // Listen لكل المحادثات الحالية
  conversations.value.forEach(conv => {
    Echo.private(`conversation.${conv.id}`)
      .listen('.message.conversation', (e) => {
        // تحديث آخر رسالة
        conv.last_message = e.message
        conv.unread_count = (conv.unread_count ?? 0) + 1

        // نحرك المحادثة للفوق
        conversations.value = [
          conv,
          ...conversations.value.filter(c => c.id !== conv.id)
        ]
      })
  })

  // Listen لأي محادثة جديدة تخص المستخدم
  Echo.private(`user.${authId}`)
    .listen('.new.conversation', (e) => {
      // إضافة المحادثة الجديدة مباشرة
      conversations.value.unshift({
        id: e.conversation.id,
        other_user: e.other_user,
        last_message: e.message,
        unread_count: 1
      })
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
            <p class="font-semibold">{{ conv.other_user?.name ?? 'مستخدم محذوف' }}</p>
            <p class="text-sm text-gray-500 truncate max-w-xs">
              {{ conv.last_message?.content ?? 'لا توجد رسائل بعد' }}
            </p>
          </div>

          <div class="flex flex-col items-end gap-1">
            <span class="text-xs text-gray-400">
              {{ conv.last_message
                ? new Date(conv.last_message.created_at).toLocaleTimeString()
                : ''
              }}
            </span>
            <span
              v-if="conv.unread_count > 0"
              class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"
            >
              {{ conv.unread_count }}
            </span>
          </div>
        </div>
      </div>

      <div v-else class="text-gray-500 text-center">
        لا توجد محادثات بعد
      </div>
    </div>
  </AuthenticatedLayout>
</template> -->


<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()
const authId = page.props.auth.user.id

// المحادثات
const conversations = ref(page.props.conversations)

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

    // 2️⃣ محادثة جديدة (Chat list realtime)
        // Echo.private(`user.${authId}`)
        //     .listen('.new.conversation', (e) => {
        //         console.log('NEW CONVERSATION EVENT:', e);

        //         const exists = conversations.value.find(
        //             c => c.id === e.conversation.id
        //         );

        //         if (!exists) {
        //             conversations.value.unshift({
        //                 id: e.conversation.id,
        //                 other_user: e.otherUser, // ✅ الراسل
        //                 last_message: e.message,
        //                 unread_count: 1
        //             });
        //         }
        //     });


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

    </div>
  </AuthenticatedLayout>
</template>
