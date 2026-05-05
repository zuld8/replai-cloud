<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Label;
use App\Observers\Master\LabelObserver;
use Illuminate\Http\Request;

class TicketLabelController extends Controller
{
    protected $labelObserver;

    public function __construct(LabelObserver $labelObserver)
    {
        $this->labelObserver        = $labelObserver;
    }

    public function index(Request $request)
    {
        $labels     = $this->labelObserver->getData($request)->where('type', 'ticket')->get(['id', 'name', 'position', 'color']);
        return view('master.ticket_label.index', ['page' => __('master.ticket_label.ticket_label_list'), 'breadcumb' => true], compact('labels'));
    }

    public function create()
    {
        return view('master.ticket_label.create', ['page' => __('master.ticket_label.add_ticket_label'), 'breadcumb' => true]);
    }

    public function update(Label $label)
    {
        return view('master.ticket_label.update', ['page' => __('master.ticket_label.update_label'), 'breadcumb' => true], compact('label'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'position'  => 'required|numeric|min:1',
            'color'     => 'required|string'
        ]);

        $label = $this->labelObserver->createData($request,'ticket');

        return response()->json([
            'success' => true,
            'message' => __('general.success_add_data'),
            'data'    => $label
        ], 201);
    }

    public function edit(Request $request, Label $label)
    {
        $this->validate($request, [
            'name'      => 'required',
            'position'  => 'required|numeric|min:1',
            'color'     => 'required|string'
        ]);

        $updatedLabel = $this->labelObserver->updateData($request, $label);

        return response()->json([
            'success' => true,
            'message' => __('general.success_update'),
            'data'    => $updatedLabel
        ]);
    }

    public function delete(Label $label)
    {
        $this->labelObserver->deleteData($label);

        return response()->json([
            'success' => true,
            'message' => __('general.success_deleted'),
            'ok'      => true
        ]);
    }
}
