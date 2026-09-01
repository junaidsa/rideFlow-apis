<?php

namespace App\Models;

use App\Models\Scopes\GroupScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new GroupScope);

        static::creating(function ($car) {
            $car->created_by = Auth::id();
        });

        static::updating(function ($car) {
            $car->updated_by = Auth::id();
        });

        static::deleting(function ($car) {
            $car->deleted_by = Auth::id();
            $car->save();
        });
    }

    protected $fillable = [
        'group_id',
        'name',
        'model',
        'color',
        'plate_number',
        'image',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Image URL Accessor
     */
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}