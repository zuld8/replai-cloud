<?php

namespace App\Models\ChatBot;

use App\Models\LiveChat;
use App\Models\Meta\InstagramAccount;
use App\Models\Meta\MessengerAccount;
use App\Models\Scopes\FilterByBusinessScope;
use App\Models\Setting;
use App\Models\Store\Store;
use App\Models\TelegramKey;
use App\Models\User;
use App\Models\WhatsappDevice;
use App\Models\WhatsappKeyAccount;
use App\Observers\ChatBot\HistoryChatObserver;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class HistoryChat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'device_id',
        'livechat_id',
        'merchant_id',
        'type',
        'from_number',
        'bsuid',
        'wa_username',
        'expire_date',
        'from',
        'status',
        'label',
        'note',
        'handled_by',
        'collabolator',
        'takeover',
        'name',
        'business_id',
        'additional_data',
        'assigned_by',
        'assignment_at',
        'resolved_at',
        'resolved_by_id',
        'whatsapp_waba_id',
        'instagram_id',
        'messanger_id',
        'telegram_id',
        'avatar_url',
        'jid_number',
        'metadata',
        'store_id',
        'unread_count',
        'last_message_at'
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
        static::addGlobalScope(new FilterByBusinessScope);
        HistoryChat::observe(HistoryChatObserver::class);

        static::creating(function ($model) {
            $model->id  = Uuid::uuid4()->toString();
            $user       = auth()->user();
            if ($user) {
                $model->merchant_id = $user->merchant_id;
                $model->business_id = my_business();
            }
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
            'expire_date'       => 'date',
            'assignment_at'     => 'date',
            'resolved_at'       => 'date'
        ];
    }

    public function business()
    {
        return $this->belongsTo(Setting::class, 'business_id');
    }

    public function details()
    {
        return $this->hasMany(HistoryChatDetail::class, 'history_chat_id')->orderBy('created_at', 'asc');
    }

    public function details_desc()
    {
        return $this->hasMany(HistoryChatDetail::class, 'history_chat_id')->orderBy('created_at', 'desc');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function livechat()
    {
        return $this->belongsTo(LiveChat::class, 'livechat_id');
    }

    public function telegram()
    {
        return $this->belongsTo(TelegramKey::class, 'telegram_id');
    }

    public function instagram()
    {
        return $this->belongsTo(InstagramAccount::class, 'instagram_id');
    }

    public function messenger()
    {
        return $this->belongsTo(MessengerAccount::class, 'messanger_id');
    }

    public function waba()
    {
        return $this->belongsTo(WhatsappKeyAccount::class, 'whatsapp_waba_id');
    }

    public function device()
    {
        return $this->belongsTo(WhatsappDevice::class, 'device_id');
    }

    public function last_message()
    {
        return $this->hasOne(HistoryChatDetail::class, 'history_chat_id')->orderBy('created_at', 'desc');
    }

    public function handled()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function assignmentby()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function resolved()
    {
        return $this->belongsTo(User::class, 'resolved_by_id');
    }

    public function getDataAssignmentAttribute()
    {


        if (auth()->check()) {
            $userId = my_user()->id;

            if ($this->handled_by === $userId) {
                return true;
            }

            $collaborators = json_decode($this->collabolator ?? '[]', true);

            // Cek apakah user ID ada dalam list collaborator
            if (is_array($collaborators)) {
                foreach ($collaborators as $collaborator) {
                    if ($collaborator['id'] === $userId) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function getImageDataAttribute()
    {
        if ($this->avatar_url != null) {
            return $this->avatar_url;
        } else {
            return asset('assets/img/icons/user.png');
        }
    }

    public function getFromNameAttribute()
    {

        if ($this->from == 'whatsapp') {
            return 'Whatsapp';
        }

        if ($this->from == 'waba') {
            return 'Wa Official';
        }

        if ($this->from == 'livechat') {
            return 'Livechat';
        }

        if ($this->from == 'telegram') {
            return 'Telegram';
        }

        if ($this->from == 'instagram') {
            return 'Instagram';
        }

        if ($this->from == 'messanger') {
            return 'Messanger';
        }

        return '';
    }

    /**
     * Get the last incoming message from customer (for 24H window calculation)
     */
    public function lastIncomingMessage()
    {
        return $this->hasOne(HistoryChatDetail::class, 'history_chat_id')
            ->where('from', 'user')
            ->latest('created_at');
    }

}
