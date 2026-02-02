<!--
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import Toast from '@/Components/Toast.vue'

const page = usePage()
const conversation = page.props.conversation
const messages = ref(page.props.messages)
const otherUser = page.props.otherUser
const body = ref('')

// Toast
const toastMessage = ref('')
const showToast = (msg) => {
  toastMessage.value = msg
  setTimeout(() => toastMessage.value = '', 3000)
}

const send = async () => {
  if (!body.value) return
  const res = await axios.post('/chatprivate/send', {
    conversation_id: conversation.id,
    content: body.value
  })

  // 1️⃣ المرسل يشوف الرسالة مباشرة
//   messages.value.push(res.data)

  // 2️⃣ نحدّث آخر رسالة فـ ChatList
//   updateLastMessage(conversation.id, res.data)
  body.value = ''
}

onMounted(() => {
  Echo.private(`conversation.${conversation.id}`)
    .listen('.message.conversation', (e) => {
      messages.value.push(e.message)
      if (e.message.user_id !== page.props.auth.user.id) {
        showToast(`رسالة جديدة من ${otherUser.name}`)
      }
    })
})

onBeforeUnmount(() => {
  Echo.leave(`conversation.${conversation.id}`)
})
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-3xl mx-auto p-4 h-[80vh] flex flex-col">

      <div class="border-b pb-3 mb-3 font-bold">💬 {{ otherUser.name }}</div>

      <div class="flex-1 overflow-y-auto space-y-2">
        <div
          v-for="msg in messages"
          :key="msg.id"
          :class="msg.user_id === $page.props.auth.user.id ? 'text-right' : 'text-left'"
        >
          <div class="inline-block px-3 py-2 rounded-xl bg-gray-100">{{ msg.content }}</div>
        </div>
      </div>


      <div class="mt-3 flex gap-2">
        <input v-model="body" @keyup.enter="send" placeholder="اكتب رسالة..." class="flex-1 border rounded-xl px-3 py-2" />
        <button @click="send" class="bg-blue-600 text-white px-4 rounded-xl">إرسال</button>
      </div>


      <Toast :message="toastMessage" />
    </div>
  </AuthenticatedLayout>
</template> -->


<!-- <script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage()
const conversation = page.props.conversation
const messages = ref(page.props.messages)
const otherUser = page.props.otherUser

const body = ref('')

// إرسال رسالة
const send = async () => {
  if (!body.value) return

  const res = await axios.post('/chatprivate/send', {
    conversation_id: conversation.id,
    content: body.value
  })

  // أضف الرسالة مباشرة للواجهة
  messages.value.push(res.data)
  body.value = ''
}

// عند التركيب: استماع للرسائل الجديدة
onMounted(() => {
  Echo.private(`conversation.${conversation.id}`)
    .listen('.message.conversation', (e) => {
      messages.value.push(e.message)
    })
})

// مغادرة القناة عند الخروج
onBeforeUnmount(() => {
  Echo.leave(`conversation.${conversation.id}`)
})
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-3xl mx-auto p-4 h-[80vh] flex flex-col">

      <div class="border-b pb-3 mb-3 font-bold">
        💬 {{ otherUser.name }}
      </div>

      <div class="flex-1 overflow-y-auto space-y-2">
        <div
          v-for="msg in messages"
          :key="msg.id"
          :class="msg.user_id === $page.props.auth.user.id
            ? 'text-right'
            : 'text-left'"
        >
          <div class="inline-block px-3 py-2 rounded-xl bg-gray-100">
            {{ msg.content }}
          </div>
        </div>
      </div>

      <div class="mt-3 flex gap-2">
        <input
          v-model="body"
          @keyup.enter="send"
          placeholder="اكتب رسالة..."
          class="flex-1 border rounded-xl px-3 py-2"
        />
        <button
          @click="send"
          class="bg-blue-600 text-white px-4 rounded-xl"
        >
          إرسال
        </button>
      </div>

    </div>
  </AuthenticatedLayout>
</template> -->



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
      // Check if message already exists
      if (!messages.value.some(m => m.id === e.message.id)) {
        messages.value.push(e.message)
      }
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
    <div class="max-w-3xl mx-auto p-4 h-[80vh] flex flex-col">
      <div class="border-b pb-3 mb-3 font-bold">
        💬 {{ otherUser.name }}
      </div>

      <div class="flex-1 overflow-y-auto space-y-2">
        <div
          v-for="msg in messages"
          :key="msg.id"
          :class="msg.user_id === authId
            ? 'text-right'
            : 'text-left'"
        >
          <div class="inline-block px-3 py-2 rounded-xl bg-gray-100">
            {{ msg.content }}
          </div>
          <div v-if="msg.user_id === authId">
            <button @click="editMessage(msg)">✏️</button>
          </div>
        </div>
      </div>

      <div class="mt-3 flex gap-2">
        <input v-model="body" @keyup.enter="send" placeholder="اكتب رسالة..." class="flex-1 border rounded-xl px-3 py-2" />
        <button @click="send" class="bg-blue-600 text-white px-4 rounded-xl">إرسال</button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
