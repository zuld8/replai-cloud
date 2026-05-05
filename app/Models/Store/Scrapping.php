<?php

namespace App\Models\Store;

use App\Models\Master\Category;
use App\Models\Master\District;
use App\Models\Merchant\Merchant;
use App\Models\Scopes\FilterByBusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class Scrapping extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'district_id',
        'name',
        'schedule',
        'status',
        'scrapping_method',
        'devices'
    ];

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
        // static::addGlobalScope(new FilterByMerchantScope);
        static::addGlobalScope(new FilterByBusinessScope);
        static::creating(function ($model) {
            $model->id  = Uuid::uuid4()->toString();
            $user       = my_user();
            if ($user) {
                $model->merchant_id = $user->merchant_id;
                $model->business_id = my_business();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function groups()
    {
        return $this->hasMany(WhatsappGroup::class, 'scrapping_id');
    }

    public function stores()
    {
        return $this->hasMany(Store::class, 'scrapping_id');
    }
}
