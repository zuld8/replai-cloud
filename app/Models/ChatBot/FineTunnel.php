<?php

namespace App\Models\ChatBot;

use App\Models\Courier\CourierFineTunnel;
use App\Models\Master\SubDisctrict;
use App\Models\Scopes\FilterByBusinessScope;
use App\Models\Setting;
use App\Observers\ChatBot\FineTunnelObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class FineTunnel extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'filejson',
        'fine_tunnel_id',
        'status',
        'method',
        'url',
        'option_audio_to_text_ai',
        'min_audio',
        'transfer_condition',
        'stop_ai_handoff',
        'welcome_message',
        'welcome_image',
        'term_condition',
        'model_ai',
        'history_limit',
        'label',
        'context_limit',
        'delay',
        'message_limit',
        'zip_code',
        'weight',
        'address',
        'sub_district_id',
        'agent',
        'google_sheet_id'
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
        // static::addGlobalScope(new FilterByMerchantScope);
        FineTunnel::observe(FineTunnelObserver::class);
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
        return $this->hasMany(FineTunnelDetail::class, 'fine_tunnel_id');
    }

    public function follow_ups()
    {
        return $this->hasMany(FollowUp::class, 'finetunnel_id')->orderBy('delay', 'asc');
    }

    public function gsheets()
    {
        return $this->hasMany(FineTunnelSheet::class, 'fine_tunnel_id');
    }

    public function couriers()
    {
        return $this->hasMany(CourierFineTunnel::class, 'finetunnel_id');
    }

    public function subdistrict()
    {
        return $this->belongsTo(SubDisctrict::class, 'sub_district_id');
    }

    public function documents()
    {
        return $this->hasMany(FineTunnelDocument::class, 'fine_tunnel_id');
    }
}
