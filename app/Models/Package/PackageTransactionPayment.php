<?php

namespace App\Models\Package;

use App\Models\Master\Bank;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class PackageTransactionPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'package_transaction_id',
        'amount',
        'method',
        'to_bank',
        'bank_name',
        'bank_number',
        'file',
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

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'to_bank');
    }

    public function transaction()
    {
        return $this->belongsTo(PackageTransaction::class, 'package_transaction_id');
    }
}
