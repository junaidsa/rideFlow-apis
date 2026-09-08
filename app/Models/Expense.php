<?php

namespace App\Models;

use App\Models\Scopes\GroupScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new GroupScope);

        static::creating(function ($expense) {
            $expense->created_by = Auth::id();
        });

        static::updating(function ($expense) {
            $expense->updated_by = Auth::id();
        });

        static::deleting(function ($expense) {
            $expense->deleted_by = Auth::id();
            $expense->save();
        });
    }

    protected $fillable = [
        'group_id',
        'account_id',
        'account_name',
        'amount',
        'expense_date',
        'reference',
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

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}