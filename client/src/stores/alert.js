import { inject, ref } from 'vue'
import { defineStore } from 'pinia'

export const useAlertStore = defineStore('alert', () => {
    const axiosApi = inject('axiosApi') // Axios
    const notyf = inject('notyf') // Notyf
    
    const socket = new WebSocket('ws://10.10.88.244:8080/');
    socket.binaryType = 'blob'

    const alerts = ref([]) // Cameras

    async function loadAlerts() {
        await axiosApi.get('alerts').then(response => {
            alerts.value = response.data
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
                    
                    registerAlert(reader.result.split(':')[0],reader.result.split(':')[1])
                    
                    notyf.error(reader.result.split(':')[0])  
                };
        
                reader.readAsText(event.data);
            } else {
                
                registerAlert(event.data.split(':')[0],event.data.split(':')[1])
                notyf.error(event.data.split(':')[0])
            }
        
        };
    };
    

    return {
        loadAlerts,
        getAlerts,
        registerAlert
    }
})
