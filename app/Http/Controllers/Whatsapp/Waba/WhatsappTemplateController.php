<?php

namespace App\Http\Controllers\Whatsapp\Waba;

use App\Http\Controllers\Controller;
use App\Http\Requests\Whatsapp\Official\TemplateRequest;
use App\Models\ChatBot\ChatBot;
use App\Models\Master\MessageTemplate;
use App\Models\MetaAccount;
use App\Models\WhatsappKeyAccount;
use App\Observers\Master\TemplateObserver;
use App\Observers\WhatsappOfficial\WhatsappOfficialServiceObserver;
use App\Observers\WhatsappOfficial\WhatsappTemplateServiceObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WhatsappTemplateController extends Controller
{


    /*
    |--------------------------------------------------------------------------
    | Message Template
    |--------------------------------------------------------------------------
    */

    protected $templateObserver;
    protected $templateServiceObserver;
    protected $whatsappServiceObserver;

    public function __construct(TemplateObserver $templateObserver, WhatsappTemplateServiceObserver $templateServiceObserver, WhatsappOfficialServiceObserver $whatsappServiceObserver)
    {
        $this->templateObserver             = $templateObserver;
        $this->templateServiceObserver      = $templateServiceObserver;
        $this->whatsappServiceObserver      = $whatsappServiceObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. List Template Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request, MetaAccount $meta)

    {
        $templates = $this->templateObserver->getData($request)
            ->where('type', 'whatsapp')
            ->where("meta_account_id", $meta->id)
            ->where("for_waba", 'yes')
            ->get(['id', 'name', 'image', 'type_content', 'category', 'lang', 'waba_status_template', 'quality_score']);

        // Template Analytics: aggregate delivery stats from blash_details
        $templateIds = $templates->pluck('id')->toArray();

        $analyticsRaw = DB::table('blash_details as bd')
            ->join('blash_whatsapps as bw', 'bw.id', '=', 'bd.blash_whatsapp_id')
            ->whereIn('bw.template_id', $templateIds)
            ->select(
                'bw.template_id',
                DB::raw('COUNT(bd.id) as total_sent'),
                DB::raw('SUM(bd.delivery_status IN ("delivered","read")) as total_delivered'),
                DB::raw('SUM(bd.delivery_status = "read") as total_read'),
                DB::raw('SUM(bd.delivery_status = "failed") as total_failed')
            )
            ->groupBy('bw.template_id')
            ->get()
            ->keyBy('template_id');

        // Attach analytics to each template
        $templates = $templates->map(function ($template) use ($analyticsRaw) {
            $stats = $analyticsRaw->get($template->id);
            $template->total_sent      = $stats ? $stats->total_sent : 0;
            $template->total_delivered = $stats ? $stats->total_delivered : 0;
            $template->total_read      = $stats ? $stats->total_read : 0;
            $template->total_failed    = $stats ? $stats->total_failed : 0;
            $template->delivery_rate   = ($stats && $stats->total_sent > 0)
                ? round($stats->total_delivered * 100 / $stats->total_sent, 1)
                : null;
            $template->read_rate       = ($stats && $stats->total_delivered > 0)
                ? round($stats->total_read * 100 / $stats->total_delivered, 1)
                : null;
            return $template;
        });

        return view('waba.template.index', ['page' => __('page.template.page'), 'breadcumb' => true], compact('templates', 'meta'));
    }


    public function create(MetaAccount $meta)
    {
        return view('waba.template.create', ['page' => __('page.template.add'), 'breadcumb' => true], compact('meta'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Template Page
    |--------------------------------------------------------------------------
    */

    public function update(MetaAccount $meta, MessageTemplate $template)
    {
        return view('waba.template.update', ['page' => __('page.template.update'), 'breadcumb' => true], compact('meta'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Store Template Data
    |--------------------------------------------------------------------------
    */

    public function store(TemplateRequest $request, MetaAccount $meta)
    {

        $validationCheck = $this->templateObserver->checkLimit();

        if ($validationCheck == false) {
            return response([
                'status'    => false,
                'message'   =>  __('validation.template_limit')
            ], 422);
        }


        $template = $this->templateServiceObserver->createData($request, $meta);

        if ($template['status'] == false) {
            return response([
                'status'    => false,
                'message'   => $template['message']
            ], 422);
        } else {
            return response([
                'status'    => true,
                'message'   => __('general.success_add_data')
            ], 200);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Template Data
    |--------------------------------------------------------------------------
    */

    public function edit(TemplateRequest $request, MetaAccount $meta,  MessageTemplate $template)
    {


        $template = $this->templateServiceObserver->updateData($request, $meta, $template);

        if ($template['status'] == false) {
            return response([
                'status'    => false,
                'message'   => $template['message']
            ], 422);
        } else {
            return response([
                'status'    => false,
                'message'   => __('general.success_update')
            ], 200);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Template Data
    |--------------------------------------------------------------------------
    */

    public function delete(MetaAccount $meta, MessageTemplate $template)
    {
        $response = $this->templateServiceObserver->deleteTemplate($template, $meta);
        if (!$response->success) {
            return redirect()->back()->with(['gagal'    => $response->message]);
        }

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 8. Syncronize Data
    |--------------------------------------------------------------------------
    */

    public function syncData(MetaAccount $meta)
    {

        try {

            DB::beginTransaction();

             
            $response = $this->whatsappServiceObserver->syncTemplates($meta->access_token, $meta->business_app, $meta);

            if ($response->success == false) {
                return redirect()->back()->with(['gagal'    => $response->message]);
            }

            DB::commit();

            return redirect()->back()->with(['flash' => __('general.success_add_data')]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with(['gagal'    => $e->getMessage()]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 9. Get Details
    |--------------------------------------------------------------------------
    */

    public function details(MetaAccount $meta, MessageTemplate $template)
    {
        // Fetch fresh components from Meta API (includes HEADER type accurately)
        $components = null;
        if ($template->meta_id) {
            try {
                $url = "https://graph.facebook.com/" . config('custom.api_waba_version') . "/{$template->meta_id}";
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $meta->access_token,
                ])->get($url, ['fields' => 'components,name,status,category,language']);

                if ($response->status() == 200) {
                    $metaData = $response->json();
                    $components = $metaData['components'] ?? null;

                    // Auto-update local DB with fresh Meta data (fixes HEADER sync)
                    if ($components) {
                        // Check if Meta returned HEADER; if not, check local DB
                        $hasHeader = false;
                        foreach ($components as $comp) {
                            if (($comp['type'] ?? '') === 'HEADER') { $hasHeader = true; break; }
                        }
                        if (!$hasHeader) {
                            // Try to get HEADER from local DB
                            $localData = json_decode($template->message, true) ?? [];
                            foreach ($localData['components'] ?? [] as $comp) {
                                if (($comp['type'] ?? '') === 'HEADER') {
                                    array_unshift($components, $comp); // prepend local HEADER
                                    break;
                                }
                            }
                        }
                        // Sync updated components back to DB
                        $storedMessage = json_decode($template->message, true) ?? [];
                        $storedMessage['components'] = $components;
                        $template->message = json_encode($storedMessage);
                        $template->saveQuietly();
                    }
                }
            } catch (\Exception $e) {
                // Fall back to local DB
            }
        }

        // Fallback: local DB data
        if (!$components) {
            $localData = json_decode($template->message, true);
            $components = $localData['components'] ?? [];
        }

        return response([
            'status'    => true,
            'id'        => $template->id,
            'meta_id'   => $template->meta_id,
            'meta'      => array(
                'name'      => $template->name,
                'category'  => $template->category,
                'lang'      => $template->lang,
            ),
            'details'   => ['components' => $components]
        ], 200);
    }

   
}
