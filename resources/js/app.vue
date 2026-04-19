<template>
  <div class="min-h-screen bg-gray-950 text-white">

    <nav class="flex items-center justify-between px-6 py-4 bg-gray-900 border-b border-gray-800">

      <div class="font-bold text-lg">
        Encryptofy
      </div>

      <div class="flex items-center gap-4">

        <span v-if="auth.user" class="text-sm text-gray-300">
          {{ auth.user.name }} ({{ auth.roles?.[0] }})
        </span>

        <button
          v-if="auth.user"
          @click="logout"
          class="px-3 py-1 bg-red-600 hover:bg-red-700 rounded"
        >
          Logout
        </button>

      </div>

    </nav>

    <main class="p-6">
      <router-view />
    </main>

  </div>
</template>

<script setup>
import { onMounted } from "vue"
import { useAuthStore } from "./stores/auth"
import { useRouter } from "vue-router"

const auth = useAuthStore()
const router = useRouter()

const logout = () => {
  auth.logout()
  router.push("/login")
}

onMounted(async () => {
  const token = localStorage.getItem("token")

  if (token) {
    try {
      await auth.me()
    } catch {
      auth.logout()
      router.push("/login")
    }
  }
})
</script>