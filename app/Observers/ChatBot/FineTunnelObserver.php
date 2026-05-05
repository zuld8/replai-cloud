<?php

namespace App\Observers\ChatBot;

use App\Models\ChatBot\FineTunnel;
use App\Models\ChatBot\FineTunnelDetail;
use App\Models\ChatBot\FineTunnelSheet;
use App\Models\ChatBot\FollowUp;
use App\Models\Courier\Courier;
use App\Models\Courier\CourierFineTunnel;
use Illuminate\Http\Request;

class FineTunnelObserver
{

    public function getData(Request $request)
    {
        return FineTunnel::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->orderBy('name', 'asc');
    }

    public function checkLimit()
    {
        if (my_user()->role == 'user') {
            $trainingLimitation  = ai_agent_limitation(my_business());
            if (!$trainingLimitation) {
                return false;
            }
        }


        return true;
    }

    public function createData(Request $request, $description, String $image = '')
    {

        return FineTunnel::create([
            'name'                      => $request->name,
            'description'               => $description,
            'welcome_message'           => $request->welcome_message,
            'transfer_condition'        => $request->term_condition,
            'welcome_image'             => $image,
            'model_ai'                  => $request->model_ai,
            'history_limit'             => $request->history_limit,
            'label'                     => !empty($request->label) ? implode(",", $request->label) : null,
            'context_limit'             => $request->context_limit,
            'delay'                     => $request->delay,
            'message_limit'             => $request->message_limit,
            'zip_code'                  => !empty($request->zip_code) ? $request->zip_code : null,
            'weight'                    => !empty($request->weight) ? $request->weight : 0,
            'address'                   => !empty($request->address) ? $request->address : null,
            'agent'                     => $request->agent ? implode(",", $request->agent) : null,
        ]);
    }

    public function createDetails(Request $request, FineTunnel $fineTunnel)
    {
        $fineTunnel->details()->delete();

        if (isset($request->command)) {
            $i  = 0;
            while ($i < count($request->command)) {
                FineTunnelDetail::create([
                    'fine_tunnel_id'    => $fineTunnel->id,
                    'command'           => $request->command[$i],
                    'answer'            => $request->answer[$i]
                ]);

                $i++;
            }
        }
    }

    public function createFollowUps(Request $request, FineTunnel $fineTunnel)
    {
 
        $fineTunnel->follow_ups()->delete();
        if ($request->has('prompt')) {
            foreach ($request->prompt as $key => $text) {
                FollowUp::create([
                    'finetunnel_id' => $fineTunnel->id,
                    'text'          => $text,
                    'delay'         => $request->delay_followups[$key] ?? 10, // Default 10 jika tidak ada input
                    'exact'         => isset($request->exact[$key]) ? ($request->exact[$key] == 'on' ? 'yes' : 'no') : 'no',  // Checkbox bernilai 1 jika dicentang, 0 jika tidak
                    'handoff'       => isset($request->handoff[$key]) ? ($request->handoff[$key] == 'on' ? 'yes' : 'no') : 'no',
                ]);
            }
        }
    }

    public function createGSheet(Request $request, FineTunnel $fineTunnel)
    {
        $fineTunnel->gsheets()->delete();

        if (isset($request->url)) {
            $i  = 0;
            while ($i < count($request->url)) {
                FineTunnelSheet::create([
                    'fine_tunnel_id'    => $fineTunnel->id,
                    'url'               => $request->url[$i],
                    'status'            => $request->status_sheet[$i]
                ]);

                $i++;
            }
        }
    }


    public function updateData(Request $request, FineTunnel $fineTunnel, $description, String $image = '')
    {

        $fineTunnel->update([
            'name'                      => $request->name,
            'description'               => $description,
            'welcome_message'           => $request->welcome_message,
            'transfer_condition'        => $request->term_condition,
            'welcome_image'             => $image == '' ? $fineTunnel->welcome_image : $image,
            'model_ai'                  => $request->model_ai,
            'history_limit'             => $request->history_limit,
            'label'                     => !empty($request->label) ? implode(",", $request->label) : null,
            'context_limit'             => $request->context_limit,
            'delay'                     => $request->delay,
            'message_limit'             => $request->message_limit,
            'zip_code'                  => !empty($request->zip_code) ? $request->zip_code : null,
            'weight'                    => !empty($request->weight) ? $request->weight : 0,
            'address'                   => !empty($request->address) ? ($request->address != null ? $request->address : $fineTunnel->address) : null,
            'agent'                     => $request->agent ? implode(",", $request->agent) : null,
        ]);
    }

    public function deleteData(FineTunnel $fineTunnel)
    {
        $fineTunnel->details()->delete();
        return $fineTunnel->delete();
    }

    public function createCouriers(FineTunnel $fineTunnel, Request $request)
    {
        foreach ($request->couriers as $key => $courierId) {
            $courier    = Courier::where('code', $courierId)->where('status', 'yes')->first(['id', 'name', 'code', 'service']);
            if ($courier) {
                CourierFineTunnel::create([
                    'finetunnel_id'         => $fineTunnel->id,
                    'name'                  => $courier->name,
                    'service'               => $courier->service,
                    'code'                  => $courier->code,
                ]);
            }
        }
    }

    public function deleting(FineTunnel $fineTunnel)
    {
        $fineTunnel->details()->delete();
        $fineTunnel->follow_ups()->delete();
        $fineTunnel->couriers()->delete();
        $fineTunnel->gsheets()->delete();
        $fineTunnel->documents()->delete();
    }
}
