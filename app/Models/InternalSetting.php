<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class InternalSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'app_name',
        'logo',
        'white_logo',
        'icon',
        'meta_keyword',
        'meta_description',
        'register',
        'frontend',
        'blog',
        'pricing',
        'contact',
        'copyright',
        'footer_description',
        'tax',
        'email_contact',
        'phone_contact',
        'contact_address',
        'footer_web',
        'web_template',
        'footer_1',
        'footer_2',
        'footer_3',
        'loader',
        'currency',
        'currency_position',
        'price_token',
        'token_per_price',
        'credit_token_basic',
        'credit_token_advance',
        'method',
        'merchant_code',
        'api_key',
        'is_production',
        'fb_app_id',
        'fb_app_secret',
        'fb_config_id',
        'google_client_id',
        'google_client_secret',
        'google_redirect',
        'instagram_config_id',
        'messanger_config_id',
        'mua_per_price',
        'price_mua',
        'using_mua_limit',
        'new_order_mua_limit'
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
}
