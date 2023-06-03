<script setup>
import { ref, onBeforeMount, computed, watch } from 'vue'
import { useCameraStore } from '../stores/camera.js'
import { useAlertStore } from '../stores/alert.js'

const cameraStore = useCameraStore()
const alertStore = useAlertStore()

const cameraID = ref(null) // Camera ID

const loadCameras = (() => { cameraStore.loadCameras() })
const cameras = computed(() => { return cameraStore.getCameras() })

const loadRecognitions = (() => { cameraStore.loadRecognitions() })
const recognitions = computed(() => { return cameraStore.getRecognitions() })

const deleteRecognition = ((recon) => { cameraStore.deleteRecognition(recon, cameraID.value) })

const parseDate = ((date) => {
    const newDate = new Date(date)

    const day = newDate.getDate()
    const month = newDate.getMonth() + 1
    const year = newDate.getFullYear()

    const hours = newDate.getHours()
    const minutes = newDate.getMinutes()
    const seconds = newDate.getSeconds()

    const formattedDate = `${day < 10 ? '0' : ''}${day}/${month < 10 ? '0' : ''}${month}/${year}`
    const formattedTime = `${hours < 10 ? '0' : ''}${hours}:${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`

    return formattedDate + ' ' + formattedTime
})

// Get all the cameras in the system
onBeforeMount(() => {
    loadCameras()
})

// Load the recognitions according to the camera choosen
watch(cameraID, () => {
    let data = { id: cameraID.value }
    loadRecognitions(data)
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
                <h2 class="p-title">All Recognitions</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card card-h-100">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">All the recognitions registered in the system</h4>
                        </div>
                        <div class="card-body pt-0">
                            <table class="table table-responsive align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width:5%">#ID</th>
                                        <th>Camera Address</th>
                                        <th>Camera Name</th>
                                        <th>Camera Location</th>
                                        <th>Recognition Name</th>
                                        <th style="width:15%;">Creation Date</th>
                                        <th class="text-center" style="width:13%;">Number of Photos</th>
                                        <th class="text-center" style="width:10%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="recognitions.length == 0">
                                        <td colspan="8" class="text-center" style="height:55px!important;">
                                            Please, select a camera to view the recognitions in that camera.</td>
                                    </tr>
                                    <tr v-for="recon in recognitions">
                                        <td class="text-center" style="height:55px!important;">{{ recon.id }}</td>
                                        <td>{{ recon.camera.ip_address }}</td>
                                        <td>{{ recon.camera.name }}</td>
                                        <td>{{ recon.camera.location }}</td>
                                        <td>{{ recon.name }}</td>
                                        <td>{{ parseDate(recon.created_at) }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-info-lighten">{{ JSON.parse(recon.photos).length }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-xs btn-light table-button" title="Delete Recognition" @click="deleteRecognition(recon)">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.badge-info-lighten {
    padding-left: 13px;
    padding-right: 13px;
    padding-top: 5px;
    padding-bottom: 5px;
}

</style>