<?php

namespace App\Models\Blash;

use App\Models\Master\Category;
use App\Models\Master\City;
use App\Models\Master\District;
use App\Models\Master\MessageTemplate;
use App\Models\Scopes\FilterByBusinessScope;
use App\Models\Setting;
use App\Models\Store\WhatsappGroup;
use App\Models\WhatsappKeyAccount;
use App\Observers\Blash\BlashWhatsappObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class BlashWhatsapp extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'city_id',
        'district_id',
        'name',
        'schedule',
        'status',
        'use',
        'template_id',
        'delay',
        'waba',
        'meta_id',
        'waba_id',
        'metadata',
        'file',
        'devices',
        'whatsapp_sender_notif',
        'delay_message',
        'labels',
        'just_for_no_reply',
        'delay_for_not_reply',
        'delay_message_option',
        'delay_message',
        'ai_prompt',
        'broadcast_method',
        'group_whatsapp_id',
        'meta_account_id',
        'groups',
        'stop_sending',
        'rest_sending',

        'schedule_frequency',
        'days',
        'month',
        'time',
        'end_date',
        'start_date',
        'schedule_yearly_date',
        'yearly'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // protected function casts(): array
    // {
    //     return [
    //         'schedule' => 'datetime',
    //     ];
    // }

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

        BlashWhatsapp::observe(BlashWhatsappObserver::class);
 
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

    public function business()
    {
        return $this->belongsTo(Setting::class, 'business_id');
    }

    public function details()
    {
        return $this->hasMany(BlashDetail::class, 'blash_whatsapp_id')->orderBy('created_at', 'desc');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function template()
    {
        return $this->belongsTo(MessageTemplate::class, 'template_id');
    }

    public function waba_device()
    {
        return $this->belongsTo(WhatsappKeyAccount::class, 'waba_id');
    }

    public function sender()
    {
        return $this->hasMany(BlashDetail::class, 'blash_whatsapp_id')->where("sending", '!=', null);
    }

    public function group()
    {
        return $this->belongsTo(WhatsappGroup::class, 'group_whatsapp_id');
    }
}
