<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Resources\Master\TemplateResource;
use App\Observers\Master\CategoryObserver;
use App\Observers\Master\DirectoryObserver;
use App\Observers\Master\TemplateObserver;
use App\Observers\WhatsappOfficial\WhatsappOfficialObserver;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Components Controllers
    |--------------------------------------------------------------------------
    */

    protected $directoryObserver;
    protected $categoryObserver;
    protected $wabaObserver;
    protected $templatesObserver;

    public function __construct(DirectoryObserver $directoryObserver, CategoryObserver $categoryObserver, WhatsappOfficialObserver $wabaObserver, TemplateObserver $templateObserver)
    {
        $this->directoryObserver        = $directoryObserver;
        $this->categoryObserver         = $categoryObserver;
        $this->wabaObserver             = $wabaObserver;
        $this->templatesObserver        = $templateObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Provinces List
    |--------------------------------------------------------------------------
    */

    public function provinces(Request $request)
    {
        $provinces  = $this->directoryObserver->getProvince($request)->get(['id', 'name', 'status']);
        return response()->json($provinces);
    }


    /*
    |--------------------------------------------------------------------------
    | 2. Cities List
    |--------------------------------------------------------------------------
    */

    public function cities(Request $request)
    {
        $cities  = $this->directoryObserver->getCity($request)->get(['id', 'name', 'status', 'province_id', 'type']);
        return response()->json($cities);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Districts List
    |--------------------------------------------------------------------------
    */

    public function districts(Request $request)
    {
        $districts  = $this->directoryObserver->getDistrict($request)->get(['id', 'name', 'city_id']);
        return response()->json($districts);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Categories List
    |--------------------------------------------------------------------------
    */

    public function categories(Request $request)
    {
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        return response()->json($categories);
    }


    /*
    |--------------------------------------------------------------------------
    | 5. Waba Device List
    |--------------------------------------------------------------------------
    */

    public function devices(Request $request)
    {
        $devices     = $this->wabaObserver->getData($request)->get(['id', 'phone']);
        return response()->json($devices);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Template List
    |--------------------------------------------------------------------------
    */

    public function templates(Request $request)
    {
        $templates     = $this->templatesObserver->getData($request)->get(['id', 'name', 'meta_id', 'message']);
        return response()->json(TemplateResource::collection($templates));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Districts List
    |--------------------------------------------------------------------------
    */

    public function subdistricts(Request $request)
    {
        $districts  = $this->directoryObserver->getSubDistrict($request)->get(['id', 'name', 'postal_code']);
        return response()->json($districts);
    }

    public function categoryCount(Request $request)
    {
        $categoryId = $request->get('category_id');
        if (!$categoryId) {
            return response()->json(['count' => 0]);
        }
        $count = \App\Models\Store\Store::where('category_id', $categoryId)
            ->where('merchant_id', auth()->user()->merchant_id)
            ->count();
        return response()->json(['count' => $count]);
    }

}
