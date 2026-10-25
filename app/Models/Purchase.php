<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'supplier_id',
        'user_id',
        'purchase_date',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Pemasok terkait transaksi pembelian
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Pengguna internal yang mencatat pembelian
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Detail item produk dalam pembelian
     */
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
