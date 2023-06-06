<script setup>
import { ref, onBeforeMount, computed, watch } from 'vue'
import { useCameraStore } from '../stores/camera.js'
import { useAlertStore } from '../stores/alert.js'

const cameraStore = useCameraStore()
const alertStore = useAlertStore()

const cameraID = ref(null) // Camera ID

const loadCameras = (() => { cameraStore.loadCameras() })
const cameras = computed(() => { return cameraStore.getCameras() })

const loadPets = ((data) => { cameraStore.loadPets(data) })
const pets = computed(() => { return cameraStore.getPets() })

// const deleteRecognition = ((recon) => { cameraStore.deleteRecognition(recon, cameraID.value) })

const newPetIdentification = ref({ camera: null, action: null, pet: null })

const registerPet = () => {
    let formData = new FormData()

    formData.append('camera_id', newPetIdentification.value.camera)
    formData.append('pet_name', newPetIdentification.value.pet)
    formData.append('pet_action', newPetIdentification.value.action)

    cameraStore.registerPet(formData)
    Object.entries(newPetIdentification.value).forEach(([i]) => { newPetIdentification.value[i] = null })
}

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
    loadPets(data)
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
                        <option v-for="camera in cameras" :key="camera.id" :value="camera.id">{{ camera.name }}</option>
                    </select>
                </div>
                <h2 class="p-title">All Pet Identifications</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-8">
                    <div class="card card-h-100">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">All the Pet Identifications registered in the system</h4>
                        </div>
                        <div class="card-body pt-0">
                            <table class="table table-responsive align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width:6%">#ID</th>
                                        <th>Cam. Address</th>
                                        <th>Cam. Name</th>
                                        <th>Cam. Location</th>
                                        <th>Pet Name</th>
                                        <th>Pet Action</th>
                                        <th style="width:15%;">Creation Date</th>
                                        <th class="text-center" style="width:10%;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="pets.length == 0">
                                        <td colspan="8" class="text-center" style="height:55px!important;">
                                            Please, select a camera to view the pets identification in that camera.</td>
                                    </tr>
                                    <tr v-for="pet in pets">
                                        <td class="text-center" style="height:55px!important;">{{ pet.id }}</td>
                                        <td>{{ pet.ip_address }}</td>
                                        <td>{{ pet.name }}</td>
                                        <td>{{ pet.location }}</td>
                                        <td style="text-transform: capitalize;">{{ pet.pet_name }}</td>
                                        <td style="text-transform: capitalize;">{{ pet.pet_action }}</td>
                                        <td>{{ parseDate(pet.created_at) }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-xs btn-light table-button" title="Delete Recognition"
                                                    @click="deleteRecognition(recon)">
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
                <div class="col-4">
                    <div class="card card-h-100">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Add a new pet identification</h4>
                        </div>
                        <div class="card-body pt-0">
                            <form class="row g-3 needs-validation" @submit.prevent="registerPet">
                                <div class="col-12">
                                    <label for="camera" class="form-label">Camera <span class="text-danger">*</span></label>
                                    <select name="camera" id="camera" class="form-select" v-model="newPetIdentification.camera"
                                        required>
                                        <option value="null" hidden disabled selected>Select a camera</option>
                                        <option v-for="camera in cameras" :key="camera.id" :value="camera.id">{{ camera.name
                                        }}</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="pet" class="form-label">Pet to identify <span class="text-danger">*</span></label>
                                    <select name="pet" id="pet" class="form-select" v-model="newPetIdentification.pet" required>
                                        <option value="null" hidden disabled selected>Select a pet</option>
                                        <option value="cat">Cat</option>                               
                                        <option value="dog">Dog</option>                          
                                        <option value="bird">Bird</option>                              
                                        <option value="horse">Horse</option>                             
                                        <option value="sheep">Sheep</option>                            
                                        <option value="cow">Cow</option>                          
                                        <option value="bear">Bear</option>                             
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label for="action" class="form-label">Action <span class="text-danger">*</span></label>
                                    <select name="action" id="action" class="form-select" v-model="newPetIdentification.action" required>
                                        <option value="null" hidden disabled selected>Select an option</option>
                                        <option value="identify">To identify</option>                               
                                        <option value="alert">To alert</option>                               
                                    </select>
                                </div>
                                <div class="col-12 mt-4 d-flex justify-content-end">
                                    <div class="px-1">
                                        <button type="reset" class="btn btn-light px-4 me-1">Clear</button>
                                    </div>
                                    <div class="px-1">
                                        <button type="submit" class="btn btn-primary">Register identification</button>
                                    </div>
                                </div>
                            </form>
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
}</style>