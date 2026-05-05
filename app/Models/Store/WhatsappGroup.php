<?php

namespace App\Models\Store;

use App\Models\Merchant\Merchant;
use App\Models\Scopes\FilterByBusinessScope;
use App\Models\WhatsappDevice;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class WhatsappGroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image',
        'scrapping_id',
        'description',
        'merchant_id',
        'business_id',
        'group_id',
        'scraping',
        'device_id'
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
            $model->id = Uuid::uuid4()->toString();

            if (empty($model->merchant_id) || empty($model->business_id)) {
                $user = my_user();
                if ($user) {
                    $model->merchant_id = $user->merchant_id;
                    $model->business_id = my_business();
                }
            }
        });
    }

    public function contacts()
    {
        return $this->hasMany(Store::class, 'whatsapp_group_id');
    }

    public function device()
    {
        return $this->belongsTo(WhatsappDevice::class, 'device_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
}
