<?php

namespace App\Models;

use App\Models\Scopes\GroupScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Ride extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new GroupScope);

        static::creating(function ($ride) {
            $ride->created_by = Auth::id();
        });

        static::updating(function ($ride) {
            $ride->updated_by = Auth::id();
        });

        static::deleting(function ($ride) {
            $ride->deleted_by = Auth::id();
            $ride->save();
        });
    }

    protected $fillable = [
        'group_id',
        'driver_id',
        'car_id',
        'name',
        'amount',
        'date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function driver()
    {
        return $this->belongsTo(Account::class, 'driver_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}