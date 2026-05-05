<?php

namespace App\Http\Controllers;

use App\Models\WhatsappKeyAccount;
use App\Observers\WhatsappObserver;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{

   
    
    protected $whatsappObserver;

    public function __construct(WhatsappObserver $whatsappKeyAccount)
    {
        $this->whatsappObserver     = $whatsappKeyAccount;
    }

    public function index(Request $request)
    {
        $whatsapp   = $this->whatsappObserver->getData($request)->get();
        return view('whatsapp.index', ['page'    => 'Whatsapp Akun'], compact('whatsapp'));
    }

    public function create()
    {
        return view('whatsapp.create', ['page'   => 'Tambah Whatsapp Akun']);
    }

    public function update(WhatsappKeyAccount $whatsapp)
    {
        return view('whatsapp.update', ['page'  => 'Edit Whatsapp Akun'], compact('whatsapp'));
    }

    public function delete(WhatsappKeyAccount $whatsapp)
    {
        $this->whatsappObserver->deleteData($whatsapp);
        return redirect()->back()->with(['flash'    => 'Berhasil menghapus data']);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'phone'     => 'required|numeric',
            'key'       => 'required',
            'session'   => 'required',
            'limit'     => 'required|numeric'
        ]);

        $this->whatsappObserver->createData($request);
        return redirect()->route('whatsapp')->with(['flash' => 'Berhasil menambahkan data']);
    }

    public function edit(Request $request, WhatsappKeyAccount $whatsapp)
    {
        $this->validate($request, [
            'phone'     => 'required|numeric',
            'key'       => 'required',
            'session'   => 'required',
            'limit'     => 'required|numeric'
        ]);

        $this->whatsappObserver->updateData($request, $whatsapp);
        return redirect()->route('whatsapp')->with(['flash' => 'Berhasil memperbaharui data']);
    }

    public function changeStatus(WhatsappKeyAccount $whatsapp)
    {
        $whatsapp->update([
            'status'        => $whatsapp->status == 'active' ? 'no_active' : 'active'
        ]);

        return response()->json([
            'message'  => 'Berhasil Memperbaharui status whatsapp',
        ]);
    }
    
}
