<?php

namespace App\Models\Master;

use App\Models\Blash\BlashWhatsapp;
use App\Models\Scopes\FilterByBusinessScope;
use App\Models\Scopes\FilterByMerchantScope;
use App\Models\WhatsappKeyAccount;
use App\Process\TemplateEditor\Editable;
use App\Trait\EditorEmailTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class MessageTemplate extends Model implements Editable
{
    use HasFactory, EditorEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'image',
        'message',
        'type',
        'type_content',
        'button_or_list',
        'waba_device_id',
        'for_waba',
        'metadata',
        'waba_status_template',
        'meta_id',
        'category',
        'lang',
        'created_by',
        'master_type',
        'media_type',
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

    public function device()
    {
        return $this->belongsTo(WhatsappKeyAccount::class, 'waba_device_id');
    }

    public function blashs()
    {
        return $this->hasMany(BlashWhatsapp::class, 'template_id');
    }

    public function getContentNameAttribute()
    {
        if ($this->type_content == 'description') {
            return 'Plain Text';
        }

        if ($this->type_content == 'button') {
            return 'Button Text';
        }

        if ($this->type_content == 'list') {
            return 'List';
        }

        if ($this->type_content == 'vote') {
            return 'Poll';
        }

        if ($this->type_content == 'location') {
            return 'Lokasi';
        }
    }


    public function getMediaDataAttribute()
    {
        if (file_exists($this->image)) {
            return asset($this->image);
        } else {
            return null;
        }
    }
}
