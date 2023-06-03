<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\Recognition;

class PhotoController extends Controller
{
    public function getAllPhotos(Request $request)
    {
        $recons = Recognition::where('user_id', Auth()->guard('api')->user()->id)->with('camera')->get();
        return $recons;
    }

    public function registerPhoto(Request $request) 
    {
        $photos = [];

        for ($i=0; $i < 20; $i++) { 
            if ($request->has('photo' . $i)) {
                $username = strtolower(preg_replace('/\s+/', '', $request->name));
                $photoName = $username . "_" . $i . "." . explode('/', explode(';', $request->input('photo' . $i))[0])[1];
                $photo = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $request['photo' . $i]));
                
                Storage::put('public/photos/' . $photoName, $photo);
                Storage::disk('ftp')->put('photos/' . $photoName, $photo);
                
                array_push($photos, $photoName);
                continue;
            }
            break;
        }

        if (count($photos) == 0) return;
        
        $recon = new Recognition;
        $recon->user_id = Auth()->guard('api')->user()->id;
        $recon->camera_id = $request->camera;
        $recon->name = $request->name;
        $recon->photos = json_encode($photos);
        $recon->save();

        $newRecon = Recognition::where('id', $recon->id)->with('camera')->first();

        return $newRecon;
    }

    public function deletePhoto(Request $request)
    {
        $recon = Recognition::where('id', $request->route('id'))->first();

        // Delete photos from local and ftp disks
        $photos = json_decode($recon->photos);
        
        foreach ($photos as $photo) {
            Storage::delete('public/photos/' . $photo);
            Storage::disk('ftp')->delete('photos/' . $photo);
        }

        $recon->delete();
        return response()->json("Recognition deleted with success.", 200);
    }
}
