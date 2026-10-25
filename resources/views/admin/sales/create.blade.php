@extends('layouts.admin')

@section('title', 'Kasir Penjualan Baru')
@section('header_title', '🛒 Kasir Penjualan / Barang Keluar')

@section('content')
<form action="{{ route('sales.store') }}" method="POST" id="saleForm">
    @csrf

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        
        <!-- Left: Cart Items Table -->
        <div class="card">
            <div class="card-header">
                <div>
                    <h3>Keranjang Bunga Pelanggan</h3>
                    <p style="font-size: 13px; color: var(--color-text-muted); margin-top: 2px;">
                        Pilih produk bunga yang dibeli. Sistem akan secara otomatis memverifikasi ketersediaan stok fisik.
                    </p>
                </div>

                <button type="button" class="btn btn-outline btn-sm" onclick="addSaleRow()">
                    ➕ Tambah Bunga Lain
                </button>
            </div>

            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="data-table" id="saleTable">
                        <thead>
                            <tr>
                                <th style="width: 45%;">Bunga & Stok Tersedia</th>
                                <th style="width: 20%;">Jumlah Beli</th>
                                <th style="width: 20%; text-align: right;">Harga Jual</th>
                                <th style="width: 15%; text-align: right;">Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="saleItemsBody">
                            <!-- Row 0 -->
                            <tr class="sale-row" data-index="0">
                                <td>
                                    <select name="items[0][product_id]" class="form-control product-select" required onchange="onSaleProductChange(this, 0)">
                                        <option value="">-- Pilih Bunga --</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}" data-price="{{ $prod->sell_price }}" data-stock="{{ $prod->stock }}">
                                                {{ $prod->name }} (Sisa Stok: {{ $prod->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="stock-warning-msg" id="stockWarning_0" style="display: none; color: var(--color-danger); font-size: 12px; margin-top: 4px; font-weight: 600;"></div>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control qty-input" value="1" min="1" required oninput="calculateSaleSubtotal(0)">
                                </td>
                                <td style="text-align: right; color: var(--color-text-muted);">
                                    <span class="unit-price-text" id="unitPrice_0">Rp 0</span>
                                </td>
                                <td style="text-align: right; font-weight: 700; color: var(--color-warm-brown-dark);">
                                    <span class="subtotal-text" id="subtotal_0">Rp 0</span>
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeSaleRow(this)" style="padding: 4px 8px;" title="Hapus baris">
                                        ✕
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr style="background-color: var(--color-warm-ivory); font-weight: 700;">
                                <td colspan="3" style="text-align: right; padding: 16px;">
                                    TOTAL TAGIHAN PELANGGAN:
                                </td>
                                <td style="text-align: right; font-size: 18px; color: var(--color-muted-olive-dark); padding: 16px;">
                                    <span id="grandTotalText">Rp 0</span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Checkout Header Info -->
        <div class="card">
            <div class="card-header">
                <h3>Informasi Pembayaran</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label" for="invoice_number">Nomor Invoice Kasir <span class="req">*</span></label>
                    <input type="text" name="invoice_number" id="invoice_number" class="form-control" value="{{ old('invoice_number', $defaultInvoice) }}" required style="font-family: monospace; font-weight: 600;">
                </div>

                <div class="form-group">
                    <label class="form-label" for="sale_date">Tanggal Transaksi <span class="req">*</span></label>
                    <input type="date" name="sale_date" id="sale_date" class="form-control" value="{{ old('sale_date', date('Y-m-d')) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="customer_name">Nama Pelanggan</label>
                    <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ old('customer_name') }}" placeholder="Contoh: Kak Amanda (atau kosongkan untuk Umum)">
                </div>

                <div class="form-group">
                    <label class="form-label" for="payment_method">Metode Pembayaran <span class="req">*</span></label>
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>💵 Tunai (Cash)</option>
                        <option value="QRIS" {{ old('payment_method') === 'QRIS' ? 'selected' : '' }}>📱 QRIS / E-Wallet</option>
                        <option value="Transfer" {{ old('payment_method') === 'Transfer' ? 'selected' : '' }}>🏦 Transfer Bank</option>
                        <option value="Debit Card" {{ old('payment_method') === 'Debit Card' ? 'selected' : '' }}>💳 Kartu Debit</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="notes">Catatan Pesanan</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Contoh: Tambah pita satin putih, kartu ucapan wisuda...">{{ old('notes') }}</textarea>
                </div>

                <div style="background-color: var(--color-dusty-pink-light); border: 1px solid var(--color-dusty-pink); border-radius: var(--radius-sm); padding: 12px; margin-bottom: 20px; font-size: 12.5px;">
                    🌸 <strong>Aturan Stok:</strong> Transaksi akan ditolak otomatis oleh server jika kuantitas melebihi stok yang tersedia saat ini.
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px;" id="btnSubmitSale">
                    🧾 Proses Penjualan & Cetak Struk
                </button>

                <a href="{{ route('sales.index') }}" class="btn btn-outline" style="width: 100%; margin-top: 10px; text-align: center;">
                    Batal
                </a>
            </div>
        </div>

    </div>
</form>

<script>
    let saleIndex = 1;
    const availableProducts = @json($products);

    function onSaleProductChange(selectElem, idx) {
        const option = selectElem.options[selectElem.selectedIndex];
        const price = parseFloat(option.getAttribute('data-price')) || 0;
        const stock = parseInt(option.getAttribute('data-stock')) || 0;

        const row = selectElem.closest('.sale-row');
        row.querySelector('.unit-price-text').innerText = 'Rp ' + price.toLocaleString('id-ID');
        row.setAttribute('data-price', price);
        row.setAttribute('data-stock', stock);

        const qtyInput = row.querySelector('.qty-input');
        qtyInput.setAttribute('max', stock);

        calculateSaleSubtotal(idx);
    }

    function calculateSaleSubtotal(idx) {
        const row = document.querySelector(`.sale-row[data-index="${idx}"]`);
        if (!row) return;

        const price = parseFloat(row.getAttribute('data-price')) || 0;
        const stock = parseInt(row.getAttribute('data-stock')) || 0;
        const qty = parseInt(row.querySelector('.qty-input').value) || 0;
        const warningElem = document.getElementById(`stockWarning_${idx}`);

        // Peringatan jika qty melebihi stok
        if (stock > 0 && qty > stock) {
            warningElem.innerText = `⚠️ Kuantitas (${qty}) melebihi stok tersedia (${stock})!`;
            warningElem.style.display = 'block';
        } else {
            warningElem.style.display = 'none';
        }

        const subtotal = qty * price;
        const subtotalElem = row.querySelector('.subtotal-text');
        if (subtotalElem) {
            subtotalElem.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        }

        updateSaleGrandTotal();
    }

    function updateSaleGrandTotal() {
        let total = 0;
        document.querySelectorAll('.sale-row').forEach(row => {
            const price = parseFloat(row.getAttribute('data-price')) || 0;
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            total += (qty * price);
        });

        document.getElementById('grandTotalText').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    function addSaleRow() {
        const tbody = document.getElementById('saleItemsBody');
        const currentIdx = saleIndex++;

        let productOptions = '<option value="">-- Pilih Bunga --</option>';
        availableProducts.forEach(p => {
            productOptions += `<option value="${p.id}" data-price="${p.sell_price}" data-stock="${p.stock}">${p.name} (Sisa Stok: ${p.stock})</option>`;
        });

        const tr = document.createElement('tr');
        tr.className = 'sale-row';
        tr.setAttribute('data-index', currentIdx);
        tr.innerHTML = `
            <td>
                <select name="items[${currentIdx}][product_id]" class="form-control product-select" required onchange="onSaleProductChange(this, ${currentIdx})">
                    ${productOptions}
                </select>
                <div class="stock-warning-msg" id="stockWarning_${currentIdx}" style="display: none; color: var(--color-danger); font-size: 12px; margin-top: 4px; font-weight: 600;"></div>
            </td>
            <td>
                <input type="number" name="items[${currentIdx}][quantity]" class="form-control qty-input" value="1" min="1" required oninput="calculateSaleSubtotal(${currentIdx})">
            </td>
            <td style="text-align: right; color: var(--color-text-muted);">
                <span class="unit-price-text" id="unitPrice_${currentIdx}">Rp 0</span>
            </td>
            <td style="text-align: right; font-weight: 700; color: var(--color-warm-brown-dark);">
                <span class="subtotal-text" id="subtotal_${currentIdx}">Rp 0</span>
            </td>
            <td style="text-align: center;">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeSaleRow(this)" style="padding: 4px 8px;" title="Hapus baris">
                    ✕
                </button>
            </td>
        `;

        tbody.appendChild(tr);
    }

    function removeSaleRow(btn) {
        const rows = document.querySelectorAll('.sale-row');
        if (rows.length <= 1) {
            alert('Penjualan harus memiliki minimal 1 item bunga.');
            return;
        }
        btn.closest('.sale-row').remove();
        updateSaleGrandTotal();
    }
</script>
@endsection
