<?php

namespace App\Models;

use App\Models\Scopes\GroupScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new GroupScope);

        static::creating(function ($transaction) {
            $transaction->created_by = Auth::id();
        });

        static::updating(function ($transaction) {
            $transaction->updated_by = Auth::id();
        });

        static::deleting(function ($transaction) {
            $transaction->deleted_by = Auth::id();
            $transaction->save();
        });
    }

    protected $fillable = [
        'group_id',
        'login_id',
        'ride_id',
        'account_id',
        'type',
        'reference',
        'amount',
        'transaction_date',
        'is_reconcile',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}