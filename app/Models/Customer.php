<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'notes',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTotalTransactionsAttribute()
    {
        return $this->transactions()->where('status', 'lunas')->count();
    }

    public function getTotalSpentAttribute()
    {
        return $this->transactions()->where('status', 'lunas')->sum('total');
    }
}
