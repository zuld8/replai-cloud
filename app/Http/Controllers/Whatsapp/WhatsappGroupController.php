<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller;
use App\Observers\WhatsappServiceObserver;
use App\Process\MasterData\UploadImageProcess; 

class WhatsappGroupController extends Controller
{
    protected $whatsappServiceObserver;
    protected $templateObserver;
    protected $uploadImageProcess;

    public function __construct(WhatsappServiceObserver $whatsappServiceObserver,  UploadImageProcess $uploadImageProcess)
    {
        $this->whatsappServiceObserver  = $whatsappServiceObserver; 
        $this->uploadImageProcess       = $uploadImageProcess;
    }
}
