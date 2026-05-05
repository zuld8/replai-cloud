<?php

namespace App\Models;

use App\Models\Blash\BlashWhatsapp;
use App\Models\ChatBot\ChatBot;
use App\Models\Master\MessageTemplate;
use App\Models\Scopes\FilterByBusinessScope;
use App\Observers\WhatsappOfficial\WhatsappOfficialObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

class MetaAccount extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'app_id',
        'business_id',
        'app_secret',
        'access_token',
        'name',
        'details',
        'business_app'
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
        MetaAccount::observe(WhatsappOfficialObserver::class);

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

    public function templates()
    {
        return $this->hasMany(MessageTemplate::class, 'meta_account_id');
    }

    public function devices()
    {
        return $this->hasMany(WhatsappKeyAccount::class, 'meta_account_id');
    }

    public function broadcasts()
    {
        return $this->hasMany(BlashWhatsapp::class, 'meta_account_id');
    }

    public function chatbots()
    {
        return $this->hasMany(ChatBot::class, 'meta_account_id');
    }
}
