<script setup>
import { ref, onMounted } from 'vue'
import { useAlertStore } from "../stores/alert.js"

const alertStore = useAlertStore()
</script>

<template>
    <div class="row">
        <div class="col-12">
            <div class="p-title-box">
                <h2 class="p-title">Persons & Pets Identifications</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-5">
                    <div class="card card-h-100">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Capture or Import a Picture</h4>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div class="col-12">
                                    <div class="beforePhotoFrame">
                                        <p class="m-0 text-center">
                                            <a href="#" @click="openWebcam()">Click here</a> to take a photo, or drop it to
                                            upload.
                                        </p>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <button type="button" name="button" class="btn btn-primary w-100">Take a photo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-7">
                    <div class="card card-h-100">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Details</h4>
                        </div>
                        <div class="card-body pt-0">
                            <p>Lorem Ipsum</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.beforePhotoFrame {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    background-color: #fafbfe;
    height: 50vh;
    border-radius: 4px;
    outline: 2px dashed #ececec;
    outline-offset: -15px;
}
</style>
