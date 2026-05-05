<?php

namespace App\Models\ChatBot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ChatBotImage extends Model
{
    
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chatbot_id',
        'url',
        'name'
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
            $model->id  = Uuid::uuid4()->toString(); 
        });
    }

    public function chatbot()
    {
        return $this->belongsTo(ChatBot::class,'chatbot_id');
    }
}
