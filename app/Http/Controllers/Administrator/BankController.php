<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Master\Bank;
use App\Observers\Saas\BankObserver;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;

class BankController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Master Bank
    |--------------------------------------------------------------------------
    */

    protected $bankObserver;
    protected $uploadImageProcess;

    public function __construct(BankObserver $bankObserver, UploadImageProcess $uploadImageProcess)
    {
        $this->bankObserver         = $bankObserver;
        $this->uploadImageProcess   = $uploadImageProcess;
    }


    /*
    |--------------------------------------------------------------------------
    | 1. List Bank Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $banks     = $this->bankObserver->getData($request)->get(['id', 'name', 'code', 'number', 'logo']);
        return view('admin.bank.index', ['page' => __('page.bank.page'), 'breadcumb' => true], compact('banks'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Bank Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.bank.create', ['page' => __('page.bank.add'), 'breadcumb' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Bank Page
    |--------------------------------------------------------------------------
    */

    public function update(Bank $bank)
    {
        return view('admin.bank.update', ['page' => __('page.bank.edit'), 'breadcumb' => true], compact('bank'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Store Bank Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'code'      => 'required',
            'number'    => 'required',
            'image'     => 'mimes:png,jpg,jpeg'
        ]);

        $image  = '';

        if ($request->image) {
            $image =  $this->uploadImage($request, 'image', 'banks');
        }

        if ($image == '') {
            $image = $this->uploadImageProcess->createDafaultMedia($request->name, 'uploads/banks/');
        }

        $this->bankObserver->createData($request, $image);

        return redirect()->route('banks')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Bank Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, Bank $bank)
    {
        $this->validate($request, [
            'name'      => 'required',
            'code'      => 'required',
            'number'    => 'required',
            'image'     => 'mimes:png,jpg,jpeg'
        ]);

        $image  = '';

        if ($request->image) {
            $this->unlinkFile($bank->logo);
            $image =  $this->uploadImage($request, 'image', 'banks');
        }

        $this->bankObserver->updateData($request, $bank, $image);
        return redirect()->route('banks')->with(['flash'    => __('general.success_update')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Bank Data
    |--------------------------------------------------------------------------
    */

    public function delete(Bank $bank)
    {
        $this->unlinkFile($bank->logo);
        $this->bankObserver->deleteData($bank);
        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }
}
