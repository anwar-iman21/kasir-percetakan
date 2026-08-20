<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    use HasFactory;

    protected $table = 'invoice_settings';

    protected $fillable = [
        'business_name',
        'business_tagline',
        'address',
        'phone',
        'whatsapp',
        'email',
        'logo',
        'invoice_prefix',
        'invoice_format',
        'footer_text',
        'custom_notes',
        'tax_enabled',
        'tax_percent',
        'primary_color',
        'font_size',
        'paper_size',
        'show_qr',
        'dark_mode',
    ];

    protected $casts = [
        'tax_enabled' => 'boolean',
        'tax_percent' => 'decimal:2',
        'show_qr' => 'boolean',
        'dark_mode' => 'boolean',
    ];

    public static function getSetting()
    {
        return self::firstOrCreate([], [
            'business_name' => 'Percetakan Saya',
            'invoice_prefix' => 'INV',
            'invoice_format' => 'INV-{YYYY}{MM}-{NUM}',
            'primary_color' => '#0d6efd',
            'font_size' => '12',
            'paper_size' => 'a4',
            'show_qr' => true,
            'dark_mode' => false,
            'tax_enabled' => false,
            'tax_percent' => 0,
        ]);
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }
}
