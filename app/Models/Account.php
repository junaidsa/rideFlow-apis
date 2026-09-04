<?php

namespace App\Models;

use App\Models\Scopes\GroupScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new GroupScope);

        static::creating(function ($account) {
            $account->created_by = Auth::id();
        });

        static::updating(function ($account) {
            $account->updated_by = Auth::id();
        });

        static::deleting(function ($account) {
            $account->deleted_by = Auth::id();
            $account->save();
        });
    }

    protected $fillable = [
        'group_id',
        'car_id',
        'car_name',
        'commission',
        'name',
        'phone',
        'father_phone',
        'cnic',
        'account_type',
        'address',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}