<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Whatsapp\DeleteChatRequest;
use App\Http\Requests\Whatsapp\DeleteMessageEveryOneRequest;
use App\Http\Requests\Whatsapp\DeleteMessageRequest;
use App\Http\Requests\Whatsapp\DownloadMediaRequest;
use App\Http\Requests\Whatsapp\GetPhotoProfileRequest;
use App\Http\Requests\Whatsapp\MarkMessageRequest;
use App\Http\Requests\Whatsapp\ReadMessageRequest;
use App\Models\Setting;
use App\Models\WhatsappDevice;
use App\Observers\WhatsappDeviceObserver;
use App\Observers\WhatsappServiceObserver; 

class WhatsappMiscController extends Controller
{
    protected $whatsappDeviceObserver;
    protected $whatsappServiceObserver;

    public function __construct(WhatsappDeviceObserver $whatsappDeviceObserver, WhatsappServiceObserver $whatsappServiceObserver)
    {
        $this->whatsappDeviceObserver       = $whatsappDeviceObserver;
        $this->whatsappServiceObserver      = $whatsappServiceObserver;
    }

    public function readMessage(ReadMessageRequest $request, $id)
    {
        $settings   = Setting::where("local_api_key", $request->api_key)->first(['local_api_key', 'id']);

        if (!$settings) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key Tidak dapat dikenali'
            ], 401);
        }

        $device     = WhatsappDevice::where('business_id', $settings->id)->where('id', $id)->first(['id']);

        if (!$device) {
            return response()->json([
                'status'    => false,
                'message'   => 'Device cannot be found'
            ], 401);
        }


        try {

            $response   = $this->whatsappServiceObserver->readMessages($request, $device);
            if ($response->status() == 200) {
                return response()->json([
                    'status'    => true,
                    'message'   => 'Berhasil menandai status'
                ]);
            }

            return response()->json([
                'status'    => false,
                'message'   => 'There is an error'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage()
            ], 422);
        }
    }

    public function deleteForMe(DeleteMessageRequest $request, $id)
    {
        $settings   = Setting::where("local_api_key", $request->api_key)->first(['local_api_key', 'id']);

        if (!$settings) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key Tidak dapat dikenali'
            ], 401);
        }

        $device     = WhatsappDevice::where('business_id', $settings->id)->where('id', $id)->first(['id']);

        if (!$device) {
            return response()->json([
                'status'    => false,
                'message'   => 'Device cannot be found'
            ], 401);
        }


        try {

            $response   = $this->whatsappServiceObserver->deleteMessage($request, $device);
            if ($response->status() == 200) {
                return response()->json([
                    'status'    => true,
                    'message'   => 'Successfully deleted message'
                ]);
            }

            return response()->json([
                'status'    => false,
                'message'   => 'There is an error'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage()
            ], 422);
        }
    }

    public function deleteEveryOne(DeleteMessageEveryOneRequest $request, $id)
    {
        $settings   = Setting::where("local_api_key", $request->api_key)->first(['local_api_key', 'id']);

        if (!$settings) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key Tidak dapat dikenali'
            ], 401);
        }

        $device     = WhatsappDevice::where('business_id', $settings->id)->where('id', $id)->first(['id']);

        if (!$device) {
            return response()->json([
                'status'    => false,
                'message'   => 'Device cannot be found'
            ], 401);
        }


        try {

            $response   = $this->whatsappServiceObserver->deleteEveryOne($request, $device);
            if ($response->status() == 200) {
                return response()->json([
                    'status'    => true,
                    'message'   => 'Successfully deleted message'
                ]);
            }

            return response()->json([
                'status'    => false,
                'message'   => 'There is an error'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage()
            ], 422);
        }
    }

    public function downloadMedia(DownloadMediaRequest $request, $id)
    {
        $settings   = Setting::where("local_api_key", $request->api_key)->first(['local_api_key', 'id']);

        if (!$settings) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key Tidak dapat dikenali'
            ], 401);
        }

        $device     = WhatsappDevice::where('business_id', $settings->id)->where('id', $id)->first(['id']);

        if (!$device) {
            return response()->json([
                'status'    => false,
                'message'   => 'Device cannot be found'
            ], 401);
        }

        try {

            $response   = $this->whatsappServiceObserver->downloadMedia($request, $device);
            if ($response->status() == 200) {
                return response()->json(json_decode($response->body()));
            }

            return response()->json([
                'status'    => false,
                'message'   => 'There is an error'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage()
            ], 422);
        }
    }

    public function getPhotoProfile(GetPhotoProfileRequest $request, $id)
    {
        $settings   = Setting::where("local_api_key", $request->api_key)->first(['local_api_key', 'id']);

        if (!$settings) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key Tidak dapat dikenali'
            ], 401);
        }

        $device     = WhatsappDevice::where('business_id', $settings->id)->where('id', $id)->first(['id']);

        if (!$device) {
            return response()->json([
                'status'    => false,
                'message'   => 'Device cannot be found'
            ], 401);
        }

        try {

            $response   = $this->whatsappServiceObserver->getPhotoProfile($request, $device);
            if ($response->status() == 200) {
                return response()->json(json_decode($response->body()));
            }

            return response()->json([
                'status'    => false,
                'message'   => 'There is an error'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage()
            ], 422);
        }
    }

    public function markMessage(MarkMessageRequest $request, $id)
    {
        $settings   = Setting::where("local_api_key", $request->api_key)->first(['local_api_key', 'id']);

        if (!$settings) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key Tidak dapat dikenali'
            ], 401);
        }

        $device     = WhatsappDevice::where('business_id', $settings->id)->where('id', $id)->first(['id']);

        if (!$device) {
            return response()->json([
                'status'    => false,
                'message'   => 'Device cannot be found'
            ], 401);
        }

        try {

            $response   = $this->whatsappServiceObserver->markMessage($request, $device);
            if ($response->status() == 200) {
                return response()->json(json_decode($response->body()));
            }

            return response()->json([
                'status'    => false,
                'message'   => 'There is an error'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage()
            ], 422);
        }
    }

    public function deleteChats(DeleteChatRequest $request, $id)
    {
        $settings   = Setting::where("local_api_key", $request->api_key)->first(['local_api_key', 'id']);

        if (!$settings) {
            return response()->json([
                'status'    => false,
                'message'   => 'Api Key Tidak dapat dikenali'
            ], 401);
        }

        $device     = WhatsappDevice::where('business_id', $settings->id)->where('id', $id)->first(['id']);

        if (!$device) {
            return response()->json([
                'status'    => false,
                'message'   => 'Device cannot be found'
            ], 401);
        }

        try {

            $response   = $this->whatsappServiceObserver->deleteChats($request, $device);
            if ($response->status() == 200) {
                return response()->json(json_decode($response->body()));
            }

            return response()->json([
                'status'    => false,
                'message'   => 'There is an error'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage()
            ], 422);
        }
    }
 
}
