<?php

namespace App\Http\Controllers\Kanban;

use App\Http\Controllers\Controller;
use App\Http\Resources\Kanban\LabelResource;
use App\Models\Master\Label;
use App\Observers\Master\LabelObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabelController extends Controller
{

    protected $labelObserver;

    public function __construct(LabelObserver $labelObserver)
    {
        $this->labelObserver    = $labelObserver;
    }


    /**
     * Create new label in pipeline
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'pipeline_segment_id'   => 'required|exists:pipeline_segments,id',
            'color'                 => 'nullable|string|max:7',
            'position'              => 'nullable|integer'
        ]);

        $label  = $this->labelObserver->createData($request, 'CRM');

        return response()->json([
            'success'   => true,
            'message'   => 'Label berhasil ditambahkan',
            'label'     => LabelResource::make($label)
        ], 201);
    }

    /**
     * Update label
     */
    public function update(Request $request, Label $label)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7'
        ]);

        $label->update($request->only(['name', 'color']));

        return response()->json([
            'success' => true,
            'message' => 'Label berhasil diupdate',
            'label' => $label
        ]);
    }

    /**
     * Delete label (only if not closeable and no stores)
     */
  public function destroy($id)
    {
        try {
            $label = Label::findOrFail($id);
 
            if ($label->is_closeable === 'yes') {
                return response()->json([
                    'success' => false,
                    'message' => 'Label tahap closing tidak dapat dihapus'
                ], 400);
            }
 
            $storeCount = $label->stores()->count();
            if ($storeCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Label masih digunakan oleh {$storeCount} kontak"
                ], 400);
            }

            $position = $label->position;
            $pipelineId = $label->pipeline_segment_id;

            $label->delete();
 
            Label::where('pipeline_segment_id', $pipelineId)
                ->where('position', '>', $position)
                ->decrement('position');

            return response()->json([
                'success' => true,
                'message' => 'Label berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function reorder(Request $request)
    {
        $request->validate([
            'labels' => 'required|array',
            'labels.*.id' => 'required|exists:labels,id',
            'labels.*.position' => 'required|integer'
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->labels as $labelData) {
                Label::where('id', $labelData['id'])
                    ->update(['position' => $labelData['position']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Urutan label berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
