<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Camera;
use App\Http\Requests\StoreCameraRequest;
use App\Http\Resources\CameraResource;

class CameraController extends Controller
{
    public function getAllCameras()
    {
        $cameras = Camera::all();

        // Check connectivity
        foreach ($cameras as $camera) {
            $ip_address = $camera->ip_address;
            $test_port = "2020";

            $ch = curl_init($ip_address . ":" . $test_port);

            curl_setopt($ch, CURLOPT_TIMEOUT, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $data = curl_exec($ch);
            $health = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            $camera->disabled = $health > 0 ? false : true;
        }

        return $cameras;
    }

    public function registerCamera(StoreCameraRequest $request)
    {
        $camera = new Camera;
        $camera->fill($request->validated());
        $camera->save();
        $currentUser = auth()->guard('api')->user();
        $currentUser->camera()->attach($camera->id);
        return new CameraResource($camera);
    }
}
