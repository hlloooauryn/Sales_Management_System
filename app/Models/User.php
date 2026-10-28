<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Periksa apakah pengguna memiliki role admin/owner
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Periksa apakah pengguna memiliki role staff
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Periksa apakah pengguna memiliki role user/pelanggan
     */
    public function isUser(): bool
    {
        return $this->role === 'user' || $this->role === 'customer';
    }

    /**
     * Alias untuk isUser
     */
    public function isCustomer(): bool
    {
        return $this->isUser();
    }

    /**
     * Transaksi pembelian yang dicatat oleh user ini
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Transaksi penjualan yang dicatat oleh user ini
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
