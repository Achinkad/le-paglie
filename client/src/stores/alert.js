import { inject, ref } from 'vue'
import { defineStore } from 'pinia'

export const useAlertStore = defineStore('alert', () => {
    const axiosApi = inject('axiosApi') // Axios
    const notyf = inject('notyf') // Notyf
    
    const socket = new WebSocket('ws://localhost:8080/');
    socket.binaryType = 'blob'

    const alerts = ref([]) // Cameras

    async function loadAlerts() {
        await axiosApi.get('alerts').then(response => {
            alerts.value = response.data
            console.log(response)
        }).catch(error => {
            notyf.error(error.response.data + " (" + error.response.status + ")")
        })
    }

    const getAlerts = (() => { return alerts.value })

    async function registerAlert(message,camera_ip) {
        let formData = new FormData()

        formData.append('message', message)
        formData.append('camera_ip', camera_ip)
       
        await axiosApi.post('alerts/create',formData).then((response) => {
            
            alerts.value.push(response.data.data)
            
        }).catch((error) => {
            notyf.error(error.response.data + " (" + error.response.status + ")")
        })
    }


    socket.onopen = () => {
        console.log('Connected to the server.');
      
        socket.onmessage = (event) => {
        
            if (event.data instanceof Blob) {
                reader = new FileReader();
        
                reader.onload = () => {
                    
                    registerAlert(reader.result,'192.168.56.107')
                    notyf.error(reader.result)  
                };
        
                reader.readAsText(event.data);
            } else {
                registerAlert(event.data,'192.168.56.107')
                notyf.error(event.data)
            }
        
        };
    };
    

    return {
        loadAlerts,
        getAlerts,
        registerAlert
    }
})
