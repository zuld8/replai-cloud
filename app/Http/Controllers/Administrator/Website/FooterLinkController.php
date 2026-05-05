<?php

namespace App\Http\Controllers\Administrator\Website;

use App\Http\Controllers\Controller;
use App\Models\Cms\FooterLink;
use App\Observers\Saas\Web\FooterLinkObserver;
use Illuminate\Http\Request;

class FooterLinkController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Footer Links
    |--------------------------------------------------------------------------
    */

    protected $footerLinkObserver;

    public function __construct(FooterLinkObserver $footerLinkObserver)
    {
        $this->footerLinkObserver     = $footerLinkObserver;
    }


    /*
    |--------------------------------------------------------------------------
    | 1. List Category Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $links     = $this->footerLinkObserver->getData($request)->get();
        return view('admin.links.index', ['page' => __('page.web_link.page'), 'breadcumb' => true], compact('links'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Link Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.links.create', ['page' => __("page.web_link.add"), 'breadcumb' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Link Page
    |--------------------------------------------------------------------------
    */

    public function update(FooterLink $link)
    {
        return view('admin.links.update', ['page' => __('page.web_link.edit'), 'breadcumb' => true], compact('link'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Store Category Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'url'               => 'required|url',
            'position'          => 'required|in:1,2,3,4',
            'name'              => 'required|string',
            'order_position'    => 'required|numeric'
        ]);

        $this->footerLinkObserver->createData($request);

        return redirect()->route('links')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Link Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, FooterLink $link)
    {
        $this->validate($request, [
            'url'               => 'required|url',
            'position'          => 'required|in:1,2,3,4',
            'name'              => 'required|string',
            'order_position'    => 'required|numeric'
        ]);

        $this->footerLinkObserver->updateData($request, $link);

        return redirect()->route('links')->with(['flash'    => __('general.success_update')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Link Data
    |--------------------------------------------------------------------------
    */

    public function delete(FooterLink $link)
    {
        $this->footerLinkObserver->deleteData($link);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }
}
