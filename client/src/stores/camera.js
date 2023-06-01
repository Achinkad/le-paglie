import { inject, ref } from 'vue'
import { defineStore } from 'pinia'

export const useCameraStore = defineStore('camera', () => {
    const axiosApi = inject('axiosApi') // Axios
    const notyf = inject('notyf') // Notyf
    
    const socket = new WebSocket('ws://localhost:8080/');
    socket.binaryType = 'blob'

    const cameras = ref([]) // Cameras

    async function loadCameras(body) {
        await axiosApi.get('cameras', { params: body }).then(response => {
            cameras.value = response.data
        }).catch(error => {
            notyf.error(error.response.data + " (" + error.response.status + ")")
        })
    }

    const getCameras = (() => { return cameras.value })

    async function registerCamera(data) {
        await axiosApi.post('cameras/create', data).then((response) => {
            cameras.value.push(response.data.data)
            notyf.success('The camera was registered with success.')
        }).catch((error) => {
            notyf.error(error.response.data + " (" + error.response.status + ")")
        })
    }
    

    return {
        loadCameras,
        getCameras,
        registerCamera

    }
})
