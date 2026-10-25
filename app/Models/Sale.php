<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'sale_date',
        'customer_name',
        'payment_method',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Petugas kasir/internal yang memproses penjualan
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Detail item bunga dalam transaksi penjualan
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
