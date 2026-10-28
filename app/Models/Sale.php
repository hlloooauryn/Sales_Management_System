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
        'order_type',
        'status',
        'customer_name',
        'customer_phone',
        'shipping_address',
        'delivery_date',
        'payment_method',
        'payment_proof',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'delivery_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Cek apakah pesanan berasal dari pembelian online web
     */
    public function isOnline(): bool
    {
        return $this->order_type === 'online';
    }

    /**
     * Label status bahasa Indonesia
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Bayar / Menunggu Verifikasi',
            'processing' => 'Bunga Sedang Dirangkai',
            'shipped' => 'Sedang Dikirim Kurir',
            'completed' => 'Pesanan Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status ?? 'Selesai'),
        };
    }

    /**
     * Badge status CSS class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'badge-warning',
            'paid' => 'badge-info',
            'processing' => 'badge-primary',
            'shipped' => 'badge-neutral',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
            default => 'badge-neutral',
        };
    }

    /**
     * URL Bukti Pembayaran
     */
    public function getPaymentProofUrlAttribute(): ?string
    {
        if (!$this->payment_proof) {
            return null;
        }

        if (str_starts_with($this->payment_proof, 'http')) {
            return $this->payment_proof;
        }

        return asset('storage/' . $this->payment_proof);
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
