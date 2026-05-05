<?php

namespace App\Http\Controllers\Administrator\Website;

use App\Http\Controllers\Controller;
use App\Models\Cms\Page;
use App\Observers\Saas\Web\PageObserver;
use App\Process\MasterData\UploadImageProcess;
use App\Process\TemplateEditor\WebTemplateProcess;
use Illuminate\Http\Request;

class PageController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Page Controller
    |--------------------------------------------------------------------------
    */

    protected $pageObserver;
    protected $uploadImageProcess;
    protected $webTemplateProcess;

    public function __construct(PageObserver $pageObserver, UploadImageProcess $uploadImageProcess, WebTemplateProcess $webTemplateProcess)
    {
        $this->pageObserver         = $pageObserver;
        $this->uploadImageProcess   = $uploadImageProcess;
        $this->webTemplateProcess   = $webTemplateProcess;
    }


    /*
    |--------------------------------------------------------------------------
    | 1. List Web Pages
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $pages     = $this->pageObserver->getData($request)->get(['id', 'name', 'page']);
        return view('admin.page.index', ['page' => __('page.web.page'), 'breadcumb' => true], compact('pages'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Update Web Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Page $page)
    { 
        return $this->webTemplateProcess->showEditorTemplate($request, $page);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Store Web Page Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
 
        if ($request->page_type != 'page') {
            $pages  = $this->pageObserver->getData($request)->where("page", $request->page_type)->first(['id']);
            if ($pages) {
                return redirect()->back()->with(['gagal'    => __('cms.create_alert')]);
            }
        }

        $this->validate($request, [
            'page_name'              => 'required',
            'page_type'              => 'required'
        ]);

        $page   = $this->pageObserver->cerateData($request);
        return redirect()->route('pages.update', $page->id)->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Upload Asset For Custom Email
    |--------------------------------------------------------------------------
    */

    public function uploadAsset(Request $request)
    {
        $this->validate($request, [
            'file'      => 'required|array',
            'file.*'    => 'required|file'
        ]);

        return response()->json([
            'data' => $this->uploadImageProcess->uploadFilesFromRequest('file')
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | 5. Get Web Template Components
    |--------------------------------------------------------------------------
    */

    public function getComponentTemplate(Request $request, Page $template)
    {
        return $this->webTemplateProcess->showTemplates($request, $template);
    }


     /*
    |--------------------------------------------------------------------------
    | 6. Update Web Page Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, Page $page)
    {
        $this->pageObserver->updateData($request, $page);
        return response()->noContent(200);
    }

    public function delete(Page $page)
    {
        $this->pageObserver->deleteData($page);
        return redirect()->back()->with(['flash'    => 'Data berhasil di hapus']);
    }
}
