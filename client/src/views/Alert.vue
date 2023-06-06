<script setup>
import { inject,onBeforeMount,ref,computed,watch } from 'vue'
import { useAlertStore } from "../stores/alert.js"

const axiosApi = inject('axiosApi')

const alertStore = useAlertStore()

const loadAlerts = (() => { alertStore.loadAlerts() })
const alerts = computed(() => { return alertStore.getAlerts() })

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

const alert = ref({
    name: null,
    ip_address: null,
    location: null,
    username: null,
    password: null
})


onBeforeMount(() => {
    loadAlerts()
})
</script>

<template>
    <div class="row">
        <div class="col-12">
            <div class="p-title-box">
                
                <h2 class="p-title">Alerts </h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-h-100">
                <div class="d-flex card-header justify-content-between align-items-center">
                    <h4 class="header-title">List of all available alerts</h4>
                </div>
                <div class="card-body pt-0">
                    <table class="table table-responsive align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:7%">#ID</th>
                                <th>Camera Name</th>
                                <th>Camera Address</th>
                                <th>Camera Location</th>
                                <th>Message</th>
                                <th>Alert Date/Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="alerts.length==0">
                                <td colspan="6" class="text-center" style="height:55px!important;">There are no alerts.</td>
                            </tr>
                            <tr v-for="alert in alerts">
                                <td style="height:55px!important;">{{alert.id}}</td>
                                <td>{{alert.camera.name}}</td>
                                <td>{{alert.camera.ip_address}}</td>
                                <td>{{alert.camera.location}}</td>
                                <td>
                                    <span class="badge badge-danger-lighten">{{ alert.message }}</span>
                                </td>
                                <td>{{parseDate(alert.created_at)}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
