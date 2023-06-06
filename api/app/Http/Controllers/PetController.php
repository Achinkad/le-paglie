<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Models\Camera;
use App\Models\User;
use App\Jobs\SendHttpRequestJob;

class PetController extends Controller
{
    public function getPets(Request $request)
    {
        $camera = Camera::where('id', $request->id)->first();

        if($camera->pet_identification && $camera->user[0]->id == Auth()->guard('api')->user()->id) {
            return array($camera);
        }

        return false;
    }

    public function registerPet(Request $request)
    {
        $camera = Camera::where('id', $request->camera_id)->first();
        $camera->pet_identification = true;
        $camera->pet_name = $request->pet_name;
        $camera->pet_action = $request->pet_action;
        $camera->save();        

        SendHttpRequestJob::dispatch($camera);

        return array($camera);
    }
}
