<?php

namespace App\Http\Controllers\Store;

use App\Exports\StoreExport;
use App\Http\Controllers\Controller;
use App\Imports\Store\StoreImport;
use App\Models\Master\Category;
use App\Models\Store\Store;
use App\Observers\Master\CategoryObserver;
use App\Observers\Master\DirectoryObserver;
use App\Observers\Master\LabelObserver;
use App\Observers\Store\StoreObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class StoreController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Store or Customer Data
    |--------------------------------------------------------------------------
    */

    protected $storeObserver;
    protected $directoryObserver;
    protected $categoryObserver;
    protected $labelObserver;

    public function __construct(StoreObserver $storeObserver, DirectoryObserver $directoryObserver, CategoryObserver $categoryObserver, LabelObserver $labelObserver)
    {
        $this->storeObserver        = $storeObserver;
        $this->directoryObserver    = $directoryObserver;
        $this->categoryObserver     = $categoryObserver;
        $this->labelObserver        = $labelObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Stores List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        $queryArray     = $request->all();
        $params         = http_build_query($queryArray);

        // WA Personal (whatsapp_devices)
        $personalAccounts = collect(\App\Models\WhatsappDevice::orderBy('name')
            ->get(['id', 'name', 'phone'])
            ->map(fn($d) => ['id' => 'personal_' . $d->id, 'name' => $d->name . ($d->phone ? ' (' . $d->phone . ')' : ''), 'type' => 'personal'])->toArray());

        // WABA (meta_accounts)
        $wabaList = collect(\App\Models\MetaAccount::orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($d) => ['id' => 'waba_' . $d->id, 'name' => $d->name, 'type' => 'waba'])->toArray());

        $wabaAccounts = $personalAccounts->merge($wabaList);


        if ($request->has('draw') || $request->ajax()) {  // DataTables sends 'draw' param

            $stores         = $this->storeObserver->getData($request);

            return DataTables::of($stores)
                ->addColumn('province', function ($row) {
                    $province   = $row->district->city->province->name ?? '';
                    $provinceID = $row->district->city->province_id ?? '';
                    $html = '<a href="' . route('directory.cities') . '?province=' . $provinceID . '" class="text-info">' . $province . '</a>';
                    return $html;
                })->addColumn('city', function ($row) {
                    $cityType = $row->district->city->type ?? '';
                    $cityName = $row->district->city->name ?? '';
                    $html = '<a href="' . route('directory.districts') . '?city=' . ($row->district->city_id ?? '') . '" class="text-info">' . $cityType . ' ' . $cityName . '</a>';
                    return $html;
                })->addColumn('category', function ($row) {
                    $html = '<a class="text-info" href="' . route('stores') . '?category=' . $row->category_id . '"> ' . ($row->category->name ?? '') . ' </a>';
                    return $html;
                })->addColumn('district', function ($row) {
                    $html = '<a class="text-info" href="' . route('stores') . '?district=' . $row->district_id . '"> ' . ($row->district->name ?? '') . ' </a>';
                    return $html;
                })->addColumn('identity', function ($row) {
                    // Sub-label: @username (IG-style) atau email
                    $sub = !empty($row->wa_username)
                        ? '<i class="bx bx-at" style="font-size:11px"></i>' . e($row->wa_username)
                        : e($row->email ?? '');

                    // Badge "Username" untuk kontak tanpa nomor HP (BSUID-only)
                    $privacyBadge = (empty($row->phone) && !empty($row->bsuid))
                        ? ' <span style="display:inline-flex;align-items:center;gap:2px;font-size:10px;'
                          . 'padding:1px 5px;border-radius:9px;border:1px solid #dee2e6;'
                          . 'color:#6c757d;background:#f8f9fa;">'
                          . '<i class="bx bx-lock-alt" style="font-size:10px"></i>&nbsp;Username</span>'
                        : '';

                    return '<div class="d-flex align-items-center">
                                <div class="d-flex flex-column">
                                  <span class="fw-semibold lh-1">' . e($row->name) . $privacyBadge . '</span>
                                  <small class="text-muted">' . $sub . '</small>
                                </div>
                              </div>';
                })->addColumn('ai_agent', function ($row) {
                    $history = $row->history;
                    if ($history) {
                        if ($history->from == 'livechat') {
                            return $history->livechat->finetunnel->name ?? '';
                        }

                        if ($history->from == 'whatsapp') {
                            return $history->device->finetunnel->name ?? '';
                        }
                    }

                    return '';
                })->addColumn('sumber', function ($row) {
                    $history = $row->history;
                    if ($history) {
                        return '<div class="d-flex align-items-center"> 
                                <div class="d-flex flex-column">
                                  <span class="fw-semibold lh-1">' . $history->from_name . '</span> 
                                </div>
                              </div>';
                    }

                    return '';
                })->addColumn('handled', function ($row) {
                    $history = $row->history;
                    if ($history) {
                        return '<div class="d-flex align-items-center"> 
                                <div class="d-flex flex-column">
                                  <span class="fw-semibold lh-1">' . ($history->handled->name ?? '') . '</span>
                                  <small class="text-muted">' . ($history->assignment_at ? $history->assignment_at->format('Y-m-d') : '') . '</small>
                                </div>
                              </div>';
                    }
                    return '';
                })->addColumn('resolved', function ($row) {
                    $history = $row->history;
                    if ($history) {
                        return '<div class="d-flex align-items-center"> 
                                <div class="d-flex flex-column">
                                  <span class="fw-semibold lh-1">' . ($history->resolved->name ?? '') . '</span>
                                  <small class="text-muted">' . ($history->resolved_at ? $history->resolved_at->format('Y-m-d') : '') . '</small>
                                </div>
                              </div>';
                    }
                    return '';
                })->addColumn('status_chat', function ($row) {
                    $history = $row->history;
                    if ($history) {
                        if ($history->status == 'open') {
                            return '<span class="badge bg-outline-primary">Open</span>';
                        }

                        if ($history->status == 'resolved') {
                            return '<span class="badge bg-label-success">Resolved</span>';
                        }

                        if ($history->status == 'pending') {
                            return '<span class="badge bg-label-warning">Pending</span>';
                        }

                        if ($history->status == 'block') {
                            return '<span class="badge bg-outline-danger">Blok</span>';
                        }
                    }

                    return '<span class="badge bg-label-warning">No Data</span>';
                })->addColumn('lables', function ($row) {
                    $html   = '';
                    if ($row->label) {
                        $html = '<span class="badge bg-info me-1">' . $row->label->name . '</span>';
                    }
                    return $html;
                })->addColumn('action', function ($row) {
                    $html = '<a href="' . route('stores.update', $row->id) . '" class="btn btn-outline-warning btn-icon fs-16 ">
                                <i class="bx bx-pencil"></i>
                            </a>
                            <a href="' . route('stores.delete', $row->id) . '" class="btn btn-outline-danger btn-icon fs-16 deletebutton">
                                <i class="bx bx-trash "></i>
                            </a>';

                    return $html;
                })->rawColumns(['province',  'city', 'category', 'district', 'action', 'identity', 'status_chat', 'handled', 'lables', 'sumber', 'ai_agent', 'resolved'])
                ->make(true);
        }

        return view('stores.index', [
            'wabaAccounts' => $wabaAccounts,'page'  => __("page.contact.page"), 'breadcumb' => true], compact('params'));
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Page
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        $provinces      = $this->directoryObserver->getProvince($request)->get(['id', 'name']);
        $labels         = $this->labelObserver->getData($request)->orderBy('position', 'asc')->get(['id', 'name', 'position']);
        return view('stores.create', ['page' => __('page.contact.add'), 'breadcumb' => true], compact('categories', 'provinces', 'labels'));
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Update Page
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Store $store)
    {
        $categories     = $this->categoryObserver->getData($request)->get(['id', 'name']);
        $provinces      = $this->directoryObserver->getProvince($request)->get(['id', 'name']);
        $labels         = $this->labelObserver->getData($request)->orderBy('position', 'asc')->get(['id', 'name', 'position']);
        return view('stores.update', ['page' => __('page.contact.edit'), 'breadcumb' => true], compact('categories', 'provinces', 'store', 'labels'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. Create Data
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $this->validate($request, [
            'category'      => 'nullable',
            'name'          => 'required',
            'pemilik'       => 'nullable',
        ]);


        $contact = $this->storeObserver->createData($request);


        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Kontak berhasil disimpan.',
                'contact' => $contact,
            ], 201);
        }

        return redirect()->route('stores')->with(['flash'    => __('general.success_add_data')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Data
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request, Store $store)
    {
        $this->validate($request, [
            'category'      => 'required',
            'name'          => 'required',
            'pemilik'       => 'nullable',
        ]);

        $this->storeObserver->updateData($request, $store);

        return redirect()->route('stores')->with(['flash'    => __('general.success_update')]);
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Delete Data
    |--------------------------------------------------------------------------
    */

    public function delete(Store $store)
    {
        $this->storeObserver->deleteData($store);

        return redirect()->back()->with(['flash'    => __('general.success_deleted')]);
    }

    /*
    |--------------------------------------------------------------------------
    | 7. Import Data
    |--------------------------------------------------------------------------
    */

    public function import(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        if (!$request->file('file')) {
            return redirect()->back()->with(['gagal' => 'File tidak ditemukan']);
        }

        // Allow up to 10 menit untuk file besar
        set_time_limit(600);
        ini_set('memory_limit', '512M');

        try {
            $rawData = Excel::toArray(new StoreImport(), $request->file('file'));
        } catch (\Exception $e) {
            return redirect()->back()->with(['gagal' => 'Gagal membaca file: ' . $e->getMessage()]);
        }

        $rows = $rawData[0] ?? [];
        if (empty($rows)) {
            return redirect()->back()->with(['gagal' => __('general.file_not_reader')]);
        }

        // Validasi: WA account wajib dipilih sebelum import
        if (empty($request->meta_account_id)) {
            return redirect()->back()->with(['gagal' => '⚠️ Pilih akun WhatsApp terlebih dahulu sebelum mengimpor kontak.']);
        }

        // Filter empty rows BEFORE counting (Excel often adds hundreds of empty trailing rows)
        $rows = array_filter($rows, fn($r) => !empty(trim($r['name'] ?? '')));
        $rows = array_values($rows); // re-index

        if (empty($rows)) {
            return redirect()->back()->with(['gagal' => 'File tidak berisi data. Pastikan kolom NAME terisi.']);
        }

        $authUser    = auth()->user();
        $merchantId  = $authUser->merchant_id ?? null;
        $businessId  = my_business() ?? ($authUser->business_id ?? null);
        $chunkSize   = 1000;
        $inserted    = 0;
        $skipped     = 0;   // duplicate skip counter
        $totalRows   = count($rows); // only counts non-empty rows

        try {
            // -----------------------------------------------
            // 1. Pre-cache semua categories (1 query saja)
            // -----------------------------------------------
            $uniqueCatNames = array_unique(
                array_filter(array_map(fn($r) => strtoupper($r['category'] ?? ''), $rows))
            );

            $existingCats = Category::whereIn('name', $uniqueCatNames)
                ->pluck('id', 'name')
                ->toArray();

            $missingCats = array_diff($uniqueCatNames, array_keys($existingCats));
            foreach ($missingCats as $catName) {
                $cat = Category::create(['name' => $catName]);
                $existingCats[$catName] = $cat->id;
            }

            // -----------------------------------------------
            // 2. Pre-load existing phones untuk skip duplikat
            //    (1 query, simpan di memory sebagai Set)
            // -----------------------------------------------
            // Strip waba_/personal_ prefix — DB column is char(36) UUID only
            $rawMeta = $request->meta_account_id ?? null;
            $importMetaAccountId = $rawMeta
                ? preg_replace('/^(waba_|personal_)/', '', $rawMeta)
                : null;

            // Scope duplicate check ke business_id (bukan per akun WA)
            // Nomor HP wajib unik per bisnis — import via 2 akun WA berbeda tetap ter-dedup
            $dupQuery = DB::table('stores')->where('business_id', $businessId);

            // phone => store_id map (biar bisa update kategori nanti)
            $existingMap    = (clone $dupQuery)->whereNotNull('phone')->pluck('id', 'phone')->toArray();
            $existingPhones = $existingMap; // compat alias (isset check)
            $updateExisting = $request->update_existing_category === 'yes';
            $updated        = 0;
            $catUpdates     = []; // store_id => category_id

            $existingNames = array_flip(
                (clone $dupQuery)->whereNull('phone')->pluck('name')->toArray()
            );

            // -----------------------------------------------
            // 3. Proses per chunk → bulk insert
            // -----------------------------------------------
            $chunks = array_chunk($rows, $chunkSize);

            foreach ($chunks as $chunk) {
                $toInsert = [];

                foreach ($chunk as $d) {
                    if (empty($d['name'])) {
                        $skipped++;
                        continue;
                    }

                    $phone   = !empty($d['phone']) ? (string)$d['phone'] : null;
                    $catName = strtoupper($d['category'] ?? 'UMUM');
                    $catId   = $existingCats[$catName] ?? null;

                    // Duplikat phone: skip atau update kategori
                    if ($phone !== null && isset($existingMap[$phone])) {
                        if ($updateExisting && $catId) {
                            $catUpdates[$existingMap[$phone]] = $catId; // jadwalkan update
                            $updated++;
                        } else {
                            $skipped++;
                        }
                        continue;
                    }
                    if ($phone === null && isset($existingNames[$d['name']])) {
                        $skipped++;
                        continue;
                    }

                    $uuid = \Illuminate\Support\Str::uuid()->toString();
                    $toInsert[] = [
                        'id'          => $uuid,
                        'category_id' => $catId,
                        'district_id' => $d['district'] ?? null,
                        'name'        => $d['name'],
                        'phone'       => $phone,
                        'email'       => !empty($d['email']) ? $d['email'] : null,
                        'address'     => $d['address'] ?? null,
                        'pemilik'     => !empty($d['pemilik']) ? $d['pemilik'] : null,
                        'merchant_id' => $merchantId,
                        'business_id' => $businessId,
                        'meta_account_id' => $importMetaAccountId,
                        'status'      => 'no',
                        'prospek'     => 'pending',
                        'position'    => 0,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                    $inserted++;

                    // Update memory set supaya chunk berikutnya tidak duplikat dalam file
                    if ($phone !== null) { $existingMap[$phone] = $uuid; $existingPhones[$phone] = $uuid; }
                    else $existingNames[$d['name']] = true;
                }

                if (!empty($toInsert)) {
                    DB::table('stores')->insert($toInsert);
                }
            }

            // Eksekusi bulk update kategori kontak existing
            if (!empty($catUpdates)) {
                $byCat = [];
                foreach ($catUpdates as $sid => $cid) { $byCat[$cid][] = $sid; }
                foreach ($byCat as $cid => $ids) {
                    \App\Models\Store\Store::whereIn('id', $ids)->update([
                        'category_id' => $cid,
                        'updated_at'  => now(),
                    ]);
                }
            }

            $msg = "✅ Berhasil import {$inserted} kontak.";
            if ($updated > 0) $msg .= " Kategori diperbarui: {$updated}.";
            if ($skipped > 0) $msg .= " Dilewati: {$skipped}.";
            $msg .= " Total baris valid: {$totalRows}.";

            return redirect()->back()->with(['flash' => $msg]);

        } catch (\Exception $e) {
            return redirect()->back()->with(['gagal' => 'Import gagal: ' . $e->getMessage()]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 8. Export Data
    |--------------------------------------------------------------------------
    */

    public function export(Request $request)
    {
        return (new StoreExport($request, $this->storeObserver))->download('customer_or_store_reports-' . date('Y-m-d') . '.xlsx');
    }


    /*
    |--------------------------------------------------------------------------
    | 9. Delete Multiple Data
    |--------------------------------------------------------------------------
    */

    public function deleteMultiple(Request $request)
    {
        // Select All mode — hapus semua data merchant ini
        if ($request->select_all) {
            $authUser  = auth()->user();
            $merchantId = $authUser->merchant_id ?? null;
            $query = Store::query();
            if ($merchantId) {
                $query->where('merchant_id', $merchantId);
            }
            $deleted = $query->delete();
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deleted} kontak"
            ]);
        }

        // Normal mode — hapus berdasarkan IDs
        $ids = $request->ids;
        if (!empty($ids)) {
            $deleted = Store::whereIn('id', $ids)->delete();
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deleted} kontak"
            ]);
        }

        return response()->json(['success' => false, 'message' => __('general.choosed_not_found')]);
    }
}
