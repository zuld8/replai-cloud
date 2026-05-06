<?php

namespace App\Http\Controllers\Flow;

use App\Http\Controllers\Controller;
use App\Models\WhatsappKeyAccount;
use App\Models\Flow\Flow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FlowController extends Controller
{
    /**
     * Display list of flows
     */
    public function index()
    {
        $business_id = my_business()->id ?? null;
        $flows = Flow::where('business_id', $business_id)
                     ->orderBy('created_at', 'desc')
                     ->get();

        return view('flow.index', compact('flows'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $business_id  = my_business()->id ?? null;
        $wabaDevices  = WhatsappKeyAccount::where('business_id', $business_id)
                            ->where('status', 'active')
                            ->get(['id', 'phone']);
        return view('flow.create', compact('wabaDevices'));
    }

    /**
     * Store new flow
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'keyword'   => 'required|string',
            'flow_type' => 'required|in:payment,form,order,booking,survey',
            'message_open'  => 'nullable|string',
            'message_close' => 'nullable|string',
        ]);

        $business_id = my_business()->id ?? null;

        $qrisPath = null;
        if ($request->hasFile('qris_image')) {
            $file       = $request->file('qris_image');
            $folder     = "uploads/folders/{$business_id}/qris/";
            $filename   = 'qris_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($folder), $filename);
            $qrisPath   = $folder . $filename;
        }

        // Build payment_accounts array
        $accounts = [];
        if ($request->has('bank_name')) {
            $banks   = $request->bank_name ?? [];
            $numbers = $request->account_number ?? [];
            $owners  = $request->account_owner ?? [];
            foreach ($banks as $i => $bank) {
                if (!empty($bank) && !empty($numbers[$i])) {
                    $accounts[] = [
                        'bank'   => $bank,
                        'number' => $numbers[$i],
                        'owner'  => $owners[$i] ?? '',
                    ];
                }
            }
        }

        Flow::create([
            'business_id'       => $business_id,
            'merchant_id'       => my_business()->merchant_id ?? null,
            'name'              => $request->name,
            'keyword'           => $request->keyword,
            'flow_type'         => $request->flow_type,
            'select_device'     => $request->has('select_device') ? implode(',', $request->select_device) : null,
            'select_waba'       => $request->has('select_waba') ? implode(',', $request->select_waba) : null,
            'select_telegram'   => $request->has('select_telegram') ? implode(',', $request->select_telegram) : null,
            'qris_image'        => $qrisPath,
            'payment_accounts'  => !empty($accounts) ? json_encode($accounts) : null,
            'message_open'      => $request->message_open,
            'message_close'     => $request->message_close,
            'status'            => $request->status ?? 'active',
        ]);

        return redirect()->route('flow')->with('success', 'Flow berhasil dibuat!');
    }

    /**
     * Show edit form
     */
    public function update(Flow $flow)
    {
        $business_id  = my_business()->id ?? null;
        $wabaDevices  = WhatsappKeyAccount::where('business_id', $business_id)
                            ->where('status', 'active')
                            ->get(['id', 'phone']);
        return view('flow.update', compact('flow', 'wabaDevices'));
    }

    /**
     * Save edited flow
     */
    public function edit(Request $request, Flow $flow)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'keyword'   => 'required|string',
            'flow_type' => 'required|in:payment,form,order,booking,survey',
        ]);

        $qrisPath = $flow->qris_image;
        if ($request->hasFile('qris_image')) {
            // Delete old
            if ($qrisPath && file_exists(public_path($qrisPath))) {
                unlink(public_path($qrisPath));
            }
            $business_id = my_business()->id ?? 'general';
            $file       = $request->file('qris_image');
            $folder     = "uploads/folders/{$business_id}/qris/";
            $filename   = 'qris_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($folder), $filename);
            $qrisPath   = $folder . $filename;
        }

        $accounts = [];
        if ($request->has('bank_name')) {
            $banks   = $request->bank_name ?? [];
            $numbers = $request->account_number ?? [];
            $owners  = $request->account_owner ?? [];
            foreach ($banks as $i => $bank) {
                if (!empty($bank) && !empty($numbers[$i])) {
                    $accounts[] = ['bank' => $bank, 'number' => $numbers[$i], 'owner' => $owners[$i] ?? ''];
                }
            }
        }

        $flow->update([
            'name'              => $request->name,
            'keyword'           => $request->keyword,
            'flow_type'         => $request->flow_type,
            'select_device'     => $request->has('select_device') ? implode(',', $request->select_device) : null,
            'select_waba'       => $request->has('select_waba') ? implode(',', $request->select_waba) : null,
            'qris_image'        => $qrisPath,
            'payment_accounts'  => !empty($accounts) ? json_encode($accounts) : $flow->payment_accounts,
            'message_open'      => $request->message_open,
            'message_close'     => $request->message_close,
            'status'            => $request->status ?? 'active',
        ]);

        return redirect()->route('flow')->with('success', 'Flow berhasil diupdate!');
    }

    /**
     * Delete a flow
     */
    public function delete(Flow $flow)
    {
        if ($flow->qris_image && file_exists(public_path($flow->qris_image))) {
            unlink(public_path($flow->qris_image));
        }
        $flow->delete();
        return redirect()->route('flow')->with('success', 'Flow berhasil dihapus!');
    }

    /**
     * Toggle status active/inactive
     */
    public function toggleStatus(Flow $flow)
    {
        $flow->update(['status' => $flow->status === 'active' ? 'inactive' : 'active']);
        return response()->json(['status' => $flow->status]);
    }
}
