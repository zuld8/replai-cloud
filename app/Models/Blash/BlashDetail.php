<?php

namespace App\Models\Blash;

use App\Models\Store\Store;
use App\Models\Store\WhatsappGroup;
use App\Models\WhatsappDevice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class BlashDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'blash_whatsapp_id',
        'phone',
        'email',
        'store_id',
        'reports',
        'status',
        'sending_status',
        'delivery_status',
        'delivery_error',
        'wamid',
        'schedule',
        'type',
        'sending',
        'text',
        'device_id',
        'whatsapp_group_id'
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
        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'schedule' => 'datetime',
        ];
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id')->withoutGlobalScopes();
    }

    public function parent()
    {
        return $this->belongsTo(BlashWhatsapp::class, 'blash_whatsapp_id')->withoutGlobalScopes();
    }

    public function device()
    {
        return $this->belongsTo(WhatsappDevice::class, 'device_id')->withoutGlobalScopes();
    }

    public function group()
    {
        return $this->belongsTo(WhatsappGroup::class, 'whatsapp_group_id');
    }
}
