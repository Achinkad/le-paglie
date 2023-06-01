<script setup>
import { inject,onBeforeMount,ref,computed,watch } from 'vue'
import { useAlertStore } from "../stores/alert.js"

const axiosApi = inject('axiosApi')

const alertStore = useAlertStore()

const loadAlerts = (() => { alertStore.loadAlerts() })
const alerts = computed(() => { return alertStore.getAlerts() })

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
                                <th class="text-center" style="width:5%">#ID</th>
                                <th>Message</th>
                                <th>Camera ID</th>
                                <th>Creation Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="alerts.length==0">
                                <td colspan="4" class="text-center" style="height:55px!important;">There are no alerts.</td>
                            </tr>
                            <tr v-for="alert in alerts">

                                <td class="text-center" style="height:55px!important;">{{alert.id}}</td>

                                <td>{{alert.message}}</td>

                                <td>{{alert.camera_id}}</td>
  
                                <td>{{alert.created_at}}</td>

                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</template>
