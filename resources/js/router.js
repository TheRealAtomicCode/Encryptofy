import { createRouter, createWebHistory } from "vue-router"
import Login from "./pages/LoginView.vue"
import Files from "./pages/FIlesView.vue"

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/login",
      component: Login,
    },
    {
      path: "/",
      component: Files,
      meta: { requiresAuth: true },
    },
  ],
})

// GLOBAL GUARD
router.beforeEach((to) => {
  const token = localStorage.getItem("token")

  if (to.meta.requiresAuth && !token) {
    return "/login"
  }
})

export default router