<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserObserver
{
    public function getData(Request $request)
    {
        return User::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->orderBy('name', 'asc');
    }

    public function checkLimit()
    {
        // Use the same pattern as chatbot_limitation() / waba_limitation()
        // Check human agent limit from the active package of the current business
        $businessId = my_business();
        $business   = \App\Models\Setting::where('id', $businessId)->first(['id']);
        $package    = $business?->package_active;

        if ($package && $package->limit_user_option == 'yes') {
            // business_id is stored as comma-separated text → use LIKE
            $userCount = User::where('business_id', 'LIKE', "%{$businessId}%")
                             ->whereNull('deleted_at')
                             ->count();
            if ($userCount >= $package->users_limit) {
                return false;
            }
        }

        return true;
    }

    public function createData(Request $request, String $image)
    {
        return User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone ?? '',
            'password'          => Hash::make($request->password),
            'photo'             => $image,
            'email_verified_at' => now(),
            // A4: filter business_id ke bisnis milik merchant ini (anti cross-tenant)
            'business_id'       => (function() use ($request) {
                                        $allowed = \App\Models\Setting::where('merchant_id', my_user()->merchant_id)
                                            ->whereIn('id', (array)($request->business ?? []))->pluck('id')->all();
                                        return !empty($allowed) ? implode(',', $allowed) : my_user()->business_id;
                                    })(),
            'role'              => my_user()->role,
            'role_id'           => $request->role,
            'gender'            => $request->gender
        ]);
    }

    public function updateData(Request $request, User $user, String $image)
    {
        $user->update([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone ?? '',
            // A4: filter business_id ke bisnis milik merchant ini (anti cross-tenant)
            'business_id'       => (function() use ($request, $user) {
                                        $allowed = \App\Models\Setting::where('merchant_id', my_user()->merchant_id)
                                            ->whereIn('id', (array)($request->business ?? []))->pluck('id')->all();
                                        return !empty($allowed) ? implode(',', $allowed) : $user->business_id;
                                    })(),
            'photo'             => $image != '' ? $image : $user->photo,
            'gender'            => $request->gender,
            'role_id'           => $request->role,
        ]);
    }

    public function changePassword(Request $request, User $user)
    {
        $user->update([
            'password'          => Hash::make($request->password)
        ]);
    }

    public function getCheckUserPassword(Request $request, User $user)
    {

        return User::where("password", "!=", Hash::check($request->old_password, $user->password))->first();
    }

    public function deleteData(User $user)
    {
        $user->delete();
    }

    /**
     * Get user permissions melalui role
     */
    public function getUserPermissions(User $user)
    {
        if (!$user->role_id) {
            return collect([]);
        }

        return $user->role_access->permissions;
    }
}
