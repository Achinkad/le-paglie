<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendHttpRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $requestData;

    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }

    public function handle(): void
    {
        $response = Http::post('http://localhost:8000/pet-recon', [
            'camera_ip' => $this->requestData->ip_address, 
            'animal' => $this->requestData->pet_name, 
            'action' => $this->requestData->pet_action
        ]);
    }
}
