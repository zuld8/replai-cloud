<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\WhatsappTemplateRequest;
use App\Http\Resources\Master\TemplateWhatsappResource;
use App\Models\Master\MessageTemplate;
use App\Observers\Master\TemplateObserver;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TemplateController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Message Template
    |--------------------------------------------------------------------------
    */

    protected $templateObserver;

    public function __construct(TemplateObserver $templateObserver)
    {
        $this->templateObserver     = $templateObserver;
    }


    /*
    |--------------------------------------------------------------------------
    | 1. List Template Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $templates     = $this->templateObserver->getData($request)->where('type', 'whatsapp')->where("for_waba", 'no')->select(['id', 'name', 'image', 'type_content'])->get();

        $queryArray     = $request->all();
        $params         = http_build_query($queryArray);

        if ($request->ajax()) {

            $stores         = $this->templateObserver->getData($request)->where('for_waba', 'no')->select(['id', 'name', 'image', 'type_content']);

            return DataTables::of($stores)
                ->addColumn('cname', function ($row) {
                    return $row->content_name;
                })
                ->addColumn('used', function ($row) {
                    $numberFormat = number_format($row->blashs->count()) . ' ' . __('general.blash');
                    return '<a class="text-info" href="' . route('blash') . '?template=' . $row->id . '">
                                   ' . $numberFormat . '
                                </a>';
                })->addColumn('action', function ($row) {
                    $html = '<a href="' . route('templates.update', $row->id) . '" class="btn btn-outline-warning btn-icon fs-16 ">
                                <i class="bx bx-pencil"></i>
                            </a>
                            <a href="javascript:void(0);" onclick="copyId(`' . $row->id . '`)" class="btn btn-outline-info btn-icon fs-16 ">
                                    <i class="bx bx-link"></i>
                                </a>
                            <a href="javascript:void(0);" onclick="deleteData(`' . $row->id . '`)" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                <i class="bx bx-trash "></i>
                            </a>';

                    return $html;
                })->rawColumns(['action', 'used'])
                ->make(true);
        }

        return view('master.template.index', ['page' => __('page.template.page'), 'breadcumb' => true], compact('params'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Template Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('master.template.create', ['page' => __('page.template.add'), 'breadcumb' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Template Page
    |--------------------------------------------------------------------------
    */

    public function update(MessageTemplate $template)
    {
        $body   = json_decode($template->button_or_list);
        return view('master.template.create', ['page' => __('page.template.update'), 'breadcumb' => true], compact('template', 'body'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Store Template Data
    |--------------------------------------------------------------------------
    */

    public function store(WhatsappTemplateRequest $request)
    {

        $validationCheck = $this->templateObserver->checkLimit();

        if ($validationCheck == false) {
            return redirect()->back()->with(['gagal'    => __('validation.template_limit')]);
        }

        $image  = '';
        if ($request->type_content == 'description' || $request->type_content == 'button') {
            $image = $request->media_type != 'text' && $request->image != null ? $this->uploadImage($request, 'image', 'template') : '';
        }

        $this->templateObserver->createData($request, $image);

        return response()->json([
            'status'    => true,
            'message'   => __('general.success_add_data'),
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Template Data
    |--------------------------------------------------------------------------
    */

    public function edit(WhatsappTemplateRequest $request, MessageTemplate $template)
    {

        if ($request->media_type != $template->media_type && $request->media_type != 'text' && ($request->image == null || $request->image == '')) {
            return response()->json([
                'status'    => false,
                'message'   => 'Must include media',
            ], 419);
        }

        $image = $request->image != null && $request->media_type != 'text' ? $this->uploadImage($request, 'image', 'template') : '';
        $image != '' || $request->media_type == 'text' ? $this->unlinkFile($template->image) : '';
        $this->templateObserver->updateData($request, $template, $image);

        return response()->json([
            'status'    => true,
            'message'   => __('general.success_update'),
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Template Data
    |--------------------------------------------------------------------------
    */

    public function delete(MessageTemplate $template)
    {
        $this->unlinkFile($template->image);
        $this->templateObserver->deleteData($template);

        return response()->json([
            'status'    => true,
            'message'   => __('general.success_deleted')
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | 7. Details Template Data
    |--------------------------------------------------------------------------
    */

    public function details(MessageTemplate $template)
    {
        return response()->json([
            'status'    => true,
            'details'   => TemplateWhatsappResource::make($template)
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | 8. Delete Multiple Data
    |--------------------------------------------------------------------------
    */

    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids;
        if (!empty($ids)) {
            MessageTemplate::whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => __('general.success_deleted')]);
        }

        return response()->json(['success' => false, 'message' => __('general.choosed_not_found')]);
    }
}
