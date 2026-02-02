<template>
    <AuthenticatedLayout>
        <div class="chat-container">


            <div v-if="messagesarray.length" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div
                v-for="message in messagesarray"
                :key="message.id"
                class="flex items-center justify-between bg-gray-50 hover:bg-indigo-50 border rounded-xl p-4 transition"
                >
                    {{ message.user.name }}

                    {{ message.content }}
                </div>
            </div>


        <ul>
            <li v-for="(message, index) in messages" :key="index">
                {{ message.user_id }} - {{ message.content }}
            </li>
        </ul>
            <div class="inputs">
            <input v-model="user_id" placeholder="اسمك" />
            <input v-model="content" placeholder="رسالتك" @keyup.enter="send" />
            <button @click="send">إرسال</button>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';


defineProps({
  messagesarray: Array
})

const page = usePage();

// ⚡️ هنا نتحقق من أن الرسائل موجودة
const messages = ref([]);
onMounted(() => {
  messages.value = page.props.value?.messages ?? [];
});

const user_id = ref('');
const content = ref('');

// إرسال رسالة
const send = async () => {
  if (!user_id.value || !content.value) return;

  try {
    const res = await fetch('/chat/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ user_id: user_id.value, content: content.value })
    });

    const data = await res.json();
    // messages.value.push(data.message); // إضافة الرسالة مباشرة
    user_id.value = '';
    content.value = '';
  } catch (e) {
    console.error(e);
  }
};

// استقبال الرسائل الجديدة عبر Echo
onMounted(() => {
  if (!window.Echo) return console.error('Echo غير معرف ❌');

  window.Echo.channel('chat')
    .listen('.MessageSent', (e) => {
      messages.value.push(e.message);
    });
});
</script>

<style scoped>
.chat-container {
  max-width: 500px;
  margin: 0 auto;
}
ul {
  list-style: none;
  padding: 0;
}
.inputs {
  display: flex;
  gap: 5px;
  margin-top: 10px;
}
input {
  flex: 1;
  padding: 5px;
}
</style>
