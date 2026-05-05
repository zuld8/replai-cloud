<?php

namespace App\Http\Controllers\Whatsapp;

use App\Http\Controllers\Controller; 
use App\Models\WhatsappDevice;
use App\Observers\WhatsappDeviceObserver;
use App\Observers\WhatsappServiceObserver;
use Illuminate\Http\Request;

class WhatsappMiscController extends Controller
{
    protected $whatsappDeviceObserver;
    protected $whatsappServiceObserver;

    public function __construct(WhatsappDeviceObserver $whatsappDeviceObserver, WhatsappServiceObserver $whatsappServiceObserver)
    {
        $this->whatsappDeviceObserver       = $whatsappDeviceObserver;
        $this->whatsappServiceObserver      = $whatsappServiceObserver;
    }

    public function readMessage(Request $request, $id)
    {

        $device     = WhatsappDevice::where('id', $id)->first(['id']);

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
                    'message'   => 'Successfully marked status'
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

    public function deleteForMe(Request $request, $id)
    {


        $device     = WhatsappDevice::where('id', $id)->first(['id']);

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

    public function deleteEveryOne(Request $request, $id)
    {


        $device     = WhatsappDevice::where('id', $id)->first(['id']);

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

    public function downloadMedia(Request $request, $id)
    {


        $device     = WhatsappDevice::where('id', $id)->first(['id']);

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

    public function getPhotoProfile(Request $request, $id)
    {


        $device     = WhatsappDevice::where('id', $id)->first(['id']);

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

    public function markMessage(Request $request, $id)
    {


        $device     = WhatsappDevice::where('id', $id)->first(['id']);

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

    public function deleteChats(Request $request, $id)
    {


        $device     = WhatsappDevice::where('id', $id)->first(['id']);

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
