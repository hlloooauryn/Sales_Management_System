@extends('layouts.public')

@section('title', 'Pesanan ' . $sale->invoice_number . ' — Pembayaran & Detail')

@section('content')
<!-- Breadcrumbs -->
<div style="background-color: var(--color-warm-ivory); border-bottom: 1px solid var(--color-border); padding: 14px 0; font-size: 13px;">
    <div class="pub-container">
        <a href="{{ url('/') }}">Beranda</a>
        <span style="margin: 0 8px; color: var(--color-text-muted);">/</span>
        <a href="{{ route('orders.index') }}">Pesanan Saya</a>
        <span style="margin: 0 8px; color: var(--color-text-muted);">/</span>
        <span style="color: var(--color-warm-brown-dark); font-weight: 600;">{{ $sale->invoice_number }}</span>
    </div>
</div>

<div class="pub-container" style="padding: 40px 24px 80px;">
    
    <!-- Top Action & Invoice Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 14px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <h1 style="font-size: 28px; margin: 0; font-family: monospace;">{{ $sale->invoice_number }}</h1>
                <span class="badge {{ $sale->status_badge_class }}" style="font-size: 13px; padding: 6px 14px;">
                    ● {{ $sale->status_label }}
                </span>
            </div>
            <p style="color: var(--color-text-muted); font-size: 13.5px; margin: 0;">
                Dipesan pada {{ $sale->created_at->format('d F Y, H:i') }} WIB • Rencana Kirim: <strong>{{ $sale->delivery_date ? $sale->delivery_date->format('d F Y') : '-' }}</strong>
            </p>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('orders.index') }}" class="btn btn-outline btn-sm">
                ← Kembali ke Daftar Pesanan
            </a>
            <a href="{{ route('orders.receipt', $sale->invoice_number) }}" target="_blank" class="btn btn-outline btn-sm">
                🖨️ Cetak Struk
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1.3fr 1fr; gap: 32px; align-items: start;">
        
        <!-- Left Column: Payment Box & Proof Upload -->
        <div>
            
            <!-- Box Panduan Pembayaran Interaktif -->
            <div class="card" style="box-shadow: var(--shadow-md); margin-bottom: 28px; border-top: 4px solid var(--color-sage-green);">
                <div class="card-header" style="background: var(--color-warm-ivory); display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 17px; margin: 0;">
                        💳 Instruksi Pembayaran ({{ $sale->payment_method }})
                    </h3>
                    <span class="badge badge-neutral" style="font-size: 12px;">{{ $sale->payment_method }}</span>
                </div>

                <div class="card-body">
                    
                    @if($paymentInfo['type'] === 'bank_transfer')
                        <!-- Transfer Bank Box -->
                        <div style="background: var(--color-surface-soft); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 20px; margin-bottom: 20px;">
                            <div style="font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-muted); font-weight: 700; margin-bottom: 6px;">
                                Bank Tujuan Transfer:
                            </div>
                            <div style="font-size: 20px; font-weight: 800; color: var(--color-dark-brown); font-family: var(--font-heading); margin-bottom: 14px;">
                                {{ $paymentInfo['bank_name'] }}
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; background: #FFFFFF; border: 1px dashed var(--color-border); padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 12px;">
                                <div>
                                    <div style="font-size: 11.5px; color: var(--color-text-muted);">Nomor Rekening:</div>
                                    <div style="font-size: 18px; font-family: monospace; font-weight: 800; color: var(--color-dark-brown);" id="accNumber">
                                        {{ $paymentInfo['account_number'] }}
                                    </div>
                                    <div style="font-size: 12px; color: var(--color-text-muted);">a/n {{ $paymentInfo['account_holder'] }}</div>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ $paymentInfo['account_number'] }}', this)" class="btn btn-outline btn-sm" style="padding: 6px 12px; font-size: 12px;">
                                    📋 Salin
                                </button>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; background: #FFFFFF; border: 1px dashed var(--color-border); padding: 12px 16px; border-radius: var(--radius-sm);">
                                <div>
                                    <div style="font-size: 11.5px; color: var(--color-text-muted);">Nominal Tepat Transfer:</div>
                                    <div style="font-size: 20px; font-family: var(--font-heading); font-weight: 800; color: var(--color-muted-olive-dark);">
                                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                    </div>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ (int)$sale->total_amount }}', this)" class="btn btn-outline btn-sm" style="padding: 6px 12px; font-size: 12px;">
                                    📋 Salin Nominal
                                </button>
                            </div>
                        </div>

                    @elseif($paymentInfo['type'] === 'qris')
                        <!-- QRIS Box -->
                        <div style="text-align: center; background: var(--color-surface-soft); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 24px 20px; margin-bottom: 20px;">
                            <div style="font-size: 12.5px; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; margin-bottom: 8px;">
                                Scan Kode QRIS di Bawah Ini:
                            </div>

                            <!-- QRIS Canvas / Simulated Presentation -->
                            <div style="background: #FFFFFF; border: 2px solid var(--color-dark-brown); border-radius: var(--radius-md); padding: 20px; display: inline-block; box-shadow: var(--shadow-sm); margin: 0 auto 16px; max-width: 260px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; border-bottom: 1px solid #EEE; padding-bottom: 8px;">
                                    <strong style="font-size: 14px; letter-spacing: 1px;">QRIS</strong>
                                    <span style="font-size: 10px; color: #777;">GPN</span>
                                </div>

                                <!-- QR Image Graphic (SVG) -->
                                <svg width="190" height="190" viewBox="0 0 100 100" style="display: block; margin: 0 auto; shape-rendering: crispEdges;">
                                    <rect width="100" height="100" fill="#FFFFFF"/>
                                    <!-- Top Left Finder -->
                                    <rect x="5" y="5" width="26" height="26" fill="#000000"/>
                                    <rect x="9" y="9" width="18" height="18" fill="#FFFFFF"/>
                                    <rect x="13" y="13" width="10" height="10" fill="#000000"/>
                                    <!-- Top Right Finder -->
                                    <rect x="69" y="5" width="26" height="26" fill="#000000"/>
                                    <rect x="73" y="9" width="18" height="18" fill="#FFFFFF"/>
                                    <rect x="77" y="13" width="10" height="10" fill="#000000"/>
                                    <!-- Bottom Left Finder -->
                                    <rect x="5" y="69" width="26" height="26" fill="#000000"/>
                                    <rect x="9" y="73" width="18" height="18" fill="#FFFFFF"/>
                                    <rect x="13" y="77" width="10" height="10" fill="#000000"/>
                                    <!-- Decorative pattern dots -->
                                    <rect x="36" y="8" width="5" height="5" fill="#000"/>
                                    <rect x="46" y="8" width="5" height="5" fill="#000"/>
                                    <rect x="56" y="8" width="5" height="5" fill="#000"/>
                                    <rect x="36" y="18" width="10" height="5" fill="#000"/>
                                    <rect x="51" y="18" width="5" height="5" fill="#000"/>
                                    <rect x="8" y="36" width="5" height="10" fill="#000"/>
                                    <rect x="18" y="36" width="10" height="5" fill="#000"/>
                                    <rect x="36" y="36" width="28" height="28" fill="#87977A"/>
                                    <text x="50" y="53" font-size="10" font-weight="bold" fill="#FFF" text-anchor="middle" dominant-baseline="central">BLOOM</text>
                                    <rect x="69" y="36" width="5" height="10" fill="#000"/>
                                    <rect x="79" y="36" width="10" height="5" fill="#000"/>
                                    <rect x="8" y="51" width="15" height="5" fill="#000"/>
                                    <rect x="69" y="51" width="15" height="5" fill="#000"/>
                                    <rect x="36" y="69" width="10" height="5" fill="#000"/>
                                    <rect x="51" y="69" width="5" height="10" fill="#000"/>
                                    <rect x="61" y="69" width="10" height="5" fill="#000"/>
                                    <rect x="76" y="69" width="10" height="5" fill="#000"/>
                                    <rect x="36" y="79" width="5" height="10" fill="#000"/>
                                    <rect x="46" y="79" width="15" height="5" fill="#000"/>
                                    <rect x="66" y="79" width="5" height="10" fill="#000"/>
                                    <rect x="76" y="84" width="15" height="5" fill="#000"/>
                                </svg>

                                <div style="font-size: 12px; font-weight: 700; margin-top: 10px; color: var(--color-dark-brown);">BLOOM MOMENT STORE</div>
                                <div style="font-size: 11px; color: var(--color-text-muted);">NMID: ID1020038847291</div>
                            </div>

                            <div style="font-size: 17px; font-weight: 800; color: var(--color-muted-olive-dark); font-family: var(--font-heading);">
                                Total: Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                            </div>
                        </div>

                    @elseif($paymentInfo['type'] === 'ewallet')
                        <!-- E-Wallet Box -->
                        <div style="background: var(--color-surface-soft); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 20px; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <div style="font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-muted); font-weight: 700;">
                                    Tujuan Transfer Dompet Digital:
                                </div>
                                <span class="badge badge-success" style="font-size: 11px;">{{ $paymentInfo['badge'] }}</span>
                            </div>

                            <div style="font-size: 20px; font-weight: 800; color: var(--color-dark-brown); font-family: var(--font-heading); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                                <span>{{ $paymentInfo['icon'] ?? '📱' }}</span>
                                <span>{{ $paymentInfo['wallet_name'] }}</span>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; background: #FFFFFF; border: 1px dashed var(--color-border); padding: 12px 16px; border-radius: var(--radius-sm); margin-bottom: 12px;">
                                <div>
                                    <div style="font-size: 11.5px; color: var(--color-text-muted);">Nomor HP / Akun E-Wallet:</div>
                                    <div style="font-size: 19px; font-family: monospace; font-weight: 800; color: var(--color-dark-brown);" id="ewalletNumber">
                                        {{ $paymentInfo['phone_number'] }}
                                    </div>
                                    <div style="font-size: 12px; color: var(--color-text-muted);">a/n {{ $paymentInfo['account_holder'] }}</div>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ $paymentInfo['phone_number'] }}', this)" class="btn btn-outline btn-sm" style="padding: 6px 12px; font-size: 12px;">
                                    📋 Salin No. HP
                                </button>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; background: #FFFFFF; border: 1px dashed var(--color-border); padding: 12px 16px; border-radius: var(--radius-sm);">
                                <div>
                                    <div style="font-size: 11.5px; color: var(--color-text-muted);">Nominal Pas Transfer:</div>
                                    <div style="font-size: 20px; font-family: var(--font-heading); font-weight: 800; color: var(--color-muted-olive-dark);">
                                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                    </div>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ (int)$sale->total_amount }}', this)" class="btn btn-outline btn-sm" style="padding: 6px 12px; font-size: 12px;">
                                    📋 Salin Nominal
                                </button>
                            </div>
                        </div>

                    @elseif($paymentInfo['type'] === 'retail')
                        <!-- Retail Minimarket Box -->
                        <div style="background: var(--color-surface-soft); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 20px; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <div style="font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-muted); font-weight: 700;">
                                    Bayar di Kasir Minimarket:
                                </div>
                                <span class="badge badge-success" style="font-size: 11px;">{{ $paymentInfo['badge'] }}</span>
                            </div>

                            <div style="font-size: 20px; font-weight: 800; color: var(--color-dark-brown); font-family: var(--font-heading); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                                <span>🏪</span>
                                <span>{{ $paymentInfo['retail_name'] }}</span>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; background: #FFFFFF; border: 1px dashed var(--color-border); padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 12px;">
                                <div>
                                    <div style="font-size: 11.5px; color: var(--color-text-muted);">Tunjukkan Kode Pembayaran ke Kasir:</div>
                                    <div style="font-size: 22px; font-family: monospace; font-weight: 800; color: var(--color-dark-brown);" id="retailCode">
                                        {{ $paymentInfo['payment_code'] }}
                                    </div>
                                    <div style="font-size: 12px; color: var(--color-text-muted);">Merchant: {{ $paymentInfo['merchant'] }} (a/n {{ $sale->customer_name }})</div>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ $paymentInfo['payment_code'] }}', this)" class="btn btn-outline btn-sm" style="padding: 6px 12px; font-size: 12px;">
                                    📋 Salin Kode
                                </button>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; background: #FFFFFF; border: 1px dashed var(--color-border); padding: 12px 16px; border-radius: var(--radius-sm);">
                                <div>
                                    <div style="font-size: 11.5px; color: var(--color-text-muted);">Total Tagihan Kasir:</div>
                                    <div style="font-size: 20px; font-family: var(--font-heading); font-weight: 800; color: var(--color-muted-olive-dark);">
                                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                                    </div>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ (int)$sale->total_amount }}', this)" class="btn btn-outline btn-sm" style="padding: 6px 12px; font-size: 12px;">
                                    📋 Salin Nominal
                                </button>
                            </div>
                        </div>

                    @elseif($paymentInfo['type'] === 'va')
                        <!-- Virtual Account Box -->
                        <div style="background: var(--color-surface-soft); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 20px; margin-bottom: 20px;">
                            <div style="font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-text-muted); font-weight: 700; margin-bottom: 6px;">
                                Nomor Virtual Account (VA):
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; background: #FFFFFF; border: 1px dashed var(--color-border); padding: 14px 18px; border-radius: var(--radius-sm); margin-bottom: 12px;">
                                <div>
                                    <div style="font-size: 20px; font-family: monospace; font-weight: 800; color: var(--color-dark-brown);">
                                        {{ $paymentInfo['va_number'] }}
                                    </div>
                                    <div style="font-size: 12px; color: var(--color-text-muted);">Atas Nama: Bloom Moment - {{ $sale->customer_name }}</div>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ $paymentInfo['va_number'] }}', this)" class="btn btn-outline btn-sm">
                                    📋 Salin
                                </button>
                            </div>
                            <div style="font-size: 13px; color: var(--color-text-muted);">
                                Tagihan akan otomatis lunas dalam hitungan detik setelah Anda menyelesaikan transfer dari m-Banking.
                            </div>
                        </div>

                    @else
                        <!-- COD Box -->
                        <div style="background: var(--color-surface-soft); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 20px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                                <span style="font-size: 32px;">💵</span>
                                <div>
                                    <h4 style="font-size: 16px; margin: 0; color: var(--color-dark-brown);">Metode Pembayaran COD (Tunai di Tempat)</h4>
                                    <div style="font-size: 13px; color: var(--color-text-muted);">Siapkan uang tunai saat kurir tiba.</div>
                                </div>
                            </div>
                            <div style="font-size: 18px; font-weight: 800; color: var(--color-muted-olive-dark); font-family: var(--font-heading); margin-top: 8px;">
                                Tagihan Tunai: Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                            </div>
                        </div>
                    @endif

                    <!-- Petunjuk Pembayaran Step by Step -->
                    <div style="margin-top: 14px;">
                        <h4 style="font-size: 14px; margin-bottom: 8px; color: var(--color-dark-brown);">Panduan Pembayaran:</h4>
                        <ol style="font-size: 13px; color: var(--color-text); padding-left: 20px; line-height: 1.7;">
                            @foreach($paymentInfo['instructions'] as $ins)
                                <li>{{ $ins }}</li>
                            @endforeach
                        </ol>
                    </div>

                </div>
            </div>

            <!-- Box Unggah Bukti Pembayaran -->
            <div class="card" style="box-shadow: var(--shadow-sm);">
                <div class="card-header" style="background: var(--color-warm-ivory);">
                    <h3 style="font-size: 17px; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <span>📤</span> Konfirmasi Bukti Pembayaran
                    </h3>
                </div>

                <div class="card-body">
                    @if($paymentInfo['type'] === 'cod')
                        <!-- Info Khusus COD -->
                        <div style="background: var(--color-warm-ivory); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 18px 20px; display: flex; gap: 14px; align-items: center; margin-bottom: 18px;">
                            <span style="font-size: 30px;">🛵</span>
                            <div>
                                <strong style="color: var(--color-dark-brown); font-size: 14px;">Metode Bayar di Tempat (COD)</strong>
                                <p style="margin: 4px 0 0; font-size: 13px; color: var(--color-text-muted); line-height: 1.5;">
                                    Anda <strong>tidak perlu mengunggah bukti transfer</strong>. Kurir resmi Bloom Moment akan membawa rangkaian bunga segar Anda dan menerima pembayaran tunai sebesar <strong>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</strong> saat serah terima.
                                </p>
                            </div>
                        </div>
                    @else
                        @if($sale->payment_proof)
                            <div style="display: flex; gap: 20px; align-items: center; background: var(--color-surface-soft); padding: 16px; border-radius: var(--radius-md); border: 1px solid var(--color-border); margin-bottom: 20px;">
                                <div style="width: 100px; height: 100px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--color-border); flex-shrink: 0; background: #FFF;">
                                    <a href="{{ $sale->payment_proof_url }}" target="_blank">
                                        <img src="{{ $sale->payment_proof_url }}" alt="Bukti Transfer" style="width: 100%; height: 100%; object-fit: cover;">
                                    </a>
                                </div>
                                <div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                        <span class="badge badge-success">✓ Bukti Pembayaran Terunggah</span>
                                    </div>
                                    <p style="font-size: 13px; color: var(--color-text-muted); margin: 0 0 8px;">
                                        Florist kami sedang memverifikasi bukti pembayaran Anda. Bunga akan segera dirangkai.
                                    </p>
                                    <a href="{{ $sale->payment_proof_url }}" target="_blank" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 12px;">
                                        🔍 Lihat Bukti Asli
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- Form Unggah -->
                        <form action="{{ route('orders.uploadProof', $sale->invoice_number) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label class="form-label" for="payment_proof">
                                    {{ $sale->payment_proof ? 'Perbarui / Ganti Bukti Transfer:' : 'Pilih Foto / Screenshot Bukti Transfer:' }}
                                </label>
                                <input type="file" name="payment_proof" id="payment_proof" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp" required>
                                <small style="font-size: 12px; color: var(--color-text-muted);">
                                    Format JPG, PNG, atau WebP (Maksimal 4 MB).
                                </small>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 14.5px;">
                                📤 {{ $sale->payment_proof ? 'Unggah Ulang Bukti Pembayaran' : 'Kirim Bukti Pembayaran' }}
                            </button>
                        </form>
                    @endif

                    <!-- Tombol WhatsApp Konfirmasi Cepat -->
                    <div style="margin-top: 18px; text-align: center;">
                        <a href="https://wa.me/6281293038852?text=Halo%20Bloom%20Moment,%20saya%20telah%20membayar%20pesanan%20nomor%20invoice%20*{{ $sale->invoice_number }}*%20sebesar%20*Rp%20{{ number_format($sale->total_amount, 0, ',', '.') }}*%20atas%20nama%20*{{ urlencode($sale->customer_name) }}*.%20Mohon%20bantuannya%20untuk%20segera%20diproses." target="_blank" class="btn btn-outline" style="width: 100%; padding: 11px; font-size: 14px; display: inline-flex; justify-content: center; align-items: center; gap: 8px;">
                            <span>💬</span>
                            <span>Konfirmasi Cepat via WhatsApp Florist</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Order Details & Recipient Info -->
        <div>
            
            <div class="card" style="box-shadow: var(--shadow-sm); margin-bottom: 24px;">
                <div class="card-header" style="background: var(--color-warm-ivory);">
                    <h3 style="font-size: 17px; margin: 0;">Rincian Item Bunga Dipesan</h3>
                </div>

                <div class="card-body">
                    <div style="margin-bottom: 16px;">
                        @foreach($sale->items as $item)
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px dashed var(--color-border);">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 48px; height: 48px; border-radius: var(--radius-sm); overflow: hidden; background: var(--color-warm-ivory); flex-shrink: 0; border: 1px solid var(--color-border);">
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div>
                                        <div style="font-size: 13.5px; font-weight: 700; color: var(--color-dark-brown);">
                                            {{ $item->product->name }}
                                        </div>
                                        <div style="font-size: 12px; color: var(--color-text-muted);">
                                            {{ $item->quantity }} tangkai @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                                <div style="font-weight: 700; font-size: 13.5px; color: var(--color-muted-olive-dark);">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div style="border-top: 2px solid var(--color-dark-brown); padding-top: 14px; display: flex; justify-content: space-between; align-items: baseline;">
                        <span style="font-weight: 700; color: var(--color-dark-brown);">TOTAL TAGIHAN:</span>
                        <span style="font-size: 20px; font-weight: 800; color: var(--color-muted-olive-dark); font-family: var(--font-heading);">
                            Rp {{ number_format($sale->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Recipient & Delivery Details Card -->
            <div class="card" style="box-shadow: var(--shadow-sm);">
                <div class="card-header" style="background: var(--color-warm-ivory);">
                    <h3 style="font-size: 17px; margin: 0;">Alamat & Penerima Bunga</h3>
                </div>

                <div class="card-body" style="font-size: 13.5px; line-height: 1.6;">
                    <div style="margin-bottom: 12px;">
                        <span style="color: var(--color-text-muted); display: block; font-size: 12px;">Penerima:</span>
                        <strong>{{ $sale->customer_name }}</strong>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <span style="color: var(--color-text-muted); display: block; font-size: 12px;">No. WhatsApp / Telepon:</span>
                        <strong>{{ $sale->customer_phone ?? '-' }}</strong>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <span style="color: var(--color-text-muted); display: block; font-size: 12px;">Alamat Pengiriman:</span>
                        <div style="background: var(--color-warm-ivory); padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--color-border); margin-top: 4px;">
                            {{ $sale->shipping_address ?? '-' }}
                        </div>
                    </div>

                    @if($sale->notes)
                        <div>
                            <span style="color: var(--color-text-muted); display: block; font-size: 12px;">Pesan Kartu Ucapan:</span>
                            <div style="font-style: italic; background: var(--color-dusty-pink-light); border: 1px solid var(--color-dusty-pink); padding: 10px 14px; border-radius: var(--radius-sm); margin-top: 4px;">
                                " {{ $sale->notes }} "
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>

<script>
    function copyToClipboard(text, btnElement) {
        navigator.clipboard.writeText(text).then(function() {
            var orig = btnElement.innerHTML;
            btnElement.innerHTML = '✓ Disalin!';
            btnElement.style.borderColor = 'var(--color-sage-green)';
            btnElement.style.color = 'var(--color-sage-green-dark)';
            setTimeout(function() {
                btnElement.innerHTML = orig;
                btnElement.style.borderColor = '';
                btnElement.style.color = '';
            }, 2000);
        });
    }
</script>
@endsection
