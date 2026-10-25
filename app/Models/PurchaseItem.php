<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_cost',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_cost' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Transaksi pembelian induk
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Produk yang dibeli
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
