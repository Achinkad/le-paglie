<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AlertResource;
use App\Http\Requests\StoreAlertRequest;
use App\Models\Camera;

class AlertController extends Controller
{
    public function getAllAlerts()
    {
       
        $user = User::where('id', auth()->guard('api')->user()->id)->first();

        $allAlerts = $user->alert;

        return $allAlerts;
    }

    public function registerAlert(Request $request)
    {
        
        $request['camera_id']=Camera::where('ip_address',$request->camera_ip)->pluck('id')->first();
        unset($request['camera_ip']);

        $alert = new Alert();
        $alert->fill($request->all());
        $alert->save();
        $currentUser = auth()->guard('api')->user();
        $currentUser->alert()->attach($alert->id);
        return new AlertResource($alert);
        
    }
}
