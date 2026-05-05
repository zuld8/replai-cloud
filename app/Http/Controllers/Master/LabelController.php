<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Label;
use App\Observers\Master\LabelObserver;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    protected $labelObserver;

    public function __construct(LabelObserver $labelObserver)
    {
        $this->labelObserver        = $labelObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. List Label Page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $labels     = $this->labelObserver->getData($request)->get(['id', 'name', 'position', 'color', 'type']);
        // Add chat count for each label
        $labels->each(function ($label) {
            $label->chat_count = \App\Models\ChatBot\HistoryChat::whereJsonContains('label', ['id' => $label->id])->count();
        });
        return view('master.label.index', ['page' => 'Daftar Label', 'breadcumb' => true], compact('labels'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Label Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('master.label.create', ['page' => __('master.label.add_label'), 'breadcumb' => true]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Label Page
    |--------------------------------------------------------------------------
    */

    public function update(Label $label)
    {
        return view('master.label.update', ['page' => __('master.label.update_label'), 'breadcumb' => true], compact('label'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Store Label Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'position'  => 'required|numeric|min:1',
            'color'     => 'required|string'
        ]);

        $this->labelObserver->createData($request);

        return redirect()->route('labels')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Label Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, Label $label)
    {
        $this->validate($request, [
            'name'      => 'required',
            'position'  => 'required|numeric|min:1',
            'color'     => 'required|string'
        ]);

        $this->labelObserver->updateData($request, $label);

        return redirect()->route('labels')->with(['flash'    => __('general.success_update')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Delete Kategori Data
    |--------------------------------------------------------------------------
    */

    public function delete(Label $label)
    {
        $this->labelObserver->deleteData($label);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }
    /*
    |--------------------------------------------------------------------------
    | 6. Download Contacts by Label (CSV Export)
    |--------------------------------------------------------------------------
    */

    public function downloadContacts(Label $label)
    {
        $chats = \App\Models\ChatBot\HistoryChat::whereJsonContains('label', ['id' => $label->id])
            ->whereNotNull('from_number')
            ->get(['name', 'from_number', 'from', 'created_at']);

        $filename = 'kontak_label_' . str_replace(' ', '_', strtolower($label->name)) . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($chats, $label) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");
            // Header row
            fputcsv($file, ['No', 'Nama', 'Nomor', 'Channel', 'Tanggal Masuk']);

            foreach ($chats as $i => $chat) {
                fputcsv($file, [
                    $i + 1,
                    $chat->name ?? '-',
                    $chat->from_number,
                    strtoupper($chat->from ?? '-'),
                    $chat->created_at ? $chat->created_at->format('d/m/Y H:i') : '-',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /*
    |--------------------------------------------------------------------------
    | 7. Get Contacts for a Label (JSON - for modal)
    |--------------------------------------------------------------------------
    */

    public function contacts(Label $label)
    {
        $chats = \App\Models\ChatBot\HistoryChat::whereJsonContains('label', ['id' => $label->id])
            ->whereNotNull('from_number')
            ->orderBy('name')
            ->get(['id', 'name', 'from_number', 'from', 'created_at', 'status']);

        return response()->json([
            'label'    => $label->name,
            'color'    => $label->color,
            'total'    => $chats->count(),
            'contacts' => $chats->map(fn($c) => [
                'id'         => $c->id,
                'name'       => $c->name ?? 'Unknown',
                'phone'      => $c->from_number,
                'channel'    => strtoupper($c->from ?? 'WA'),
                'status'     => $c->status,
                'created_at' => $c->created_at?->format('d/m/Y'),
            ])->values(),
        ]);
    }

}