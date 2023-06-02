<script setup>
import { ref, onMounted } from 'vue'
import { loadPlayer } from 'rtsp-relay/browser'
import { useAlertStore } from "../stores/alert.js"

const alertStore = useAlertStore()
const canvas = ref(null)

onMounted(() => {
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
                <h2 class="p-title">Video Streaming</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card card-h-100">
                <div class="d-flex card-header justify-content-between align-items-center">
                    <h4 class="header-title">Video streaming for the webcam 192.168.10.243</h4>
                </div>
                <div class="card-body pt-0">
                    <canvas id="canvas" width="300" height="300" ref="canvas"></canvas>
                </div>
            </div>
        </div>
    </div>
</template>
