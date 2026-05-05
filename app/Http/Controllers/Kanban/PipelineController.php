<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Http\Resources\Kanban\PipelineResource;
use App\Models\Master\Label;
use App\Models\Master\PipelineSegment;
use App\Services\Kanban\PipelineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PipelineController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Master Pipeline Segments
    |--------------------------------------------------------------------------
    */

    protected $pipelineService;

    public function __construct(PipelineService $pipelineService)
    {
        $this->pipelineService      = $pipelineService;
    }


    public function index(Request $request)
    {
        $pipelines  = $this->pipelineService->getData($request)->get();

        return response()->json([
            'success'       => true,
            'pipelines'     => PipelineResource::collection($pipelines)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'template'  => 'nullable|in:default,sales,service,custom'
        ]);

        DB::beginTransaction();


        try { 
            $pipeline = $this->pipelineService->createData($request);
            $template = $request->template ?? 'default';
            $labels = $this->getTemplateLabels($template);

            foreach ($labels as $index => $labelData) {
                Label::create([
                    'name' => $labelData['name'],
                    'color' => $labelData['color'],
                    'position' => $index + 1,
                    'type' => 'crm',
                    'is_closeable' => $labelData['is_closeable'] ?? 'no',
                    'is_default' => 'no',
                    'pipeline_segment_id' => $pipeline->id
                ]);
            }

            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'Pipeline berhasil dibuat',
                'pipeline'  => PipelineResource::make($pipeline)
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
     * Update pipeline name/color
     */
    public function update(Request $request, PipelineSegment $pipeline)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7'
        ]);
 
        $pipeline->update($request->only(['name', 'color']));

        return response()->json([
            'success'   => true,
            'message'   => 'Pipeline berhasil diupdate',
            'pipeline'  => PipelineResource::make($pipeline)
        ]);
    }
 
    public function destroy(PipelineSegment $pipeline)
    {
      
        $hasStores = DB::table('stores')
            ->whereIn('label_id', function ($q) use ($pipeline) {
                $q->select('id')
                    ->from('labels')
                    ->where('pipeline_segment_id', $pipeline->id);
            })
            ->exists();

        if ($hasStores) {
            return response()->json([
                'success' => false,
                'message' => 'Pipeline masih digunakan oleh kontak. Pindahkan kontak terlebih dahulu.'
            ], 400);
        }

        DB::beginTransaction();
        try { 
            $pipeline->labels()->delete();
            $pipeline->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pipeline berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
 
    private function getTemplateLabels($template)
    {
        $templates = [
            'default' => [ 
                ['name' => 'Penawaran Terkirim', 'color' => '#fbbf24', 'is_closeable' => 'no'],
                ['name' => 'Negosiasi', 'color' => '#f59e0b', 'is_closeable' => 'no'],
                ['name' => 'Penjualan Berhasil', 'color' => '#10b981', 'is_closeable' => 'yes'],
                ['name' => 'Penjualan Gagal', 'color' => '#6b7280', 'is_closeable' => 'yes'],
            ],
            'sales' => [ 
                ['name' => 'Qualified', 'color' => '#8b5cf6', 'is_closeable' => 'no'],
                ['name' => 'Proposal', 'color' => '#ec4899', 'is_closeable' => 'no'],
                ['name' => 'Closing', 'color' => '#f59e0b', 'is_closeable' => 'no'],
                ['name' => 'Won', 'color' => '#10b981', 'is_closeable' => 'yes'],
                ['name' => 'Lost', 'color' => '#ef4444', 'is_closeable' => 'yes'],
            ],
            'service' => [
                ['name' => 'Ticket Baru', 'color' => '#3b82f6', 'is_closeable' => 'no'],
                ['name' => 'In Progress', 'color' => '#fbbf24', 'is_closeable' => 'no'],
                ['name' => 'Waiting Response', 'color' => '#f59e0b', 'is_closeable' => 'no'],
                ['name' => 'Resolved', 'color' => '#10b981', 'is_closeable' => 'yes'],
                ['name' => 'Closed', 'color' => '#6b7280', 'is_closeable' => 'yes'],
            ]
        ];

        return $templates[$template] ?? $templates['default'];
    }
}
