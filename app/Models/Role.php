<?php

namespace App\Models;

use App\Models\Scopes\FilterByMerchantScope;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\RefreshesPermissionCache;

class Role extends SpatieRole
{
    use HasPermissions;
    use RefreshesPermissionCache;

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        static::addGlobalScope(new FilterByMerchantScope);

        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }

            if (!isset($model->merchant_id)) {
                $user = my_user();
                if ($user && $user->merchant_id) {
                    $model->merchant_id = $user->merchant_id;
                }
            }
        });
    }

    public static function findByName(string $name, $guardName = null): self
    {
        $guardName = $guardName ?? config('auth.defaults.guard');

        $query = static::where('name', $name)
            ->where('guard_name', $guardName);

        // Tambahkan filter merchant
        $user = my_user();
        if ($user && $user->merchant_id) {
            $query->where('merchant_id', $user->merchant_id);
        }

        return $query->firstOrFail();
    }

    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        $query = static::where('name', $attributes['name'])
            ->where('guard_name', $attributes['guard_name']);

        // Tambahkan merchant_id check
        if (isset($attributes['merchant_id'])) {
            $query->where('merchant_id', $attributes['merchant_id']);
        }

        if ($query->exists()) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        return static::query()->create($attributes);
    }

    // ========================================
    // OVERRIDE PERMISSION METHODS - INI KUNCI!
    // ========================================

    /**
     * Override permissions relationship untuk filter by merchant
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            'role_id',
            'permission_id'
        );
    }

    /**
     * Override givePermissionTo untuk memastikan tidak affect role lain
     */
    public function givePermissionTo(...$permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (empty($permission)) {
                    return false;
                }

                return $this->getStoredPermission($permission);
            })
            ->filter()
            ->each(function ($permission) {
                $this->ensureModelSharesGuard($permission);
            })
            ->map->id
            ->all();

        $model = $this->getModel();

        if ($model->exists) {
            // Attach hanya untuk role ini, tidak affect role lain
            $this->permissions()->syncWithoutDetaching($permissions);
        } else {
            $class = \get_class($model);

            $class::saved(
                function ($object) use ($permissions, $model) {
                    if ($object->getKey() != $model->getKey()) {
                        return;
                    }

                    $model->permissions()->syncWithoutDetaching($permissions);
                }
            );
        }

        $this->forgetCachedPermissions();

        return $this;
    }

    /**
     * Override syncPermissions untuk clear hanya permission role ini
     */
    public function syncPermissions(...$permissions)
    {
        // Detach hanya permission milik role ini
        $this->permissions()->detach();

        return $this->givePermissionTo($permissions);
    }

    /**
     * Helper untuk get stored permission by merchant
     */
    protected function getStoredPermission($permissions)
    {
        $permissionClass = $this->getPermissionClass();

        if (is_numeric($permissions)) {
            return $permissionClass->findById($permissions, $this->getDefaultGuardName());
        }

        if (is_string($permissions)) {
            // Pastikan cari permission by merchant juga
            $query = $permissionClass::where('name', $permissions)
                ->where('guard_name', $this->getDefaultGuardName());

            // Filter by merchant_id jika permission model punya merchant_id
            if ($this->merchant_id) {
                $query->where(function ($q) {
                    $q->where('merchant_id', $this->merchant_id)
                        ->orWhereNull('merchant_id'); // Global permissions
                });
            }

            return $query->first();
        }

        if (is_object($permissions)) {
            return $permissions;
        }

        return $permissionClass->findByName($permissions, $this->getDefaultGuardName());
    }

    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            User::class,
            'model',
            'model_has_roles',
            'role_id',
            'model_id'
        );
    }
}
