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
          :disabled="!selectedFile || uploading"
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

    <!-- ERROR -->
    <div v-if="errorMessage" class="mb-4 text-red-400">
      {{ errorMessage }}
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
const errorMessage = ref("")

const MAX_SIZE = 2 * 1024 * 1024 // 2MB

/* =========================
   LOAD FILES
========================= */
const loadFiles = async () => {
  const res = await api.get("/files")
  files.value = res.data.files
}

/* =========================
   FILE SELECT
========================= */
const handleFileSelect = (e) => {
  const file = e.target.files[0]

  errorMessage.value = ""

  if (!file) {
    selectedFile.value = null
    return
  }

  if (file.size > MAX_SIZE) {
    errorMessage.value = "File is too large. Maximum allowed size is 2MB."
    selectedFile.value = null
    fileInput.value.value = null
    return
  }

  selectedFile.value = file
}

/* =========================
   UPLOAD
========================= */
const triggerUpload = () => {
  fileInput.value.click()
}

const uploadFile = async () => {
  if (!selectedFile.value) return

  errorMessage.value = ""

  const formData = new FormData()
  formData.append("file", selectedFile.value)

  const name = customName.value.trim()
  if (name !== "") {
    formData.append("name", name)
  }

  uploading.value = true

  try {
    await api.post("/files", formData)

    selectedFile.value = null
    customName.value = ""

    await loadFiles()
  } catch (err) {
    errorMessage.value =
      err.response?.data?.message || "Upload failed"
  } finally {
    uploading.value = false
    fileInput.value.value = null
  }
}

/* =========================
   DOWNLOAD
========================= */
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

/* =========================
   INIT
========================= */
onMounted(() => {
  loadFiles()
})
</script>