<script setup>
import { ref, inject, computed, onBeforeMount } from 'vue'
import { useCameraStore } from '../stores/camera.js'
import { useAlertStore } from "../stores/alert.js"
const axiosApi = inject('axiosApi')
const notyf = inject('notyf')


const cameraStore = useCameraStore()
const alertStore = useAlertStore()
const loadCameras = (() => { cameraStore.loadCameras() })
const cameras = computed(() => { return cameraStore.getCameras() })

const camera = ref({
    name: null,
    ip_address: null,
    location: null,
    username: null,
    password: null
})

const registerCamera = () => {
    let formData = new FormData()

    formData.append('name', camera.value.name)
    formData.append('ip_address', camera.value.ip_address)
    formData.append('location', camera.value.location)
    formData.append('authorization', btoa(camera.value.username + ':' + camera.value.password))

    cameraStore.registerCamera(formData)
    Object.entries(camera.value).forEach(([i]) => { camera.value[i] = null })
}

// Copy camera authorization into the clipboard
const copy = ((camera) => {
    navigator.clipboard.writeText(decodeAuthorization(camera.authorization))
    notyf.open({type: 'info', message: 'The RTSP authorization is now on your clipboard. Go paste it!'})
})

const decodeAuthorization = ((authorization) => {
    return atob(authorization)
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
                <h2 class="p-title">Cameras</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
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
                                        <th style="width:15%">Name</th>
                                        <th style="width:18%">IP address</th>
                                        <th style="width:15%">Location</th>
                                        <th>Authorization</th>
                                        <th class="text-center" style="width:14%">Active</th>
                                        <th class="text-center" style="width:12%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="cameras.length == 0">
                                        <td colspan="7" class="text-center" style="height:55px!important;">There are no cameras registered in the system.</td>
                                    </tr>
                                    <tr v-for="camera in cameras" :key="camera.id">
                                        <td class="align-middle" style="height:55px!important;">#{{ camera.id }}</td>
                                        <td>{{ camera.name }}</td>
                                        <td>{{ camera.ip_address }}</td>
                                        <td>{{ camera.location }}</td>
                                        <td style="white-space: nowrap; text-overflow:ellipsis; overflow: hidden; max-width:1px; cursor:pointer;"
                                        @click="copy(camera)" title="Click to copy to your clipboard!"><u>{{ decodeAuthorization(camera.authorization) }}</u></td>
                                        <td class="text-center" v-if="camera.disabled">
                                            <span class="badge badge-danger-lighten">Disabled</span>
                                        </td>
                                        <td class="text-center" v-else>
                                            <span class="badge badge-success-lighten">Active</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-xs btn-light table-button" title="Backup">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="callout">
                        <b>Note:</b> You can click in the authorization code to copy it to your clipboard automatically.
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card card-h-100">
                <div class="d-flex card-header justify-content-between align-items-center">
                    <h4 class="header-title">Register a new camera</h4>
                </div>
                <div class="card-body pt-0">
                    <form class="row g-3 needs-validation" @submit.prevent="registerCamera">
                        <div class="col-4">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" placeholder="Enter a name"
                            v-model="camera.name" required>
                        </div>
                        <div class="col-4">
                            <label for="ip_address" class="form-label">IP address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ip_address" placeholder="Enter an IP address"
                            pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                            v-model="camera.ip_address" required>
                        </div>
                        <div class="col-4">
                            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location" placeholder="Enter a location"
                            v-model="camera.location" required>
                        </div>
                        <div class="col-6">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username"
                            placeholder="Enter a username" v-model="camera.username" required>
                        </div>
                        <div class="col-6">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password"
                            placeholder="Enter a password" v-model="camera.password" required>
                        </div>
                        <div class="col-12 mt-4 d-flex justify-content-end">
                            <div class="px-1">
                                <button type="reset" class="btn btn-light px-4 me-1">Clear</button>
                            </div>
                            <div class="px-1">
                                <button type="submit" class="btn btn-primary">Register camera</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
