@extends('layouts.public')

@section('title', 'Checkout & Metode Pembayaran Bunga Online')

@section('content')
<!-- Breadcrumbs -->
<div style="background-color: var(--color-warm-ivory); border-bottom: 1px solid var(--color-border); padding: 14px 0; font-size: 13px;">
    <div class="pub-container">
        <a href="{{ url('/') }}">Beranda</a>
        <span style="margin: 0 8px; color: var(--color-text-muted);">/</span>
        <a href="{{ route('cart.index') }}">Keranjang Belanja</a>
        <span style="margin: 0 8px; color: var(--color-text-muted);">/</span>
        <span style="color: var(--color-warm-brown-dark); font-weight: 600;">Checkout & Pembayaran</span>
    </div>
</div>

<div class="pub-container" style="padding: 40px 24px 80px;">
    
    <div style="margin-bottom: 28px;">
        <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; color: var(--color-muted-olive-dark);">Langkah Terakhir</span>
        <h1 style="font-size: 32px; margin-top: 4px;">💐 Pengiriman & Metode Pembayaran</h1>
        <p style="color: var(--color-text-muted); font-size: 14px; margin-top: 4px;">
            Lengkapi data pengiriman bunga dan pilih metode pembayaran favorit Anda.
        </p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 24px;">
            <span style="font-size: 20px;">⚠️</span>
            <div>
                <strong>Terdapat data yang belum lengkap:</strong>
                <ul style="margin-top: 4px; padding-left: 18px; font-size: 13px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm">
        @csrf

        <div style="display: grid; grid-template-columns: 1.8fr 1.2fr; gap: 36px; align-items: start;">
            
            <!-- Left Column: Delivery Info & Payment Methods -->
            <div>
                
                <!-- Section 1: Customer & Delivery Info -->
                <div class="card" style="box-shadow: var(--shadow-sm); margin-bottom: 28px;">
                    <div class="card-header" style="background: var(--color-warm-ivory);">
                        <h3 style="font-size: 17px; margin: 0; display: flex; align-items: center; gap: 8px;">
                            <span>📍</span> 1. Data Penerima & Pengiriman Bunga
                        </h3>
                    </div>

                    <div class="card-body">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label" for="recipient_name">Nama Penerima <span class="req">*</span></label>
                                <input type="text" name="recipient_name" id="recipient_name" class="form-control" value="{{ old('recipient_name', $user->name) }}" placeholder="Nama lengkap penerima bunga" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="customer_phone">Nomor WhatsApp / Telepon <span class="req">*</span></label>
                                <input type="tel" name="customer_phone" id="customer_phone" class="form-control" value="{{ old('customer_phone', $user->phone) }}" placeholder="0812-xxxx-xxxx" required>
                                <small style="font-size: 11.5px; color: var(--color-text-muted);">Kurir akan menghubungi nomor ini saat pengantaran.</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="shipping_address">Alamat Lengkap Pengiriman Bunga <span class="req">*</span></label>
                            <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" placeholder="Contoh: Jl. Kemang Raya No. 12, RT 03/RW 02, Gedung Graha Lantai 2, Jakarta Selatan" required>{{ old('shipping_address', $user->address) }}</textarea>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group">
                                <label class="form-label" for="delivery_date">Tanggal Pengiriman Bunga</label>
                                <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="{{ old('delivery_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}">
                                <small style="font-size: 11.5px; color: var(--color-text-muted);">Pilih tanggal bunga ingin diterima.</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Email Pemesan</label>
                                <input type="email" value="{{ $user->email }}" class="form-control" disabled style="background: #F8F8F8;">
                                <small style="font-size: 11.5px; color: var(--color-text-muted);">Invoice resmi akan dikaitkan dengan akun ini.</small>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" for="notes">Catatan Tambahan & Kartu Ucapan (Opsional)</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Tuliskan ucapan kartu bunga (misal: Happy Birthday Amanda, wish you all the best!) atau instruksi khusus kurir">{{ old('notes') }}</textarea>
                            <small style="font-size: 11.5px; color: var(--color-text-muted);">Bloom Moment menyediakan kartu ucapan eksklusif gratis di setiap pesanan.</small>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Payment Methods Selection -->
                <div class="card" style="box-shadow: var(--shadow-sm);">
                    <div class="card-header" style="background: var(--color-warm-ivory);">
                        <h3 style="font-size: 17px; margin: 0; display: flex; align-items: center; gap: 8px;">
                            <span>💳</span> 2. Pilih Metode Pembayaran
                        </h3>
                    </div>

                    <div class="card-body">
                        <p style="font-size: 13.5px; color: var(--color-text-muted); margin-bottom: 18px;">
                            Pilih salah satu metode pembayaran yang paling nyaman untuk Anda:
                        </p>

                        <style>
                            .payment-method-card {
                                border: 1.5px solid var(--color-border);
                                border-radius: var(--radius-sm);
                                padding: 11px 14px;
                                display: flex;
                                align-items: center;
                                gap: 12px;
                                cursor: pointer;
                                transition: all 0.2s ease;
                                background: #FFFFFF;
                            }
                            .payment-method-card:hover {
                                border-color: var(--color-sage-green);
                                background: #FAF9F6;
                                transform: translateY(-1px);
                            }
                            .payment-method-card:has(input:checked) {
                                border-color: var(--color-sage-green-dark);
                                background-color: var(--color-sage-green-light);
                                box-shadow: 0 0 0 1px var(--color-sage-green);
                            }
                            @media (max-width: 640px) {
                                .payment-dynamic-grid {
                                    grid-template-columns: 1fr !important;
                                }
                            }
                        </style>

                        <div style="display: flex; flex-direction: column; gap: 16px;">
                            @foreach($paymentMethods as $groupKey => $group)
                                <div style="border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 15px 18px; background: #FFFFFF; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                    <div style="font-weight: 700; color: var(--color-dark-brown); font-size: 14.5px; margin-bottom: 2px; display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 16px;">{{ $group['icon'] ?? '💳' }}</span>
                                        <span>{{ $group['title'] }}</span>
                                    </div>
                                    @if(!empty($group['description']))
                                        <div style="font-size: 12px; color: var(--color-text-muted); margin-bottom: 12px;">
                                            {{ $group['description'] }}
                                        </div>
                                    @endif

                                    <div class="payment-dynamic-grid" style="display: grid; grid-template-columns: {{ count($group['options']) === 1 ? '1fr' : '1fr 1fr' }}; gap: 10px;">
                                        @foreach($group['options'] as $opt)
                                            <label class="payment-method-card">
                                                <input type="radio" name="payment_method" value="{{ $opt['code'] }}" {{ old('payment_method', 'Transfer BCA') === $opt['code'] ? 'checked' : '' }} style="accent-color: var(--color-sage-green); width: 17px; height: 17px; cursor: pointer; flex-shrink: 0;">
                                                <div style="flex: 1; min-width: 0;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 6px;">
                                                        <div style="font-weight: 700; font-size: 13.5px; color: var(--color-dark-brown); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                            {{ $opt['name'] }}
                                                        </div>
                                                        @if(!empty($opt['badge']))
                                                            <span class="badge {{ $opt['code'] === 'COD' ? 'badge-neutral' : 'badge-success' }}" style="font-size: 10px; padding: 2px 7px; flex-shrink: 0;">
                                                                {{ $opt['badge'] }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div style="font-size: 11.5px; color: var(--color-text-muted); margin-top: 2px; font-family: monospace; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                        {{ $opt['account'] }}
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Order Summary & Confirmation Button -->
            <div style="position: sticky; top: 90px;">
                <div class="card" style="box-shadow: var(--shadow-md);">
                    <div class="card-header" style="background: var(--color-warm-ivory);">
                        <h3 style="font-size: 17px; margin: 0;">Ringkasan Pesanan Bunga</h3>
                    </div>

                    <div class="card-body">
                        <!-- Items list -->
                        <div style="max-height: 260px; overflow-y: auto; margin-bottom: 18px; padding-right: 4px;">
                            @foreach($cart as $item)
                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px dashed var(--color-border);">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); overflow: hidden; background: var(--color-warm-ivory); flex-shrink: 0; border: 1px solid var(--color-border);">
                                            <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div>
                                            <div style="font-size: 13.5px; font-weight: 700; color: var(--color-dark-brown); line-height: 1.3;">
                                                {{ $item['name'] }}
                                            </div>
                                            <div style="font-size: 12px; color: var(--color-text-muted);">
                                                {{ $item['quantity'] }}x @ Rp {{ number_format($item['price'], 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div style="font-weight: 700; font-size: 13.5px; color: var(--color-muted-olive-dark); white-space: nowrap;">
                                        Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Price calculation -->
                        <div style="font-size: 13.5px; margin-bottom: 8px; display: flex; justify-content: space-between;">
                            <span style="color: var(--color-text-muted);">Jumlah Bunga:</span>
                            <span>{{ $totalItems }} Tangkai</span>
                        </div>

                        <div style="font-size: 13.5px; margin-bottom: 8px; display: flex; justify-content: space-between;">
                            <span style="color: var(--color-text-muted);">Subtotal:</span>
                            <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                        </div>

                        <div style="font-size: 13.5px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--color-text-muted);">Pengiriman Kurir Bunga:</span>
                            <span class="badge badge-success" style="font-size: 11.5px;">GRATIS (Promo)</span>
                        </div>

                        <div style="border-top: 2px solid var(--color-dark-brown); padding-top: 14px; margin-top: 14px; display: flex; justify-content: space-between; align-items: baseline;">
                            <span style="font-size: 15px; font-weight: 700; color: var(--color-dark-brown);">TOTAL PEMBAYARAN:</span>
                            <span style="font-size: 22px; font-weight: 800; color: var(--color-muted-olive-dark); font-family: var(--font-heading);">
                                Rp {{ number_format($totalAmount, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 15.5px; font-weight: 700; margin-top: 22px; border-radius: var(--radius-md); box-shadow: var(--shadow-md);">
                            🌸 Buat Pesanan & Bayar Sekarang
                        </button>

                        <div style="text-align: center; margin-top: 12px; font-size: 12.5px; color: var(--color-text-muted);">
                            Dengan mengklik tombol di atas, stok bunga Anda langsung diamankan.
                        </div>
                    </div>
                </div>

                <!-- Assistance box -->
                <div style="margin-top: 16px; text-align: center; font-size: 13px; color: var(--color-text-muted);">
                    Butuh bantuan pesanan? Hubungi 
                    <a href="https://wa.me/6281293038852" target="_blank" style="color: var(--color-muted-olive-dark); font-weight: 700;">
                        WhatsApp Florist (+62 812-9988-7766)
                    </a>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
