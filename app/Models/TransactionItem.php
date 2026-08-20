<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_service_id',
        'name',
        'description',
        'paper_type',
        'size',
        'color',
        'qty',
        'unit',
        'price',
        'subtotal',
        'custom_notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function productService()
    {
        return $this->belongsTo(ProductService::class, 'product_service_id');
    }
}
