<?php

namespace App\Models;

use App\Models\Scopes\GroupScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class DriverRating extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new GroupScope);

        static::creating(function ($rating) {
            $rating->created_by = Auth::id();
        });

        static::updating(function ($rating) {
            $rating->updated_by = Auth::id();
        });

        static::deleting(function ($rating) {
            $rating->deleted_by = Auth::id();
            $rating->save();
        });
    }

    protected $fillable = [
        'group_id',
        'driver_id',
        'rating',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function driver()
    {
        return $this->belongsTo(Account::class, 'driver_id');
    }
}