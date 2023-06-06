<script setup>
import { ref, inject, computed, onBeforeMount } from 'vue'

import { useCameraStore } from '../stores/camera.js'
import { useAlertStore } from '../stores/alert.js'

import { loadPlayer } from 'rtsp-relay/browser'

const notyf = inject('notyf')
const axiosApi = inject('axiosApi')

const cameraStore = useCameraStore()
const alertStore = useAlertStore()

const loadCameras = (() => { cameraStore.loadCameras() })
const cameras = computed(() => { return cameraStore.getCameras() })

const loadAlerts = (() => { alertStore.loadAlerts() })
const alerts = computed(() => { return alertStore.getAlerts() })

const loadRecognitions = (() => { cameraStore.loadRecognitions() })
const recognitions = computed(() => { return cameraStore.getRecognitions() })

const cameraStreaming = ref(false)
const canvas = ref(null)

const showStream = ((camera) => {
    cameraStreaming.value = cameraStreaming.value == false ? camera : false
    loadPlayer({
        url: 'ws://localhost:2000/api/stream',
        canvas: canvas.value
    })
})

const activeCameras = (() => {
    if (cameras.value.length == 0) return

    let active = 0

    cameras.value.forEach(i => {
        if (!i.disabled) active++
    })

    return (active / cameras.value.length * 100).toFixed(0) + "%"
})

const lastWeek = (() => {
    if (alerts.value.length == 0) return

    let lastWeekAlerts = 0
    let currentDate = new Date()
    let oneWeekAgo = new Date()

    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7)

    alerts.value.forEach(i => {
        let alertDate = new Date(i.created_at)
        if (alertDate >= oneWeekAgo && alertDate <= currentDate) lastWeekAlerts++
    })

    return (lastWeekAlerts / alerts.value.length * 100).toFixed(0) + "%"
})

const lastWeekRecons = (() => {
    if (recognitions.value.length == 0) return

    let lastWeekRecons = 0
    let currentDate = new Date()
    let oneWeekAgo = new Date()

    oneWeekAgo.setDate(oneWeekAgo.getDate() - 7)

    recognitions.value.forEach(i => {
        let reconDate = new Date(i.created_at)
        if (reconDate >= oneWeekAgo && reconDate <= currentDate) lastWeekRecons++
    })

    return (lastWeekRecons / recognitions.value.length * 100).toFixed(0) + "%"
})

const tooglePet = ((camera) => {
    let body = {
        camera: camera.id,
        pet: camera.pet_identification
    }

    axiosApi.patch('cameras/pet/' + camera.id, body).then((response) => {
        if (body.pet) {
            notyf.success('The pet identification was disabled with success.')
        } else {
            notyf.success('The pet identification was activated with success.')
        }

        loadCameras()
    }).catch((error) => {
        notyf.error('Oops, an error has occurred.')
    })
})

const petIdentifications = (() => {
    if (cameras.value.length == 0) return
    let petActive = 0
    cameras.value.forEach(i => { if (i.pet_identification == '1') petActive++ })
    return petActive
})

const petIdentificationsPercentage = (() => {
    if (cameras.value.length == 0) return
    let petActive = 0
    cameras.value.forEach(i => { if (i.pet_identification == '1' && !i.disabled) petActive++ })
    return (petActive / cameras.value.length * 100).toFixed(0) + "%"
})

// Get all cameras in the system
onBeforeMount(() => {
    loadCameras()
    loadAlerts()
    loadRecognitions()
})
</script>

<template>
    <div class="row">
        <div class="col-12">
            <div class="p-title-box">
                <h2 class="p-title">Dashboard</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-5 col-lg-12">
            <div class="row">
                <div class="col-md-12 col-lg-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <div class="float-end">
                                <i class="bi bi-camera2 card-icon"></i>
                            </div>
                            <h5 class="text-muted fw-normal mt-0">Wi-Fi Cameras</h5>
                            <h3 class="mt-3 mb-3" v-if="cameras.length > 0">{{ cameras.length }}</h3>
                            <h3 class="mt-3 mb-3" v-else> - </h3>
                            <p class="mb-0 text-muted" v-if="cameras.length > 0">
                                <span class="me-2" :class="{ 'text-success': activeCameras() != '0%', 'text-danger': activeCameras() == '0%' }"> {{ activeCameras() }} </span>
                                <span class="text-nowrap">Active right now</span>
                            </p>
                            <p class="mb-0 text-muted" v-else>
                                <span class="text-success me-2"> - % </span>
                                <span class="text-nowrap">Active right now</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <div class="float-end">
                                <i class="bi bi-exclamation-triangle-fill card-icon"></i>
                            </div>
                            <h5 class="text-muted fw-normal mt-0">Alerts</h5>
                            <h3 class="mt-3 mb-3" v-if="alerts.length > 0">{{ alerts.length }}</h3>
                            <h3 class="mt-3 mb-3" v-else> - </h3>
                            <p class="mb-0 text-muted" v-if="alerts.length > 0">
                                <span class="me-2" :class="{ 'text-success': lastWeek() != '0%', 'text-danger': lastWeek() == '0%' }"> {{ lastWeek() }} </span>
                                <span class="text-nowrap">In last week</span>
                            </p>
                            <p class="mb-0 text-muted" v-else>
                                <span class="text-success me-2"> - % </span>
                                <span class="text-nowrap">In last week</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <div class="float-end">
                                <i class="bi bi-person-fill card-icon"></i>
                            </div>
                            <h5 class="text-muted fw-normal mt-0">Persons</h5>
                            <h3 class="mt-3 mb-3" v-if="recognitions.length > 0">{{ recognitions.length }}</h3>
                            <h3 class="mt-3 mb-3" v-else> - </h3>
                            <p class="mb-0 text-muted">
                                <span class="me-2" v-if="recognitions.length > 0"
                                    :class="{ 'text-success': lastWeekRecons() != '0%', 'text-danger': lastWeekRecons() == '0%' }">
                                    {{ lastWeekRecons() }}
                                </span>
                                <span class="text-success me-2" v-else> - % </span>
                                <span class="text-nowrap">Added last week</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6">
                    <div class="card widget-flat">
                        <div class="card-body">
                            <div class="float-end">
                                <i class="bi bi-browser-firefox card-icon"></i>
                            </div>
                            <h5 class="text-muted fw-normal mt-0">Pets</h5>
                            <h3 class="mt-3 mb-3" v-if="cameras.length > 0">{{ petIdentifications() }}</h3>
                            <h3 class="mt-3 mb-3" v-else> - </h3>
                            <p class="mb-0 text-muted" v-if="cameras.length > 0">
                                <span class="me-2" 
                                    :class="{ 'text-success': petIdentificationsPercentage() != '0%', 'text-danger': petIdentificationsPercentage() == '0%' }"> 
                                    {{ petIdentificationsPercentage() }} 
                                </span>
                                <span class="text-nowrap">Active right now</span>
                            </p>
                            <p class="mb-0 text-muted" v-else>
                                <span class="text-success me-2"> - % </span>
                                <span class="text-nowrap">In last week</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-7 col-lg-7">
            <div class="row">
                <div class="col-12">
                    <div class="card card-h-100">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Registered cameras</h4>
                        </div>
                        <div class="card-body pt-0">
                            <table class="table table-responsive align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:8%">#ID</th>
                                        <th style="width:13%">Name</th>
                                        <th style="width:18%">IP address</th>
                                        <th style="width:11%">Location</th>
                                        <th class="text-center" style="width:20%">Pets Identification</th>
                                        <th class="text-center" style="width:10%">Active</th>
                                        <th class="text-center" style="width:14%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="cameras.length == 0">
                                        <td colspan="7" class="text-center" style="height:55px!important;">There are no
                                            cameras registered in the system.</td>
                                    </tr>
                                    <tr v-for="camera in cameras" :key="camera.id">
                                        <td class="align-middle" style="height:55px!important;">#{{ camera.id }}</td>
                                        <td>{{ camera.name }}</td>
                                        <td>{{ camera.ip_address }}</td>
                                        <td>{{ camera.location }}</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch">
                                                <div class="d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        @click="tooglePet(camera)"
                                                        :checked="camera.pet_identification == true">
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center" v-if="camera.disabled">
                                            <span class="badge badge-danger-lighten">Disabled</span>
                                        </td>
                                        <td class="text-center" v-else>
                                            <span class="badge badge-success-lighten">Active</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-xs btn-light table-button"
                                                    :disabled="camera.disabled" title="View Stream"
                                                    @click="showStream(camera)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12" v-show="cameraStreaming">
                    <div class="card card-h-100">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Stream for the camera in the {{ cameraStreaming.name }} ({{
                                cameraStreaming.ip_address }})</h4>
                        </div>
                        <div class="card-body pt-0">
                            <canvas id="canvas" ref="canvas" style="width:100%!important;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.widget-flat {
    position: relative;
    overflow: hidden;
}

.card {
    border: none;
    margin-bottom: 24px;
    box-shadow: 0 0 35px 0 rgba(154, 161, 171, 0.15) !important;
    border-radius: 3px;
}

.card-header {
    margin-top: 0;
    background-color: #fff;
    border: 0;
    margin-bottom: 0;
    padding: 1.5rem;
}

.card-header .header-title {
    text-transform: uppercase;
    letter-spacing: 0.02em;
    font-size: 0.9rem;
    margin-top: 0;
}

.card-body {
    padding: 1.5rem;
}

.card-icon {
    color: #727cf5;
    font-size: 16px;
    background-color: rgba(114, 124, 245, 0.25);
    height: 40px;
    width: 40px;
    text-align: center;
    line-height: 40px;
    border-radius: 3px;
    display: inline-block;
}

.text-muted {
    color: #8a969c !important;
}

.mt-3 {
    margin-top: 1.5rem !important;
}

.mb-3 {
    margin-bottom: 1.5rem !important;
}

.me-2 {
    margin-right: 0.75rem !important;
}

.text-success {
    color: rgb(10, 207, 151) !important;
}

.text-danger {
    color: rgb(250, 92, 124) !important;
}

#arrow-icons {
    font-size: 14.4px;
    margin: 0;
}

.bi {
    margin: 0 !important;
}

.text-nowrap {
    font-size: 14.4px;
    color: rgb(138, 150, 156);
}</style>
