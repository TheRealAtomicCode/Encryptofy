import { defineStore } from "pinia"
import api from "../lib/api"

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    roles: [],
    authenticated: false,
  }),

  actions: {
    async login(email, password) {
      const res = await api.post("/login", { email, password })

      localStorage.setItem("token", res.data.token)

      this.user = res.data.user
      this.roles = res.data.user.roles
      this.authenticated = true
    },

    async me() {
      const res = await api.get("/me")

      this.user = res.data.user
      this.roles = res.data.roles
      this.authenticated = res.data.authenticated
    },

    logout() {
      localStorage.removeItem("token")
      this.user = null
      this.roles = []
      this.authenticated = false
    },

    hasRole(role) {
      return this.roles.includes(role)
    },
  },
})