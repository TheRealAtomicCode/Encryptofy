<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-950">
    <div class="w-full max-w-md bg-gray-900 p-8 rounded-2xl shadow-xl">
      <h1 class="text-2xl font-bold text-white mb-6">Encryptofy Login</h1>

      <input v-model="email" placeholder="Email"
        class="w-full mb-3 p-3 rounded bg-gray-800 text-white outline-none" />

      <input v-model="password" type="password" placeholder="Password"
        class="w-full mb-4 p-3 rounded bg-gray-800 text-white outline-none" />

      <button @click="login"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg">
        Login
      </button>

      <p class="text-red-400 mt-3" v-if="error">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { useAuthStore } from "../stores/auth"
import { useRouter } from "vue-router"

const email = ref("")
const password = ref("")
const error = ref("")

const auth = useAuthStore()
const router = useRouter()

const login = async () => {
  try {
    await auth.login(email.value, password.value)
    router.push("/")
  } catch (e) {
    error.value = "Invalid credentials"
  }
}
</script>