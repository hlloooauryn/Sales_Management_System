<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * Transaksi penjualan induk
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Produk yang dijual
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }
}
