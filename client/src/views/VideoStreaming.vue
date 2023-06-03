<script setup>
import { ref, watch, computed, onBeforeMount } from 'vue'
import { loadPlayer } from 'rtsp-relay/browser'
import { useCameraStore } from '../stores/camera.js'
import { useAlertStore } from '../stores/alert.js'

const alertStore = useAlertStore()
const cameraStore = useCameraStore()

const cameraID = ref(null) // Camera ID
const canvas = ref(null) // Canvas to print the camera stream

const loadCameras = (() => { cameraStore.loadCameras() })
const cameras = computed(() => { return cameraStore.getCameras() })

// Get all the cameras in the system
onBeforeMount(() => {
    loadCameras()
})

// Load the camera stream according to the camera choosen
watch(cameraID, () => {
    let data = { id: cameraID.value }
    loadPlayer({
        url: 'ws://localhost:2000/api/stream',
        canvas: canvas.value
    })
})
</script>

<template>
    <div class="row">
        <div class="col-12">
            <div class="p-title-box">
                <div class="p-title-right" style="width:15%;">
                    <select class="form-select" v-model="cameraID">
                        <option value="null" selected hidden disabled v-if="cameras.length > 0">Select a camera</option>
                        <option value="null" selected hidden disabled v-else>Loading cameras...</option>
                        <option v-for="camera in cameras" :key="camera.id" :value="camera.id" :disabled="camera.disabled">{{
                            camera.name }}</option>
                    </select>
                </div>
                <h2 class="p-title">Video Streaming</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-h-100">
                <div class="d-flex card-header justify-content-between align-items-center">
                    <h4 class="header-title">Video streaming for the choosen camera</h4>
                </div>
                <div class="card-body pt-0">
                    <p v-if="cameraID == null">Please select a camera to watch the current stream.</p>
                    <canvas id="canvas" ref="canvas" style="width: 100%;" v-show="cameraID"></canvas>
                </div>
            </div>
        </div>
    </div>
</template>
