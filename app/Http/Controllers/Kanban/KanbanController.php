<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Models\Master\Label;
use App\Models\Store\Store;
use App\Observers\Master\LabelObserver;
use App\Observers\Store\StoreObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KanbanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Kanban Controllers
    |--------------------------------------------------------------------------
    */

    protected $storeObserver;
    protected $labelObserver;
    protected $categoryObserver;

    public function __construct(StoreObserver $storeObserver, LabelObserver $labelObserver)
    {
        $this->storeObserver        = $storeObserver;
        $this->labelObserver        = $labelObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Stores List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        return view('stores.kanban', ['page'  => 'Crm Lead', 'breadcumb' => true]);
    }


    public function getData(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 20);
            $pipelineId = $request->get('pipeline_id'); // Filter by pipeline

            // Get pipeline labels
            $labelsQuery = Label::with(['stores' => function ($q) {
                $q->select('id');
            }])->where('pipeline_segment_id', '!=', null)->orderBy('position', 'asc');

            if ($pipelineId) {
                $labelsQuery->where('pipeline_segment_id', $pipelineId);
            }

            $labels = $labelsQuery->get();

            $kanbanData = collect();

            // 1. Virtual "Kontak Awal" stage (contacts without label)
            $unlabeledStoresQuery = Store::whereNull('label_id');

            if ($request->search) {
                $search = $request->search;
                $unlabeledStoresQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->category) {
                $unlabeledStoresQuery->where('category_id', $request->category);
            }

            $unlabeledTotal = $unlabeledStoresQuery->count();
            $unlabeledStores = $unlabeledStoresQuery
                ->select(['id', 'name', 'email', 'phone', 'category_id', 'label_id', 'created_at', 'updated_at'])
                ->with([
                    'category:id,name',
                    'history' => function ($q) {
                        $q->select(['id', 'store_id', 'status', 'from', 'assignment_at', 'resolved_at', 'handled_by', 'resolved_by_id']);
                    },
                    'history.handled:id,name',
                    'history.resolved:id,name'
                ])
                ->orderBy('updated_at', 'desc')
                ->limit($perPage)
                ->get();

            // Add virtual stage
            $kanbanData->push([
                'id' => 'unlabeled',
                'name' => 'Kontak Awal',
                'color' => '#94a3b8',
                'order' => 0,
                'is_virtual' => true,
                'is_closeable' => 'no',
                'total_stores' => $unlabeledTotal,
                'loaded_count' => $unlabeledStores->count(),
                'has_more' => $unlabeledTotal > $perPage,
                'stores' => $this->mapStores($unlabeledStores)
            ]);

            // 2. Regular labels with stores
            foreach ($labels as $label) {
                $storesQuery = Store::where('label_id', $label->id);

                if ($request->search) {
                    $search = $request->search;
                    $storesQuery->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                }

                if ($request->category) {
                    $storesQuery->where('category_id', $request->category);
                }

                $totalStores = $storesQuery->count();
                $stores = $storesQuery
                    ->select(['id', 'name', 'email', 'phone', 'category_id', 'label_id', 'created_at', 'updated_at'])
                    ->with([
                        'category:id,name',
                        'history' => function ($q) {
                            $q->select(['id', 'store_id', 'status', 'from', 'assignment_at', 'resolved_at', 'handled_by', 'resolved_by_id']);
                        },
                        'history.handled:id,name',
                        'history.resolved:id,name'
                    ])
                    ->orderBy('position', 'asc')
                    ->orderBy('updated_at', 'desc')
                    ->limit($perPage)
                    ->get();

                $kanbanData->push([
                    'id' => $label->id,
                    'name' => $label->name,
                    'color' => $label->color ?? '#3b82f6',
                    'order' => $label->position ?? 0,
                    'is_virtual' => false,
                    'is_closeable' => $label->is_closeable,
                    'total_stores' => $totalStores,
                    'loaded_count' => $stores->count(),
                    'has_more' => $totalStores > $perPage,
                    'stores' => $this->mapStores($stores)
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $kanbanData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map stores to response format
     */
    private function mapStores($stores)
    {
        return $stores->map(function ($store) {
            return [
                'id' => $store->id,
                'name' => $store->name,
                'email' => $store->email,
                'phone' => $store->phone,
                'from_channel' => $store->history->from_name ?? '',
                'label' => $store->label->name ?? null,
                'category' => $store->category ? [
                    'id' => $store->category->id,
                    'name' => $store->category->name
                ] : null,
                'handled_by' => $store->history && $store->history->handled ? [
                    'name' => $store->history->handled->name,
                    'assigned_at' => $store->history->assignment_at ? $store->history->assignment_at->format('Y-m-d') : null
                ] : null,
                'resolved_by' => $store->history && $store->history->resolved ? [
                    'name' => $store->history->resolved->name,
                    'resolved_at' => $store->history->resolved_at ? $store->history->resolved_at->format('Y-m-d') : null
                ] : null,
                'status' => $store->history ? $store->history->status : 'open',
                'created_at' => $store->created_at->format('Y-m-d'),
                'updated_at' => $store->created_at->diffForHumans(),
            ];
        });
    }

    public function loadMoreStores(Request $request, $labelId)
    {
        try {
            $perPage    = $request->get('per_page', 20);
            $offset     = $request->get('offset', 0); // Dari mana mulai load

            // Query stores
            $storesQuery = Store::where('label_id', $labelId);

            // Apply filters (sama seperti getData)
            if ($request->search) {
                $search = $request->search;
                $storesQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->category) {
                $storesQuery->where('category_id', $request->category);
            }

            // Get total untuk cek has_more
            $totalCount = $storesQuery->count();

            // Get stores dengan offset
            $stores = $storesQuery
                ->select(['id', 'name', 'email', 'phone', 'category_id', 'label_id', 'position', 'created_at', 'updated_at'])
                ->with([
                    'category:id,name',
                    'history:id,store_id,status,assignment_at,resolved_at,handled_by,resolved_by_id',
                    'history.handled:id,name',
                    'history.resolved:id,name'
                ])
                ->orderBy('position', 'asc')
                ->orderBy('updated_at', 'desc')
                ->skip($offset)
                ->take($perPage)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stores->map(function ($store) {
                    return [
                        'id' => $store->id,
                        'name' => $store->name,
                        'email' => $store->email,
                        'phone' => $store->phone,
                        'from_channel'  => $store->history->from_name ?? '',
                        'label' => $store->label->name ?? '',
                        'category' => $store->category ? [
                            'id' => $store->category->id,
                            'name' => $store->category->name
                        ] : null,
                        'handled_by' => $store->history && $store->history->handled ? [
                            'name' => $store->history->handled->name,
                            'assigned_at' => optional($store->history->assignment_at)->format('Y-m-d')
                        ] : null,
                        'resolved_by' => $store->history && $store->history->resolved ? [
                            'name' => $store->history->resolved->name,
                            'resolved_at' => optional($store->history->resolved_at)->format('Y-m-d')
                        ] : null,
                        'status' => $store->history ? $store->history->status : 'open',
                        'created_at' => $store->created_at->format('Y-m-d'),
                        'updated_at' => $store->updated_at->format('Y-m-d H:i'),
                    ];
                }),
                'pagination' => [
                    'total' => $totalCount,
                    'per_page' => $perPage,
                    'current_offset' => $offset,
                    'loaded' => $stores->count(),
                    'has_more' => ($offset + $perPage) < $totalCount
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create contact directly in label
     */
    public function createContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email',
            'label_id' => 'nullable|exists:labels,id',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        DB::beginTransaction();
        try {
            $store = Store::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'label_id' => $request->label_id,
                'category_id' => $request->category_id,
                'jid_number' => $request->phone . '@s.whatsapp.net',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kontak berhasil ditambahkan',
                'store' => $store->load('category', 'label')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Update store label (drag & drop)
     */
    public function updateStoreLabel(Request $request)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'label_id' => 'required|exists:labels,id',
            'position' => 'nullable|integer'
        ]);

        try {
            DB::beginTransaction();

            $store = Store::findOrFail($request->store_id);
            $label = Label::findOrFail($request->label_id);

            $store->label_id = $label->id;

            if ($request->has('position')) {
                $store->position = $request->position;
            }

            $store->save();

            // Update history jika ada
            if ($store->history) {
                // $store->history->label = json_encode([
                //     ['id' => $label->id, 'name' => $label->name]
                // ]);
                // $store->history->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Label berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update order dalam kolom yang sama
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'stores' => 'required|array',
            'stores.*.id' => 'required|exists:stores,id',
            'stores.*.position' => 'required|integer'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->stores as $storeData) {
                Store::where('id', $storeData['id'])
                    ->update(['position' => $storeData['position']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get store detail
     */
    public function getStoreDetail($id)
    {
        try {
            $store = Store::with([
                'category',
                'district.city.province',
                'history.handled',
                'history.resolved',
                'history.livechat.finetunnel',
                'history.device.finetunnel'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $store
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Store tidak ditemukan'
            ], 404);
        }
    }
}
