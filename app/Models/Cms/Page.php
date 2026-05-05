<?php

namespace App\Models\Cms;

use App\Process\TemplateEditor\Editable;
use App\Trait\EditorWebTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Page extends Model implements Editable
{
    use HasFactory, EditorWebTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'page',
        'name',
        'content',
        'status'
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

    public function getPageNameAttribute()
    {
        if ($this->page == 'home') {
            return 'Home Page';
        }

        if ($this->page == 'pricing') {
            return 'Pricing Page';
        }

        if ($this->page == 'contact') {
            return 'Contact Page';
        }

        if ($this->page == 'page') {
            return 'Other Page';
        }
    }

    public function getPageUrlAttribute()
    {
        if ($this->page == 'home') {
            return route('web.home');
        }

        if ($this->page == 'pricing') {
            return route('web.pricing');
        }

        if ($this->page == 'contact') {
            return route('web.pricing');
        }

        if ($this->page == 'page') {
            return route('web.page',$this->id);
        }

        return '';
    }
}
