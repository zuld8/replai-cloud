<?php

namespace App\Services\Master;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleService
{
    public function getData(Request $request)
    {
        return Role::query()
            ->when($request->name, function ($query, $request) {
                $query->where('name', 'like', "%{$request->name}%");
            })->withCount(['permissions', 'users'])->get();
    }

    public function createData(Request $request)
    {
        return Role::create([
            'name'          => $request->name,
            'guard_name'    => 'web',
        ]);
    }

    public function updateData(Request $request, Role $role)
    {
        $role->update([
            'name'          => $request->name,
        ]);
    }
}
