import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from "../stores/user.js";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'Dashboard',
            component: () => import('../views/Dashboard.vue')
        },
        {
            path: '/cameras',
            name: 'Cameras',
            component: () => import('../views/Cameras.vue')
        },
        {
            path: '/video-streaming',
            name: 'VideoStreaming',
            component: () => import('../views/VideoStreaming.vue')
        },
        {
            path: '/person-recognition',
            name: 'PersonRecognition',
            component: () => import('../views/PersonRecognition.vue')
        },
        {
            path: '/recognitions',
            name: 'Recognitions',
            component: () => import('../views/Recognitions.vue')
        },
        {
            path: '/alert',
            name: 'Alert',
            component: () => import('../views/Alert.vue')
        },
        {
            path: '/login',
            name: 'Login',
            component: () => import('../views/Login.vue')
        },
        {
            path: '/register',
            name: 'Register',
            component: () => import('../views/Register.vue')
        }
    ]
})

let handlingFirstRoute = true

router.beforeEach(async (to, from, next) => {
    const userStore = useUserStore()

    if (handlingFirstRoute) {
        handlingFirstRoute = false
        await userStore.restoreToken()
    }

    if (to.name == "Login" || to.name == "Register") {
        next()
        return
    } else {
        if (!userStore.user) {
            next({ name: "Login" })
            return
        }
        next()
    }
})

export default router
