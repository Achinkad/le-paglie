<script setup>
import { ref, onBeforeMount, inject, computed } from 'vue'
import { useCameraStore } from '../stores/camera.js'

const axiosApi = inject('axiosApi')

const isWebcamOpen = ref(false)
const arrayPhotos = ref([])

const cameraStore = useCameraStore()

const loadCameras = (() => { cameraStore.loadCameras() })
const cameras = computed(() => { return cameraStore.getCameras() })

const openWebcam = (() => {
    isWebcamOpen.value = true

    const width = 609
    let height = 0
    let streaming = false
    let video = null
    let photoCanvas = null
    let takePhotoBtn = null

    video = document.getElementById('video')
    takePhotoBtn = document.getElementById('takePhotoBtn')
    photoCanvas = document.getElementById('photoCanvas')

    navigator.mediaDevices
        .getUserMedia({ video: true, audio: false })
        .then((stream) => {
            video.srcObject = stream
            video.play()
        })
        .catch((err) => {
            console.error(`An error occurred: ${err}`)
        })

    video.addEventListener(
        'canplay',
        (ev) => {
            if (!streaming) {
                height = video.videoHeight / (video.videoWidth / width)

                if (isNaN(height)) {
                    height = width / (4 / 3)
                }

                video.setAttribute('width', width)
                video.setAttribute('height', height)
                streaming = true
            }
        },
        false
    )

    takePhotoBtn.addEventListener(
        'click',
        (ev) => {
            takepicture()
            ev.preventDefault()
        },
        false
    )
    function takepicture() {
        let div = document.createElement('div')
        let canvas = document.createElement('canvas')
        let context = canvas.getContext('2d')

        // Canvas -> Send to API
        let canvasAPI = document.createElement('canvas')
        let contextAPI = canvasAPI.getContext('2d')
        canvasAPI.width = width
        canvasAPI.height = height

        let canvasWidth = 210
        let canvasHeigth = 158

        canvas.width = canvasWidth
        canvas.height = canvasHeigth

        div.classList.add('col-3')

        context.drawImage(video, 0, 0, canvasWidth, canvasHeigth)
        contextAPI.drawImage(video, 0, 0, width, height)

        div.append(canvas)
        photoCanvas.append(div)

        const data = canvasAPI.toDataURL('image/png')
        arrayPhotos.value.push(data)
    }
})

const recognition = ref({ name: null, camera: null })

const sendRecognition = (() => {
    let formData = new FormData()

    formData.append('name', recognition.value.name)
    formData.append('camera', recognition.value.camera)

    arrayPhotos.value.forEach((element, index) => {
        formData.append('photo' + index, element)
    })

    cameraStore.registerPhotos(formData)
    Object.entries(recognition.value).forEach(([i]) => { recognition.value[i] = null })
})

// Get all cameras in the system
onBeforeMount(() => {
    loadCameras()
})
</script>

<template>
    <div class="row">
        <div class="col-12">
            <div class="p-title-box">
                <h2 class="p-title">Person Recognition</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-5">
                    <div class="card card-h-100">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Capture or Import a Picture</h4>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-12">
                                    <div class="beforePhotoFrame" v-if="!isWebcamOpen">
                                        <p class="m-0 text-center">
                                            <a href="#" @click="openWebcam()">Click here</a> to take a photo, or drop it to
                                            upload.
                                        </p>
                                    </div>
                                    <div v-show="isWebcamOpen">
                                        <video id="video">Video stream not available.</video>
                                    </div>
                                </div>
                                <div class="col-12 mt-3" v-show="isWebcamOpen">
                                    <button type="button" name="button" class="btn btn-primary w-100" id="takePhotoBtn">Take
                                        a photo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-7">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-h-100" v-show="arrayPhotos.length > 0">
                                <div class="d-flex card-header justify-content-between align-items-center">
                                    <h4 class="header-title">Photos Taken</h4>
                                </div>
                                <div class="card-body pt-0">
                                    <div class="row g-2 text-center" id="photoCanvas"></div>
                                </div>
                            </div>
                            <div class="callout mt-0" v-if="arrayPhotos.length == 0">
                                <b>Note:</b> Take a picture to view the frame taken here.
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card card-h-100">
                                <div class="d-flex card-header justify-content-between align-items-center">
                                    <h4 class="header-title">Form for Recognitions</h4>
                                </div>
                                <div class="card-body pt-0">
                                    <form class="row g-3 needs-validation" @submit.prevent="sendRecognition">
                                        <div class="col-6">
                                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" placeholder="Enter a name"
                                                v-model="recognition.name" required>
                                        </div>
                                        <div class="col-6">
                                            <label for="camera" class="form-label">Camera <span class="text-danger">*</span></label>
                                            <select name="camera" id="camera" class="form-select" v-model="recognition.camera" required>
                                                <option value="null" hidden disabled selected>Select a camera</option>
                                                <option v-for="camera in cameras" :key="camera.id" :value="camera.id">{{ camera.name }}</option>
                                            </select>
                                        </div>
                                        <div class="col-12 mt-4 d-flex justify-content-end">
                                            <div class="px-1">
                                                <button type="reset" class="btn btn-light px-4 me-1">Clear</button>
                                            </div>
                                            <div class="px-1">
                                                <button type="submit" class="btn btn-primary">Register recognition</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.beforePhotoFrame {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    background-color: #fafbfe;
    height: 50vh;
    border-radius: 4px;
    outline: 2px dashed #ececec;
    outline-offset: -15px;
}
</style>
