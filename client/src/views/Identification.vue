<script setup>
import { ref, onMounted } from 'vue'
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
                <div class="col-6">
                    <div class="card card-h-100">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Take or Upload a Photo of a person or a pet</h4>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row">
                                <div>
                                    <p>Put the photo frame here.</p>
                                </div>
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="button" name="button" class="btn btn-primary">Take a photo</button>
                                    <button type="button" name="button" class="btn btn-primary ms-2">Upload a photo</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
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
