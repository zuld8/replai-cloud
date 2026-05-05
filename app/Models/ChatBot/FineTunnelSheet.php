<?php

namespace App\Models\ChatBot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class FineTunnelSheet extends Model
{
     use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fine_tunnel_id', 
        'url',
        'status'
    ];

    /**
     * Kolom yang dilindungi dari mass assignment
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * Primary key configuration
     */
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Cast atribut ke tipe data tertentu
     */
    protected $casts = [
        // Removed imported_data and last_imported_at since we read data real-time
    ];

    /**
     * Boot method untuk auto-generate UUID
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id = Uuid::uuid4()->toString();
        });
    }

    /**
     * Relasi ke tabel fine_tunnels
     */
    public function fineTunnel()
    {
        return $this->belongsTo(FineTunnel::class, 'fine_tunnel_id');
    }

    /**
     * Scope untuk mengambil data yang aktif saja
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'yes');
    }

    /**
     * Accessor untuk memformat URL
     */
    public function getFormattedUrlAttribute()
    {
        $url = $this->url;

        // Konversi URL edit ke format yang bisa diakses publik
        if (str_contains($url, '/edit')) {
            $url = str_replace('/edit', '/export?format=csv', $url);
        }

        return $url;
    }

    /**
     * Mutator untuk validasi URL Google Sheets
     */
    public function setUrlAttribute($value)
    {
        // Validasi bahwa URL adalah Google Sheets
        if (!str_contains($value, 'docs.google.com/spreadsheets')) {
            throw new \InvalidArgumentException('URL harus berupa Google Spreadsheet');
        }

        $this->attributes['url'] = $value;
    }
}