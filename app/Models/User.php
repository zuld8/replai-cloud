<?php
// app/Models/User.php

namespace App\Models;

use App\Models\Merchant\Merchant;
use App\Models\Scopes\FilterByMerchantScope;
use App\Notifications\User\EmailVerificationNotification;
use App\Notifications\User\ForgetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        'gender',
        'merchant_id',
        'phone',
        'role',
        'email_verified_at',
        'business_id',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $guarded      = ['id'];
    protected $primaryKey   = 'id';
    protected $keyType      = 'string';
    public $incrementing    = false;

    protected static function booted()
    {
        if (auth()->guard('web')->check()) {
            static::addGlobalScope(new FilterByMerchantScope);
        }

        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
            $user = my_user();
            if ($user) {
                $model->merchant_id = $user->merchant_id;
                $model->business_id = my_business();
            }
        });
    }

    /**
     * Relasi ke Role (One-to-Many)
     */
    public function role_access(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function getImageDataAttribute()
    {
        if (file_exists($this->photo)) {
            return $this->photo;
        } else {
            return 'images/user.png';
        }
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new EmailVerificationNotification);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ForgetPasswordNotification($token));
    }

    /**
     * Check if user is admin (bypass permission)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user (need permission check)
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Override hasPermissionTo untuk handle admin bypass
     * IMPORTANT: Jangan override hasPermissionTo, gunakan Gate::before instead
     */
    public function can($ability, $arguments = []): bool
    {
        // Admin bypass semua permission
        if ($this->isAdmin()) {
            return true;
        }

        // User biasa check permission dari Spatie
        return parent::can($ability, $arguments);
    }

    /**
     * Check permission with admin bypass
     * Method alternatif untuk check permission
     */
    public function hasPermission(string $permission): bool
    {
        // Admin bypass
        if ($this->isAdmin()) {
            return true;
        }

        // Check via Spatie
        try {
            return $this->hasPermissionTo($permission, 'web');
        } catch (\Exception $e) {

            return false;
        }
    }

    /**
     * Get user display role name
     */
    public function getRoleNameAttribute(): string
    {
        if ($this->isAdmin()) {
            return 'Administrator';
        }

        return $this->role_access?->name ?? 'User';
    }

    /**
     * Get all permissions (including via roles)
     */
    public function getAllPermissionsAttribute()
    {
        if ($this->isAdmin()) {
            return collect(['*']); // Admin has all permissions
        }

        return $this->getAllPermissions();
    }
}
