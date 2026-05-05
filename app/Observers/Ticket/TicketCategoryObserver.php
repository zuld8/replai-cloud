<?php

namespace App\Observers\Ticket;

use App\Models\Ticket\TicketCategory;
use Illuminate\Http\Request;

class TicketCategoryObserver
{
    public function getData(Request $request)
    {
        $query = TicketCategory::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->get('name') . '%');
        }

        return $query;
    }

    public function createData(Request $request)
    {
        $slug = $request->slug ?: $this->generateSlug($request->name);

        return TicketCategory::create([
            'name'              => $request->name, 
            'slug'              => $slug,
            'description'       => $request->description,
            'is_active'         => $request->get('is_active', true),
        ]);
    }

    public function updateData(Request $request, TicketCategory $category)
    {
        $slug = $request->slug ?: $this->generateSlug($request->name);

        $category->update([
            'name'              => $request->name, 
            'slug'              => $slug,
            'description'       => $request->description,
            'is_active'         => $request->get('is_active', $category->is_active),
        ]);

        return $category;
    }

    private function generateSlug($name)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }

    public function getOptions(Request $request)
    {
        $categories = TicketCategory::select('id', 'name', 'slug', 'description', 'is_active')
                                    ->where('is_active', true)
                                    ->orderBy('name', 'asc')
                                    ->get();

        return $categories;

    }

    public function search(Request $request)
    {
        $searchQuery = $request->get('query');
        $perPage = $request->get('per_page', 20);

        $categories = TicketCategory::where('name', 'like', '%' . $searchQuery . '%')
                                    ->orWhere('description', 'like', '%' . $searchQuery . '%')
                                    ->orderBy('name', 'asc')
                                    ->paginate($perPage);

            
        return $categories;
    }
}