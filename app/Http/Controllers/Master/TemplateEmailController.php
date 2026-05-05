<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\MessageTemplate;
use App\Observers\Master\TemplateObserver;
use App\Process\MasterData\UploadImageProcess;
use App\Process\TemplateEditor\EmailTemplateProcess;
use Illuminate\Http\Request;

class TemplateEmailController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Message Template
    |--------------------------------------------------------------------------
    */

    protected $templateObserver;
    protected $uploadImageProcess;
    protected $emailTemplateProcess;

    public function __construct(TemplateObserver $templateObserver, UploadImageProcess $uploadImageProcess, EmailTemplateProcess $emailTemplateProcess)
    {
        $this->templateObserver     = $templateObserver;
        $this->uploadImageProcess   = $uploadImageProcess;
        $this->emailTemplateProcess = $emailTemplateProcess;
    }


    /*
    |--------------------------------------------------------------------------
    | 1. List Email Template Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $templates     = $this->templateObserver->getData($request)->where('type', 'email')->where("for_waba", 'no')->get(['id', 'name', 'image']);
        return view('master.email.index', ['page' => __('page.template.email_page'), 'breadcumb' => true], compact('templates'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Update Email Template Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, MessageTemplate $template)
    {
        return $this->emailTemplateProcess->showEditorTemplate($request, $template);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Store Template Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'              => 'required',
        ]);

        $validationCheck = $this->templateObserver->checkLimit();

        if ($validationCheck == false) {
            return redirect()->back()->with(['gagal'    => __('validation.template_limit')]);
        }

        $template   = $this->templateObserver->createForEmail($request);

        return redirect()->route('templatemail.update', $template->id)->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Update Template Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, MessageTemplate $template)
    {  
        $this->templateObserver->updateForEmail($request, $template);
        return response()->noContent(200);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Delete Template Data
    |--------------------------------------------------------------------------
    */

    public function delete(MessageTemplate $template)
    {
        $this->unlinkFile($template->image);
        $this->templateObserver->deleteData($template);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Upload Asset For Custom Email
    |--------------------------------------------------------------------------
    */

    public function uploadAsset(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|array',
            'file.*' => 'required|file'
        ]);

        return response()->json([
            'data' => $this->uploadImageProcess->uploadFilesFromRequest('file')
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | 7. Get Email Template Components
    |--------------------------------------------------------------------------
    */

    public function getComponentTemplate(Request $request, MessageTemplate $template)
    {
        return $this->emailTemplateProcess->showTemplates($request, $template);
    }
}
