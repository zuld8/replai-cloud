<?php

namespace App\Services\Kanban;

use App\Models\Master\PipelineSegment;
use Illuminate\Http\Request;

class PipelineService
{
    public function getData(Request $request)
    {
        return PipelineSegment::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->orderBy('position', 'asc');
    }

    public function createData(Request $request)
    {
        return PipelineSegment::create([
            'name' => $request->name,
            'color' => $this->generateRandomColor()
        ]);
    }

     /**
     * Generate random color
     */
    private function generateRandomColor()
    {
        $colors = ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981', '#06b6d4', '#f43f5e', '#84cc16'];
        return $colors[array_rand($colors)];
    }
}
