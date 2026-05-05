<?php

namespace App\Observers\Master;

use App\Models\Master\Label;
use Illuminate\Http\Request;

class LabelObserver
{
    public function getData(Request $request)
    {
        return Label::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->orderBy('name', 'asc');
    }

    public function createData(Request $request, String $type = 'keyword')
    {
        return Label::create([
            'name'              => $request->name,
            'tag'               => $request->tag,
            'position'          => $request->position,
            'color'             => $request->color ?? $this->generateRandomColor(),
            'type'              => $type,
            'is_closeable'      => 'no',
            'is_default'        => 'no',
            'pipeline_segment_id'   => $request->pipeline_segment_id ?? null
        ]);
    }

    public function updateData(Request $request, Label $label)
    {
        $label->update([
            'name'              => $request->name,
            'tag'               => $request->tag,
            'position'          => $request->position,
            'color'             => $request->color
        ]);
    }


    public function deleteData(Label $label)
    {
        $label->delete();
    }


    /**
     * Generate random color
     */
    private function generateRandomColor()
    {
        $colors = [
            '#ef4444',
            '#f97316',
            '#f59e0b',
            '#eab308',
            '#84cc16',
            '#22c55e',
            '#10b981',
            '#14b8a6',
            '#06b6d4',
            '#0ea5e9',
            '#3b82f6',
            '#6366f1',
            '#8b5cf6',
            '#a855f7',
            '#d946ef',
            '#ec4899',
            '#f43f5e'
        ];
        return $colors[array_rand($colors)];
    }
}
