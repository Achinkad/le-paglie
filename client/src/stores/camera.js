import { inject, ref } from 'vue'
import { defineStore } from 'pinia'

export const useCameraStore = defineStore('camera', () => {
    const axiosApi = inject('axiosApi') // Axios
    const notyf = inject('notyf') // Notyf
    
    const socket = new WebSocket('ws://localhost:8080/');
    socket.binaryType = 'blob'

    const cameras = ref([]) // Cameras
    const photos = ref([]) // Photos

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
    
    async function registerPhotos(data) {
        await axiosApi.post('photos/create', data).then((response) => {
            photos.value.push(response.data)
            notyf.success('The photos were uploaded with success.')
        }).catch((error) => {
            notyf.error(error.response.data + " (" + error.response.status + ")")
        })
    }

    async function loadRecognitions(body) {
        await axiosApi.get('photos', { params: body }).then(response => {
            photos.value = response.data
        }).catch(error => {
            notyf.error(error.response.data + " (" + error.response.status + ")")
        })
    }

    const getRecognitions = (() => { return photos.value })

    async function deleteRecognition(recon, cameraID) {
        let data = { id: cameraID }

        await axiosApi.delete('photos/delete/' + recon.id,  { params: data }).then(response => {
            notyf.success('The recognition was deleted with success.')

            // Remove from the array of photos
            let index = photos.value.indexOf(recon)
            if (index > -1) photos.value.splice(index, 1)
        }).catch(error => {
            console.log(error)
            notyf.error(error.response.data + " (" + error.response.status + ")")
        })
    }

    return {
        loadCameras,
        getCameras,
        registerCamera,
        registerPhotos,
        loadRecognitions,
        getRecognitions,
        deleteRecognition
    }
})
