<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'user_id',
        'subtotal',
        'discount',
        'discount_type',
        'tax',
        'tax_percent',
        'total',
        'payment_method',
        'amount_paid',
        'change_amount',
        'status',
        'notes',
        'deadline',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'deadline' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function getPaymentMethodLabelAttribute()
    {
        return match($this->payment_method) {
            'tunai' => 'Tunai',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            default => $this->payment_method,
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'lunas' => 'Lunas',
            'batal' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'lunas' => 'success',
            'batal' => 'danger',
            default => 'secondary',
        };
    }

    public static function generateInvoiceNumber()
    {
        $setting = InvoiceSetting::first();
        $prefix = $setting ? $setting->invoice_prefix : 'INV';
        $year = date('Y');
        $month = date('m');
        $count = self::whereYear('created_at', $year)->whereMonth('created_at', $month)->count() + 1;
        return $prefix . '-' . $year . $month . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
