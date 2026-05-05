<?php

namespace App\Models\Store;

use App\Models\ChatBot\HistoryChat;
use App\Models\Master\Category;
use App\Models\Master\District;
use App\Models\Master\Label;
use App\Models\Merchant\Merchant;
use App\Models\Scopes\FilterByBusinessScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Uuid;

class Store extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'district_id',
        'name',
        'phone',
        'bsuid',
        'wa_username',
        'address',
        'pemilik',
        'status',
        'prospek',
        'email',
        'scrapping_id',
        'merchant_id',
        'business_id',
        'history_chat_id',
        'whatsapp_group_id',
        'label_id',
        'jid_number',
        'metadata',
        'meta_account_id'
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

            // Hanya isi otomatis jika belum diset manual
            if (empty($model->merchant_id) || empty($model->business_id)) {
                $user = my_user();
                if ($user) {
                    $model->merchant_id = $user->merchant_id;
                    $model->business_id = my_business();
                }
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

    public function history()
    {
        return $this->hasOne(HistoryChat::class, 'store_id')->where('status', 'open')->latest();
    }

    public function histories()
    {
        return $this->hasMany(HistoryChat::class, 'store_id');
    }

    public function group()
    {
        return $this->belongsTo(WhatsappGroup::class, 'whatsapp_group_id');
    }

    public function label()
    {
        return $this->belongsTo(Label::class, 'label_id');
    }
}
