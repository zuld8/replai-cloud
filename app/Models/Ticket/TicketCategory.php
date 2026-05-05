<?php

namespace App\Models\Ticket;

use App\Models\Ticket\Ticket;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Scopes\FilterByBusinessScope;
use Illuminate\Database\Eloquent\Model; 
use Ramsey\Uuid\Uuid;

class TicketCategory extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'business_id',
        'is_active',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded          = ['id'];
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    public $incrementing        = false;

    protected static function booted()
    {
        static::addGlobalScope(new FilterByBusinessScope);

        static::creating(function ($model) { 
            $user       = auth()->user();
            if ($user) { 
                $model->business_id = my_business();
            }
        });
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should have default values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class,'category_id');
    }
}
