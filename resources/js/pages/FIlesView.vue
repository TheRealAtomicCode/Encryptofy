<template>
  <div class="min-h-screen bg-gray-950 text-white p-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Encrypted Files</h1>

      <div class="flex gap-3 items-center">


        <input
          v-if="auth.hasRole('admin')"
          v-model="customName"
          placeholder="Optional file name"
          class="bg-gray-800 px-3 py-2 rounded text-sm outline-none"
        />

        <button
          v-if="auth.hasRole('admin')"
          @click="triggerUpload"
          class="bg-gray-700 px-4 py-2 rounded"
        >
          Choose File
        </button>

        <button
          v-if="auth.hasRole('admin')"
          :disabled="!selectedFile"
          @click="uploadFile"
          class="bg-green-600 px-4 py-2 rounded disabled:opacity-50"
        >
          {{ uploading ? "Uploading..." : "Upload" }}
        </button>

        <input
          type="file"
          ref="fileInput"
          class="hidden"
          @change="handleFileSelect"
        />

      </div>
    </div>

    <!-- FILE LIST -->
    <div class="grid gap-3">

      <div
        v-for="file in files"
        :key="file.id"
        class="bg-gray-900 p-4 rounded flex justify-between items-center"
      >

        <div>
          <p class="font-semibold">{{ file.name }}</p>
          <p class="text-sm text-gray-400">ID: {{ file.id }}</p>
        </div>

        <div class="flex gap-2">

          <button
            v-if="auth.hasRole('admin') || auth.hasRole('manager')"
            @click="downloadFile(file.id, file.name)"
            class="bg-blue-600 px-3 py-1 rounded"
          >
            Download
          </button>

          <span v-else class="text-gray-500 text-sm">
            No access
          </span>

        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { onMounted, ref } from "vue"
import api from "../lib/api"
import { useAuthStore } from "../stores/auth"
import { useRouter } from "vue-router"

const files = ref([])
const auth = useAuthStore()
const router = useRouter()

const fileInput = ref(null)

const selectedFile = ref(null)
const customName = ref("")
const uploading = ref(false)

/* =========================
   LOAD FILES
========================= */
const loadFiles = async () => {
  const res = await api.get("/files")
  files.value = res.data.files
}


const triggerUpload = () => {
  fileInput.value.click()
}

const handleFileSelect = (e) => {
  selectedFile.value = e.target.files[0] || null
}


const uploadFile = async () => {
  if (!selectedFile.value) return

  const formData = new FormData()
  formData.append("file", selectedFile.value)

  formData.append(
    "name",
    customName.value.trim() !== "" ? customName.value : null
  )

  uploading.value = true
console.log(formData)
  try {
    await api.post("/files", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    })

    selectedFile.value = null
    customName.value = ""

    await loadFiles()
  } catch (err) {
    console.error(err)
  } finally {
    uploading.value = false
    fileInput.value.value = null
  }
}


const downloadFile = async (id, filename) => {
  const res = await api.get(`/files/${id}`, {
    responseType: "blob",
  })

  const url = window.URL.createObjectURL(new Blob([res.data]))

  const link = document.createElement("a")
  link.href = url
  link.setAttribute("download", filename)

  document.body.appendChild(link)
  link.click()

  link.remove()
  window.URL.revokeObjectURL(url)
}


const logout = () => {
  auth.logout()
  router.push("/login")
}


onMounted(() => {
  loadFiles()
})
</script>